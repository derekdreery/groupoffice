<?php

class GO_Email_Controller_Message extends GO_Base_Controller_AbstractController {
	
	protected function allowGuests() {
		return array("mailto");
	}
	/*
	 * Example URL: http://localhost/groupoffice-4.0/www/?r=email/message/mailto&mailto=mailto:info@intermesh.nl&bcc=test@intermesh.nl&body=jaja&cc=cc@intermesh.nl&subject=subject
	 */
	protected function actionMailto($params){
		$qs=strtolower(str_replace('mailto:','', urldecode($_SERVER['QUERY_STRING'])));
		$qs=str_replace('?subject','&subject', $qs);

		parse_str($qs, $vars);
		

		$vars['to']=isset($vars['mailto']) ? $vars['mailto'] : '';
		unset($vars['mailto'], $vars['r']);

		if(!isset($vars['subject']))
			$vars['subject']='';

		if(!isset($vars['body']))
			$vars['body']='';
		//
//		var_dump($vars);
//		exit();

		header('Location: '.GO::createExternalUrl('email', 'showComposer', array('values'=>$vars)));
		exit();
	}
		
	protected function actionNotification($params){
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		
		$alias = $this->_findAliasFromRecipients($account, new GO_Base_Mail_EmailRecipients($params['message_to']));	
		if(!$alias)
			$alias = $account->getDefaultAlias();

		$body = sprintf(GO::t('notification_body','email'), $params['subject'], GO_Base_Util_Date::get_timestamp(time()));

		$message = new GO_Base_Mail_Message(
						sprintf(GO::t('notification_subject','email'),$params['subject']),
						$body
						);
		$message->setFrom($alias->email, $alias->name);
		$toList = new GO_Base_Mail_EmailRecipients($params['notification_to']);
		$address=$toList->getAddress();
		$message->setTo($address['email'], $address['personal']);
			
		$mailer = GO_Base_Mail_Mailer::newGoInstance(GO_Email_Transport::newGoInstance($account));
		$response['success'] = $mailer->send($message);
		
		return $response;
	}
	
	
	private function _moveMessages($imap, $params, &$response){
		if(isset($params['action']) && $params['action']=='move') {
			
			$messages = json_decode($params['messages']);
			$imap->move($messages, $params['to_mailbox']);		
			
			//return possible changed unseen status
			$unseen = $imap->get_unseen($params['to_mailbox']);	
			$response['unseen'][$params['to_mailbox']]=$unseen['count'];
		}
	}
	
	private function _filterMessages($mailbox, GO_Email_Model_Account $account) {

		$filters = $account->filters->fetchAll();

		if (count($filters)) {
			$imap = $account->openImapConnection($mailbox);

			$messages = GO_Email_Model_ImapMessage::model()->find($account, $mailbox,0, 100, GO_Base_Mail_Imap::SORT_ARRIVAL, false, "UNSEEN");
			if(count($messages)){
				while ($filter = array_shift($filters)) {
					$matches = array();
					$notMatched = array();
					while ($message = array_shift($messages)) {

						if (stripos($message->{$filter->field}, $filter->keyword) !== false) {
							$matches[] = $message->uid;
						} else {
							$notMatched[] = $message;
						}
					}
					$messages = $notMatched;

					if(count($matches)){
						if ($filter->mark_as_read)
							$imap->set_message_flag($matches, "\Seen");

						$imap->move($matches, $filter->folder);
					}
				}
			}
		}
	}
	
	protected function actionTestSearch(){
		
		$imapSearch = new GO_Email_Model_ImapSearchQuery();
		
//		$imapSearch->addSearchWord('test', GO_Email_Model_ImapSearchQuery::TO);
//		$imapSearch->addSearchWord('test2', GO_Email_Model_ImapSearchQuery::TO);
//		
//		$imapSearch->addSearchWord('test', GO_Email_Model_ImapSearchQuery::BCC);
//		$imapSearch->addSearchWord('test2', GO_Email_Model_ImapSearchQuery::BCC);
//		
	//	$imapSearch->addSearchWord('test', GO_Email_Model_ImapSearchQuery::CC);
//		$imapSearch->addSearchWord('test2', GO_Email_Model_ImapSearchQuery::CC);
//		
//		$imapSearch->addSearchWord('test', GO_Email_Model_ImapSearchQuery::FROM);
//		$imapSearch->addSearchWord('test2', GO_Email_Model_ImapSearchQuery::FROM);
//		
//		$imapSearch->addSearchWord('test', GO_Email_Model_ImapSearchQuery::BODY);
//		$imapSearch->addSearchWord('test2', GO_Email_Model_ImapSearchQuery::BODY);
//		
		$imapSearch->addSearchWord('test', GO_Email_Model_ImapSearchQuery::SUBJECT);
		$imapSearch->addSearchWord('test2', GO_Email_Model_ImapSearchQuery::SUBJECT);
//		
//		$imapSearch->addSearchWord('test', GO_Email_Model_ImapSearchQuery::TEXT);
//		$imapSearch->addSearchWord('test2', GO_Email_Model_ImapSearchQuery::TEXT);
//		
//		$imapSearch->addSearchWord('test', GO_Email_Model_ImapSearchQuery::KEYWORD);
//		$imapSearch->addSearchWord('test2', GO_Email_Model_ImapSearchQuery::KEYWORD);
		
//		$imapSearch->addSearchWord('test', GO_Email_Model_ImapSearchQuery::UNKEYWORD);
//		$imapSearch->addSearchWord('test2', GO_Email_Model_ImapSearchQuery::UNKEYWORD);
		
	//		$imapSearch->searchAll();
//		$imapSearch->searchAnswered();
//		$imapSearch->searchDeleted();
//		$imapSearch->searchFlagged();
//		$imapSearch->searchNew();
		$imapSearch->searchOld();
//		$imapSearch->searchRecent();
//		$imapSearch->searchSeen();
//		$imapSearch->searchUnDeleted();
//		$imapSearch->searchUnFlagged();
//		$imapSearch->searchUnSeen();
//		$imapSearch->searchUnanswered();
		
//		$imapSearch->searchSince();
//		$imapSearch->searchOn();
//		$imapSearch->searchBefore();
						
		$command = $imapSearch->getImapSearchQuery();
		
		echo $command."</br>";
		
		$account = GO_Email_Model_Account::model()->findByPk(145);
		$imap = $account->openImapConnection('INBOX');
		
		$messages = GO_Email_Model_ImapMessage::model()->find(
						$account, 
						'INBOX',
						0, 
						50, 
						GO_Base_Mail_Imap::SORT_DATE , 
						'ASC', 
						$command);
		
		$response["results"]=array();
		foreach($messages as $message){
			$record = $message->getAttributes(true);
			$record['subject'] = htmlspecialchars($record['subject'],ENT_COMPAT,'UTF-8');
			$response["results"][]=$record;
		}
	
		$response['total'] = $imap->sort_count;
		
		return $response;
	}

