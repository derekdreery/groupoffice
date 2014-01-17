<?php
class GO_Admin2userlogin_Controller_Switch extends GO_Base_Controller_AbstractController {
	protected function actionSwitch($params){
//		
//		if(!GO::user()->isAdmin())
//			throw new Exception("This feature is for admins only!");
		
		$oldUsername=GO::user()->username;
		
		$debug = !empty(GO::session()->values['debug']);
		
		$user = GO_Base_Model_User::model()->findByPk($params['user_id']);
		
		GO::session()->values=array(); //clear session
		GO::session()->setCurrentUser($user->id);
		//GO::session()->setCompatibilitySessionVars();
		
		if($debug)
			GO::session()->values['debug']=$debug;
		
		GO::infolog("ADMIN logged-in as user: \"".$user->username."\" from IP: ".$_SERVER['REMOTE_ADDR']);
		
		if(GO::modules()->isInstalled('log')){		
			GO_Log_Model_Log::create('switchuser', "'".$oldUsername."' logged in as '".$user->username."'");
		}
		
		$this->redirect();
	}
}