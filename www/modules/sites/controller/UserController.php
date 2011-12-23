<?php

class GO_Sites_Controller_User extends GO_Sites_Controller_Site{
	
	protected function allowGuests() {
		return array('register','login','logout','recover');
	}
	
	
	public function actionRegister($params){
		GO::$ignoreAclPermissions=true; // To tell no permissions are required to save a new user.

		$model = new GO_Base_Model_User();
		$model->setAttributes($params);

		if(GO_Base_Html_Input::checkRequired() && $model->validate()){
			$model->save();
		}else{
			$errors = $model->getValidationErrors();
			foreach($errors as $attribute=>$message){
				GO_Base_Html_Input::setError($attribute, $message);
			}				
			GO_Base_Html_Error::setError('Your form has errors');
		}
	}
	
	public function actionRecover($params){
		
		
	}
	
	public function actionLogin($params){
		
		if(GO_Base_Util_Http::isPostRequest()){
		
			$user = GO::session()->login($params['username'], $params['password']);

			$response['success'] = $user != false;

			if (empty($response['success'])) {
				GO_Base_Html_Error::setError("Login failed!"); // set the correct login failure message

				GO::infolog("LOGIN TO WEBSITE FAILED for user: \"" . $params['username'] . "\" from IP: " . $_SERVER['REMOTE_ADDR']);

				//sleep 3 seconds for slowing down brute force attacks
				sleep(1);
			} else {
				GO::infolog("LOGIN TO WEBSITE WAS A SUCCESS for user: \"" . $params['username'] . "\" from IP: " . $_SERVER['REMOTE_ADDR']);

				if (!empty($params['remind'])) {

					$encUsername = GO_Base_Util_Crypt::encrypt($params['username']);
					if ($encUsername)
						$encUsername = $params['username'];

					$encPassword = GO_Base_Util_Crypt::encrypt($params['password']);
					if ($encPassword)
						$encPassword = $params['password'];

					$this->setCookie('GO_UN', $encUsername, 3600 * 24 * 30);
					$this->setCookie('GO_PW', $encPassword, 3600 * 24 * 30);
				}
				
				$this->pageRedirect($this->getSite()->getLastPath());
				
			}
		}
		
//		if ($this->isAjax())
//			return $response;
//		else
//			$this->pageRedirect(GO::session()->values['sites']['beforeLoginPath']);		
		$this->renderPage($params);	
	}
	
	public function actionLogout($params){
		GO::session()->logout();
		GO::session()->start();		
		$this->pageRedirect($this->getSite()->getLoginPath());	
	}
	
}