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
		$domainModel = GO_Postfixadmin_Model_Domain::model()->findByPk($params['domain_id']);
		
		if(isset($params['quota'])){
			$model->quota=  GO_Base_Util_Number::unlocalize($params['quota'])*1024;
			unset($params['quota']);
		}
		
		if ($params['password']!=$params['password2'])
			throw new Exception(GO::t('passwordMatchError'));
		
		if(isset($params['username']))
			$params['username'] .= '@'.$domainModel->domain;
	}
		
	public function formatStoreRecord($record, $model, $store) {
		$record['quota'] = GO_Base_Util_Number::localize($model->quota/1024);
		return $record;
	}
	
	
	protected function actionCacheUsage($params){
		if(!$this->isCli())
			throw new Exception("Not in CLI");
		
		if(isset($argv[1]))
			define('CONFIG_FILE', $argv[1]);

		if(!GO::modules()->isInstalled('postfixadmin'))
			trigger_error('Postfixadmin module must be installed',E_USER_ERROR);

		$activeStmt = GO_Postfixadmin_Model_Mailbox::model()->find();
		
		while ($mailboxModel = $activeStmt->fetch()) {
			$folder = new GO_Base_Fs_Folder('/home/vmail/'.$mailboxModel->maildir);
			echo 'Calculating size of '.$folder->path()."\n";
			$mailboxModel->usage = $folder->calculateSize($path)/1024;
			echo $mailboxModel->usage."\n";
			$mailboxModel->save();
		}

	}
	
}

