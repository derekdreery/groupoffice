<?php

class GO_Addressbook_Controller_SentMailing extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Addressbook_Model_SentMailing';

	protected function allowGuests() {
		return array("batchsend");
	}
	
	
	/**
	 * This function is made specially to convert paramaters from the EmailComposer
	 * to match GO_Base_Mail_Message::handleFormInput in actionSendMailing.
	 * @param Array $params Parameters from EmailComposer
	 * @return Array $params Parameters for GO_Base_Mail_Message::handleFormInput 
	 */
	private function _convertOldParams($params) {
		$params['inlineAttachments'] = json_decode($params['inline_attachments']);

		foreach ($params['inlineAttachments'] as $k => $ia) {
			// tmpdir part may already be at the beginning of $ia['tmp_file']
			if (strpos($ia->tmp_file, GO::config()->tmpdir) == 0)
				$ia->tmp_file = substr($ia->tmp_file, strlen(GO::config()->tmpdir));

			$params['inlineAttachments'][$k] = $ia;
		}
		$params['inlineAttachments'] = json_encode($params['inlineAttachments']);

		if (!empty($params['content_type']) && strcmp($params['content_type'], 'html') != 0)
			$params['body'] = $params['textbody'];

		// Replace "[id:" string part in subject by the actual alias id
		if (!empty($params['alias_id']) && !empty($params['subject']))
			$params['subject'] = str_replace('[id:', '[' . $params['alias_id'] . ':', $params['subject']);

		return $params;
	}


	public function actionSend($params) {
		if (empty($params['addresslist_id'])) {
			throw new Exception(GO::t('feedbackNoReciepent', 'email'));
		} else {
			try {
				$params = $this->_convertOldParams($params);

				$message = GO_Base_Mail_Message::newInstance();
				$message->handleEmailFormInput($params); // insert the inline and regular attachments in the MIME message
				
				$mailing['alias_id'] = $params['alias_id'];
				$mailing['subject'] = $params['subject'];
				$mailing['addresslist_id'] = $params['addresslist_id'];
				$mailing['message_path'] = GO::config()->file_storage_path . 'mailings/' . GO::user()->id . '_' . date('Ymd_Gis') . '.eml';


				// Set From address
				$alias = GO_Email_Model_Alias::model()->findByPk($params['alias_id']);
				$message->setFrom($alias->email, $alias->name);

				// Set the message subject
				if (!empty($params['subject']))
					$message->setSubject($params['subject']);

				// Set the priority of the message using $params['priority']
				if (!empty($params['priority']))
					$message->setPriority($params['priority']);

				// Set the message body
				if ($params['content_type'] == 'html') {
					$message->setHtmlAlternateBody($params['body']);
				} else {
					$message->setBody($params['body']);
				}

				if (!is_dir(dirname($mailing['message_path'])))
					mkdir(dirname($mailing['message_path']), 0755, true);

				// Write message MIME source to message path
				file_put_contents($mailing['message_path'], $message->toString());

				$sentMailing = new GO_Addressbook_Model_SentMailing();
				$sentMailing->setAttributes($mailing);
				$sentMailing->save();				

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
		$cmd = GO::config()->cmd_php . ' '.GO::config()->root_path.'index.php -r=addressbook/sentMailing/batchSend -c="' . GO::config()->get_config_file() . '" --mailing_id=' . $mailing_id . ' >> ' . $log;

		if (!GO_Base_Util_Common::isWindows())
			$cmd .= ' 2>&1 &';

		file_put_contents($log, GO_Base_Util_Date::get_timestamp(time()) . "\r\n" . $cmd . "\r\n\r\n", FILE_APPEND);
		if (GO_Base_Util_Common::isWindows())
			pclose(popen("start /B " . $cmd, "r"));
		else
			exec($cmd);
	}

	public function actionBatchSend($params) {

		if (PHP_SAPI != 'cli')
			throw new Exception("This action may only be executed on the command line interace");

		$mailing = GO_Addressbook_Model_SentMailing::model()->findByPk($params['mailing_id']);
		if (!$mailing)
			throw new Exception("Mailing not found!\n");

		GO::session()->setCurrentUser($mailing->user_id);
		
		echo 'Status: '.$mailing->status."\n";;
		
		if(empty($mailing->status)){
			echo "Starting mailing at ".GO_Base_Util_Date::get_timestamp(time())."\n";
			$mailing->reset();
		}elseif (!empty($params['restart'])) {
			echo "Restarting mailing at ".GO_Base_Util_Date::get_timestamp(time())."\n";
			$mailing->reset();
		}elseif($mailing->status=GO_Addressbook_Model_SentMailing::STATUS_PAUSED){
			echo "Resuming mailing at ".GO_Base_Util_Date::get_timestamp(time())."\n";
			$mailing->status=GO_Addressbook_Model_SentMailing::STATUS_RUNNING;
			$mailing->save();
		}
			
		

		//$addresslist = GO_Addressbook_Model_Addresslist::model()->findByPk($mailing->addresslist_id);
		$mimeData = file_get_contents($mailing->message_path);
		$message = GO_Base_Mail_Message::newInstance()
						->loadMimeMessage($mimeData);


		$joinCriteria = GO_Base_Db_FindCriteria::newInstance()->addRawCondition('t.id', 'a.account_id');
		$findParams = GO_Base_Db_FindParams::newInstance()
						->single()
						->debugSql()
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

		echo 'Sending a maximum of ' . GO::config()->mailing_messages_per_minute . ' messages per minute' . "\n";

		$failedRecipients = array();

		$bodyWithTags = $message->getBody();

		foreach ($mailing->contacts as $contact) {			
			$message->setTo($contact->email, $contact->name);
			$message->setBody(GO_Addressbook_Model_Template::model()->replaceModelTags($bodyWithTags, $contact));
			$this->_sendmail($message, $contact, $mailer, $mailing);			
		}

		foreach ($mailing->companies as $company) {
			$message->setTo($company->email, $company->name);
			$message->setBody(GO_Addressbook_Model_Template::model()->replaceModelTags($bodyWithTags, $company));
			$this->_sendmail($message, $company, $mailer, $mailing);			
		}

		$mailing->status = GO_Addressbook_Model_SentMailing::STATUS_FINISHED;
		$mailing->save();

		echo "Mailing finished\n";
	}

	private function _sendmail($message, $model, $mailer, $mailing) {
		
		$typestring = $model instanceof GO_Addressbook_Model_Company ? 'company' : 'contact';
		
		$error = 0;
		$sent = 0;

		if(!$model->email_allowed){
			echo "Skipping $typestring ".$model->email." because newsletter sending is disabled in the addresslists tab.\n\n";
			$error = 1;
		}elseif(empty($model->email)){
			echo "Skipping $typestring ".$model->name." no e-mail address was set.\n\n";
			$error = 1;
		}else
		{		
			echo "Sending to " . $typestring . " id: " . $model->id . " email: " . $model->email . "\n";

			$mailing = GO_Addressbook_Model_SentMailing::model()->findByPk($mailing->id, array(), true, true);
			if($mailing->status==GO_Addressbook_Model_SentMailing::STATUS_PAUSED)
			{
				echo "Mailing paused. Exiting.";
				exit();
			}

			try {
				$mailer->send($message);
			} catch (Exception $e) {
				$status = $e->getMessage();
			}
			if (!empty($status)) {
				echo "---------\n";
				echo "Failed!\n";
				echo $status . "\n";
				echo "---------\n";

				$error = 1;
				unset($status);
			} else {
				$sent = 1;
			}
		}

		if ($typestring == 'contact') {
			$mailing->removeManyMany('contacts', $model->id);
		} else {
			$mailing->removeManyMany('companies', $model->id);			
		}
		
		$mailing->setAttributes(array(
				"sent" => $sent + $mailing->sent,
				"errors" => $error + $mailing->errors
		));
		$mailing->save();
	}

	protected function beforeStore(&$response, &$params, &$store) {

		if (!empty($params['pause_mailing_id'])) {
			$mailing = GO_Addressbook_Model_SentMailing::model()->findByPk($params['pause_mailing_id']);
			$mailing->status = GO_Addressbook_Model_SentMailing::STATUS_PAUSED;
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

}