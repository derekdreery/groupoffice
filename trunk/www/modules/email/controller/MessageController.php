<?php

class GO_Email_Controller_Message extends GO_Base_Controller_AbstractController {

	private function _findUnknownRecipients($params) {

		$unknown = array();

		if (GO::modules()->addressbook && !GO::config()->get_setting('email_skip_unknown_recipients', GO::user()->id)) {

			$recipients = new GO_Base_Mail_EmailRecipients($params['to']);
			$recipients->addString($params['cc']);
			$recipients->addString($params['bcc']);

			foreach ($recipients->getAddresses() as $email => $personal) {
				$contact = GO_Addressbook_Model_Contact::model()->findSingleByEmail($email);
				if ($contact)
					continue;

				$company = GO_Addressbook_Model_Company::model()->findSingleByAttribute('email', $email);
				if ($company)
					continue;

				$recipient = GO_Base_Util_String::split_name($personal);
				if ($recipient['first_name'] == '' && $recipient['last_name'] == '') {
					$recipient['first_name'] = $email;
				}
				$recipient['email'] = $email;
				$recipient['name'] = (string) GO_Base_Mail_EmailRecipients::createSingle($email, $personal);

				$unknown[] = $recipient;
			}
		}

		return $unknown;
	}
	

	private function _link($params, GO_Base_Mail_Message $message, $model=false) {
		
		if(!$model){
			if (!empty($params['link'])) {
				$linkProps = explode(':', $params['link']);
				$model = GO::getModel($linkProps[0])->findByPk($linkProps[1]);
			}
		}else
		{
			//don't link the same model twice on sent. It parses the new autolink tag
			//and handles the link to field.
			$linkProps = explode(':', $params['link']);
			if($linkProps[0]==$model->className() && $linkProps[1]==$model->id)
				return false;
		}
	
		if ($model) {

			$path = 'email/' . date('mY') . '/sent_' . time() . '.eml';

			$file = new GO_Base_Fs_File(GO::config()->file_storage_path . $path);
			$file->parent()->create();

			$fbs = new Swift_ByteStream_FileByteStream($file->path(), true);
			$message->toByteStream($fbs);

			if ($file->exists()) {

				$linkedEmail = new GO_Savemailas_Model_LinkedEmail();

				$alias = GO_Email_Model_Alias::model()->findByPk($params['alias_id']);

				$linkedEmail->from = (string) GO_Base_Mail_EmailRecipients::createSingle($alias->email, $alias->name);
				if (isset($params['to']))
					$linkedEmail->to = $params['to'];

				if (isset($params['cc']))
					$linkedEmail->cc = $params['cc'];

				if (isset($params['bcc']))
					$linkedEmail->bcc = $params['bcc'];

				$linkedEmail->subject = !empty($params['subject']) ? $params['subject'] : GO::t('no_subject', 'email');
				$linkedEmail->acl_id = $model->findAclId();


				$linkedEmail->path = $path;

				$linkedEmail->save();

				$linkedEmail->link($model);
			}
		}
	}

	protected function actionSave($params) {
		$alias = GO_Email_Model_Alias::model()->findByPk($params['alias_id']);
		$account = GO_Email_Model_Account::model()->findByPk($alias->account_id);

		if (empty($account->drafts))
			throw new Exception(GO::t('draftsDisabled', 'email'));

		$message = new GO_Base_Mail_Message();

		$message->handleEmailFormInput($params);

		$message->setFrom($alias->email, $alias->name);

		$imap = $account->openImapConnection($account->drafts);

		$nextUid = $imap->get_uidnext();

		if ($nextUid && $imap->append_message($account->drafts, $message->toString(), "\Seen")) {
			$response['sendParams']['draft_uid'] = $nextUid;
			$response['success'] = $response['sendParams']['draft_uid'] > 0;
		}

		if (!empty($params['draft_uid'])) {
			//remove older draft version
			$imap = $account->openImapConnection($account->drafts);
			$imap->delete(array($params['draft_uid']));
		}

		if (!$response['success']) {
			$account->drafts = '';
			$account->save();

			$response['feedback'] = GO::t('noUidNext', 'email');
		}

		return $response;
	}
	
