<?php


namespace GO\Site\Controller;


class AccountController extends \GO\Site\Components\Controller {
	
	protected function allowGuests() {
		return array('register','login','lostpassword','recoverpassword','resetpassword','captcha');
	}
	
	public function actionCaptcha() {
		require_once dirname(__FILE__) . '/../widget/secureimage/assets/securimage.php';

		$img = new \Securimage();

		// You can customize the image by making changes below, some examples are included - remove the "//" to uncomment

		//$img->ttf_file        = dirname(__FILE__) . '/../widget/secureimage/assets/AHGBold.ttf';
		//$img->captcha_type    = \Securimage::SI_CAPTCHA_WORDS; // show a simple math problem instead of text
		//$img->case_sensitive  = true;                              // true to use case sensitve codes - not recommended
		//$img->image_height    = 90;                                // height in pixels of the image
		//$img->image_width     = $img->image_height * M_E *1.5;          // a good formula for image size based on the height
		//$img->perturbation    = .75;                               // 1.0 = high distortion, higher numbers = more distortion
		$img->image_bg_color  = new \Securimage_Color("#f1f3f4");   // image background color
		//$img->text_color      = new \Securimage_Color("#000");   // captcha text color
		//$img->num_lines       = 8;                                 // how many lines to draw over the image
		//$img->line_color      = new Securimage_Color("#0000CC");   // color of lines over the image
		//$img->image_type      = SI_IMAGE_JPEG;                     // render as a jpeg image
		//$img->signature_color = new Securimage_Color(rand(0, 64),
		//                                             rand(64, 128),
		//                                             rand(128, 255));  // random signature color
		if (!empty($_GET['namespace']))
			$img->setNamespace($_GET['namespace']);

		$img->show();
	}
	
	/**
	 * Register a new user this controller can save User, Contact and Company
	 * Only if attributes are provided by the POST request shall the model be saved
	 */
	public function actionRegister() {
		
		\GO::config()->password_validate=false;
		
		$user = new \GO\Base\Model\User();		
		$contact = new \GO\Addressbook\Model\Contact();
				
//		$user->setValidationRule('passwordConfirm', 'required', true);
		//$company = new \GO\Addressbook\Model\Company();		
		
		//set additional required fields
//		$company->setValidationRule('address', 'required', true);
//		$company->setValidationRule('zip', 'required', true);
//		$company->setValidationRule('city', 'required', true);
//		$company->setValidationRule('country', 'required', true);
		
		if(\GO\Base\Util\Http::isPostRequest())
		{
			//if username is deleted from form then use the e-mail adres as username
			if(!isset($_POST['User']['username']))
				$_POST['User']['username']=$_POST['User']['email'];
			
			
			$user->setAttributes($_POST['User']);
			
			$contact->setAttributes($_POST['Contact']);
			
			//$company->setAttributes($_POST['Company']);
		
//			if(!empty($_POST['Company']['postAddressIsEqual']))
//				$company->setPostAddressFromVisitAddress();
			
			$contact->addressbook_id=1;//just for validating
			
			if($user->validate() && $contact->validate())
			{				
				
				\GO::setIgnoreAclPermissions(); //allow guest to create user

				if($user->save())
				{
					$contact = $user->createContact();
//					$company->addressbook_id=$contact->addressbook_id;
//					$company->save();
					
					//$contact->company_id=$company->id;
					$contact->setAttributes($_POST['Contact']);					
					$contact->save();

					// Automatically log the newly created user in.
					if(\GO::session()->login($user->username, $_POST['User']['password']))
						$this->redirect($this->getReturnUrl());
					else
						throw new \Exception('Login after registreation failed.');
				}
			}
			else {
//				var_dump($user->getValidationErrors());
//				var_dump($contact->getValidationErrors());
//				var_dump($company->getValidationErrors());
			}
		}
		else {
			$user->password="";
			$user->passwordConfirm="";
		}
		
		
		echo $this->render('register', array('user'=>$user,'contact'=>$contact));
	}
	
	/**
	 * Action that needs to be called for the page to let the user recover 
	 * the password.
	 */
	public function actionRecoverPassword() {
		
		if (\GO\Base\Util\Http::isPostRequest())
		{
			$user = \GO\Base\Model\User::model()->findSingleByAttribute('email', $_POST['email']);
			
			if($user == null){
				\Site::notifier()->setMessage('error', \GO::t("invaliduser","site"));
			}else{
				$siteTitle = \Site::model()->name;
				$url = \Site::request()->getHostInfo(). \Site::urlManager()->createUrl('/site/account/resetpassword', array(), false);

				$fromName = \Site::model()->name;
				$fromEmail = 'noreply@intermesh.nl';

				$user->sendResetPasswordMail($siteTitle,$url,$fromName,$fromEmail);
				\Site::notifier()->setMessage('success', \GO::t('recoverEmailSent', 'site')." ".$user->email);
			}
		}
		
		echo $this->render('recoverPassword');
	}
	
