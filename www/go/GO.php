<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * The main Group-Office application class. This class only contains static 
 * classes to access commonly used application data like the configuration or the logged in user.
 */
class GO{

	private static $_classes = array(
			'File' => 'classes/File.class.inc.php',
			'String' => 'classes/String.class.inc.php',
			'Number' => 'classes/Number.class.inc.php',
			'Date' => 'classes/Date.class.inc.php',
			'UUID' => 'classes/UUID.class.inc.php',
			'AccessDeniedException' => 'classes/base/exceptions.class.inc.php',
			'DatabaseInsertException' => 'classes/base/exceptions.class.inc.php',
			'FileNotFoundException' => 'classes/base/exceptions.class.inc.php',
			'MissingFieldException' => 'classes/base/exceptions.class.inc.php',
			'DatabaseReplaceException' => 'classes/base/exceptions.class.inc.php',
			'DatabaseSelectException' => 'classes/base/exceptions.class.inc.php',
			'DatabaseDeleteException' => 'classes/base/exceptions.class.inc.php',
			'CONFIG' => 'classes/base/config.class.inc.php',
			'SECURITY' => 'classes/base/security.class.inc.php',
			'MODULES' => 'classes/base/modules.class.inc.php',
			'LANGUAGE' => 'classes/base/language.class.inc.php',
			'EVENTS' => 'classes/base/events.class.inc.php',
	);
	private static $_config;
	private static $_security;
	private static $_language;
	private static $_modules;
	private static $_events;
	private static $_theme;

	public static $db;
	
