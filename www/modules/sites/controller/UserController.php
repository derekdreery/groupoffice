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
 * Constroller for user specific frontend actions
 *
 * @package GO.
 * @copyright Copyright Intermesh
 * @version $Id UserController.php 2012-06-29 15:44:51 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 */
class GO_Sites_Controller_User extends GO_Sites_Components_AbstractFrontController {

	protected function ignoreAclPermissions() {
		return array('register','resetpassword','profile');
	}

	/**
	 * The action that handles the page to let a user register to the site.
	 * 
	 * @param array $params The params that are passed through to this page
	 */
	protected function actionRegister($params) {
		if (GO_Base_Util_Http::isPostRequest()) {

			GO_Base_Html_Error::checkRequired();

			GO_Base_Html_Error::getError('vat_no'); // This is required to unset the error in the session
			try{
				if(!empty($params['vat_no']) && !empty($params['country'])){
					$isValid = GO_Base_Util_Validate::checkVat ($params['country'], $params['vat_no']);
					if(!$isValid)
						GO_Base_Html_Error::setError ("The specified VAT number is not correct!", 'vat_no');
				}
			}
			catch(GO_Base_Exception_ViesDown $e){
					//GO_Base_Html_Error::setError ("The Vies service is down!", 'vat_no');
			}

			$model = new GO_Base_Model_User();
			$model->setAttributes($params);
			GO_Base_Html_Error::validateModel($model);

			if (!GO_Base_Html_Error::hasErrors()) {
				$model->save();				
				$model->checkDefaultModels();
				$model->addToGroups($this->getSite()->getDefaultGroupNames());
				
				$contact = $model->createContact();
				$contact->setAttributes($params);

				$company = new GO_Addressbook_Model_Company();
				$company->setAttributes($params);
				$company->name = $params['company'];
				$company->setPostAddressFromVisitAddress();
				$company->addressbook_id = $contact->addressbook_id;
				$company->save();

				$contact->company_id = $company->id;
				$contact->save();

				GO::session()->login($params['username'], $params['password']); // Automatically log the newly created user in.
				$this->redirect($this->getReturnUrl());
			}
		}
		$this->render('register',$params);
	}

	/**
	 * Action that needs to be called for the page to let the user recover 
	 * the password.
	 * 
	 * @param array $params The params that are passed through to this page
	 */
	protected function actionRecover($params) {
		$this->formok=false;
		if (GO_Base_Util_Http::isPostRequest()) {
			
			GO_Base_Html_Error::checkRequired();

			$user = GO_Base_Model_User::model()->findSingleByAttribute('email', $params['email']);
			
			if(!$user){
				GO_Base_Html_Error::setError(GO::t("invaliduser","sites"),"email");
			}else{
				
				GO::language()->setLanguage($user->language);

				$siteTitle = $this->getSite()->name;
				$url = $this->pageUrl('resetpassword',array(),false);

				$fromName = $this->getSite()->name;
				$fromEmail = 'noreply@intermesh.nl';
				
				$user->sendResetPasswordMail($siteTitle,$url,$fromName,$fromEmail);
				
				$this->message = "An email with recover instructions is send to the following email address: ".$user->email;
				$this->formok=true;
			}
		}
		
		$this->render('recover', $params);
	}

	protected function actionResetPassword($params) {

		$this->formok=false;
		
		if(empty($params['email'])){
			throw new Exception(GO::t("noemail","sites"));
		}else{
			$user = GO_Base_Model_User::model()->findSingleByAttribute('email', $params['email']);
			
			
			if($user){
				GO::language()->setLanguage($user->language);
			
				if(!empty($params['usertoken']) && $params['usertoken'] == $user->getSecurityToken()){

					if (GO_Base_Util_Http::isPostRequest()) {
						GO_Base_Html_Error::checkRequired();
						
						$user->password = $params['password'];
						$user->passwordConfirm = $params['confirm'];
						
						GO_Base_Html_Error::validateModel($user);
						
						if(!GO_Base_Html_Error::hasErrors()){
							$user->save();
							$this->formok=true;
						} else {
							
						}
					}
				}else{
					throw new Exception(GO::t("invalidusertoken","sites"));
				}				
			}else{
				throw new Exception(GO::t("invaliduser","sites"));
			}
		}
		
		$this->render('resetPassword',$params);
	}
	
	
	
