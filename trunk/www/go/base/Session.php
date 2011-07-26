<?php
class GO_Base_Session{
	
	public $values;
	
	public function __construct(){
		//start session
		session_name('groupoffice');
		session_start();
		
		$this->values = &$_SESSION;
		
		//$this->setDefaults();
		
		
		
//		if ($GLOBALS['GO_CONFIG']->session_inactivity_timeout > 0) {
//			$now = time();
//			if (isset($_SESSION['last_activity']) && $_SESSION['last_activity'] + $GLOBALS['GO_CONFIG']->session_inactivity_timeout < $now) {
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
			self::$values['decimal_separator'] = $GLOBALS['GO_CONFIG']->default_decimal_separator;
			self::$values['thousands_separator'] = $GLOBALS['GO_CONFIG']->default_thousands_separator;
			self::$values['date_separator'] = $GLOBALS['GO_CONFIG']->default_date_separator;
			self::$values['date_format'] = Date::get_dateformat( $GLOBALS['GO_CONFIG']->default_date_format, self::$values['date_separator']);
			self::$values['time_format'] = $GLOBALS['GO_CONFIG']->default_time_format;
			self::$values['currency'] = $GLOBALS['GO_CONFIG']->default_currency;
			self::$values['timezone'] = $GLOBALS['GO_CONFIG']->default_timezone;
			self::$values['country'] = $GLOBALS['GO_CONFIG']->default_country;
			self::$values['sort_name'] = 'last_name';
			self::$values['auth_token']=String::random_password('a-z,1-9', '', 30);
			//some url's require this token to be appended
			self::$values['security_token']=String::random_password('a-z,1-9', '', 10);
			
			if (!is_int($this->values['timezone'])) {
				//set user timezone setting after user class is loaded
				date_default_timezone_set(self::$values['timezone']);
			}

			go_debug('Setup new session '.self::$values['security_token']);
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

		if(!empty($this->user_id)){
			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			$fs = new filesystem();

			$length = -strlen($this->user_id)-1;

			//GO::debug(substr($GO_CONFIG->tmpdir,$length));

			if(substr($GO_CONFIG->tmpdir,$length)==$this->user_id.'/' && is_dir($GO_CONFIG->tmpdir)){
				$fs->delete($GO_CONFIG->tmpdir);
			}
		}


		SetCookie("GO_UN","",time()-3600,"/","",!empty($_SERVER['HTTPS']),true);
		SetCookie("GO_PW","",time()-3600,"/","",!empty($_SERVER['HTTPS']),true);

		$old_session = $_SESSION;

		unset($_SESSION, $_COOKIE['GO_UN'], $_COOKIE['GO_PW']);

		@session_destroy();
		$this->user_id = 0;

		global $GO_MODULES;
		if(isset($GO_MODULES)) {
			$GO_MODULES->load_modules();
		}

		global $GO_EVENTS;
		$GO_EVENTS->fire_event('logout', $old_session);
	}
	
	
}