	protected function actionStore($params){
		
		GO::session()->closeWriting();
		
		if(!isset($params['start']))
			$params['start']=0;
		
		if(!isset($params['limit']))
			$params['limit']=GO::user()->max_rows_list;
		
		if(!isset($params['dir']))
			$params['dir']="ASC";

		$query=isset($params['query']) ? $params['query'] : "";
		
		//passed when only unread should be shown
		if(!empty($params['unread'])) {
			$query = str_replace(array('UNSEEN', 'SEEN'), array('', ''), $query);
			if ($query == '')
				$query .= 'UNSEEN';
			else
				$query.= ' UNSEEN';
		}
		
		
			
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		if(!$account)
			throw new GO_Base_Exception_NotFound();
		/* @var $account GO_Email_Model_Account */
		
		$this->_filterMessages($params["mailbox"], $account);
		
		$imap = $account->openImapConnection($params["mailbox"]);
		
		$response['permission_level'] = $account->getPermissionLevel();

		// ADDED EXPUNGE SO THE FOLDER WILL BE UP TO DATE (When moving folders in THUNDERBIRD)
		$imap->expunge();
		
		$response['unseen']=array();
		
		//special folder flags
		$response['sent']=!empty($account->sent) && strpos($params['mailbox'],$account->sent)===0;
		$response['drafts']=!empty($account->drafts) && strpos($params['mailbox'],$account->drafts)===0;
		$response['trash']=!empty($account->trash) && strpos($params['mailbox'],$account->trash)===0;
		
		$this->_moveMessages($imap, $params, $response);
		
		
		$sort=isset($params['sort']) ? $params['sort'] : 'from';

		switch($sort) {
			case 'from':
				$sortField=$response['sent'] ? GO_Base_Mail_Imap::SORT_TO : GO_Base_Mail_Imap::SORT_FROM;
				break;
			case 'arrival':
				$sortField=GO_Base_Mail_Imap::SORT_ARRIVAL; //arrival is faster on older mail servers
				break;
			
			case 'date':
				$sortField=GO_Base_Mail_Imap::SORT_DATE; //arrival is faster on older mail servers
				break;
			
			case 'subject':
				$sortField=GO_Base_Mail_Imap::SORT_SUBJECT;
				break;
			case 'size':
				$sortField=GO_Base_Mail_Imap::SORT_SIZE;
				break;
			default:
				$sortField=GO_Base_Mail_Imap::SORT_DATE;
		}

//		$imap = $account->openImapConnection($params["mailbox"]);
		
		if(!empty($params['delete_keys'])){
			
			// TODO: Fix this on the clientside so the user is unable to delete emails with the GUI when he has insufficient rights
			// Check if the current user has at least Delete permissions for deleting.
			if(!$account->checkPermissionLevel(GO_Base_Model_Acl::CREATE_PERMISSION))
			  $response['deleteFeedback']=GO::t('strUnauthorizedText');
			else {
				$uids = json_decode($params['delete_keys']);

				if(!$response['trash'] && !empty($account->trash)) {
					$imap->set_message_flag($uids, "\Seen");
					$response['deleteSuccess']=$imap->move($uids,$account->trash);
				}else {

					$response['deleteSuccess']=$imap->delete($uids);
				}
				if(!$response['deleteSuccess']) {
					$lasterror = $imap->last_error();
					if(stripos($lasterror,'quota')!==false) {
						$response['deleteFeedback']=GO::t('quotaError','email');
					}else {
						$response['deleteFeedback']=GO::t('deleteError').":\n\n".$lasterror."\n\n".GO::t('disable_trash_folder','email');
					}
				}
			}
		}
		
		//make sure we are connected to the right mailbox after move and delete operations
//		$imap = $account->openImapConnection($params["mailbox"]);
		
		$messages = GO_Email_Model_ImapMessage::model()->find(
						$account, 
						$params['mailbox'],
						$params['start'], 
						$params['limit'], 
						$sortField , 
						$params['dir']!='ASC', 
						$query);
		
		$response["results"]=array();
		foreach($messages as $message){
				
			$record = $message->getAttributes(true);
			$record['account_id']=$account->id;
			$record['mailbox']=$params["mailbox"];
			
			if($response['sent'] || $response['drafts']){				
				$addresses = $message->to->getAddresses();
				$from=array();
				foreach($addresses as $email=>$personal)
				{
					$from[]=empty($personal) ? $email : $personal;
				}
				$record['from']=  htmlspecialchars(implode(',', $from), ENT_COMPAT, 'UTF-8');
			}
					
			if(empty($record['subject']))
				$record['subject']=GO::t('no_subject','email');
			else
				$record['subject'] = htmlspecialchars($record['subject'],ENT_COMPAT,'UTF-8');
				
				
			
			$response["results"][]=$record;
		}
	
		$response['total'] = $imap->sort_count;
		
		//$unseen = $imap->get_unseen($params['mailbox']);
		
		$mailbox = new GO_Email_Model_ImapMailbox($account, array('name'=>$params['mailbox']));
		$mailbox->snoozeAlarm();
		
		$response['unseen'][$params['mailbox']]=$mailbox->unseen;		
		
		//deletes must be confirmed if no trash folder is used or when we are in the trash folder to delete permanently
		$response['deleteConfirm']=empty($account->trash) || $account->trash==$params['mailbox'];
		
		return $response;
	}
	
	/**
	 * Add a flag to one or multiple messages
	 * 
	 * @param array $params
	 * - int account_id: the id of the GO email account
	 * - string messages: the json encoded mail messages
	 * - string mailbox: the mailbox the find the messages in
	 * - string flag: the flag to set. eg "FLAG"
	 * - boolean clear: true is the other flags should be removed 
	 * @return type
	 */
	protected function actionSetFlag($params){
			
		GO::session()->closeWriting();
		
		$messages = json_decode($params['messages']);
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		
		if(!$account->checkPermissionLevel(GO_Base_Model_Acl::CREATE_PERMISSION))
		  throw new GO_Base_Exception_AccessDenied();
		
		$imap = $account->openImapConnection($params["mailbox"]);

		$response['success']=$imap->set_message_flag($messages, "\\".$params["flag"], !empty($params["clear"]));
		
		$mailbox = new GO_Email_Model_ImapMailbox($account, array('name'=>$params['mailbox']));
		$mailbox->snoozeAlarm();
		
		$response['unseen']=$mailbox->unseen;
		
		return $response;
	}
	

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

