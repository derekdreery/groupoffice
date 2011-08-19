<?php
class GO_Base_Session extends GO_Base_Observable{
	
	public $values;
	
	public function __construct(){
		//start session
		
		if(session_id()==''){
			session_name('groupoffice');
			session_start();
		}
		
		GO::debug("Started session");
		
		$this->values = &$_SESSION['GO_SESSION'];
		
		//$this->setDefaults();
		
		
		
//		if (GO::config()->session_inactivity_timeout > 0) {
//			$now = time();
//			if (isset($_SESSION['last_activity']) && $_SESSION['last_activity'] + GO::config()->session_inactivity_timeout < $now) {
//				$GLOBALS['GO_SECURITY']->logout();
//			} elseif ($_POST['task'] != 'checker') {//don't update on the automatic checker function that runs every 2 mins.
//				$_SESSION['last_activity'] = $now;
//			}
//		}

	}
	
	
	/**
	 * This function sets some default session variables. When a user logs in
	 * they are overridden by the user settings.
	 */
	public function setDefaults(){

		if(!isset(self::$values['security_token']))
		{
			self::$values['decimal_separator'] = GO::config()->default_decimal_separator;
			self::$values['thousands_separator'] = GO::config()->default_thousands_separator;
			self::$values['date_separator'] = GO::config()->default_date_separator;
			self::$values['date_format'] = GO_Base_Util_Date::get_dateformat( GO::config()->default_date_format, self::$values['date_separator']);
			self::$values['time_format'] = GO::config()->default_time_format;
			self::$values['currency'] = GO::config()->default_currency;
			self::$values['timezone'] = GO::config()->default_timezone;
			self::$values['country'] = GO::config()->default_country;
			self::$values['sort_name'] = 'last_name';
			self::$values['auth_token']=GO_Base_Util_String::random_password('a-z,1-9', '', 30);
			//some url's require this token to be appended
			self::$values['security_token']=GO_Base_Util_String::random_password('a-z,1-9', '', 10);
			
			if (!is_int($this->values['timezone'])) {
				//set user timezone setting after user class is loaded
				date_default_timezone_set(self::$values['timezone']);
			}

			GO::debug('Setup new session '.self::$values['security_token']);
		}

		
	}
	
	
// Doesn't work well because you can't change magic properties directly.
// 
//		public function __get($name){
//		return self::$values[$name];
//	}
//	
//	public function __set($name, $value){
//		self::$values[$name]=$value;
//	}
//	
//	public function __isset($name){
//		return isset(self::$values[$name]);
//	}
	
	/**
	 * Log the current user out.
	 *
	 * @access public
	 * @return void
	 */
	public function logout() {
		
		$username = isset(self::$username) ? self::$username : 'notloggedin';
		//go_log(LOG_DEBUG, 'LOGOUT Username: '.$username.'; IP: '.$_SERVER['REMOTE_ADDR']);
		GO::infolog("LOGOUT for user: \"".$username."\" from IP: ".$_SERVER['REMOTE_ADDR']);

		if(GO::user()){
	
			$length = -strlen(GO::user()->id)-1;

			//GO::debug(substr($GO_CONFIG->tmpdir,$length));

			if(substr(GO::config()->tmpdir,$length)==GO::user()->id.'/' && is_dir(GO::config()->tmpdir)){
				$folder = new GO_Base_Fs_Folder(GO::config()->tmpdir);
				$folder->delete();
			}
		}


		SetCookie("GO_UN","",time()-3600,"/","",!empty($_SERVER['HTTPS']),true);
		SetCookie("GO_PW","",time()-3600,"/","",!empty($_SERVER['HTTPS']),true);

		$old_session = $_SESSION;

		unset($_SESSION, $_COOKIE['GO_UN'], $_COOKIE['GO_PW']);

		@session_destroy();

		$this->fireEvent('logout', array($old_session));
	}
	
	/**
	 * Logs a user in.
	 * 
	 * @param string $username
	 * @param string $password
	 * @return GO_Base_Model_User or false on failure.
	 */
	public function login($username, $password) {
		
		$this->fireEvent('beforelogin', array($username, $password));
		
		$user = GO_Base_Model_User::model()->findSingleByAttribute('username', $username);

		if (!$user)
			return false;
		
		if(!$user->enabled)
			return false;

		if ($user->password_type == 'crypt') {
			if (crypt($password, $user->password) != $user->password) {
				return false;
			}
		} else {
			//pwhash is not set yet. We're going to use the old md5 hashed password
			if (md5($password) != $user->password) {
				return false;
			} else {				
				$user->password=$password;
				$user->save();
			}
		}
		
		//remember user id in session
		$this->values['user_id']=$user->id;
		
		$this->fireEvent('login', array($username, $password, $user));
		
		return $user;
	}

}