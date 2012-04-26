<?php

/**
 * 
 * TODO
 * 
 * The whole init process of Group-Office has to be remodelled.
 * The default_scripts.inc.php file is ugly and bad design. Instead all init
 * views in modules should register client scripts and css files.
 */
class GO_Core_Controller_Auth extends GO_Base_Controller_AbstractController {

	protected $defaultAction = 'Init';
	
	/**
	 * Guests need to access these actions.
	 * 
	 * @return array. 
	 */
	protected function allowGuests() {
		return array('init', 'setview','logout','login','resetpassword','setnewpassword','sendresetpasswordmail');
	}
	
	protected function ignoreAclPermissions() {
		return array('setnewpassword');
	}

	private function loadInit() {
		GO_Base_Observable::cacheListeners();

		//when GO initializes modules need to perform their first run actions.
		unset(GO::session()->values['firstRunDone']);

		if (GO::user())
			$this->fireEvent('loadapplication', array(&$this));
	}

	protected function actionInit() {

		$this->loadInit();
		$this->render('index');
	}

	protected function actionSetView($params) {
		GO::setView($params['view']);

		$this->redirect();
	}
	
	protected function actionResetPassword($params){
		$this->render('resetpassword');
	}
	
	protected function actionSetNewPassword($params){
		
		$response = array();
	
		if(!GO_Base_Util_Http::isPostRequest() || empty($params['email']) || empty($params['usertoken'])){
			$response['success']=false;
			$response['feedback']="Invalid request!";
			return $response;
		}

		$user = GO_Base_Model_User::model()->findSingleByAttribute('email', $params['email']);
		if($user){
			if($params['usertoken'] == $user->getSecurityToken()){
				
				$user->password = $_REQUEST['password'];
				$user->passwordConfirm = $_REQUEST['confirm'];

				if($user->save()){				
					$response['success']=true;
				}else{
					$response['success']=false;
					$response['feedback']="Something went wrong with changing the users password";
				}
			}else{
				$response['success']=false;
				$response['feedback']="Usertoken did not match!";
			}
		}else{
			$response['success']=false;
			$response['feedback']="No user found!";
		}
		return $response;
	}
	
	protected function actionSendResetPasswordMail($params){
		$user = GO_Base_Model_User::model()->findSingleByAttribute('email', $params['email']);

		if(!$user){
			$response['success']=false;
			$response['feedback']=GO::t('lost_password_error','base','lostpassword');
		}else{
			
			$user->sendResetPasswordMail();
			
			$response['success']=true;
			$response['feedback']=GO::t('lost_password_success','base','lostpassword');
		}
		
		return $response;
	}

	protected function actionLogout() {

		GO::session()->logout();

		if (isset($_COOKIE['GO_FULLSCREEN']) && $_COOKIE['GO_FULLSCREEN'] == '1') {
			?>
			<script type="text/javascript">
				window.close();
			</script>
			<?php

			exit();
		} else {
			$this->redirect();
		}
	}

	protected function actionLogin($params) {
		
		if(!empty($params['domain']))
			$params['username'].=$params['domain'];	
		
		$response = array();
		
		if(!$this->fireEvent('beforelogin', array($params, &$response)))
			return $response;		
		
		$user = GO::session()->login($params['username'], $params['password']);

		$response['success'] = $user != false;

		if (!$response['success']) {		
			$response['feedback']=GO::t('badLogin');			
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
			
			$response['user_id']=$user->id;
		}

		if (GO_Base_Util_Http::isAjaxRequest())
			return $response;
		else
			$this->redirect();
	}


}