	private function _createAutoLinkTag($params){
		$tag = '';
		if (!empty($params['link'])) {
			$linkProps = explode(':', $params['link']);
			$model = GO::getModel($linkProps[0])->findByPk($linkProps[1]);
			
			$tag = "[link:".base64_encode($_SERVER['SERVER_NAME'].','.$linkProps[0].','.$linkProps[1])."]";
		}
		return $tag;
	}

	/**
	 *
	 * @todo Save to sent items should be implemented as a Swift outputstream for better memory management
	 * @param type $params
	 * @return boolean 
	 */
	protected function actionSend($params) {

		$response['success'] = true;

		$alias = GO_Email_Model_Alias::model()->findByPk($params['alias_id']);
		$account = GO_Email_Model_Account::model()->findByPk($alias->account_id);

		$message = new GO_Base_Mail_SmimeMessage();
		
		$tag = $this->_createAutoLinkTag($params);
		
		if(!empty($tag)){
			if($params['content_type']=='html')
				$params['htmlbody'].= '<div style="display:none">'.$tag.'</div>';
			else
				$params['plainbody'].= "\n\n".$tag."\n\n";
		}

		$message->handleEmailFormInput($params);

		$message->setFrom($alias->email, $alias->name);

		$mailer = GO_Base_Mail_Mailer::newGoInstance(GO_Email_Transport::newGoInstance($account));

		$logger = new Swift_Plugins_Loggers_ArrayLogger();
		$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));


		$this->fireEvent('beforesend', array(
				&$this,
				&$response,
				&$message,
				&$mailer,
				$account,
				$alias,
				$params
		));

		$failedRecipients=array();
		$success = $mailer->send($message, $failedRecipients);

		if ($success) {
			if (!empty($params['reply_uid'])) {
				//set \Answered flag on IMAP message
				$imap = $account->openImapConnection($params['reply_mailbox']);
				$imap->set_message_flag(array($params['reply_uid']), "\Answered");
			}

			if (!empty($params['forward_uid'])) {
				//set forwarded flag on IMAP message
				$imap = $account->openImapConnection($params['forward_mailbox']);
				$imap->set_message_flag(array($params['forward_uid']), "\$Forwarded");
			}

			if ($account->sent) {
				//if a sent items folder is set in the account then save it to the imap folder
				$imap = $account->openImapConnection($account->sent);
				$imap->append_message($account->sent, $message->toString(), "\Seen");
			}

			if (!empty($params['draft_uid'])) {
				//remove drafts on send
				$imap = $account->openImapConnection($account->drafts);
				$imap->delete(array($params['draft_uid']));
			}
		} 
		
		if(count($failedRecipients)){
			
			$msg = GO::t('failedRecipients','email').': '.implode(', ',$failedRecipients).'<br /><br />';
			
			$logStr = $logger->dump();

			preg_match('/<< 55[0-9] .*>>/s', $logStr, $matches);

			if (isset($matches[0])) {
				$logStr = trim(substr($matches[0], 2, -2));
			}

			throw new Exception($msg.nl2br($logStr));
		}
		
		$this->_link($params, $message);
		
		//if there's an autolink tag in the message we want to link outgoing messages too.
		if(($tag = $this->_findAutoLinkTag($params['content_type']=='html' ? $params['htmlbody'] : $params['plainbody']))){
			$linkModel = GO::getModel($tag['model'])->findByPk($tag['model_id']);				
			if($linkModel)
				$this->_link($params,$message, $linkModel);
		}

		$response['unknown_recipients'] = $this->_findUnknownRecipients($params);

		return $response;
	}

	public function loadTemplate($params) {
		if (!empty($params['template_id'])) {
			$template = GO_Addressbook_Model_Template::model()->findByPk($params['template_id']);

			$message = GO_Email_Model_SavedMessage::model()->createFromMimeData($template->content);
			$response['data'] = $message->toOutputArray(true, true);

			$presetbody = isset($params['body']) ? $params['body'] : '';
			if (!empty($presetbody) && strpos($response['data']['body'], '{body}') == false) {
				$response['data']['htmlbody'] = $params['body'] . '<br />' . $response['data']['htmlbody'];
			} else {
				$response['data']['htmlbody'] = str_replace('{body}', $presetbody, $response['data']['htmlbody']);
			}

			unset($response['data']['to'], $response['data']['cc'], $response['data']['bcc'], $response['data']['subject']);

			if (empty($params['keepTags'])) {
				$values = array();
				//$contact_id=0;
				//if contact_id is not set but email is check if there's contact info available
				if (!empty($params['to']) || !empty($params['contact_id'])) {

					if (!empty($params['contact_id'])) {
						$contact = GO_Addressbook_Model_Contact::model()->findByPk($params['contact_id']);
					} else {
						$email = GO_Base_Util_String::get_email_from_string($params['to']);
						$contact = GO_Addressbook_Model_Contact::model()->findSingleByEmail($email);
					}

					if ($contact) {
						$response['data']['htmlbody'] = GO_Addressbook_Model_Template::model()->replaceContactTags($response['data']['htmlbody'], $contact);
					} else {
						$response['data']['htmlbody'] = GO_Addressbook_Model_Template::model()->replaceUserTags($response['data']['htmlbody']);
					}
				} else {
					$response['data']['htmlbody'] = GO_Addressbook_Model_Template::model()->replaceUserTags($response['data']['htmlbody']);
				}
			}

			if ($params['content_type'] == 'plain') {
				$response['data']['plainbody'] = GO_Base_Util_String::html_to_text($response['data']['htmlbody'], false);
				unset($response['data']['htmlbody']);
			}
		} else {
			$response['data'] = array();
			if ($params['content_type'] == 'plain') {
				$response['data']['plainbody'] = '';
			} else {
				$response['data']['htmlbody'] = '';
			}
		}
		$response['success'] = true;

		return $response;
	}

	/**
	 * When changing content type or template in email composer we don't want to 
	 * reset some header fields.
	 * 
	 * @param type $response
	 * @param type $params 
	 */
	private function _keepHeaders(&$response, $params) {
		if (!empty($params['keepHeaders'])) {
			unset(
							$response['data']['to'], $response['data']['cc'], $response['data']['bcc'], $response['data']['subject']
			);
		}
	}

	protected function actionTemplate($params) {
		$response = $this->loadTemplate($params);
		$this->_keepHeaders($response, $params);
		return $response;
	}

	private function _quoteHtml($html) {
		return '<blockquote style="border:0;border-left: 2px solid #22437f; padding:0px; margin:0px; padding-left:5px; margin-left: 5px; ">' .
						$html .
						'</blockquote>';
	}

	private function _quoteText($text) {
		$text = GO_Base_Util_String::normalizeCrlf($text, "\n");

		return '> ' . str_replace("\n", "\n> ", $text);
	}

	protected function actionOpenDraft($params) {
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		$imapMessage = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'], $params['uid']);
		$imapMessage->createTempFilesForInlineAttachments = true;
		$imapMessage->createTempFilesForAttachments = true;
		$response['data'] = $imapMessage->toOutputArray($params['content_type'] == 'html', true);
		$response['sendParams']['draft_uid'] = $imapMessage->uid;
		$response['success'] = true;
		return $response;
	}

	protected function actionReply($params) {
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		$imapMessage = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'], $params['uid']);

		$html = $params['content_type'] == 'html';

		$fullDays = GO::t('full_days');

		$from = $imapMessage->from->getAddress();

		$replyText = sprintf(GO::t('replyHeader', 'email'), $fullDays[date('w', $imapMessage->udate)], date(GO::user()->completeDateFormat, $imapMessage->udate), date(GO::user()->time_format, $imapMessage->udate), $from['personal']);


		//for template loading so we can fill the template tags
		$params['to'] = $from['email'];

		$response = $this->loadTemplate($params);

		if ($html) {
			$imapMessage->createTempFilesForInlineAttachments = true;

			$oldMessage = $imapMessage->toOutputArray(true);

			$response['data']['htmlbody'] .= '<br /><br />' .
							htmlspecialchars($replyText, ENT_QUOTES, 'UTF-8') .
							'<br />' . $this->_quoteHtml($oldMessage['htmlbody']);

			$response['data']['inlineAttachments'] = array_merge($response['data']['inlineAttachments'], $oldMessage['inlineAttachments']);
		} else {
			$response['data']['plainbody'] .= "\n\n" . $replyText . "\n" . $this->_quoteText($imapMessage->getPlainBody());
		}

		//will be set at send action
		$response['data']['in_reply_to'] = $imapMessage->message_id;

		if (stripos($imapMessage->subject, 'Re:') === false) {
			$response['data']['subject'] = 'Re: ' . $imapMessage->subject;
		} else {
			$response['data']['subject'] = $imapMessage->subject;
		}


		$to = $imapMessage->to->getAddress();
//		$alias = GO_Email_Model_Alias::model()->findSingleByAttribute('email', $to['email']);
//		if(!$alias)
//			$alias = GO_Email_Model_Alias::model()->findSingle();
//		$response['data']['alias_id']=$alias->id;

		if (!empty($params['replyAll'])) {
			$toList = new GO_Base_Mail_EmailRecipients();
			$toList->mergeWith($imapMessage->from)
							->mergeWith($imapMessage->to);

			$toList->removeRecipient($to['email']);

			$response['data']['to'] = (string) $toList;

			$imapMessage->cc->removeRecipient($to['email']);
			$response['data']['cc'] = (string) $imapMessage->cc;
		} else {
			$response['data']['to'] = (string) $imapMessage->from;
		}

		//for saving sent items in actionSend
		$response['sendParams']['reply_uid'] = $imapMessage->uid;
		$response['sendParams']['reply_mailbox'] = $params['mailbox'];

		$this->_keepHeaders($response, $params);

		return $response;
	}

	protected function actionForward($params) {
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		$imapMessage = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'], $params['uid']);

		$response = $this->loadTemplate($params);

		$html = $params['content_type'] == 'html';

		if (stripos($imapMessage->subject, 'Fwd:') === false) {
			$response['data']['subject'] = 'Fwd: ' . $imapMessage->subject;
		} else {
			$response['data']['subject'] = $imapMessage->subject;
		}

		$headerLines = $this->_getForwardHeaders($imapMessage);

		if ($html) {
			$header = '<br /><br />' . GO::t('original_message', 'email') . '<br />';
			foreach ($headerLines as $line)
				$header .= '<b>' . $line[0] . ':&nbsp;</b>' . htmlspecialchars($line[1], ENT_QUOTES, 'UTF-8') . "<br />";

			$header .= "<br /><br />";

			$imapMessage->createTempFilesForInlineAttachments = true;
			$imapMessage->createTempFilesForAttachments = true;

			$oldMessage = $imapMessage->toOutputArray(true);

			$response['data']['htmlbody'] .= $header . $this->_quoteHtml($oldMessage['htmlbody']);

			$response['data']['inlineAttachments'] = array_merge($response['data']['inlineAttachments'], $oldMessage['inlineAttachments']);
			$response['data']['attachments'] = array_merge($response['data']['attachments'], $oldMessage['attachments']);
		} else {
			$header = "\n\n" . GO::t('original_message', 'email') . "\n";
			foreach ($headerLines as $line)
				$header .= $line[0] . ': ' . $line[1] . "\n";
			$header .= "\n\n";

			$response['data']['plainbody'] .= $header . $oldBody;
		}

		//for saving sent items in actionSend
		$response['sendParams']['forward_uid'] = $imapMessage->uid;
		$response['sendParams']['forward_mailbox'] = $params['mailbox'];


		$this->_keepHeaders($response, $params);

		return $response;
	}

	private function _getForwardHeaders(GO_Email_Model_ImapMessage $imapMessage) {

		$lines = array();

		$lines[] = array(GO::t('subject', 'email'), $imapMessage->subject);
		$lines[] = array(GO::t('from', 'email'), (string) $imapMessage->from);
		$lines[] = array(GO::t('to', 'email'), (string) $imapMessage->to);
		if ($imapMessage->cc->count())
			$lines[] = array("CC", (string) $imapMessage->cc);

		$lines[] = array(GO::t('date'), GO_Base_Util_Date::get_timestamp($imapMessage->udate));

		return $lines;
		$header_om = "\n\n" . $lang['email']['original_message'] . "\n";
	}

	public function actionView($params) {

		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		$imapMessage = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'], $params['uid']);

		//workaround for gmail. It doesn't flag messages as seen automatically.
		if (!$imapMessage->seen && !stripos($account->host, 'gmail') !== false)
			$imapMessage->getImapConnection()->set_message_flag(array($imapMessage->uid), "\Seen");

		$response = $imapMessage->toOutputArray(true);
		$response = $this->_blockImages($params, $response);
		$response = $this->_checkXSS($params, $response);
		
		$response = $this->_handleAutoLinkTag($imapMessage, $params, $response);
		$response = $this->_handleInvitations($imapMessage, $params, $response);
		

		$this->fireEvent('view', array(
				&$this,
				&$response,
				$imapMessage,
				$account,
				$params
		));

		$response['success'] = true;

		return $response;
	}

	private function _checkXSS($params, $response) {

		if (!empty($params['filterXSS'])) {
			$response['htmlbody'] = GO_Base_Util_String::filterXSS($response['htmlbody']);
		} elseif (GO_Base_Util_String::detectXSS($response['htmlbody'])) {
			$response['htmlbody'] = GO::t('xssMessageHidden', 'email');
			$response['xssDetected'] = true;
		} else {
			$response['xssDetected'] = false;
		}
		return $response;
	}

	private function _handleInvitations(GO_Email_Model_ImapMessage $imapMessage, $params, $response) {
		$atts = $imapMessage->getAttachments();

		foreach ($atts as $a) {
//		$a['url'] = '';
//		$a['name'] = $filename;
//		$a['number'] = $part_number_prefix . $part_number;
//		$a['content_id'] = $content_id;
//		$a['mime'] = $mime_type;
//		$a['tmp_file'] = false;
//		$a['index'] = count($this->attachments);
//		$a['size'] = isset($part->body) ? strlen($part->body) : 0;
//		$a['human_size'] = GO_Base_Util_Number::formatSize($a['size']);
//		$a['extension'] = $f->extension();
//		$a['encoding'] = isset($part->headers['content-transfer-encoding']) ? $part->headers['content-transfer-encoding'] : '';
//		$a['disposition'] = isset($part->disposition);

			if ($a['mime'] == 'text/calendar' || $a['extension'] == 'ics') {
				$imap = $imapMessage->getImapConnection();

				$data = $imap->get_message_part_decoded($imapMessage->uid, $a['number'], $a['encoding']);
				$vcalendar = GO_Base_VObject_Reader::read($data);
				$vevent = $vcalendar->vevent[0];

				//is this an update for a specific recurrence?
				$recurrenceDate = isset($vevent->{"recurrence-id"}) ? $vevent->{"recurrence-id"}->getDateTime()->format('U') : 0;

				//find existing event
				$event = GO_Calendar_Model_Event::model()->findByUuid((string) $vevent->uid, GO::user()->id, 0, $recurrenceDate);

				// invitation to a new event										
				$response['iCalendar']['feedback'] = GO::t('iCalendar_event_invitation', 'email');
				$response['iCalendar']['invitation'] = array(
						'uuid' => (string) $vevent->uid,
						'email_sender' => $response['sender'],
						'email' => $imapMessage->account->email,
						'event_declined' => $event && $event->status == 'DECLINED',
						'event_id' => $event ? $event->id : 0,
						'is_update' => $vcalendar->method == 'REPLY',
						'is_invitation' => $vcalendar->method == 'REQUEST',
						'is_cancellation' => $vcalendar->method == 'CANCEL'
				);

				switch ($vcalendar->method) {
					case 'REPLY':

						break;
				}
			}
		}

		return $response;
	}
	
	private function _findAutoLinkTag($data){
		preg_match_all('/\[link:([^]]+)\]/',$data, $matches, PREG_SET_ORDER);
		
		if($match=array_shift($matches)){
			$props = explode(',',base64_decode($match[1]));
			
			$tag=array();
			$tag['server'] = $props[0];
			$tag['model'] = $props[1];
			$tag['model_id'] = $props[2];
		
			return $tag;
		}
		return false;
	}

	/**
	 * Finds an autolink tag inserted by Group-Office and links the message to the model
	 * 
	 * @param GO_Email_Model_ImapMessage $imapMessage
	 * @param type $params
	 * @param string $response
	 * @return string 
	 */
	private function _handleAutoLinkTag(GO_Email_Model_ImapMessage $imapMessage, $params, $response) {		
		if(!$imapMessage->seen && $tag = $this->_findAutoLinkTag($response['htmlbody'])){
			if($tag['server']==$_SERVER['SERVER_NAME']){								
				$linkModel = GO::getModel($tag['model'])->findByPk($tag['model_id']);				
				if($linkModel){
					GO_Savemailas_Model_LinkedEmail::model()->createFromImapMessage($imapMessage, $linkModel);		
					
					//we need this just to display a unified name
					$searchCacheModel = $linkModel->getCachedSearchRecord();
					
					$response['htmlbody']='<div class="em-autolink-message">'.
									sprintf(GO::t('autolinked','email'),'<span class="em-autolink-link" onclick="GO.linkHandlers[\''.$tag['model'].'\'].call(this, '.
													$tag['model_id'].');">'.$searchCacheModel->name.'</div>').
									$response['htmlbody'];
				}
			}
		}
		
		return $response;
	}

	/**
	 * Block external images if sender is not in addressbook.
	 * 
	 * @param type $params
	 * @param type $response
	 * @return type 
	 */
	private function _blockImages($params, $response) {
		if (empty($params['unblock']) && !GO_Addressbook_Model_Contact::model()->findSingleByEmail($response['sender'])) {
			$blockUrl = 'about:blank';
			$response['htmlbody'] = preg_replace("/<([^a]{1})([^>]*)(https?:[^>'\"]*)/iu", "<$1$2" . $blockUrl, $response['htmlbody'], -1, $response['blocked_images']);
		}

		return $response;
	}
	
	public function actionMessageAttachment($params){
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		
		$data = $account->openImapConnection($params['mailbox'])->get_message_part_decoded($params['uid'], $params['number'], $params['encoding']);
		
		$message = GO_Email_Model_SavedMessage::model()->createFromMimeData($data);
		
		$response = $message->toOutputArray();
		$response = $this->_checkXSS($params, $response);
		
		return $response;
		
	}

	public function actionAttachment($params) {
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		$imapMessage = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'], $params['uid']);

		$file = new GO_Base_Fs_File($params['filename']);
		GO_Base_Util_Http::outputDownloadHeaders($file,true,true);

		$imapMessage->getImapConnection()->get_message_part_start($imapMessage->uid, $params['number']);
		while ($line = $imapMessage->getImapConnection()->get_message_part_line()) {
			switch (strtolower($params['encoding'])) {
				case 'base64':
					echo base64_decode($line);
					break;
				case 'quoted-printable':
					echo quoted_printable_decode($line);
					break;
				default:
					echo $line;
					break;
			}
		}
		$imapMessage->getImapConnection()->disconnect();
	}

}