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
class GO {

	private static $_classes = array(
	);
	private static $_config;
	private static $_session;

	public static $db;
	private static $_modelCache;

	/**
	 * Get's the global database connection object.
	 * 
	 * @return PDO Database connection object
	 */
	public static function getDbConnection() {
		if (!isset(self::$db)) {

			$dbname = GO::config()->db_name;
			$dbuser = GO::config()->db_user;
			$dbpass = GO::config()->db_pass;

			self::$db = new PDO("mysql:host=localhost;dbname=$dbname", $dbuser, $dbpass);
			self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}

		return self::$db;
	}

	/**
	 *
	 * @return string Returns the currently selected theme. 
	 * 
	 * TODO SHould be changed to theme().
	 */
	public static function view() {
		return 'Default';
	}

	/**
	 * Get the logged in user
	 * 
	 * @return GO_Base_Model_User The logged in user model
	 */
	public static function user() {
		if(empty(GO::session()->user_id))
			return false;
		
		return GO_Base_Model_User::model()->findByPk(GO::session()->user_id);
	}

	/**
	 * Returns a collection of Group-Office Module objects
	 * 
	 * @return GO_Base_Model_ModelCollection
	 * 
	 */
	public static function modules() {
		if (!isset(self::$_modules)) {
			self::$_modules = new GO_Base_ModuleCollection();
		}
		return self::$_modules;
	}

	/**
	 *
	 * @return GO_Base_Model_ModelCache 
	 */
	public static function modelCache() {
		if (!isset(self::$_modelCache)) {
			self::$_modelCache = new GO_Base_Model_ModelCache();
		}
		return self::$_modelCache;
	}

	/**
	 *
	 * @return GO_Base_Config
	 */
	public static function config() {
		if (!isset(self::$_config)) {
			self::$_config = new GO_Base_Config();
		}
		return self::$_config;
	}
	
	/**
	 *
	 * @return GO_Base_Session
	 */
	public static function session() {
		if (!isset(self::$_session)) {
			self::$_session = new GO_Base_Session();
		}
		return self::$_session;
	}

	/**
	 * The automatic class loader for Group-Office.
	 * 
	 * @param string $className 
	 */
	public static function autoload($className) {

		$orgClassName = $className;
		
		if (isset(self::$_classes[$className])) {
			require_once(dirname(dirname(__FILE__)) . '/' . self::$_classes[$className]);
		}

		if (substr($className, 0, 7) == 'GO_Base') {
			$arr = explode('_', $className);
			$file = array_pop($arr) . '.php';

			$path = strtolower(implode('/', $arr));
			$baseClassFile = dirname(dirname(__FILE__)) .'/'. $path . '/' . $file;
			require($baseClassFile);
		} else {

			$className = str_replace('GO_', '', $className);

			if (isset(self::$_classes[$className])) {
				require_once(dirname(dirname(__FILE__)) . '/' . self::$_classes[$className]);
			} else {
				$arr = explode('_', $className);

				if (count($arr) == 3)
					$module = strtolower(array_shift($arr));
				else
					$module=false;

				$type = strtolower(array_shift($arr));
				$file = ucfirst(array_shift($arr));

				if ($type == 'controller')
					$file .= 'Controller';

				if ($module) {
					$file = self::modules()->modules[$module]['path'] . $type . '/' . $file . '.php';
				} else {
					$file = self::config()->root_path . $type . '/' . $file . '.php';
				}



				if (!file_exists($file))
					throw new Exception('Class ' . $orgClassName . ' not found! (' . $file . ')');

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
		spl_autoload_register(array('GO', 'autoload'));

		self::config()->set_default_session();

		if (!is_int(GO::session()->timezone)) {
			//set user timezone setting after user class is loaded
			date_default_timezone_set(GO::session()->timezone);
		}

		GO::debug('[' . date('Y-m-d G:i') . '] Start of new request: ' . $_SERVER['PHP_SELF']);


		if (self::config()->session_inactivity_timeout > 0) {
			$now = time();
			if (isset($_SESSION['last_activity']) && $_SESSION['last_activity'] + GO::config()->session_inactivity_timeout < $now) {
				GO::security()->logout();
			} elseif ($_POST['task'] != 'checker') {//don't update on the automatic checker function that runs every 2 mins.
				$_SESSION['last_activity'] = $now;
			}
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

		if (GO::config()->log) {
			$username = self::user() ? self::user()->username : 'notloggedin';
			openlog('[Group-Office][' . date('Ymd G:i') . '][' . $username . ']', LOG_PERROR, LOG_USER);
		}

		if (!empty(self::session()->user_id)) {
			self::config()->tmpdir = self::config()->tmpdir . self::session()->user_id . '/';
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

	/**
	 * Add a log entry to syslog if enabled in config.php
	 *
	 * @param	int $level The log level. See sys_log() of the PHP docs
	 * @param	string $message The log message
	 * @access public
	 * @return void
	 */
	function log($level, $message) {
		if (self::config()->log) {
			$messages = str_split($message, 500);
			for ($i = 0; $i < count($messages); $i++) {
				syslog($level, $messages[$i]);
			}
		}
	}

	function infolog($message) {

		if (self::config()->log) {

			if (empty(GO::session()->logdircheck)) {
				File::mkdir(dirname(self::config()->info_log));
				GO::session()->logdircheck = true;
			}

			$msg = '[' . date('Y-m-d G:i:s') . ']';

			if (GO::user()) {
				$msg .= '[' . self::user()->username . '] ';
			}

			$msg.= $message;

			@file_put_contents(self::config()->info_log, $msg . "\n", FILE_APPEND);
		}
	}

	/**
	 * Write's to a debug log.
	 *
	 * @param string $text log entry
	 */
	public static function debug($text, $config=false) {

		if (self::config()->debug || self::config()->debug_log) {
			if (!is_string($text)) {
				$text = var_export($text, true);
			}

			if ($text == '')
				$text = '(empty string)';

			file_put_contents(self::config()->file_storage_path . 'debug.log', $text . "\n", FILE_APPEND);
		}
	}

}