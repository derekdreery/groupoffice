<?php

/**
 * 
 * TODO
 * 
 * The whole init process of Group-Office has to be remodelled.
 * The default_scripts.inc.php file is ugly and bad design. Instead all init
 * views in modules should register client scripts and css files.
 */

namespace GO\Core\Controller;
use GO;

class AuthController extends \GO\Base\Controller\AbstractController {

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
		
		\GO\Base\Observable::cacheListeners();

		//when GO initializes modules need to perform their first run actions.
		unset(\GO::session()->values['firstRunDone']);

		if (\GO::user())
			$this->fireEvent('loadapplication', array(&$this));
	}

	protected function actionInit($params) {
		
		if(!empty($params['SET_LANGUAGE']))
			\GO::config()->language=$params['SET_LANGUAGE'];

		$this->loadInit();
//		$this->render('index');
		
//		$view = \GO::view();
		
		$this->view->layout='html';
		
		if(!$this->view->findViewFile('Login')){
			//for backwards theme compat
			require(\GO::view()->getTheme()->getPath().'Layout.php');
		}  else {
			if(\GO::user()){
				$this->render('Init');
			}else
			{
				$this->render('Login');
			}
		}		
	}

	protected function actionSetView($params) {
		\GO::setView($params['view']);

		$this->redirect();
	}
	
	protected function actionResetPassword($params){
		$this->render('resetpassword');
	}
	
	protected function actionSetNewPassword($params){
		
		$response = array();
	
		if(!\GO\Base\Util\Http::isPostRequest() || empty($params['email']) || empty($params['usertoken'])){
			$response['success']=false;
			$response['feedback']="Invalid request!";
			return $response;
		}

		$user = \GO\Base\Model\User::model()->findSingleByAttribute('email', $params['email']);
		if($user){
			if($params['usertoken'] == $user->getSecurityToken()){
				
				$user->password = $_REQUEST['password'];
				$user->passwordConfirm = $_REQUEST['confirm'];

				if($user->save()){				
					$response['success']=true;
				}else{
					$response['success']=false;
					$response['feedback']=nl2br(implode("<br />", $user->getValidationErrors())."\n");			
			
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
		$user = \GO\Base\Model\User::model()->findSingleByAttribute('email', $params['email']);

		if(!$user){
			$response['success']=false;
			$response['feedback']=\GO::t('lost_password_error','base','lostpassword');
		}else{
			
			$user->sendResetPasswordMail();
			
			$response['success']=true;
			$response['feedback']=\GO::t('lost_password_success','base','lostpassword');
		}
		
		return $response;
	}

	protected function actionLogout() {

		\GO::session()->logout();

		if (\GO::request()->isAjax()) {
			$response['success']=true;
			return $response;
		}

		if (isset($_COOKIE['GO_FULLSCREEN']) && $_COOKIE['GO_FULLSCREEN'] == '1') {
			?>
			<script type="text/javascript">
				window.close();
			</script>
			<?php

			exit();
		} else {
			
			if(!empty(\GO::config()->logout_url)){
				header('Location: ' .\GO::config()->logout_url);
				exit();
			}else
			{
				$this->redirect();
			}
		}
	}

	protected function actionLogin($params) {
		
		if(!empty($params["login_language"])){
			GO::language()->setLanguage($params["login_language"]);
		}
		
		if(!empty($params['domain']))
			$params['username'].=$params['domain'];	
		
		$response = array();
		
		if(!$this->fireEvent('beforelogin', array(&$params, &$response))){
			$response['success'] = false;
			
			if(!isset($response['feedback']))
				$response['feedback']=GO::t('badLogin');

			return $response;		
		}
		
		$user = \GO::session()->login($params['username'], $params['password']);

		$response['success'] = $user != false;		

		if (!$response['success']) {		
			$response['feedback']=\GO::t('badLogin');			
		} else {			
			if (!empty($params['remind'])) {

				$encUsername = \GO\Base\Util\Crypt::encrypt($params['username']);
				if (!$encUsername)
					$encUsername = $params['username'];

				$encPassword = \GO\Base\Util\Crypt::encrypt($params['password']);
				if (!$encPassword)
					$encPassword = $params['password'];

				\GO\Base\Util\Http::setCookie('GO_UN', $encUsername);
				\GO\Base\Util\Http::setCookie('GO_PW', $encPassword);
			}
			
			$response['groupoffice_version']=\GO::config()->version;
			$response['user_id']=$user->id;
			$response['security_token']=\GO::session()->values["security_token"];
			$response['sid']=session_id();
			
			if(!empty($params['return_user_info'])){
				$response['modules']=array();
				
				foreach(\GO::modules()->getAllModules() as $module){
					$response['modules'][]=$module->id;
				}
				
				$response['user']=\GO::user()->getAttributes();
			}
			
			
			if(!empty($params["login_language"]))
			{
				GO::language()->setLanguage($params["login_language"]); 

				
				\GO::user()->language=\GO::language()->getLanguage();
				\GO::user()->save();
			}
			
		}
		
//		return $response;

		if (\GO\Base\Util\Http::isAjaxRequest())
		{
			return $response;
		}else{
			$this->redirect();
		
		}
	}


}