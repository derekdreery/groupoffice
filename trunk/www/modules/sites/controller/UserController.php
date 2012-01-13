<?php

class GO_Sites_Controller_User extends GO_Sites_Controller_Site{

	/**
	 * The action that handles the page to let a user register to the site.
	 * 
	 * @param array $params The params that are passed through to this page
	 */
	protected function actionRegister($params){
		GO::$ignoreAclPermissions=true; // To tell no permissions are required to save a new user.

		$model = new GO_Base_Model_User();
		
		if(GO_Base_Util_Http::isPostRequest()){
			$model->setAttributes($params);

			if(GO_Base_Html_Input::checkRequired() && $model->validate()){
				$model->save();
				
				$contact =$model->createContact();
				$contact->setAttributes($params);
				
				$company = new GO_Addressbook_Model_Company();
				$company->addressbook_id=$contact->addressbook_id;
				$company->setAttributes($params);
				$company->name=$params['company'];
				$company->setPostAddressFromVisitAddress();
				$company->save();
				
				$contact->company_id=$company->id;
				
				$contact->save();
				
				GO::session()->login($params['username'], $params['password']);
				$this->pageRedirect($this->getSite()->getLastPath());
			}else{
				$errors = $model->getValidationErrors();
				foreach($errors as $attribute=>$message){
					GO_Base_Html_Input::setError($attribute, $message);
				}				
				GO_Base_Html_Error::setError(GO::t('errorsInForm'));
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
	protected function actionRecover($params){
		
		
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
	protected function actionLogin($params){
		
		if(GO_Base_Util_Http::isPostRequest()){
		
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
				
				$this->pageRedirect($this->getSite()->getLastPath());
				
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
	protected function actionLogout($params){
		GO::session()->logout();
		GO::session()->start();		
		$this->pageRedirect($this->getSite()->getLoginPath());	
	}
	
}