	/**
	 * Get's the global database connection object.
	 * 
	 * @return PDO Database connection object
	 */
	public static function getDbConnection(){
		if(!isset(self::$db)){
			
			$dbname = GO::config()->db_name;
			$dbuser = GO::config()->db_user;
			$dbpass = GO::config()->db_pass;
			
			self::$db = new PDO("mysql:host=localhost;dbname=$dbname", $dbuser, $dbpass);
			self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		
		return self::$db;
	}
	
	/**
	 *
	 * @return string Returns the currently selected theme. 
	 * 
	 * TODO SHould be changed to theme().
	 */
	public static function view(){
		return 'Default';
	}
	
	/**
	 * Get the logged in user
	 * 
	 * @return GO_Base_Model_User The logged in user model
	 */
	public static function user(){
		return new GO_Base_Model_User($_SESSION['GO_SESSION']['user_id']);
	}

	/**
	 * Returns a collection of Group-Office Module objects
	 * 
	 * @return GO_Base_Model_ModelCollection
	 * 
	 */
	public static function modules() {
		if (!isset(self::$_modules)) {			
			self::$_modules=new GO_Base_ModuleCollection();
		}
		return self::$_modules;
	}

	public static function config() {
		if (!isset(self::$_config)) {
			self::$_config = new GO_CONFIG();
		}
		return self::$_config;
	}

	public static function language() {
		global $lang;
		if (!isset(self::$_language)) {
			self::$_language = new GO_LANGUAGE();
		}
		return self::$_language;
	}

	public static function events() {
		if (!isset(self::$_events)) {
			self::$_events = new GO_EVENTS();
		}
		return self::$_events;
	}
	
	public static function theme() {
		if (!isset(self::$_theme)) {
			self::$_theme = new GO_THEME();
		}
		return self::$_theme;
	}
	
	public static function security() {
		if (!isset(self::$_security)) {
			self::$_security = new GO_SECURITY();
		}
		return self::$_security;
	}

	public static function import($className, $path) {
		self::$_classes[$className] = $path;
	}

	/**
	 * The automatic class loader for Group-Office.
	 * 
	 * @param string $className 
	 */
	public static function autoload($className) {

		$orgClassName = $className;
		
		if(substr($className,0,7)=='GO_Base'){		
			$arr = explode('_', $className);		
			$file = array_pop($arr).'.php';		
			
			$path = strtolower(implode('/', $arr));
			$baseClassFile = GO::config()->root_path.$path.'/'.$file;
			require($baseClassFile);
		}  else {
			
			$className = str_replace('GO_','', $className);

			if(isset(self::$_classes[$className])){
				require_once(dirname(dirname(__FILE__)) . '/'.self::$_classes[$className]);
			}else
			{
				$arr = explode('_', $className);

				if(count($arr)==3)
					$module = strtolower(array_shift($arr));
				else
					$module=false;
				
				$type = strtolower(array_shift($arr));
				$file = ucfirst(array_shift($arr));

				if($module){
					$file = self::modules()->modules[$module]['path'].$type.'/'.$file.'.php';
				}else
				{
					$file = self::config()->root_path.$type.'/'.$file.'.php';
				}
				
				if(!file_exists($file))
					throw new Exception('Class '.$orgClassName.' not found! ('.$file.')');
				
				require($file);
			}
		}
	}

	/**
	 * This function inititalizes Group-Office.
	 * 
	 * @global type $lang 
	 */
	public static function init() {
		global $lang;

		spl_autoload_register(array('GO', 'autoload'));
		require(dirname(dirname(__FILE__)) . '/functions.inc.php');

		


		if (!defined('GO_NO_SESSION')) {
			//start session
			session_name('groupoffice');
			if (isset($_REQUEST['session_id']) && isset($_REQUEST['auth_token'])) {
				session_id($_REQUEST['session_id']);
			}
			session_start();
			if (isset($_REQUEST['auth_token'])) {
				if ($_REQUEST['auth_token'] != $_SESSION['GO_SESSION']['auth_token']) {
					session_destroy();
					die('Invalid auth_token supplied');
				} else {
					$_SESSION['GO_SESSION']['auth_token'] = String::random_password('a-z,1-9', '', 30);
					//redirect to URL without session_id
					header('Location: ' . $_SERVER['PHP_SELF']);
					exit();
				}
			}
		}
		
		

		self::config()->set_default_session();

		if (!is_int($_SESSION['GO_SESSION']['timezone'])) {
			//set user timezone setting after user class is loaded
			date_default_timezone_set($_SESSION['GO_SESSION']['timezone']);
		}

		go_debug('[' . date('Y-m-d G:i') . '] Start of new request: ' . $_SERVER['PHP_SELF']);


		if (self::config()->session_inactivity_timeout > 0) {
			$now = time();
			if (isset($_SESSION['last_activity']) && $_SESSION['last_activity'] + GO::config()->session_inactivity_timeout < $now) {
				GO::security()->logout();
			} elseif ($_POST['task'] != 'checker') {//don't update on the automatic checker function that runs every 2 mins.
				$_SESSION['last_activity'] = $now;
			}
		}


		if (!empty($_REQUEST['SET_LANGUAGE'])) {
			self::language()->set_language($_REQUEST['SET_LANGUAGE']);
		}


		define('GO_LOADED', true);

//undo magic quotes if magic_quotes_gpc is enabled. It should be disabled!
		if (get_magic_quotes_gpc()) {

			function stripslashes_array($data) {
				if (is_array($data)) {
					foreach ($data as $key => $value) {
						$data[$key] = stripslashes_array($value);
					}
					return $data;
				} else {
					return stripslashes($data);
				}
			}

			$_REQUEST = stripslashes_array($_REQUEST);
			$_GET = stripslashes_array($_GET);
			$_POST = stripslashes_array($_POST);
			$_COOKIE = stripslashes_array($_COOKIE);
		}

		umask(0);

		/*
		 * License checking for pro modules. Don't remove it or Group-Office will fail
		 * to load!
		 */
		if (PHP_SAPI != 'cli' && file_exists(GO::config()->root_path . 'modules/professional/check.php')) {
			require_once(GO::config()->root_path . 'modules/professional/check.php');
			check_license();
		}


		require(GO::language()->get_base_language_file('common'));

		if (GO::config()->log) {
			$username = isset($_SESSION['GO_SESSION']['username']) ? $_SESSION['GO_SESSION']['username'] : 'notloggedin';
			openlog('[Group-Office][' . date('Ymd G:i') . '][' . $username . ']', LOG_PERROR, LOG_USER);
		}

		if (self::security()->user_id > 0) {
			self::config()->tmpdir = self::config()->tmpdir . self::security()->user_id . '/';
		}

		if (function_exists('mb_internal_encoding'))
			mb_internal_encoding("UTF-8");


		if (!GO::config()->enabled) {
			die('<h1>Disabled</h1>This Group-Office installation has been disabled');
		}

		if (GO::config()->debug) {
			$_SESSION['connect_count'] = 0;
			$_SESSION['query_count'] = 0;
		}
		
		
	}
}