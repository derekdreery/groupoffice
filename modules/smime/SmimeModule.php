<?php
class GO_Smime_SmimeModule extends GO_Base_Module{
	public static function initListeners() {		
		$accountController = new GO_Email_Controller_Account();
		$accountController->addListener('load', "GO_Smime_EventHandlers", "loadAccount");
		$accountController->addListener('submit', "GO_Smime_EventHandlers", "submitAccount");
		
		$messageController = new GO_Email_Controller_Message();
		$messageController->addListener('beforesend', "GO_Smime_EventHandlers", "beforeSend");
		$messageController->addListener('view', "GO_Smime_EventHandlers", "viewMessage");
		
		$aliasController = new GO_Email_Controller_Alias();
		$aliasController->addListener('store', "GO_Smime_EventHandlers", "aliasesStore");
		
		GO_Email_Model_Account::model()->addListener('delete', "GO_Smime_EventHandlers", "deleteAccount");
		
		GO_Base_Model_User::model()->addListener('delete', "GO_Smime_SmimeModule", "deleteUser");
		
	}
	
	public static function deleteUser($user) {		
		GO_Smime_Model_PublicCertificate::model()->deleteByAttribute('user_id', $user->id);
	}
}