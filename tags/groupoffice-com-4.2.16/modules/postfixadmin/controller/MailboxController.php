<?php

class GO_Postfixadmin_Controller_Mailbox extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Postfixadmin_Model_Mailbox';
	
	
	protected function allowGuests() {
		return array("cacheusage");
	}
		
	protected function getStoreParams($params) {
		return GO_Base_Db_FindParams::newInstance()
						->criteria(GO_Base_Db_FindCriteria::newInstance()
				->addCondition('domain_id',$params['domain_id']));		
	}
	
	protected function afterLoad(&$response, &$model, &$params) {
		if($model->isNew)
			$model->quota=$model->domain->default_quota;
		$response['data']['password'] = '';
		$response['data']['quota'] = GO_Base_Util_Number::localize($model->quota/1024);
		$response['data']['domain']='@'.$model->domain->domain;
		$response['data']['username']=str_replace($response['data']['domain'],"", $response['data']['username']);
		return $response;
	}
	
	
	protected function actionSetPassword($params){
		$mailbox = GO_Postfixadmin_Model_Mailbox::model()->findSingleByAttributes(array(
				"username"=>$params["username"]				
		));

		$response['success']=true;
		
		if($mailbox){
			$mailbox->password=$params["password"];
			$response['success'] = $mailbox->save()===true;
			if (!$response['success']) {
				$validateErrors = $mailbox->getValidationErrors();
				$response['feedback'] = implode('<br />',$validateErrors);
			}
		}
		
		return $response;
	}
	
	protected function beforeSubmit(&$response, &$model, &$params) {
		if(isset($params['domain_id']))
			$domainModel = GO_Postfixadmin_Model_Domain::model()->findByPk($params['domain_id']);
		else {
			$domainModel = GO_Postfixadmin_Model_Domain::model()->findSingleByAttribute("domain", $params['domain']); //serverclient module doesn't know the domain_id. It sends the domain name as string.
			if(!$domainModel){
				//todo create new domain
				$domainModel = new	GO_Postfixadmin_Model_Domain();
				$domainModel->domain = $params['domain'];
				$domainModel->user_id = GO::user()->id;
				$domainModel->save();
			}
			$params['domain_id']=$domainModel->id;
			
			$model->quota = $domainModel->default_quota;
		}
		
		if(isset($params['quota'])){
			$model->quota=  GO_Base_Util_Number::unlocalize($params['quota'])*1024;
			unset($params['quota']);
		}
		
		if ($params['password']!=$params['password2'])
			throw new Exception(GO::t('passwordMatchError'));
		
		if(empty($params['password']))
			unset($params['password']);
		
		if(isset($params['username']))
			$params['username'] .= '@'.$domainModel->domain;
		
		if ($model->getIsNew()) {
			$aliasModel = GO_Postfixadmin_Model_Alias::model()->findSingleByAttribute('address', $params['username']);
			if (empty($aliasModel)) {
				$aliasModel = GO_Postfixadmin_Model_Alias::model();
			}
			$aliasModel->domain_id = $params['domain_id'];
			$aliasModel->address = $params['username'];
			$aliasModel->goto = $params['username'];
			$aliasModel->save();
		}
	}
	
	public function formatStoreRecord($record, $model, $store) {
		$record['usage'] = GO_Base_Util_Number::formatSize($model->usage*1024);
		$record['quota'] = GO_Base_Util_Number::formatSize($model->quota*1024);
		return $record;
	}
	
	
	protected function actionCacheUsage($params){
		$this->requireCli();

		if(!GO::modules()->isInstalled('postfixadmin'))
			trigger_error('Postfixadmin module must be installed',E_USER_ERROR);

		$activeStmt = GO_Postfixadmin_Model_Mailbox::model()->find();
		
		while ($mailboxModel = $activeStmt->fetch()) {
			echo 'Calculating size of '.$mailboxModel->getMaildirFolder()->path()."\n";
			$mailboxModel->cacheUsage();
			echo GO_Base_Util_Number::formatSize($mailboxModel->usage*1024)."\n";
		}

	}
	
	
//	protected function actionImport($params){
//		
//		$source=array(
//				'host'=>'imap.imfoss.nl',
//				'port'=>993,
//				'username'=>'test@intermesh.nl',
//				'password'=>'test',
//				'ssl'=>true
//		);
//		
//		$localUsername="import@intermesh.dev";
//		
//		$imap = new GO_Base_Mail_Imap();
//		if(!$imap->connect($source['host'], $source['port'], $source['username'], $source['password'], $source['ssl']))
//				throw new Exception("Could not connect to source host");
//		
//		$folders = $imap->list_folders(true, false, "", "*");
//		if(!is_array($folders))
//			throw new Exception("Failed to fetch folder list");
//		
//		$folderNames = array_keys($folders);
//		if(!in_array('INBOX', $folderNames))
//			$folderNames[]='INBOX';
//		
//		$list = implode(',', $folderNames);
//				
//    $fetchmailRc = 
//			"poll ".$source['host']."\n".
//			"proto imap\n".
//			"user ".$source['username']."\n".
//			"pass ".$source['password']."\n".
//			"is ".$localUsername."\n".
//			"limit 20480000\n".
//			"folder $list\n".
//			"keep\n".
//			"dropdelivered";
//		 
//		
//		file_put_contents('/tmp/importFetchmailRc', $fetchmailRc);
//		chmod('/tmp/importFetchmailRc',0700);
//		
//		system('fetchmail -r /tmp/importFetchmailRc -v');
//	}
	
}

