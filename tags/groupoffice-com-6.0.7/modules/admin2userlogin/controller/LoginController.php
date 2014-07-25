<?php

namespace GO\Admin2userlogin\Controller;


class LoginController extends \GO\Base\Controller\AbstractController {
	protected function actionSwitch($params){
//		
//		if(!\GO::user()->isAdmin())
//			throw new \Exception("This feature is for admins only!");
		
		$oldUsername=\GO::user()->username;
		
		$debug = !empty(\GO::session()->values['debug']);
		
		$user = \GO\Base\Model\User::model()->findByPk($params['user_id']);
		
		\GO::session()->values=array(); //clear session
		\GO::session()->setCurrentUser($user->id);
		//\GO::session()->setCompatibilitySessionVars();
		
		if($debug)
			\GO::session()->values['debug']=$debug;
		
		\GO::infolog("ADMIN logged-in as user: \"".$user->username."\" from IP: ".$_SERVER['REMOTE_ADDR']);
		
		if(\GO::modules()->isInstalled('log')){		
			\GO\Log\Model\Log::create('switchuser', "'".$oldUsername."' logged in as '".$user->username."'");
		}
		
		$this->redirect();
	}
}