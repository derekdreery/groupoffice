<?php
class GO_Base_Session{
	
	public function __construct(){
		//start session
		session_name('groupoffice');
		session_start();
	}
	
	public function __get($name){
		return $_SESSION['GO_SESSION'][$name];
	}
	
	public function __set($name, $value){
		$_SESSION['GO_SESSION'][$name]=$value;
	}
	
	public function __isset($name){
		return isset($_SESSION['GO_SESSION'][$name]);
	}
	
	/**
	 * Log the current user out.
	 *
	 * @access public
	 * @return void
	 */
	public function logout() {
		
		$username = isset(GO::session()->username) ? GO::session()->username : 'notloggedin';
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