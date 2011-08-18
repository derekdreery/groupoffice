<?php
/**
 * This module makes the new code of GO4.0 compatible with modules coded in 3.0
 * style. The new events system fires old events in this class.
 */
class GO_Compat3_Compat3Module extends GO_Base_Module{
	
	public function __construct(){
		require_once(GO::config()->root_path.'Group-Office.php');
	}
	
	public static function initListeners(){
		GO_Base_Model_User::addListener('beforesave', 'GO_Compat3_Compat3Module', 'beforeSaveUser');
		GO_Base_Model_User::addListener('save', 'GO_Compat3_Compat3Module', 'saveUser');
		GO_Base_Model_User::addListener('delete', 'GO_Compat3_Compat3Module', 'deleteUser');
	}	
	
	public static function beforeSaveUser($user){
		if($user->isNew){
			
			$userarray = $user->getAttributes();
			$ramdom_password=$user->generatedRandomPassword;
			
			$GLOBALS['GO_EVENTS']->fire_event('before_add_user', array($user, $random_password));
		}
	}
	
	public static function saveUser($user){
		if($user->isNew){
			
			$userarray = $user->getAttributes();
			$ramdom_password=$user->generatedRandomPassword;
			
			$GLOBALS['GO_EVENTS']->fire_event('add_user', array($user, $random_password));
		}
	}
	
	public static function deleteUser($user){
		
		$GLOBALS['GO_EVENTS']->fire_event('user_delete', array(&$userarray));
	}
}