	public function actionResetPassword()
	{
		if(empty($_GET['email']))
			throw new \Exception(\GO::t("noemail","site"));

		$user = \GO\Base\Model\User::model()->findSingleByAttribute('email', $_GET['email']);

		if(!$user)
			throw new \Exception(\GO::t("invaliduser","site"));

		if(isset($_GET['usertoken']) && $_GET['usertoken'] == $user->getSecurityToken())
		{
			if (\GO\Base\Util\Http::isPostRequest())
			{
				$user->password = $_POST['User']['password'];
				$user->passwordConfirm = $_POST['User']['passwordConfirm'];

				\GO::$ignoreAclPermissions = true; 
				
				if($user->validate() && $user->save())
					\Site::notifier()->setMessage('success',\GO::t('resetPasswordSuccess', 'site'));
			}
		}
		else
			\Site::notifier()->setMessage('error',\GO::t("invalidusertoken","site"));
				
		$user->password = null;
		echo $this->render('resetPassword', array('user'=>$user));
	}
	
	/**
	 * Render a login page 
	 */
	public function actionLogin(){
		
		$model = new \GO\Base\Model\User();
		
		if (\GO\Base\Util\Http::isPostRequest() && isset($_POST['User'])) {

			$model->username = $_POST['User']['username'];
			
			$password = $_POST['User']['password'];

			$user = \GO::session()->login($model->username, $password);
			
			//reset language after login
			if(!empty(\Site::model()->language))
				\GO::language()->setLanguage(\Site::model()->language);
			
			if (!$user) {
				\Site::notifier()->setMessage('error', \GO::t('badLogin')); // set the correct login failure message
			} else {
				if (!empty($_POST['rememberMe'])) {

					$encUsername = \GO\Base\Util\Crypt::encrypt($model->username);
					if ($encUsername)
						$encUsername = $model->username;

					$encPassword = \GO\Base\Util\Crypt::encrypt($password);
					if ($encPassword)
						$encPassword = $password;

					\GO\Base\Util\Http::setCookie('GO_UN', $encUsername);
					\GO\Base\Util\Http::setCookie('GO_PW', $encPassword);
				}
				$this->redirect($this->getReturnUrl());
			}
		} elseif(isset($_GET['ref'])) { // url to go to after login
			\GO::session()->values['sites']['returnUrl'] = $_GET['ref'];
		}

		echo $this->render('login',array('model'=>$model));
	}
	
	/**
	 * Logout the current user and redirect to loginpage 
	 */
	public function actionLogout(){
		\GO::session()->logout();
		\GO::session()->start();
		$this->redirect(\Site::urlManager()->getHomeUrl());
	}
	
	protected function actionProfile(){
		
		$user = \GO::user();
		
		$contact = $user->contact;
		
		//set additional required fields
		$contact->setValidationRule('address', 'required', true);
		$contact->setValidationRule('zip', 'required', true);
		$contact->setValidationRule('city', 'required', true);
		
//		$user->setValidationRule('passwordConfirm', 'required', false);
		$user->setValidationRule('password', 'required', false);
		
		\GO::config()->password_validate=false;
		
		if($contact->company)
			$company = $contact->company;
		else{
			$company = new \GO\Addressbook\Model\Company();
			$company->addressbook_id=$contact->addressbook_id;
		}
		
		if (\GO\Base\Util\Http::isPostRequest()) {
			
			if(!empty($_POST['currentPassword']) && !empty($_POST['User']['password']))
			{
				if(!$user->checkPassword($_POST['currentPassword'])){
					GOS::site()->notifier->setMessage('error', "Huidig wachtwoord onjuist");
					unset($_POST['User']['password']);
					unset($_POST['User']['passwordConfirm']);
				}
			}else{
				unset($_POST['User']['password']);
				unset($_POST['User']['passwordConfirm']);
			}
			
			$user->setAttributes($_POST['User']);				
			$contact->setAttributes($_POST['Contact']);
			$company->setAttributes($_POST['Company']);
			$company->checkVatNumber=true;
			
			if(!empty($_POST['Company']['postAddressIsEqual']))
				$company->setPostAddressFromVisitAddress();
			
			if(!GOS::site()->notifier->hasMessage('error') && $user->validate() && $contact->validate() && $company->validate())
			{	
				\GO::setIgnoreAclPermissions(); //allow guest to create user
				
				$user->save();
				$company->save();
				$contact->company_id = $company->id;				
				$contact->save();
				
				GOS::site()->notifier->setMessage('success', GOS::t('formEditSuccess'));				
			}else
			{
				GOS::site()->notifier->setMessage('error', "Please check the form for errors");
			}
		}

		$company->post_address_is_address = false;
	
		if($company->address==$company->post_address && 
			 $company->address_no==$company->post_address_no &&
			 $company->city==$company->post_city
			){
			 $company->post_address_is_address = true;
		}				
		
		//clear values for form	
		$user->password="";
		$user->passwordConfirm="";
		
		echo $this->render('profile', array('user'=>$user,'contact'=>$contact, 'company'=>$company));
	}
}