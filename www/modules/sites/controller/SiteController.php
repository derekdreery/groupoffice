<?php

/*
 * Copyright Intermesh BV
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * Default controller for site module, contains default action like register, login, logout, index, lostpassword
 *
 * @package GO.modules.sites.controller
 * @copyright Copyright Intermesh
 * @version $Id DefaultController.php 2012-06-08 10:51:35 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 */
class GO_Sites_Controller_Site extends GO_Sites_Components_AbstractFrontController
{
	public function allowGuests()
	{
		return array('login','register','content','index','error','recoverpassword','resetpassword', 'plupload');
	}
	
	/**
	 *Can be used to render homepage or something. Doesn't do much more
	 */
	public function actionIndex(){
		 $this->render('index'); 
	}
	
	public function actionPlupload(){
		$ctr = new GO_Core_Controller_Core();
		$ctr->run('plupload', $_POST);// actionPlupload($_POST);
	}
	
	/**
	 * Renders content item selected them from database using slug and render them using the content view
	 * @throws GO_Base_Exception_NotFound if the content item with given slug was not found
	 */
	public function actionContent() {
		$content = GO_Sites_Model_Content::model()->findSingleByAttribute('slug', $_GET['slug']);
		
		if($content == null)
			throw new GO_Base_Exception_NotFound('404 Page not found');
		
		$this->render('content', array('content'=>$content));
	}
	
	/**
	 * Register a new user this controller can save User, Contact and Company
	 * Only if attributes are provided by the POST request shall the model be saved
	 */
	public function actionRegister() {
		$user = new GO_Base_Model_User();
		$contact = new GO_Addressbook_Model_Contact();
		$company = new GO_Addressbook_Model_Company();
		
		if(GO_Base_Util_Http::isPostRequest())
		{
			$user->setAttributes($_POST['User']);
			$contact->setAttributes($_POST['Contact']);
			if($user->validate() && $contact->validate())
			{
				GO::$ignoreAclPermissions = true; //Guest have no right to create users by default ignore this
				if($user->save())
				{
					$contact = $user->createContact();
					$contact->setAttributes($_POST['Contact']);
					$user->addToGroups(GOS::site()->getSite()->getDefaultGroupNames()); // Default groups are in si_sites table
					$addressbook = GO_Addressbook_Model_Addressbook::model()->getUsersAddressbook();
					$contact->addressbook_id = $addressbook->id;
					$contact->user_id = $user->id;
					$contact->save();

					// Automatically log the newly created user in.
					if(GO::session()->login($user->username, $_POST['User']['password']))
						$this->redirect($this->getReturnUrl());
					else
						throw new Exception('Login after registreation failed.');
				}
			}
		}
		
		$this->render('register', array('user'=>$user,'contact'=>$contact,'company'=>$company));
	}
	
	/**
	 * Action that needs to be called for the page to let the user recover 
	 * the password.
	 */
	public function actionRecoverPassword() {
		
		if (GO_Base_Util_Http::isPostRequest())
		{
			$user = GO_Base_Model_User::model()->findSingleByAttribute('email', $_POST['email']);
			
			if($user == null){
				GOS::site()->notifier->setMessage('error', GO::t("invaliduser","sites"));
			}else{
				//GO::language()->setLanguage($user->language);

				$siteTitle = GOS::site()->getSite()->name;
				$url = $this->createUrl('/sites/site/resetpassword', array(), false);

				$fromName = GOS::site()->getSite()->name;
				$fromEmail = 'noreply@intermesh.nl'; //.GOS::site()->getSite()->domain;
				
				$user->sendResetPasswordMail($siteTitle,$url,$fromName,$fromEmail);
				GOS::site()->notifier->setMessage('success', GO::t('recoverEmailSent', 'sites')." ".$user->email);
			}
		}
		
		$this->render('recoverPassword');
	}
	
	public function actionResetPassword()
	{
		if(empty($_GET['email']))
			throw new Exception(GO::t("noemail","sites"));

		$user = GO_Base_Model_User::model()->findSingleByAttribute('email', $_GET['email']);

		if(!$user)
			throw new Exception(GO::t("invaliduser","sites"));
		
		GO::language()->setLanguage($user->language);

		if(isset($_GET['usertoken']) && $_GET['usertoken'] == $user->getSecurityToken())
		{
			if (GO_Base_Util_Http::isPostRequest())
			{
				$user->password = $_POST['User']['password'];
				$user->passwordConfirm = $_POST['User']['passwordConfirm'];

				GO::$ignoreAclPermissions = true; 
				
				if($user->validate() && $user->save())
					GOS::site()->notifier->setMessage('success',GO::t('resetPasswordSuccess', 'sites'));
			}
		}
		else
			GOS::site()->notifier->setMessage('error',GO::t("invalidusertoken","sites"));
				
		$user->password = null;
		$this->render('resetPassword', array('user'=>$user));
	}
	
	/**
	 * Render a login page 
	 */
	public function actionLogin(){
		
		$model = new GO_Base_Model_User();
		
		if (GO_Base_Util_Http::isPostRequest()) {
			$model->username = $_POST['User']['username'];
			$password = $_POST['User']['password'];

			$user = GO::session()->login($model->username, $password);

			if (!$user) {
				GOS::site()->notifier->setMessage('error', GO::t('badLogin')); // set the correct login failure message
			} else {
				if (!empty($_POST['rememberMe'])) {

					$encUsername = GO_Base_Util_Crypt::encrypt($model->username);
					if ($encUsername)
						$encUsername = $model->username;

					$encPassword = GO_Base_Util_Crypt::encrypt($password);
					if ($encPassword)
						$encPassword = $password;

					GO_Base_Util_Http::setCookie('GO_UN', $encUsername);
					GO_Base_Util_Http::setCookie('GO_PW', $encPassword);
				}
				$this->redirect($this->getReturnUrl());
			}
		}

		$this->render('login',array('model'=>$model));
	}
	
	/**
	 * Logout the current user and redirect to loginpage 
	 */
	public function actionLogout(){
		GO::session()->logout();
		GO::session()->start();
		$this->redirect(GOS::site()->getLoginUrl());
	}
}
?>
