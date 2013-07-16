<?php

class GO_Addressbook_Controller_SentMailing extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Addressbook_Model_SentMailing';
	
	/**
	 * Disable email sending
	 * 
	 * @var boolean 
	 */
	protected $dry=false;

	protected function allowGuests() {
		return array("batchsend","unsubscribe");
	}
	
	protected function ignoreAclPermissions() {
		return array('unsubscribe');
	}
	
	/**
	 * This function is made specially to convert paramaters from the EmailComposer
	 * to match GO_Base_Mail_Message::handleFormInput in actionSendMailing.
	 * @param Array $params Parameters from EmailComposer
	 * @return Array $params Parameters for GO_Base_Mail_Message::handleFormInput 
	 */
//	private function _convertOldParams($params) {
//		$params['inlineAttachments'] = json_decode($params['inline_attachments']);
//
//		foreach ($params['inlineAttachments'] as $k => $ia) {
//			// tmpdir part may already be at the beginning of $ia['tmp_file']
//			if (strpos($ia->tmp_file, GO::config()->tmpdir) == 0)
//				$ia->tmp_file = substr($ia->tmp_file, strlen(GO::config()->tmpdir));
//
//			$params['inlineAttachments'][$k] = $ia;
//		}
//		$params['inlineAttachments'] = json_encode($params['inlineAttachments']);
//
//		if (!empty($params['content_type']) && strcmp($params['content_type'], 'html') != 0)
//			$params['body'] = $params['textbody'];
//
//		// Replace "[id:" string part in subject by the actual alias id
//		if (!empty($params['alias_id']) && !empty($params['subject']))
//			$params['subject'] = str_replace('[id:', '[' . $params['alias_id'] . ':', $params['subject']);
//
//		return $params;
//	}


	protected function actionSend($params) {
		if (empty($params['addresslist_id'])) {
			throw new Exception(GO::t('feedbackNoReciepent', 'email'));
		} else {
			try {
				//$params = $this->_convertOldParams($params);

				$message = GO_Base_Mail_Message::newInstance();
				$message->handleEmailFormInput($params); // insert the inline and regular attachments in the MIME message

				$mailing['alias_id'] = $params['alias_id'];
				$mailing['subject'] = $params['subject'];
				$mailing['addresslist_id'] = $params['addresslist_id'];
				$mailing['message_path'] =  'mailings/' . GO::user()->id . '_' . date('Ymd_Gis') . '.eml';

				$folder = new GO_Base_Fs_Folder(GO::config()->file_storage_path.'mailings');
				$folder->create();

				// Write message MIME source to message path
				file_put_contents(GO::config()->file_storage_path.$mailing['message_path'], $message->toString());

				GO::debug('===== MAILING PARAMS =====');
				GO::debug(var_export($mailing,true));

				$sentMailing = new GO_Addressbook_Model_SentMailing();
				$sentMailing->setAttributes($mailing);
				if (!$sentMailing->save()) {
								GO::debug('===== VALIDATION ERRORS =====');
								GO::debug('Could not create new mailing:<br />'.implode('<br />',$sentMailing->getValidationErrors()));
								throw new Exception('Could not create new mailing:<br />'.implode('<br />',$sentMailing->getValidationErrors()).'<br />MAILING PARAMS:<br />'.var_export($mailing,true));
				}       

				$this->_launchBatchSend($sentMailing->id);

				$response['success'] = true;
			} catch (Exception $e) {
				$response['feedback'] = GO::t('feedbackUnexpectedError', 'email') . $e->getMessage();
			}
		}
		return $response;
	}

	private function _launchBatchSend($mailing_id) {
		$log = GO::config()->file_storage_path . 'log/mailings/';
		if (!is_dir($log))
			mkdir($log, 0755, true);

		$log .= $mailing_id . '.log';
		$cmd = GO::config()->cmd_php . ' '.GO::config()->root_path.'groupofficecli.php -r=addressbook/sentMailing/batchSend -c="' . GO::config()->get_config_file() . '" --mailing_id=' . $mailing_id . ' >> ' . $log;

		if (!GO_Base_Util_Common::isWindows())
			$cmd .= ' 2>&1 &';

		file_put_contents($log, GO_Base_Util_Date::get_timestamp(time()) . "\r\n" . $cmd . "\r\n\r\n", FILE_APPEND);
		if (GO_Base_Util_Common::isWindows()) {
			pclose(popen("start /B " . $cmd, "r"));
		} else {
			exec($cmd,$outputarr,$returnvar);
			GO::debug('===== CMD =====');
			GO::debug($cmd);
			GO::debug('===== OUTPUT ARR =====');
			GO::debug(var_export($outputarr,true));
			GO::debug('===== RETURN VAR =====');
			GO::debug(var_export($returnvar,true));
		}
	}

	protected function actionBatchSend($params) {

		$this->requireCli();
		
		GO::$disableModelCache=true;

		$mailing = GO_Addressbook_Model_SentMailing::model()->findByPk($params['mailing_id']);
		if (!$mailing)
			throw new Exception("Mailing not found!\n");

		GO::session()->runAs($mailing->user_id);
		
		echo 'Status: '.$mailing->status."\n";;
		
		if(empty($mailing->status)){
			echo "Starting mailing at ".GO_Base_Util_Date::get_timestamp(time())."\n";
			$mailing->reset();
		}elseif (!empty($params['restart'])) {
			echo "Restarting mailing at ".GO_Base_Util_Date::get_timestamp(time())."\n";
			$mailing->reset();
		}elseif($mailing->status==GO_Addressbook_Model_SentMailing::STATUS_PAUSED){
			echo "Resuming mailing at ".GO_Base_Util_Date::get_timestamp(time())."\n";
			$mailing->status=GO_Addressbook_Model_SentMailing::STATUS_RUNNING;
			$mailing->save();
		}
			
		$htmlToText = new GO_Base_Util_Html2Text();
		

		//$addresslist = GO_Addressbook_Model_Addresslist::model()->findByPk($mailing->addresslist_id);
		$mimeData = file_get_contents(GO::config()->file_storage_path .$mailing->message_path);
		$message = GO_Base_Mail_Message::newInstance()
						->loadMimeMessage($mimeData);


		$joinCriteria = GO_Base_Db_FindCriteria::newInstance()->addRawCondition('t.id', 'a.account_id');
		$findParams = GO_Base_Db_FindParams::newInstance()
						->single()
						->join(GO_Email_Model_Alias::model()->tableName(), $joinCriteria, 'a')
						->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('id', $mailing->alias_id, '=', 'a')
		);
		$account = GO_Email_Model_Account::model()->find($findParams);

		$mailer = GO_Base_Mail_Mailer::newGoInstance(GO_Email_Transport::newGoInstance($account));

		echo "Will send emails from " . $account->username . ".\n";
		
		if(empty(GO::config()->mailing_messages_per_minute))
			GO::config()->mailing_messages_per_minute=30;

		//Rate limit to 100 emails per-minute
		$mailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(GO::config()->mailing_messages_per_minute, Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE));
		
		// Use AntiFlood to re-connect after 50 emails
		$mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin(GO::config()->mailing_messages_per_minute));

		echo 'Sending a maximum of ' . GO::config()->mailing_messages_per_minute . ' messages per minute' . "\n";

		$failedRecipients = array();

		$bodyWithTags=$message->getBody();

		foreach ($mailing->contacts as $contact) {			
			
			$errors=1;
			
			$unsubscribeHref=GO::url('addressbook/sentMailing/unsubscribe', 
							array(
									'addresslist_id'=>$mailing->addresslist_id, 
									'contact_id'=>$contact->id, 
									'token'=>md5($contact->ctime.$contact->addressbook_id.$contact->firstEmail) //token to check so that users can't unsubscribe other members by guessing id's
									), false, true);
			
			$body = str_replace('%unsubscribe_href%', $unsubscribeHref, $bodyWithTags); //curly brackets don't work inside links in browser wysiwyg editors.
			
			$templateModel = GO_Addressbook_Model_Template::model();
			$templateModel->htmlSpecialChars = false;
			$body = $templateModel->replaceCustomTags($body,array(				
				'unsubscribe_link'=>'<a href="'.$unsubscribeHref.'" target="_blank">'.GO::t("unsubscription","addressbook").'</a>'
			), true);
			$templateModel->htmlSpecialChars = true;
			
			try{
				if(!$contact->email_allowed){
					echo "Skipping contact ".$contact->firstEmail." because newsletter sending is disabled in the addresslists tab.\n\n";
				}elseif(empty($contact->firstEmail)){
					echo "Skipping contact ".$contact->name." no e-mail address was set.\n\n";					
				}else
				{		
					$body = GO_Addressbook_Model_Template::model()->replaceContactTags($body, $contact);
					$message->setTo($contact->firstEmail, $contact->name);
					$message->setBody($body);
					
					$plainTextPart = $message->findPlainTextBody();
					if($plainTextPart){
						$htmlToText->set_html($body);
						$plainTextPart->setBody($htmlToText->get_text());
					}
					
					$this->_sendmail($message, $contact, $mailer, $mailing);
					$errors=0;
					
				}
			}catch(Exception $e){
				echo "Error for ".$contact->firstEmail.": ".$e->getMessage()."\n";
			}
			
			if($errors){
				$mailing->errors++;
				$mailing->save();
			}
		}

		foreach ($mailing->companies as $company) {
			
			$errors=1;
			
			$unsubscribeHref=GO::url('addressbook/sentMailing/unsubscribe', 
							array(
									'addresslist_id'=>$mailing->addresslist_id, 
									'company_id'=>$company->id, 
									'token'=>md5($company->ctime.$company->addressbook_id.$company->email) //token to check so that users can't unsubscribe other members by guessing id's
									), true, true);
			
			$body = str_replace('%unsubscribe_href%', $unsubscribeHref, $bodyWithTags); //curly brackets don't work inside links in browser wysiwyg editors.
			
			$body = GO_Addressbook_Model_Template::model()->replaceCustomTags($body,array(				
				'unsubscribe_link'=>'<a href="'.$unsubscribeHref.'">'.GO::t("unsubscription","addressbook").'</a>'
			), true);
			
			try{
				if(!$company->email_allowed){
					echo "Skipping company ".$company->email." because newsletter sending is disabled in the addresslists tab.\n\n";
				}elseif(empty($company->email)){
					echo "Skipping company ".$company->name." no e-mail address was set.\n\n";
				}else
				{		
					$body = GO_Addressbook_Model_Template::model()->replaceModelTags($body, $company);
					$message->setTo($company->email, $company->name);
					$message->setBody($body);
					
					$plainTextPart = $message->findPlainTextBody();
					if($plainTextPart){
						$htmlToText->set_html($body);
						$plainTextPart->setBody($htmlToText->get_text());
					}
					
					$this->_sendmail($message, $company, $mailer, $mailing);	
					$errors=0;
				}
					
			}catch(Exception $e){
				echo "Error for ".$company->email.": ".$e->getMessage()."\n";
			}
			
			if($errors){
				$mailing->errors++;
				$mailing->save();
			}
		}

		$mailing->status = GO_Addressbook_Model_SentMailing::STATUS_FINISHED;
		$mailing->save();

		echo "Mailing finished\n";
	}
	
	public function actionUnsubscribe($params){
		
		if(!isset($params['contact_id']))
			$params['contact_id']=0;
		
		if(!isset($params['company_id']))
			$params['company_id']=0;
		
		if(!empty($params['sure'])){
			if($params['contact_id']){
				$contact = GO_Addressbook_Model_Contact::model()->findByPk($params['contact_id']);
				
				if(md5($contact->ctime.$contact->addressbook_id.$contact->firstEmail) != $params['token'])
					throw new Exception("Invalid token!");
				
				$contact->email_allowed=0;
				$contact->save();					
				
				GO_Base_Mail_AdminNotifier::sendMail("Unsubscribe: ".$contact->email, "Contact ".$contact->email. " unsubscribed from receiving newsletters");
			}else
			{
				if($params['contact_id']){
					$company = GO_Addressbook_Model_Company::model()->findByPk($params['company_id']);

					if(md5($company->ctime.$company->addressbook_id.$company->email) != $params['token'])
						throw new Exception("Invalid token!");

					$company->email_allowed=0;
					$company->save();
					
					GO_Base_Mail_AdminNotifier::sendMail("Unsubscribe: ".$company->email, "Company ".$contact->email. " unsubscribed from receiving newsletters");
				}
			}
			
			$this->render('unsubscribed', $params);
		}else
		{		
			$this->render('unsubscribe',$params);
		}
	}
	
	private $smtpFailCount=0;

	private function _sendmail($message, $model, $mailer, $mailing) {
		
		$typestring = $model instanceof GO_Addressbook_Model_Company ? 'company' : 'contact';
		
		if($typestring=='contact'){
			$email = $model->firstEmail;
		}else
		{
			$email = $model->email;
		}
		
		echo '['.GO_Base_Util_Date::get_timestamp(time())."] Sending to " . $typestring . " id: " . $model->id . " email: " . $email . "\n";

		$mailing = GO_Addressbook_Model_SentMailing::model()->findByPk($mailing->id, array(), true, true);
		if($mailing->status==GO_Addressbook_Model_SentMailing::STATUS_PAUSED)
		{
			echo "Mailing paused by user. Exiting.";
			exit();
		}

		try {
			if($this->dry){
				echo "Not sending because dry is true\n";
			}else{
				$mailer->send($message);
			}
		} catch (Exception $e) {
			$status = $e->getMessage();
		}
		if (!empty($status)) {
			echo "---------\n";
			echo "Failed at ".GO_Base_Util_Date::get_timestamp(time())."\n";
			echo $status . "\n";
			echo "---------\n";
			
			$mailing->errors++;		
			
			$this->smtpFailCount++;
			
			if($this->smtpFailCount==3){
				echo "Pausing mailing because there were 3 send errors in a row\n";
				$mailing->status=GO_Addressbook_Model_SentMailing::STATUS_PAUSED;
				$mailing->save();
				exit();				
			}
			
			unset($status);
		} else {
			$mailing->sent++;
			$this->smtpFailCount=0;
		}
		
		$mailing->save();

		if ($typestring == 'contact') {
			$mailing->removeManyMany('contacts', $model->id);
		} else {
			$mailing->removeManyMany('companies', $model->id);			
		}
		
		
	}
	
	protected function getStoreParams($params) {
		
		$criteria = GO_Base_Db_FindCriteria::newInstance();
		
		if(!GO::user()->isAdmin())
			$criteria->addCondition('user_id', GO::user()->id);
		
		return GO_Base_Db_FindParams::newInstance()->criteria($criteria);
						
	}

	protected function beforeStore(&$response, &$params, &$store) {

		if (!empty($params['pause_mailing_id'])) {
			$mailing = GO_Addressbook_Model_SentMailing::model()->findByPk($params['pause_mailing_id']);
			if($mailing->status==GO_Addressbook_Model_SentMailing::STATUS_RUNNING){
				$mailing->status = GO_Addressbook_Model_SentMailing::STATUS_PAUSED;
				$mailing->save();
			}
		}

		if (!empty($params['start_mailing_id'])) {
			$this->_launchBatchSend($params['start_mailing_id']);
		}

		$store->setDefaultSortOrder('ctime', 'DESC');
		return $response;
	}

	public function formatStoreRecord($record, $model, $store) {
		$record['addresslist'] = !empty($model->addresslist) ? $model->addresslist->name : '';
		$record['user_name'] = !empty($model->user) ? $model->user->name : '';
		return parent::formatStoreRecord($record, $model, $store);
	}
	
	protected function actionViewLog($params){
		$mailing = GO_Addressbook_Model_SentMailing::model()->findByPk($params['mailing_id']);
		
		if($mailing->user_id != GO::user()->id && !GO::user()->isAdmin())
			throw new GO_Base_Exception_AccessDenied();				
		
		$file = $mailing->logFile;		
		GO_Base_Util_Http::outputDownloadHeaders($file);
		$file->output();
	}

}