<?php

class GO_Postfixadmin_Controller_Mailbox extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Postfixadmin_Model_Mailbox';
	
	protected function remoteComboFields() {
		return array('user_id'=>'$model->user->name');
	}
	
	protected function allowGuests() {
		return array("cacheusage");
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
	
	protected function beforeSubmit(&$response, &$model, &$params) {
		if(isset($params['domain_id']))
			$domainModel = GO_Postfixadmin_Model_Domain::model()->findByPk($params['domain_id']);
		else {
			$domainModel = GO_Postfixadmin_Model_Domain::model()->findSingleByAttribute("domain", $params['domain']); //serverclient module doesn't know the domain_id. It sends the domain name as string.
			if(!$domainModel){
				//todo create new domain
				$domainModel = GO_Postfixadmin_Controller_Domain::model();
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
			// Create new alias
			$now = time();
			$aliasModel = GO_Postfixadmin_Model_Alias::model()->findSingleByAttribute('address', $params['username']);
			if (empty($aliasModel)) {
				$aliasModel = GO_Postfixadmin_Model_Alias::model();
				$aliasModel->setIsNew(true);
			}
			$aliasModel->domain_id = $params['domain_id'];
			$aliasModel->address = $params['username'];
			$aliasModel->goto = $params['username'];
			$aliasModel->ctime = $now;
			$aliasModel->mtime = $now;
			$aliasModel->active = 1;
			$aliasModel->save();
		}
	}
	
	public function formatStoreRecord($record, $model, $store) {
		$record['usage'] = GO_Base_Util_Number::formatSize($model->usage*1024);
		$record['quota'] = GO_Base_Util_Number::formatSize($model->quota*1024);
		return $record;
	}
	
	
	protected function actionCacheUsage($params){
		if(!$this->isCli())
			throw new Exception("Not in CLI");

		if(!GO::modules()->isInstalled('postfixadmin'))
			trigger_error('Postfixadmin module must be installed',E_USER_ERROR);

		$activeStmt = GO_Postfixadmin_Model_Mailbox::model()->find();
		
		while ($mailboxModel = $activeStmt->fetch()) {
			$folder = new GO_Base_Fs_Folder('/home/vmail/'.$mailboxModel->maildir);
			echo 'Calculating size of '.$folder->path()."\n";
			$mailboxModel->usage = $folder->calculateSize()/1024;
			echo $mailboxModel->usage." kilobytes\n";
			$mailboxModel->save();
		}

	}
	
}

