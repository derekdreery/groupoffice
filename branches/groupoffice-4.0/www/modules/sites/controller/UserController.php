<?php

class GO_Sites_Controller_User extends GO_Sites_Controller_Site {

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
				$this->pageRedirect($this->getSite()->getLastPath(),$this->getSite()->getLastParams());
			}
		}
		$this->renderPage($params);
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
		
		$this->renderPage($params);
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
		
		$this->renderPage($params);
	}
	
	
	
	/**
	 * The login function for the site.
	 * 
	 * It will handle the login credentials if they are posted to this page.
	 * 
	 * The function will redirect you automatically to the latest page that you
	 * had visited before the login.
	 * 
	 * @param array $params The params that are passed through to this page
	 */
	protected function actionLogin($params) {

		if (GO_Base_Util_Http::isPostRequest()) {

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

				$this->pageRedirect($this->getSite()->getLastPath(),$this->getSite()->getLastParams());
			}
		}

		$this->renderPage($params);
	}

	/**
	 * The action that needs to be called if you want that a user needs to 
	 * be logged out.
	 * 
	 * @param array $params The params that need to be passed to the page after 
	 * the user is logged out.
	 */
	protected function actionLogout($params) {
		GO::session()->logout();
		GO::session()->start();
		$this->pageRedirect($this->getSite()->getLoginPath());
	}
	
	protected function actionProfile($params){
		if(!GO::user())
			$this->pageRedirect($this->getSite()->login_path);
		
		$user = GO::user();
		$contact = $user->createContact();
		
		if($contact->company)
			$company = $contact->company;
		else{
			$company = new GO_Addressbook_Model_Company();
			$company->addressbook_id=$contact->addressbook_id;
		}
		
		if (GO_Base_Util_Http::isPostRequest()) {

			GO_Base_Html_Error::checkRequired();
			
			if(!empty($params['password'])){
				if(!$user->checkPassword($params['currentPassword'])){
					GO_Base_Html_Error::setError($this->t('currentPasswordError'),'currentPassword');
				}else{
					$user->password = $params['password'];
					$user->passwordConfirm = $params['passwordConfirm'];
				}
			}else{
				unset($params['password']);
				unset($params['passwordConfirm']);
			}
				
			$contact->setAttributes($params);
			$user->setAttributes($params);	
			$company->setAttributes($params);
			
			
			if(!empty($params['post_address_is_address'])){
				$company->setPostAddressFromVisitAddress();
			}
			
			GO_Base_Html_Error::validateModel($user);
			GO_Base_Html_Error::validateModel($contact);
			GO_Base_Html_Error::validateModel($company);
			
			if(!GO_Base_Html_Error::hasErrors()){
				$user->save();
				$company->save();
				$contact->company_id = $company->id;				
				$contact->save();
				$this->notifications->addNotification('profile', $this->t('formEditSuccess'), GO_Sites_NotificationsObject::NOTIFICATION_OK);
			}
		}
		
		$company->post_address_is_address = $company->address==$comapny->post_address?true:false;
		
		var_dump($company->address);
		var_dump($company->post_address);
		
		$params['user'] = $user;
		$params['contact'] = $contact;
		$params['company'] = $company;

		$this->renderPage($params);
	}
	
	

}