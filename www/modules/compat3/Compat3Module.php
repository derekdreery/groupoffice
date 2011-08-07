<?php
/**
 * This module makes the new code of GO4.0 compatible with modules coded in 3.0
 * style. The new events system fires old events in this class.
 */
class GO_Compat3_Compat3Module extends GO_Base_Model_Module{
	
	public function __construct(){
		require_once(GO::config()->root_path.'Group-Office.php');
	}
	
	public function initListeners(){
		GO_Base_Model_User::addListener('beforesave', 'GO_Compat3_Compat3Module', 'beforeSaveUser');
	}	
	
	public static function beforeSaveUser($user){		
		if($user->isNew){
			
			$userarray = array($user);
			$ramdom_password=$user->generatedRandomPassword;
			
			$GLOBALS['GO_EVENTS']->fire_event('before_add_user', array($user, $random_password));
		}
		

	}
}