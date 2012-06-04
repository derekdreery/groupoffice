<?php
class GO_Users_UsersModule extends GO_Base_Module{	
	public function author() {
		return 'Merijn Schering';
	}
	
	public function authorEmail() {
		return 'mschering@intermesh.nl';
	}
	public function autoInstall() {
		return true;
	}
	
	public function adminModule() {
		return true;
	}
	public static function loadSettings(&$settingsController, &$params, &$response, $user) {
		$startModule = GO_Base_Model_Module::model()->findByPk($user->start_module);
		$response['data']['start_module_name']=$startModule ? $startModule->moduleManager->name() : '';
		
		if(!empty($response['data']['company_id']) && GO::modules()->addressbook){
			$company = GO_Addressbook_Model_Company::model()->findByPk($response['data']['company_id']);
			if($company)
				$response['remoteComboTexts']['company_id']=$company->name;
		}
		return parent::loadSettings($settingsController, $params, $response, $user);
	}
}