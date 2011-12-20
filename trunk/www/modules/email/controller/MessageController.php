<?php

class GO_Email_Controller_Message extends GO_Base_Controller_AbstractController {

	/**
	 *
	 * @todo Save to sent items should be implemented as a Swift outputstream for better memory management
	 * @param type $params
	 * @return boolean 
	 */
	public function actionSend($params) {
		
		$alias = GO_Email_Model_Alias::model()->findByPk($params['alias_id']);		
		$account = GO_Email_Model_Account::model()->findByPk($alias->account_id);

		$message = new GO_Base_Mail_Message();
		$message->handleEmailFormInput($params);
		
		$message->setFrom($alias->email, $alias->name);
		
		$mailer = GO_Base_Mail_Mailer::newGoInstance(GO_Email_Transport::newGoInstance($account));
		
		$success = $mailer->send($message);		
		
		if($success){
			if(!empty($params['reply_uid'])){
				//set \Answered flag on IMAP message
				$imap = $account->openImapConnection($params['reply_mailbox']);
				$imap->set_message_flag(array($params['reply_uid']), "\Answered");
			}

			if(!empty($params['forward_uid'])){
				//set forwarded flag on IMAP message
				$imap = $account->openImapConnection($params['forward_mailbox']);
				$imap->set_message_flag(array($params['forward_uid']), "$Forwarded");
			}

			if($account->sent){
				//if a sent items folder is set in the account then save it to the imap folder
				$imap = $account->openImapConnection($account->sent);
				$imap->append_message($account->sent, $message->toString(),"\Seen");
			}
		}else
		{
			throw new Exception("Failed to send the message");
		}		
		$response['success']=true;
		
		return $response;
	}
	
	private function loadTemplate($params){
		if(!empty($params['template_id'])){
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
						$contact = GO_Addressbook_Model_Contact::model()->findSingleByAttribute('email', $email);
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
		}else
		{
			$response['data']=array();
			if ($params['content_type'] == 'plain') {
				$response['plainbody']='';
			}else
			{
				$response['htmlbody']='';
			}
		}
		
		$response['success']=true;
		
		return $response;
	}

	public function actionTemplate($params) {
		$response = $this->loadTemplate ($params);		
		return $response;
	}
	
	private function _quoteHtml($html){
			return '<blockquote style="border:0;border-left: 2px solid #22437f; padding:0px; margin:0px; padding-left:5px; margin-left: 5px; ">'.
							$html.
							'</blockquote>';
	}
	
	private function _quoteText($text){
		$text = GO_Base_Util_String::normalizeCrlf($text, "\n");
			
		return '> '.str_replace("\n","\n> ",$text);
	}
	
	
	public function actionReply($params){
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		$imapMessage = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'], $params['uid']);

		$html = $params['content_type']=='html';
		
		$fullDays = GO::t('full_days');
		
		$from = $imapMessage->from->getAddress();
		
		$replyText = sprintf(GO::t('replyHeader','email'),
								$fullDays[date('w', $imapMessage->udate)],
								date(GO::user()->completeDateFormat,$imapMessage->udate),
								date(GO::user()->time_format,$imapMessage->udate),
								$from['personal']);
		
		$response = $this->loadTemplate($params);		
		
		if($html)
		{
			$imapMessage->createTempFilesForInlineAttachments=true;
			
			$oldMessage = $imapMessage->toOutputArray(true);
			
			$response['data']['htmlbody'] .= '<br /><br />'.
							htmlspecialchars($replyText, ENT_QUOTES, 'UTF-8').
							'<br />'.$this->_quoteHtml($oldMessage['htmlbody']);
			
			$response['data']['inlineAttachments']=array_merge($response['data']['inlineAttachments'],$oldMessage['inlineAttachments']);
			
		}else
		{
			$response['data']['plainbody'] .= "\n\n".$replyText."\n".$this->_quoteText($imapMessage->getPlainBody());							
		}
		
		//will be set at send action
		$response['data']['in_reply_to']=$imapMessage->message_id;
				
		if(stripos($imapMessage->subject,'Re:')===false) {
			$response['data']['subject'] = 'Re: '.$imapMessage->subject;
		}else {
			$response['data']['subject'] = $imapMessage->subject;
		}
		
		
		$to = $imapMessage->to->getAddress();		
//		$alias = GO_Email_Model_Alias::model()->findSingleByAttribute('email', $to['email']);
//		if(!$alias)
//			$alias = GO_Email_Model_Alias::model()->findSingle();
		
//		$response['data']['alias_id']=$alias->id;
		
		if(!empty($params['replyAll'])){
			$toList = new GO_Base_Mail_EmailRecipients();
			$toList->mergeWith($imapMessage->from)
					->mergeWith($imapMessage->to);
			
			$toList->removeRecipient($to['email']);
			
			$response['data']['to']=(string) $toList;	
		}else
		{
			$response['data']['to']=(string) $imapMessage->from;	
		}
		
		//for saving sent items in actionSend
		$response['data']['reply_uid']=$imapMessage->uid;
		$response['data']['reply_mailbox']=$params['mailbox'];
		
		return $response;		
	}
	
	public function actionForward($params){
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		$imapMessage = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'], $params['uid']);
		
		$response = $this->loadTemplate($params);	

		$html = $params['content_type']=='html';
		
		if(stripos($imapMessage->subject,'Fwd:')===false) {
			$response['data']['subject'] = 'Fwd: '.$imapMessage->subject;
		}else {
			$response['data']['subject'] = $imapMessage->subject;
		}
		
		$headerLines = $this->_getForwardHeaders($imapMessage);
		
		if($html){
			$header = '<br /><br />'.GO::t('original_message', 'email').'<br />';
			foreach($headerLines as $line)
				$header .= '<b>'.$line[0].':&nbsp;</b>'.htmlspecialchars($line[1], ENT_QUOTES, 'UTF-8')."<br />";			
			
			$header .= "<br /><br />";
			
			$imapMessage->createTempFilesForInlineAttachments=true;
			$imapMessage->createTempFilesForAttachments=true;
			
			$oldMessage = $imapMessage->toOutputArray(true);
			
			$response['data']['htmlbody'] .= $header.$this->_quoteHtml($oldMessage['htmlbody']);
			
			$response['data']['inlineAttachments']=array_merge($response['data']['inlineAttachments'],$oldMessage['inlineAttachments']);
			$response['data']['attachments']=array_merge($response['data']['inlineAttachments'],$oldMessage['attachments']);
			
		}else
		{
			$header = "\n\n".GO::t('original_message', 'email')."\n";
			foreach($headerLines as $line)
				$header .= $line[0].': '.$line[1]."\n";
			$header .= "\n\n";
			
			$response['data']['plainbody'] .= $header.$oldBody;
		}
		
		return $response;
		
	}
	
	private function _getForwardHeaders(GO_Email_Model_ImapMessage $imapMessage){
		
		$lines = array();
		
		$lines[]=array(GO::t('subject','email'), $imapMessage->subject);
		$lines[]=array(GO::t('from','email'), (string) $imapMessage->from);
		$lines[]=array(GO::t('to','email'), (string) $imapMessage->to);
		if($imapMessage->cc->count())
			$lines[]=array("CC", (string) $imapMessage->cc);
		
		$lines[]=array(GO::t('date'), GO_Base_Util_Date::get_timestamp($imapMessage->udate));

		return $lines;
		$header_om  = "\n\n".$lang['email']['original_message']."\n";
	}

}