	/**
	 * The login function for the site.
	 * 
	 * It will handle the login credentials if they are posted to this page.
	 * 
	 * The function will redirect you automatically to the latest page that you
	 * had visited before the login.
	 */
	public function actionLogin(){
		
		$model = new GO_Base_Model_User();
		
		if (GO_Base_Util_Http::isPostRequest()) {
			
			$params['username'] = $_POST['GO_Base_Model_User']['username'];
			$params['password'] = $_POST['GO_Base_Model_User']['password'];

			$user = GO::session()->login($params['username'], $params['password']);

			$response['success'] = $user != false;

			if (empty($response['success'])) {
				GO_Base_Html_Error::setError("Login failed!"); // set the correct login failure message
			} else {
				if (!empty($params['remind'])) {

					$encUsername = GO_Base_Util_Crypt::encrypt($params['username']);
					if ($encUsername)
						$encUsername = $params['username'];

					$encPassword = GO_Base_Util_Crypt::encrypt($params['password']);
					if ($encPassword)
						$encPassword = $params['password'];

					GO_Base_Util_Http::setCookie('GO_UN', $encUsername);
					GO_Base_Util_Http::setCookie('GO_PW', $encPassword);
				}
				$this->redirect($this->getReturnUrl());
			}
		}

		$this->render('login',array('model'=>$model));
	}

	/**
	 * The action that needs to be called if you want that a user needs to 
	 * be logged out.
	 */
	public function actionLogout(){
		GO::session()->logout();
		GO::session()->start();
		$this->redirect(GOS::site()->getLoginUrl());
	}
	
	protected function actionProfile($params){
		
		$user = GO::user();
		$contact = $user->contact;
		
		if($contact->company)
			$company = $contact->company;
		else{
			$company = new GO_Addressbook_Model_Company();
			$company->addressbook_id=$contact->addressbook_id;
		}
		
		if (GO_Base_Util_Http::isPostRequest()) {
			if(!empty($_POST['GO_Base_Model_User']['password']))
			{
				if(!$model->checkPassword($_POST['currentPassword'])){
					GOS::site()->notifier->setMessage('error', "Huidig wachtwoord onjuist");
				}else{
					$model->password = $_POST['GO_Base_Model_User']['password'];
					$model->passwordConfirm = $_POST['GO_Base_Model_User']['passwordConfirm'];
				}
			}else{
				unset($_POST['GO_Base_Model_User']['password']);
				unset($_POST['GO_Base_Model_User']['passwordConfirm']);
			}
				
			$contact->attributes = $_POST['GO_Addressbook_Model_Contact'];
			$user->attributes = $_POST['GO_Base_Model_User'];
			$company->attributes = $_POST['GO_Addressbook_Model_Company'];
			
			if(!empty($params['post_address_is_address']))
				$company->setPostAddressFromVisitAddress();

			GO_Base_Html_Error::validateModel($user);
			GO_Base_Html_Error::validateModel($contact);
			GO_Base_Html_Error::validateModel($company);
			
			if(!GO_Base_Html_Error::hasErrors()){
				$user->save();

				$company->save();
				$contact->company_id = $company->id;				
				$contact->save();
				GOS::site()->notifier->setMessage('success', GOS::t('formEditSuccess'));
				$this->pageRedirect($this->getPage()->path);
			}
		}

		$company->post_address_is_address = false;
	
		if($company->address==$company->post_address && 
			 $company->address_no==$company->post_address_no &&
			 $company->city==$company->post_city
			){
			 $company->post_address_is_address = true;
		}
				
		$params['user'] = $user;
		$params['contact'] = $contact;
		$params['company'] = $company;

		$this->getPage()->attachHeaderInclude('js',$this->getRootTemplateUrl().'js/jquery-1.7.2.min.js');
		$this->getPage()->attachHeaderInclude('js',$this->getRootTemplateUrl().'js/profileToggle.js');		
		
		$this->render('profile', array('user'=>$user,'contact'=>$contact));

	}

}
?>