		$autoLinkContacts=false;
		if(!$model){
			if (!empty($params['link'])) {
				$linkProps = explode(':', $params['link']);
				$model = GO::getModel($linkProps[0])->findByPk($linkProps[1]);
			}
			
			$autoLinkContacts = GO::modules()->addressbook && GO::modules()->savemailas && !empty(GO::config()->email_autolink_contacts);
		}else
		{
			//don't link the same model twice on sent. It parses the new autolink tag
			//and handles the link to field.
			$linkProps = explode(':', $params['link']);
			if($linkProps[0]==$model->className() && $linkProps[1]==$model->id)
				return false;
		}

		if ($model || $autoLinkContacts) {

			$path = 'email/' . date('mY') . '/sent_' . time() . '.eml';

			$file = new GO_Base_Fs_File(GO::config()->file_storage_path . $path);
			$file->parent()->create();

			$fbs = new Swift_ByteStream_FileByteStream($file->path(), true);
			$message->toByteStream($fbs);

			if ($file->exists()) {
				
				$attributes = array();

				

				$alias = GO_Email_Model_Alias::model()->findByPk($params['alias_id']);

				$attributes['from'] = (string) GO_Base_Mail_EmailRecipients::createSingle($alias->email, $alias->name);
				if (isset($params['to']))
					$attributes['to'] = $params['to'];

				if (isset($params['cc']))
					$attributes['cc'] = $params['cc'];

				if (isset($params['bcc']))
					$attributes['bcc'] = $params['bcc'];

				$attributes['subject'] = !empty($params['subject']) ? $params['subject'] : GO::t('no_subject', 'email');
				//


				$attributes['path'] = $path;

				
				if($model){
					
					$linkedEmail = new GO_Savemailas_Model_LinkedEmail();
					$linkedEmail->setAttributes($attributes);
					$linkedEmail->acl_id = $model->findAclId();
					$linkedEmail->save();

					$linkedEmail->link($model);
				}
				
				
				if($autoLinkContacts){
					$to = new GO_Base_Mail_EmailRecipients($params['to'].",".$params['bcc']);
					$to = $to->getAddresses();
					
//					var_dump($to);

					foreach($to as $email=>$name){
						//$contact = GO_Addressbook_Model_Contact::model()->findByEmail($email, GO_Base_Db_FindParams::newInstance()->permissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION)->single());
						$stmt = GO_Addressbook_Model_Contact::model()->findByEmail($email, GO_Base_Db_FindParams::newInstance()->permissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION)->limit(1));
						$contact = $stmt->fetch();
			

						if($contact){					
							$linkedEmail = new GO_Savemailas_Model_LinkedEmail();
							$linkedEmail->setAttributes($attributes);
							$linkedEmail->acl_id = $contact->findAclId();
							$linkedEmail->save();

							$linkedEmail->link($contact);
						}
					}
				}
			}
		}
	}

	protected function actionSave($params) {
		
		GO::session()->closeWriting();
		
		$alias = GO_Email_Model_Alias::model()->findByPk($params['alias_id']);
		$account = GO_Email_Model_Account::model()->findByPk($alias->account_id);

		if (empty($account->drafts))
			throw new Exception(GO::t('draftsDisabled', 'email'));

		$message = new GO_Base_Mail_Message();

		$message->handleEmailFormInput($params);

		$message->setFrom($alias->email, $alias->name);

		$imap = $account->openImapConnection($account->drafts);

		$nextUid = $imap->get_uidnext();
		$response=array('success'=>false);
		if ($nextUid) {
			$response['sendParams']['draft_uid'] = $nextUid;
			$response['success'] = $response['sendParams']['draft_uid'] > 0;
		}
		
		if(!$imap->append_message($account->drafts, $message, "\Seen")){
			$response['success'] = false;
			$response['feedback']=$imap->last_error();
		}

		if (!empty($params['draft_uid'])) {
			//remove older draft version
			$imap = $account->openImapConnection($account->drafts);
			$imap->delete(array($params['draft_uid']));
		}

		if (!$nextUid) {
			$account->drafts = '';
			$account->save();

			$response['feedback'] = GO::t('noUidNext', 'email');
		}

		return $response;
	}


	protected function actionSaveToFile($params){
		$message = new GO_Base_Mail_Message();
		$alias = GO_Email_Model_Alias::model()->findByPk($params['alias_id']);
		$message->handleEmailFormInput($params);
		$message->setFrom($alias->email, $alias->name);

		$file = new GO_Base_Fs_File(GO::config()->file_storage_path.$params['save_to_path']);

		$fbs = new Swift_ByteStream_FileByteStream($file->path(), true);

		$message->toByteStream($fbs);

		$response['success']=$file->exists();

		return $response;
	}

	private function _createAutoLinkTag($params, $account){
		$tag = '';
		if (!empty($params['link'])) {
			$linkProps = explode(':', $params['link']);
			//$model = GO::getModel($linkProps[0])->findByPk($linkProps[1]);

			$tag = "[link:".base64_encode($_SERVER['SERVER_NAME'].','.$account->id.','.$linkProps[0].','.$linkProps[1])."]";
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
		
		GO::session()->closeWriting();

		$response['success'] = true;
		$response['feedback']='';

		$alias = GO_Email_Model_Alias::model()->findByPk($params['alias_id']);
		$account = GO_Email_Model_Account::model()->findByPk($alias->account_id);

		$message = new GO_Base_Mail_SmimeMessage();

		$tag = $this->_createAutoLinkTag($params, $account);

		if(!empty($tag)){
			if($params['content_type']=='html')
				$params['htmlbody'].= '<div style="display:none">'.$tag.'</div>';
			else
				$params['plainbody'].= "\n\n".$tag."\n\n";
		}

		$message->handleEmailFormInput($params);

		if(!$message->hasRecipients())
			throw new Exception(GO::t('feedbackNoReciepent','email'));

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
				GO::debug("Reply");
				$account2 = GO_Email_Model_Account::model()->findByPk($params['reply_account_id']);
				$imap = $account2->openImapConnection($params['reply_mailbox']);
				$imap->set_message_flag(array($params['reply_uid']), "\Answered");
			}

			if (!empty($params['forward_uid'])) {
				//set forwarded flag on IMAP message
				$account2 = GO_Email_Model_Account::model()->findByPk($params['forward_account_id']);
				$imap = $account2->openImapConnection($params['forward_mailbox']);
				$imap->set_message_flag(array($params['forward_uid']), "\$Forwarded");
			}

			/**
			 * if you want ignore default sent folder message will be store in
			 * folder wherefrom user sent it
			 */
			if ($account->ignore_sent_folder && !empty($params['reply_mailbox']))
				$account->sent = $params['reply_mailbox'];
			
		
			if ($account->sent) {
				
				GO::debug("Sent");
				//if a sent items folder is set in the account then save it to the imap folder
				$imap = $account->openImapConnection($account->sent);
				if(!$imap->append_message($account->sent, $message, "\Seen")){
					$response['success']=false;
					$response['feedback'].='Failed to save send item to '.$account->sent;
				}
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
		$tags = $this->_findAutoLinkTags($params['content_type']=='html' ? $params['htmlbody'] : $params['plainbody']);
		while($tag = array_shift($tags)){
			if($tag['account_id']==$account->id){
				$linkModel = GO::getModel($tag['model'])->findByPk($tag['model_id'], false, true);
				if($linkModel && $linkModel->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION))
					$this->_link($params,$message, $linkModel);				
			}
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
			if (!empty($presetbody) && strpos($response['data']['htmlbody'], '{body}') == false) {
				$response['data']['htmlbody'] = $params['body'] . '<br />' . $response['data']['htmlbody'];
			} else {
				$response['data']['htmlbody'] = str_replace('{body}', $presetbody, $response['data']['htmlbody']);
			}

			unset($response['data']['to'], $response['data']['cc'], $response['data']['bcc'], $response['data']['subject']);

			$defaultTags = array(
					'contact:salutation'=>GO::t('default_salutation_unknown')
			);
			//keep template tags for mailings to addresslists
			if (empty($params['addresslist_id'])) {
				//if contact_id is not set but email is check if there's contact info available
				if (!empty($params['to']) || !empty($params['contact_id']) || !empty($params['company_id'])) {

					if (!empty($params['contact_id'])) {
						$contact = GO_Addressbook_Model_Contact::model()->findByPk($params['contact_id']);
					} else {
						$email = GO_Base_Util_String::get_email_from_string($params['to']);
						$contact = GO_Addressbook_Model_Contact::model()->findSingleByEmail($email);
					}

					$company = false;
					if(!empty($params['company_id']))
						$company = GO_Addressbook_Model_Company::model()->findByPk($params['company_id']);
					
					if($company){
						$response['data']['htmlbody'] = GO_Addressbook_Model_Template::model()->replaceModelTags($response['data']['htmlbody'], $company,'company:',true);
					}
					
					if ($contact) {
						$response['data']['htmlbody'] = GO_Addressbook_Model_Template::model()->replaceContactTags($response['data']['htmlbody'], $contact);
					} else {
	
						$response['data']['htmlbody'] = GO_Addressbook_Model_Template::model()->replaceCustomTags($response['data']['htmlbody'],$defaultTags, true);
						$response['data']['htmlbody'] = GO_Addressbook_Model_Template::model()->replaceUserTags($response['data']['htmlbody']);
					}
				} else {
					$response['data']['htmlbody'] = GO_Addressbook_Model_Template::model()->replaceCustomTags($response['data']['htmlbody'],$defaultTags, true);
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
							$response['data']['to'], 
							$response['data']['cc'], 
							$response['data']['bcc'], 
							$response['data']['subject']
//							$response['data']['attachments']
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
		if(!empty($params['uid'])){
			$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
			$message = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'], $params['uid']);
			$message->createTempFilesForAttachments();
			$response['sendParams']['draft_uid'] = $message->uid;
		}else
		{
			$message = GO_Email_Model_SavedMessage::model()->createFromMimeFile($params['path']);
		}
		$response['data'] = $message->toOutputArray($params['content_type'] == 'html', true);
		
		if(!empty($params['uid'])){
			$alias = $this->_findAliasFromRecipients($account, $message->from);	
			if($alias)
				$response['data']['alias_id']=$alias->id;
		}
		
		$response['success'] = true;
		return $response;
	}

	/**
	 * Reply to a mail message. It can handle an IMAP message or a saved message.
	 *
	 * @param type $params
	 * @return type
	 */
	protected function actionReply($params){

		if(!empty($params['uid'])){
			$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
			if(!$account)
				throw new GO_Base_Exception_NotFound();
			
			$message = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'], $params['uid']);
			if(!$message)
				throw new GO_Base_Exception_NotFound();
		}else
		{
			$account=false;
			$message = GO_Email_Model_SavedMessage::model()->createFromMimeFile($params['path']);
		}

		return $this->_messageToReplyResponse($params, $message, $account);
	}

	private function _messageToReplyResponse($params, GO_Email_Model_ComposerMessage $message, $account=false) {
		$html = $params['content_type'] == 'html';

		$fullDays = GO::t('full_days');

		$replyTo = $message->reply_to->count() ? $message->reply_to : $message->from;
		$from =$replyTo->getAddress();
		
		$fromArr = $message->from->getAddress();

		$replyText = sprintf(GO::t('replyHeader', 'email'), $fullDays[date('w', $message->udate)], date(GO::user()->completeDateFormat, $message->udate), date(GO::user()->time_format, $message->udate), $fromArr['personal']);
		
		//for template loading so we can fill the template tags
		$params['to'] = $from['email'];

		$response = $this->loadTemplate($params);

		if ($html) {
			//saved messages always create temp files
			if($message instanceof GO_Email_Model_ImapMessage)
				$message->createTempFilesForAttachments(true);

			$oldMessage = $message->toOutputArray(true,false,true);

			$response['data']['htmlbody'] .= '<br /><br />' .
							htmlspecialchars($replyText, ENT_QUOTES, 'UTF-8') .
							'<br />' . $this->_quoteHtml($oldMessage['htmlbody']);

			// Fix for array_merge function on line below when the $response['data']['inlineAttachments'] do not exist
			if(empty($response['data']['inlineAttachments']))
				$response['data']['inlineAttachments'] = array();

			$response['data']['inlineAttachments'] = array_merge($response['data']['inlineAttachments'], $oldMessage['inlineAttachments']);
		} else {
			$response['data']['plainbody'] .= "\n\n" . $replyText . "\n" . $this->_quoteText($message->getPlainBody());
		}

		//will be set at send action
		$response['data']['in_reply_to'] = $message->message_id;

		if (stripos($message->subject, 'Re:') === false) {
			$response['data']['subject'] = 'Re: ' . $message->subject;
		} else {
			$response['data']['subject'] = $message->subject;
		}
		
		if(!isset($params['alias_id']))
			$params['alias_id']=0;
		
		$recipients = new GO_Base_Mail_EmailRecipients();
		$recipients->mergeWith($message->cc)->mergeWith($message->to);
		
		$alias = $this->_findAliasFromRecipients($account, $recipients, $params['alias_id']);	
		
				
		$response['data']['alias_id']=$alias->id;		

		if (!empty($params['replyAll'])) {
			$toList = new GO_Base_Mail_EmailRecipients();
			$toList->mergeWith($replyTo)
							->mergeWith($message->to);			

			//remove our own alias from the recipients.		
			if($toList->count()>1){
				$toList->removeRecipient($alias->email);
				$message->cc->removeRecipient($alias->email);
			}

			$response['data']['to'] = (string) $toList;
			$response['data']['cc'] = (string) $message->cc;
		} else {
			$response['data']['to'] = (string) $replyTo;
		}

		//for saving sent items in actionSend
		if($message instanceof GO_Email_Model_ImapMessage){
			$response['sendParams']['reply_uid'] = $message->uid;
			$response['sendParams']['reply_mailbox'] = $params['mailbox'];
			$response['sendParams']['reply_account_id'] = $params['account_id'];
			
			
			if(GO::modules()->savemailas){
				
				$from = $message->from->getAddress();
				
				$contact = GO_Addressbook_Model_Contact::model()->findSingleByEmail($from['email']);
				if($contact){
					
					
					$linkedMessage = GO_Savemailas_Model_LinkedEmail::model()->findByImapMessage($message, $contact);
					
					
					if($linkedMessage){
						$response['data']['link_text']=$contact->name;
						$response['data']['link_value']=$contact->className().':'.$contact->id;
					}
				}
			}
		}

		$this->_keepHeaders($response, $params);

		return $response;
	}
	
	/**
	 *
	 * @param GO_Email_Model_Account $account
	 * @param GO_Base_Mail_EmailRecipients $recipients
	 * @return GO_Email_Model_Alias|false 
	 */
	private function _findAliasFromRecipients($account, GO_Base_Mail_EmailRecipients $recipients, $alias_id=0){
		$alias=false;
		$defaultAlias=false;
		
		
		$findParams = GO_Base_Db_FindParams::newInstance()
				->select('t.*')
				->joinModel(array(
						'model' => 'GO_Email_Model_AccountSort',
						'foreignField' => 'account_id', //defaults to primary key of the remote model
						'localField' => 'account_id', //defaults to primary key of the model
						'type' => 'LEFT'
				))
				->ignoreAdminGroup()
				->order('order', 'DESC');
		
		
		//find the right sender alias
		$stmt = $account ? $account->aliases : GO_Email_Model_Alias::model()->find($findParams);
		while($possibleAlias = $stmt->fetch()){
			
			if(!$defaultAlias)
				$defaultAlias = $possibleAlias;
			
			if($recipients->hasRecipient($possibleAlias->email)){
				$alias = $possibleAlias;
				break;
			}
		}
		
		if(!$alias)
			$alias = empty($alias_id)  ? $defaultAlias : GO_Email_Model_Alias::model()->findByPk($alias_id);
		
		return $alias;
	}

	/**
	 * Forward a mail message. It can handle an IMAP message or a saved message.
	 *
	 * @param type $params
	 * @return type
	 */
	protected function actionForward($params){

		if(!empty($params['uid'])){
			$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
			$message = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'], $params['uid']);
		}else
		{
			$message = GO_Email_Model_SavedMessage::model()->createFromMimeFile($params['path']);
		}
		
		return $this->_messageToForwardResponse($params, $message);
	}

	private function _messageToForwardResponse($params, GO_Email_Model_ComposerMessage $message) {

		$response = $this->loadTemplate($params);

		$html = $params['content_type'] == 'html';

		if (stripos($message->subject, 'Fwd:') === false) {
			$response['data']['subject'] = 'Fwd: ' . $message->subject;
		} else {
			$response['data']['subject'] = $message->subject;
		}

		$headerLines = $this->_getForwardHeaders($message);
		
		if($message instanceof GO_Email_Model_ImapMessage){
			//saved messages always create temp files
			$message->createTempFilesForAttachments();
		}

		$oldMessage = $message->toOutputArray($html,false,true);

		// Fix for array_merge functions on lines below when the $response['data']['inlineAttachments'] and $response['data']['attachments'] do not exist
		if(empty($response['data']['inlineAttachments']))
			$response['data']['inlineAttachments'] = array();

		if(empty($response['data']['attachments']))
			$response['data']['attachments'] = array();

		$response['data']['inlineAttachments'] = array_merge($response['data']['inlineAttachments'], $oldMessage['inlineAttachments']);
		$response['data']['attachments'] = array_merge($response['data']['attachments'], $oldMessage['attachments']);


		if ($html) {
			$header = '<br /><br />' . GO::t('original_message', 'email') . '<br />';
			foreach ($headerLines as $line)
				$header .= '<b>' . $line[0] . ':&nbsp;</b>' . htmlspecialchars($line[1], ENT_QUOTES, 'UTF-8') . "<br />";

			$header .= "<br /><br />";

			$response['data']['htmlbody'] .= $header . $oldMessage['htmlbody'];			
		} else {
			$header = "\n\n" . GO::t('original_message', 'email') . "\n";
			foreach ($headerLines as $line)
				$header .= $line[0] . ': ' . $line[1] . "\n";
			$header .= "\n\n";

			$response['data']['plainbody'] .= $header . $oldMessage['plainbody'];
		}

		if($message instanceof GO_Email_Model_ImapMessage){
			//for saving sent items in actionSend
			$response['sendParams']['forward_uid'] = $message->uid;
			$response['sendParams']['forward_mailbox'] = $params['mailbox'];
			$response['sendParams']['forward_account_id'] = $params['account_id'];
		}

		$this->_keepHeaders($response, $params);

		return $response;
	}

	private function _getForwardHeaders(GO_Email_Model_ComposerMessage $message) {

		$lines = array();

		$lines[] = array(GO::t('subject', 'email'), $message->subject);
		$lines[] = array(GO::t('from', 'email'), (string) $message->from);
		$lines[] = array(GO::t('to', 'email'), (string) $message->to);
		if ($message->cc->count())
			$lines[] = array("CC", (string) $message->cc);

		$lines[] = array(GO::t('date'), GO_Base_Util_Date::get_timestamp($message->udate));

		return $lines;
	}

	public function actionView($params) {
		
		GO::session()->closeWriting();

		$params['no_max_body_size'] = !empty($params['no_max_body_size']) && $params['no_max_body_size']!=='false' ? true : false;
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		if(!$account)
			throw new GO_Base_Exception_NotFound();
		
		$imapMessage = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'], $params['uid']);
		
		if(!$imapMessage)
			throw new GO_Base_Exception_NotFound();
		
		//workaround for gmail. It doesn't flag messages as seen automatically.
//		if (!$imapMessage->seen && stripos($account->host, 'gmail') !== false)
//			$imapMessage->getImapConnection()->set_message_flag(array($imapMessage->uid), "\Seen");
		
		if(!empty($params['create_temporary_attachments']))
			$imapMessage->createTempFilesForAttachments();
		
		$plaintext = !empty($params['plaintext']);
		
		$response = $imapMessage->toOutputArray(!$plaintext,false,$params['no_max_body_size']);
		$response['uid'] = intval($params['uid']);
		$response['mailbox'] = $params['mailbox'];
		$response['account_id'] = intval($params['account_id']);
		$response['do_not_mark_as_read'] = $account->do_not_mark_as_read;
		
		if(!$plaintext){
			
			//Don't do these special actions in the special folders
			if($params['mailbox']!=$account->sent && $params['mailbox']!=$account->trash && $params['mailbox']!=$account->drafts){
				$response = $this->_blockImages($params, $response);
				$response = $this->_checkXSS($params, $response);

				$response = $this->_handleAutoLinkTag($imapMessage, $params, $response);
				$response = $this->_handleInvitations($imapMessage, $params, $response);
				$response = $this->_handleAutoContactLinkFromSender($imapMessage, $params, $response);
			}
		}
		
		$response = $this->_getContactInfo($imapMessage, $params, $response);

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
	
	private function _getContactInfo(GO_Email_Model_ImapMessage $imapMessage,$params, $response){
		$response['sender_contact_id']=0;
		$response['sender_company_id']=0;
		$response['allow_quicklink']=1;
		$response['contact_name']="";			
		$response['contact_thumb_url']=GO::config()->host.'modules/addressbook/themes/Default/images/unknown-person.png';
		
		$useQL = GO::config()->allow_quicklink;
		$response['allow_quicklink']=$useQL?1:0;
	
		$contact = GO_Addressbook_Model_Contact::model()->findSingleByEmail($response['sender']);
		if(!empty($contact)){
			$response['contact_thumb_url']=$contact->getPhotoThumbURL();
			
			if($useQL){
				$response['sender_contact_id']=$contact->id;
				$response['contact_name']=$contact->name.' ('.$contact->addressbook->name.')';


				$company = $contact->company;
				if(!empty($company) && GO_Base_Model_Acl::getUserPermissionLevel($company->addressbook->acl_id)>=GO_Base_Model_Acl::WRITE_PERMISSION){
					$response['sender_company_id']=$company->id;
					$response['company_name']=$company->name.' ('.$company->addressbook->name.')';
				}

				if(GO::modules()->savemailas){
					$contactLinkedMessage = GO_Savemailas_Model_LinkedEmail::model()->findByImapMessage($imapMessage, $contact);
					$response['contact_linked_message_id']=$contactLinkedMessage && $contactLinkedMessage->linkExists($contact) ? $contactLinkedMessage->id : 0;

					if(!empty($company)){
						$companyLinkedMessage = GO_Savemailas_Model_LinkedEmail::model()->findByImapMessage($imapMessage, $company);
						$response['company_linked_message_id']=$companyLinkedMessage && $companyLinkedMessage->linkExists($company) ? $companyLinkedMessage->id : 0;
					}				
				}
			}
		}
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

		if(!GO::modules()->isInstalled('calendar'))
			return $response;
		
		$vcalendar = $imapMessage->getInvitationVcalendar();
		if($vcalendar){
			$vevent = $vcalendar->vevent[0];

			//is this an update for a specific recurrence?
			$recurrenceDate = isset($vevent->{"recurrence-id"}) ? $vevent->{"recurrence-id"}->getDateTime()->format('U') : 0;

			//find existing event
			$event = GO_Calendar_Model_Event::model()->findByUuid((string) $vevent->uid, GO::user()->id, $recurrenceDate);
//			var_dump($event);
			
			$uuid = (string) $vevent->uid;
			
			$alreadyProcessed = false;
			if($event && $vevent->{"last-modified"}){
				
//				throw new Exception(GO_Base_Util_Date::get_timestamp($vevent->{"last-modified"}->getDateTime()->format('U')).' < '.GO_Base_Util_Date::get_timestamp($event->mtime));
				$alreadyProcessed=$vevent->{"last-modified"}->getDateTime()->format('U')<$event->mtime;
			}
			
//			if(!$event || $event->is_organizer){
				switch($vcalendar->method){
					case 'CANCEL':					
						$response['iCalendar']['feedback'] = GO::t('iCalendar_event_cancelled', 'email');
						break;

					case 'REPLY':					
						$response['iCalendar']['feedback'] = GO::t('iCalendar_update_available', 'email');
						break;

					case 'REQUEST':					
						$response['iCalendar']['feedback'] = GO::t('iCalendar_event_invitation', 'email');
						break;
				}

				if($vcalendar->method!='REQUEST' && $vcalendar->method!='PUBLISH' && !$event){
					$response['iCalendar']['feedback'] = GO::t('iCalendar_event_not_found', 'email');
				}
				
				$response['iCalendar']['invitation'] = array(
						'uuid' => $uuid,
						'email_sender' => $response['sender'],
						'email' => $imapMessage->account->getDefaultAlias()->email,
						//'event_declined' => $event && $event->status == 'DECLINED',
						'event_id' => $event ? $event->id : 0,
						'is_organizer'=>$event && $event->is_organizer,
						'is_processed'=>$alreadyProcessed,
						'is_update' => !$alreadyProcessed && $vcalendar->method == 'REPLY',// || ($vcalendar->method == 'REQUEST' && $event),
						'is_invitation' => !$alreadyProcessed && $vcalendar->method == 'REQUEST', //&& !$event,
						'is_cancellation' => $vcalendar->method == 'CANCEL'
				);
//			}elseif($event){
				
//			if($event){
//				$response['attendance_event_id']=$event->id;
//			}
//			$subject = (string) $vevent->summary;
			if(empty($uuid) || strpos($response['htmlbody'], $uuid)===false){
				//if(!$event){
					$event = new GO_Calendar_Model_Event();
					try{
						$event->importVObject($vevent, array(), true);
					//}

					$response['htmlbody'].= '<div style="border: 1px solid black;margin-top:10px">'.
									'<div style="font-weight:bold;margin:2px;">'.GO::t('attachedAppointmentInfo','email').'</div>'.
									$event->toHtml().
									'</div>';
					}
					catch(Exception $e){
						//$response['htmlbody'].= '<div style="border: 1px solid black;margin-top:10px">Could not render event</div>';
					}
			}
		}
				
		return $response;
	}

	private function _findAutoLinkTags($data){
		preg_match_all('/\[link:([^]]+)\]/',$data, $matches, PREG_SET_ORDER);

		$tags = array();
		$unique=array();
		while($match=array_shift($matches)){
			//make sure we don't parse the same tag twice.
			if(!in_array($match[1], $unique)){
				$props = explode(',',base64_decode($match[1]));

				if($props[0]==$_SERVER['SERVER_NAME']){
					$tag=array();
	//				$tag['server'] = $props[0];
					$tag['account_id'] = $props[1];
					$tag['model'] = $props[2];
					$tag['model_id'] = $props[3];
					
					$tags[]=$tag;
				}

				$unique[]=$match[1];
			}

		}
		return $tags;
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
		//seen flag is expensive because it can't be recovered from cache
//		if(!$imapMessage->seen){	

		
		if(GO::modules()->savemailas){
			$tags = $this->_findAutoLinkTags($response['htmlbody']);

			while($tag = array_shift($tags)){
				if($imapMessage->account->id == $tag['account_id']){
					$linkModel = GO::getModel($tag['model'])->findByPk($tag['model_id'],false, true);
					if($linkModel && $linkModel->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION)){
						GO_Savemailas_Model_LinkedEmail::model()->createFromImapMessage($imapMessage, $linkModel);

						//we need this just to display a unified name
						$searchCacheModel = $linkModel->getCachedSearchRecord();

						$response['htmlbody']='<div class="em-autolink-message">'.
										sprintf(GO::t('autolinked','email'),'<span class="em-autolink-link" onclick="GO.linkHandlers[\''.$tag['model'].'\'].call(this, '.
														$tag['model_id'].');">'.$searchCacheModel->name.'</span>').'</div>'.
										$response['htmlbody'];
					}
				}
			}
		}
//		}

		return $response;
	}
	
	
	/**
	 * When automatic contact linking is enabled this will link received messages to the sender in the addressbook
	 *
	 * @param GO_Email_Model_ImapMessage $imapMessage
	 * @param type $params
	 * @param string $response
	 * @return string
	 */
	private function _handleAutoContactLinkFromSender(GO_Email_Model_ImapMessage $imapMessage, $params, $response) {
		
		if(GO::modules()->addressbook && GO::modules()->savemailas && !empty(GO::config()->email_autolink_contacts)){
			$from = $imapMessage->from->getAddress();

			$stmt = GO_Addressbook_Model_Contact::model()->findByEmail($from['email'], GO_Base_Db_FindParams::newInstance()->permissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION)->limit(1));
			$contact = $stmt->fetch();
			
			if($contact){
				GO_Savemailas_Model_LinkedEmail::model()->createFromImapMessage($imapMessage, $contact);

				$response['htmlbody']='<div class="em-autolink-message">'.
								sprintf(GO::t('autolinked','email'),'<span class="em-autolink-link" onclick="GO.linkHandlers[\'GO_Addressbook_Model_Contact\'].call(this, '.
												$contact->id.');">'.$contact->name.'</div>').
								$response['htmlbody'];
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

	//still used?
	public function actionMessageAttachment($params){

		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);

		$data = $account->openImapConnection($params['mailbox'])->get_message_part_decoded($params['uid'], $params['number'], $params['encoding']);

		$message = GO_Email_Model_SavedMessage::model()->createFromMimeData($data);

		$response = $message->toOutputArray();
		$response = $this->_checkXSS($params, $response);
		$response['success']=true;
		return $response;

	}
	
	private function _tnefAttachment($params, GO_Email_Model_Account  $account){
		
		$tmpFolder = GO_Base_Fs_Folder::tempFolder(uniqid(time()));
		$tmpFile = $tmpFolder->createChild('winmail.dat');
		
		$imap = $account->openImapConnection($params['mailbox']);
		
		$success = $imap->save_to_file($params['uid'], $tmpFile->path(), $params['number'], $params['encoding']);
		if(!$success)
			throw new Exception("Could not save temp file for tnef extraction");
		
		chdir($tmpFolder->path());
		exec(GO::config()->cmd_tnef.' '.$tmpFile->path(), $output, $retVar);
		if($retVar!=0)
			throw new Exception("TNEF extraction failed: ".implode("\n", $output));		
		$tmpFile->delete();
		
		$items = $tmpFolder->ls();
		if(!count($items)){
			$this->render("Plain",GO::t('winmailNoFiles', 'email'));
			exit();
		}

		exec(GO::config()->cmd_zip.' -r "winmail.zip" *', $output, $retVar);
		if($retVar!=0)
			throw new Exception("ZIP compression failed: ".implode("\n", $output));		
		
		$zipFile = $tmpFolder->child('winmail.zip');
		GO_Base_Util_Http::outputDownloadHeaders($zipFile,false,true);
		$zipFile->output();
		
		$tmpFolder->delete();
	}

	public function actionAttachment($params) {
		
		GO::session()->closeWriting();
		
		$file = new GO_Base_Fs_File($params['filename']);
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		//$imapMessage = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'], $params['uid']);
		
		if($file->extension()=='dat')
			return $this->_tnefAttachment ($params, $account);

		$inline = true;

		if(isset($params['inline']) && $params['inline'] == 0)
			$inline = false;	
		
		//to work around office crap: http://support.microsoft.com/kb/2019105/en-us		
		//never use inline on IE with office documents because it will prompt for authentication.
		if(GO_Base_Util_Http::isInternetExplorer() && strlen($file->extension())==4 && substr($file->extension(),-1)=='x'){
			$inline=false;
		}

		GO_Base_Util_Http::outputDownloadHeaders($file,$inline,true);
		
		$imap = $account->openImapConnection($params['mailbox']);				
		$fp =fopen("php://output",'w');
		$imap->get_message_part_decoded($params['uid'], $params['number'], $params['encoding'], false, false, false, $fp);
		fclose($fp);
	}

//	Z-push testing
//	public function actionAttachment($uid, $number, $encoding, $account_id, $mailbox, $filename){
//		
//		$file = new GO_Base_Fs_File($filename);
//		GO_Base_Util_Http::outputDownloadHeaders($file,true,true);
//		
//		$account = GO_Email_Model_Account::model()->findByPk($account_id);
//		$imap = $account->openImapConnection($mailbox);
//		include_once('modules/z-push2/backend/go/GoImapStreamWrapper.php');
//		
//		$fp = GoImapStreamWrapper::Open($imap, $uid, $number, $encoding);
//		
//		while($line = fgets($fp)){
//			echo $line;
//		}
//	}
	
	
	protected function actionTnefAttachmentFromTempFile($params){
		$tmpFolder = GO_Base_Fs_Folder::tempFolder(uniqid(time()));
		$tmpFile = new GO_Base_Fs_File(GO::config()->tmpdir.$params['tmp_file']);
		
				chdir($tmpFolder->path());
		exec(GO::config()->cmd_tnef.' -C '.$tmpFolder->path().' '.$tmpFile->path(), $output, $retVar);
		if($retVar!=0)
			throw new Exception("TNEF extraction failed: ".implode("\n", $output));		
		
		exec(GO::config()->cmd_zip.' -r "winmail.zip" *', $output, $retVar);
		if($retVar!=0)
			throw new Exception("ZIP compression failed: ".implode("\n", $output));		
		
		$zipFile = $tmpFolder->child('winmail.zip');
		GO_Base_Util_Http::outputDownloadHeaders($zipFile,false,true);
		$zipFile->output();
		
		$tmpFolder->delete();	
	}
	
	
	protected function actionSaveAttachment($params){
		$folder = GO_Files_Model_Folder::model()->findByPk($params['folder_id']);
		
		if(!$folder){
			trigger_error("GO_Email_Controller_Message::actionSaveAttachment(".$params['folder_id'].") folder not found", E_USER_WARNING);
			throw new GO_Base_Exception_NotFound("Specified folder not found");
		}
		
		$params['filename'] = GO_Base_Fs_File::stripInvalidChars($params['filename']);
		$file = new GO_Base_Fs_File(GO::config()->file_storage_path.$folder->path.'/'.$params['filename']);
		
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);		
		$imap = $account->openImapConnection($params['mailbox']);
		
		$response['success'] = $imap->save_to_file($params['uid'], $file->path(), $params['number'], $params['encoding']);
		
		if(!$folder->hasFile($file->name()))
			$folder->addFile($file->name());
		
		if(!$response['success'])
			$response['feedback']='Could not save to '.$file->stripFileStoragePath();
		return $response;
	}
	
	protected function actionSource($params) {
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		$imap  = $account->openImapConnection($params['mailbox']);
		
		$filename = empty($params['download']) ? "message.txt" :"message.eml";
		
		GO_Base_Util_Http::outputDownloadHeaders(new GO_Base_Fs_File($filename), empty($params['download']));	

		/*
		 * Somehow fetching a message with an empty message part which should fetch it
		 * all doesn't work. (http://tools.ietf.org/html/rfc3501#section-6.4.5)
		 *
		 * That's why I first fetch the header and then the text.
		 */
		$header = $imap->get_message_part($params['uid'], 'HEADER', true) . "\r\n\r\n";
		$size = $imap->get_message_part_start($params['uid'], 'TEXT', true);

		header('Content-Length: ' . (strlen($header) + $size));

		echo $header;
		while ($line = $imap->get_message_part_line())
			echo $line;
	}

	protected function actionMoveOld($params){
		
		$this->checkRequiredParameters(array('mailbox','target_mailbox'), $params);
		
		if($params['mailbox']==$params['target_mailbox'])
		{
			throw new Exception(GO::t("sourceAndTargetSame","email"));
		}

		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		$imap  = $account->openImapConnection($params['mailbox']);


		$before_timestamp = GO_Base_Util_Date::to_unixtime($params['until_date']);
		if (empty($before_timestamp))
			throw new Exception(GO::t('untilDateError','email').': '.$params['until_date']);

		$date_string = date('d-M-Y',$before_timestamp);
		
		$uids = $imap->sort_mailbox('ARRIVAL',false,'BEFORE "'.$date_string.'"');		
		
		$response['total']=count($uids);
		//$response['success'] = $imap->delete($uids);
		$response['success'] =true;
		if($response['total']){
			$chunks = array_chunk($uids, 1000);
			while($uids=array_shift($chunks)){
				if(!$imap->move($uids, $params['target_mailbox'])){
					throw new Exception("Could not move mails! ".$imap->last_error());
				}
			}
		}
		
		
		
		return $response;
	}
//	
//	protected function moveOld($params){
//		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
//		$imap  = $account->openImapConnection($params['mailbox']);
//
//
//		$before_timestamp = GO_Base_Util_Date::to_unixtime($params['until_date']);
//		if (empty($before_timestamp))
//			throw new Exception(GO::t('untilDateError','email').': '.$params['until_date']);
//
//		$date_string = date('d-M-Y',$before_timestamp);
//		
//		$uids = $imap->sort_mailbox('ARRIVAL',false,'BEFORE "'.$date_string.'"');		
//		
//		$response['total']=count($uids);
//		$response['success'] = $imap->move($uids, $params['target_mailbox']);
//		
//		return $response;
//	}

	/**
	 * This action will move imap messages from one folder to another
	 * 
	 * @param array $params
	 * - string messages: json encoded message uid's
	 * - int total: total messages to be moved
	 * - int from_account_id: the GO email account id the messages should be moved from
	 * - int to_account_id: the GO email account id the message should be moved to
	 * - string from_mailbox: the imap mailbox name to move messages from
	 * - string to_mailbox: the imap mailbox name to move messages to
	 * @return array $response
	 * @throws Exception when moving a message fails
	 */
	protected function actionMove($params){
			$start_time = time();
			
			$messages= json_decode($params['messages'], true);
			$total = $params['total'];

			//move to another imap account
			//$imap2 = new cached_imap();
			//$from_account = $imap->open_account($params['from_account_id'], $params['from_mailbox']);
			$from_account=GO_Email_Model_Account::model()->findByPk($params['from_account_id']);
			$to_account=GO_Email_Model_Account::model()->findByPk($params['to_account_id']);
			
			if(!$from_account->checkPermissionLevel(GO_Base_Model_Acl::CREATE_PERMISSION))
			  throw new GO_Base_Exception_AccessDenied();
			
			if(!$to_account->checkPermissionLevel(GO_Base_Model_Acl::CREATE_PERMISSION))
			  throw new GO_Base_Exception_AccessDenied();

			$imap = $from_account->openImapConnection($params['from_mailbox']);
			$imap2 = $to_account->openImapConnection($params['to_mailbox']);

			$delete_messages =array();
			while($uid=array_shift($messages)) {
				$source = $imap->get_message_part($uid);

				$header = $imap->get_message_header($uid);

				$flags = '\Seen';
				if(!empty($header['flagged'])) {
					$flags .= ' \Flagged';
				}
				if(!empty($header['answered'])) {
					$flags .= ' \Answered';
				}
				if(!empty($header['forwarded'])) {
					$flags .= ' $Forwarded';				}

				if(!$imap2->append_message($params['to_mailbox'], $source, $flags)) {
					$imap2->disconnect();
					throw new Exception('Could not move message');
				}

				$delete_messages[]=$uid;

				$left = count($messages);

				if($left && $start_time-5<time()) {

					$done = $total-$left;

					$response['messages']=$messages;
					$response['progress']=number_format($done/$total,2);
				
					break;
				}
			}
			$imap->delete($delete_messages);

			$imap2->disconnect();
			$imap->disconnect();

			$response['success']=true;
			
			return $response;
	}
	
	protected function actionZipAllAttachments($params){
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		//$imap  = $account->openImapConnection($params['mailbox']);
		
		$message = GO_Email_Model_ImapMessage::model()->findByUid($account, $params["mailbox"], $params["uid"]);
		
		$tmpFolder = GO_Base_Fs_Folder::tempFolder(uniqid(time()));
		$atts = $message->getAttachments();
		while($att=array_shift($atts)){
			if(empty($att->content_id) || $att->disposition=='attachment')
				$att->saveToFile($tmpFolder);
		}	
		
		$archiveFile = $tmpFolder->parent()->createChild(GO::t('attachments','email').'.zip');
		
		GO_Base_Fs_Zip::create($archiveFile, $tmpFolder, $tmpFolder->ls());

			
		GO_Base_Util_Http::outputDownloadHeaders($archiveFile, false);
		
		readfile($archiveFile->path());
		
		$tmpFolder->delete();
		$archiveFile->delete();
		
	}
	
}
