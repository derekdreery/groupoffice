<?php
/**
 * Group-Office
 *
 * Copyright Intermesh BV.
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base
 */

/**
 * The main Group-Office application class. This class only contains static
 * classes to access commonly used application data like the configuration or the logged in user.
 *
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO

 */
class GO{

	/**
	 * If you set this to true then all acl's will allow all actions. Useful
	 * for maintenance scripts.
	 *
	 * @var boolean
	 */
	public static $ignoreAclPermissions=false;
	
	
	/**
	 * Use registerErrorLogCallback to register a custom function to log errors
	 * @var array 
	 */
	private static $_errorLogCallbacks=array();
	
	
	private static $_lastReportedError=false;


	private static $_view;
	/**
	 * If you set this to true then all acl's will allow all actions. Useful
	 * for maintenance scripts.
	 *
	 * It returns the old value.
	 *
	 * @param string $ignore
	 * @return boolean Old value
	 */
	public static function setIgnoreAclPermissions($ignore=true){
		
		GO::debug("setIgnoreAclPermissions");
		
		$oldValue = GO::$ignoreAclPermissions;
		GO::$ignoreAclPermissions=$ignore;

		return $oldValue;
	}
	
	/**
	 * Set the max execution time only if the current max execution time is lower than the given value.
	 * 
	 * Note: this may be blocked by the suhosin PHP module
	 * 
	 * @param int $seconds
	 * @return boolean
	 */
	public static function setMaxExecutionTime($seconds){
		$max = ini_get("max_execution_time");
		if($max != 0 && ($seconds==0 || $seconds>$max)){
			return ini_set("max_execution_time", $seconds);
		}else
		{
			return true;
		}
	}
	
	/**
	 * Set the memory limit in MB if the given value is higher then the current limit.
	 * 
	 * Note: this may be blocked by the suhosin PHP module
	 * 
	 * @param int $mb
	 * @return boolean
	 */
	public static function setMemoryLimit($mb){
		$max = GO_Base_Util_Number::configSizeToMB(ini_get("memory_limit"));

		if($mb>$max){
			return ini_set("memory_limit", $mb.'M');
		}else
		{
			return true;
		}
	}

	/**
	 * Get a unique ID for Group-Office. It's mainly used for the javascript window id.
	 * @return type 
	 */
	public static function getId(){
		
		$serverName = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "unknown";
		
		//added MD5 because IE doesn't like dots I suspect
		return md5(GO::config()->id.'AT'.$serverName);
	}
	
	/**
	 * This GO_Base_Model_ModelCache.php mechanism can consume a lot of memory 
	 * when running large batch scripts. That's why it can be disabled.
	 *
	 * @var boolean
	 */
	public static $disableModelCache=false;

	/**
	 * Commonly used classes indexed for faster autoloading
	 * 
	 * @var array 
	 */
	private static $_classes = array (
		'GO_Base_Observable' => 'go/base/Observable.php',
		'GO_Base_Session' => 'go/base/Session.php',
		'GO_Base_Config' => 'go/base/Config.php',
		'GO_Base_Model' => 'go/base/Model.php',
		'GO_Base_Db_ActiveRecord' => 'go/base/db/ActiveRecord.php',
		'GO_Base_Model_User' => 'go/base/model/User.php',
		'GO_Base_Cache_Interface' => 'go/base/cache/Interface.php',
		'GO_Base_Cache_Disk' => 'go/base/cache/Disk.php',
		'GO_Base_Cache_Apc' => 'go/base/cache/Apc.php',
		'GO_Base_Db_ActiveStatement' => 'go/base/db/ActiveStatement.php',
		'GO_Base_Util_String' => 'go/base/util/String.php',
		'GO_Base_Model_ModelCache' => 'go/base/model/ModelCache.php',
		'GO_Base_Router' => 'go/base/Router.php',
		'GO_Base_Controller_AbstractController' => 'go/base/controller/AbstractController.php',
		'GO_Base_Model_Module' => 'go/base/model/Module.php',
		'GO_Base_Controller_AbstractModelController' => 'go/base/controller/AbstractModelController.php',
		'GO_Base_Model_Acl' => 'go/base/model/Acl.php',
		'GO_Base_Model_AclUsersGroups' => 'go/base/model/AclUsersGroups.php',
		'GO_Base_Data_AbstractStore' => 'go/base/data/AbstractStore.php',
		'GO_Base_Data_Store' => 'go/base/data/Store.php',
		'GO_Base_Data_ColumnModel' => 'go/base/data/ColumnModel.php',
		'GO_Base_Module' => 'go/base/Module.php',
		'GO_Base_Model_AbstractUserDefaultModel' => 'go/base/model/AbstractUserDefaultModel.php',
		'GO_Base_Db_FindParams' => 'go/base/db/FindParams.php',
		'GO_Base_Db_FindCriteria' => 'go/base/db/FindCriteria.php',
		'GO_Base_Util_Date' => 'go/base/util/Date.php',
		'GO_Base_Data_Column' => 'go/base/data/Column.php',
		'GO_Base_Language' => 'go/base/Language.php',
		'GO_Base_Model_ModelCollection' => 'go/base/model/ModelCollection.php',
		'GO_Base_ModuleCollection' => 'go/base/ModuleCollection.php',
		'GO_Base_Model_Setting' => 'go/base/model/Setting.php',
	);

	private static $_config;
	private static $_session;
	private static $_modules;
	private static $_router;

	/**
	 *
	 * @var PDO
	 */
	public static $db;

	private static $_modelCache;

	/**
	 * Gets the global database connection object.
	 *
	 * @return PDO Database connection object
	 */
	public static function getDbConnection(){
		if(!isset(self::$db)){
			self::setDbConnection();
		}
		return self::$db;
	}
	
	/**
	 * Close the database connection. Beware that all active PDO statements must be set to null too
	 * in the current scope.
	 * 
	 * Wierd things happen when using fsockopen. This test case leaves the conneciton open. When removing the fputs call it seems to work.
	 * 
	 * 			
	    GO::session()->login('admin','admin');
			
			$settings = GO_Sync_Model_Settings::model()->findForUser(GO::user());
			$account = GO_Email_Model_Account::model()->findByPk($settings->account_id);
			
			
			$handle = stream_socket_client("tcp://localhost:143");
			$login = 'A1 LOGIN "admin@intermesh.dev" "admin"'."\r\n";
			fputs($handle, $login);
			fclose($handle);
			$handle=null;			
			
			echo "Test\n";
			
			GO::unsetDbConnection();
			sleep(10);
	 */
	public static function unsetDbConnection(){
		self::$db=null;
	}

	public static function setDbConnection($dbname=false, $dbuser=false, $dbpass=false, $dbhost=false, $dbport=false, $options=array()){
				
		self::$db=null;

		if($dbname===false)
			$dbname=GO::config()->db_name;

		if($dbuser===false)
			$dbuser=GO::config()->db_user;

		if($dbpass===false)
			$dbpass=GO::config()->db_pass;

		if($dbhost===false)
			$dbhost=GO::config()->db_host;
		
		if($dbport===false)
			$dbport=GO::config()->db_port;
		

//		GO::debug("Connect: mysql:host=$dbhost;dbname=$dbname, $dbuser, ***",$options);

		self::$db = new GO_Base_Db_PDO("mysql:host=$dbhost;dbname=$dbname;port=$dbport", $dbuser, $dbpass, $options);
	}

	/**
	 * Clears the:
	 * 
	 * 1. GO::config()->cachedir folder. This folder contains mainly cached javascripts.
	 * 2. GO_Base_Model objects cached in memory for a single script run
	 * 3. The permanent cache stored in GO::cache()
	 * 
	 */
	public static function clearCache(){
		
		GO::config()->getCacheFolder(false)->delete();		
		
		GO::cache()->flush();

		GO_Base_Model::clearCache();
	}

	/**
	 *
	 * @return GO_Base_View_Extjs3 Returns the currently selected theme.
	 *
	 * 
	 */
	public static function view(){
		if(!isset(self::$_view)){
			self::$_view = new GO_Base_View_Extjs3();
		}
		return self::$_view;//isset(GO::session()->values['view']) ? GO::session()->values['view'] : GO::config()->defaultView;
	}

	public static function setView($viewName){
		GO::session()->values['view']=$viewName;
	}

	private static $_user;

	/**
	 * Get the logged in user
	 *
	 * @return GO_Base_Model_User The logged in user model
	 */
	public static function user(){
		return self::session()->user();
	}

	/**
	 * Returns the router that routes requests to controller actions.
	 *
	 * @return GO_Base_Router
	 */
	public static function router() {
		if (!isset(self::$_router)) {
			self::$_router=new GO_Base_Router();
		}
		return self::$_router;
	}

	/**
	 * Returns a collection of Group-Office Module objects
	 *
	 * @return GO_Base_ModuleCollection
	 *
	 */
	public static function modules() {
		if (!isset(self::$_modules)) {
//			if(GO::user()){
//			
//			Caching caused more problems than benefits
//			
//				if(isset(GO::session()->values['modulesObject']) && !isset($GLOBALS['GO_CONFIG'])){
//					self::$_modules=GO::session()->values['modulesObject'];
//				}else{
//					self::$_modules=GO::session()->values['modulesObject']=new GO_Base_ModuleCollection();
//				}
//			}else
//			{
//				self::$_modules=new GO_Base_ModuleCollection();
//			}
			
			self::$_modules=new GO_Base_ModuleCollection();
		}
		return self::$_modules;
	}

	/**
	 * Models are cached within one script run
	 *
	 * @return GO_Base_Model_ModelCache
	 */
	public static function modelCache() {
		if (!isset(self::$_modelCache)) {
			self::$_modelCache=new GO_Base_Model_ModelCache();
		}
		return self::$_modelCache;
	}


	private static $_cache;
	
	/**
	 * Returns cache driver. Cached items will persist between connections and are
	 * available to all users. When debug is enabled a dummy cache driver is used
	 * that caches nothing.
	 * 
	 * @return GO_Base_Cache_Interface
	 */
	public static function cache(){

		if (!isset(self::$_cache)) {
			if(GO::config()->debug || !GO::isInstalled())
				self::$_cache=new GO_Base_Cache_None();
//			Disable apc cache temporarily because it seems to cause the random logouts
//			elseif(function_exists("apc_store"))
//				self::$_cache=new GO_Base_Cache_Apc();
			else
				self::$_cache=new GO_Base_Cache_Disk();
		}
		return self::$_cache;
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
		if(isset(self::$_classes[$className])){
			//don't use GO::config()->root_path here because it might not be autoloaded yet causing an infite loop.
			require(dirname(dirname(__FILE__)) . '/'.self::$_classes[$className]);
		}else
		{
//			echo "Autoloading: ".$className."\n";

			if(substr($className,0,7)=='GO_Base'){
				$arr = explode('_', $className);
				$file = array_pop($arr).'.php';

				$path = strtolower(implode('/', $arr));
				$location =$path.'/'.$file;
				$baseClassFile = dirname(dirname(__FILE__)) . '/'.$location;
				require($baseClassFile);

			} else if(substr($className,0,4)=='GOFS'){
						
				$arr = explode('_', $className);
				
				array_shift($arr);
				
				$file = array_pop($arr).'.php';
				$path = strtolower(implode('/', $arr));
				$location =$path.'/'.$file;
				$baseClassFile = GO::config()->file_storage_path.'php/'.$location;			
				require($baseClassFile);
			} else {
				//$orgClassName = $className;
				$forGO = substr($className,0,3)=='GO_';

				if ($forGO)
				{
					$arr = explode('_', $className);

					//remove GO_
					array_shift($arr);

					$module = strtolower(array_shift($arr));

					if($module!='core'){
						//$file = self::modules()->$module->path; //doesn't play nice with objects in the session and autoloading
						$file = 'modules/'.$module.'/';
					}else
					{
						$file = "";
					}
					for($i=0,$c=count($arr);$i<$c;$i++){
						if($i==$c-1){
							$file .= ucfirst($arr[$i]);
							if(isset($arr[$c-2]) && $arr[$c-2]=='Controller')
								$file .= 'Controller';
							$file .='.php';
						}else
						{
							$file .= strtolower($arr[$i]).'/';
						}

					}
					
					$fullPath = self::config()->root_path.$file;
					
//					echo $fullPath."\n";

					if(!file_exists($fullPath) || is_dir($fullPath)){
						//throw new Exception('Class '.$orgClassName.' not found! ('.$file.')');
						return false;
					}
					
					require($fullPath);
				}elseif(strpos($className,'Sabre\VObject')===0) {
					$classFile = self::config()->root_path . 'go/vendor/VObject/lib/'.str_replace('\\','/',$className).'.php';
					require $classFile;
					return true;
				}elseif(strpos($className,'Sabre')===0) {
					require self::config()->root_path . 'go/vendor/SabreDAV/lib/'.str_replace('\\','/',$className). '.php';
					return true;					
				}else	if (0 === strpos($className, 'Swift'))
				{
					require_once self::config()->root_path.'go/vendor/swift/lib/classes/Swift.php';
					//Load the init script to set up dependency injection
					require_once self::config()->root_path.'go/vendor/swift/lib/swift_init.php';

					$path = self::config()->root_path.'go/vendor/swift/lib/classes/'.str_replace('_', '/', $className).'.php';
					require_once $path;
					return true;
				}
			}
		}
	}

	private static $initialized=false;

	/**
	 * This function inititalizes Group-Office. It starts the session,registers
	 * error logging functions, class autoloading and set's PHP defaults.
	 */
	public static function init() {

		if(self::$initialized){
			throw new Exception("Group-Office was already initialized");
		}
		self::$initialized=true;
		
		//register our custom error handler here
		error_reporting(E_ALL | E_STRICT);
		set_error_handler(array('GO','errorHandler'));
		register_shutdown_function(array('GO','shutdown'));

   	spl_autoload_register(array('GO', 'autoload'));	

		//Start session here. Important that it's called before GO::config().
		GO::session();
		
		//set local to utf-8 so functions will behave consistently
		if ( !empty(GO::config()->locale_all) )
			setlocale(LC_ALL, GO::config()->locale_all);
		
		if(!empty(GO::session()->values['debug']))
			GO::config()->debug=true;
		
		if(GO::config()->debug || GO::config()->debug_log){
			$log = '['.date('Y-m-d H:i').'] INIT';
			GO::debug($log);
		}
		
		if(GO::config()->debug)
			ini_set("display_errors","On");
		else
			ini_set("display_errors","Off");

		date_default_timezone_set(GO::user() ? GO::user()->timezone : GO::config()->default_timezone);

		if (self::config()->firephp) {
			if (self::requireExists('FirePHPCore/fb.php')) {
				require_once 'FirePHPCore/fb.php';
			}
		}

		if(!defined('GO_LOADED')){ //check if old Group-Office.php was loaded
			
			self::_undoMagicQuotes();

			//set umask to 0 so we can create new files with mask defined in GO::config()->file_create_mode
			umask(0);
			
			//We use UTF8 by default.
			if (function_exists('mb_internal_encoding'))
				mb_internal_encoding("UTF-8");
		}

		//Every logged on user get's a personal temp dir.
		if (!empty(self::session()->values['user_id'])) {
			self::config()->tmpdir = self::config()->tmpdir . self::session()->values['user_id'] . '/';
		}
	}
	
	/**
	 * undo magic quotes if magic_quotes_gpc is enabled. It should be disabled!
	 */
	private static function _undoMagicQuotes(){
		
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
			if(isset($_FILES))
				$_FILES = stripslashes_array($_FILES);
		}
	}

	/**
	 * Called when PHP exits.
	 */
	public static function shutdown(){
		
		$error = error_get_last();		
		if($error){			
			//Log only fatal errors because other errors should have been logged by the normal error handler
			if($error['type']==E_ERROR || $error['type']==E_CORE_ERROR || $error['type']==E_COMPILE_ERROR || $error['type']==E_RECOVERABLE_ERROR)
				self::errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
		}
		
		//clear temp files on the command line because we may run as root
		if(PHP_SAPI=='cli')
			GO::session()->clearUserTempFiles(false);
		
		GO::debug("--------------------\n");
	}
	
	/**
	 * Register a callback function when an error occurs. It will be called with
	 * the error message as string
	 * 
	 * @param string|array $func
	 */
	public static function registerErrorLogCallback($func){
		self::$_errorLogCallbacks[]=$func;
	}

	/**
	 * Custom error handler that logs to our own error log
	 * 
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 * @return boolean
	 */
	public static function errorHandler($errno, $errstr, $errfile, $errline) {
		
		//prevent that the shutdown function will log this error again.
		if(self::$_lastReportedError == $errno.$errfile.$errline)
			return;
		
		self::$_lastReportedError = $errno.$errfile.$errline;
		
		//log only errors that are in error_reporting
		$error_reporting = ini_get('error_reporting');
		if (!($error_reporting & $errno)) return;
		
		$type="Unknown error";

		switch ($errno) {
			case E_ERROR:
			case E_USER_ERROR:
					$type='Fatal error';
					break;

			case E_WARNING:
			case E_USER_WARNING:
					$type = 'Warning';
					break;

			case E_NOTICE:
			case E_USER_NOTICE:
					$type='Notice';
					break;
		}		
		
		$errorMsg="[".date("Ymd H:i:s")."] PHP $type: $errstr in $errfile on line $errline";
		
		$user = isset(GO::session()->values['username']) ? GO::session()->values['username'] : 'notloggedin';
		$agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown';
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
		
		$errorMsg .= "\nUser: ".$user." Agent: ".$agent." IP: ".$ip."\n";
		
		if(isset($_SERVER['QUERY_STRING']))
			$errorMsg .= "Query: ".$_SERVER['QUERY_STRING']."\n";
			
		
		$backtrace = debug_backtrace();
		array_shift($backtrace); //first item is this function which we don't have to see
		
		$errorMsg .= "Backtrace:\n";
		foreach($backtrace as $o){
			
			if(!isset($o['class']))
				$o['class']='global';
			
			if(!isset($o['function']))
				$o['function']='global';
			
			if(!isset($o['file']))
				$o['file']='unknown';
			
			if(!isset($o['line']))
				$o['line']='unknown';
			
			$errorMsg .= $o['class'].'::'.$o['function'].' in file '.$o['file'].' on line '.$o['line']."\n";			
		}
		$errorMsg .= "----------------";
		
		GO::debug($errorMsg);
		GO::logError($errorMsg);	
		
		foreach(self::$_errorLogCallbacks as $callback){
			call_user_func($callback, $errorMsg);
		}
		
		/* Execute PHP internal error handler too */
		return false;
	}
	
	/**
	 * Writes a string to the Group-Office error log
	 * 
	 * @param string $errorMsg
	 */
	public static function logError($errorMsg){		
		$logDir = GO::config()->file_storage_path . 'log';
		
		if(is_writable(GO::config()->file_storage_path)){
			if(!is_dir($logDir))
				mkdir($logDir,0755, true);

			file_put_contents($logDir. '/error.log', $errorMsg . "\n", FILE_APPEND);
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
	public static function log($level, $message) {
//		if (self::config()->log) {
//			$messages = str_split($message, 500);
//			for ($i = 0; $i < count($messages); $i++) {
//				syslog($level, $messages[$i]);
//			}
//		}
	}

	public static function infolog($message) {

		if (!empty(self::config()->info_log)) {

			if (empty(GO::session()->values["logdircheck"])) {
				$folder = new GO_Base_Fs_Folder(dirname(self::config()->info_log));
				$folder->create();
				GO::session()->values["logdircheck"] = true;
			}

			$msg = '[' . date('Y-m-d H:i:s') . ']';

			if (GO::user()) {
				$msg .= '[' . self::user()->username . '] ';
			}

			$msg.= $message;

			@file_put_contents(self::config()->info_log, $msg . "\n", FILE_APPEND);
		}
	}

	/**
	 * Check if require exists
	 *
	 * @param string $fileName
	 *
	 * @return boolean
	 */
	public static function requireExists($fileName) {
		$paths = explode(PATH_SEPARATOR, get_include_path());
		foreach ($paths as $path) {
			if (file_exists($path . DIRECTORY_SEPARATOR . $fileName)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Write's to a debug log.
	 *
	 * @param string $text log entry
	 */
	public static function debug($text, $config=false) {

		if (   self::config()->debug
			|| self::config()->debug_log
			|| self::config()->firephp
		) {
			
	
			
			if(!isset($_REQUEST['r']) || $_REQUEST['r']!='core/debug')
			{
				if (self::config()->firephp) {
					if (class_exists('FB')) {
						ob_start();
						FB::send($text);
					}
				}

//				if (self::config()->debug_log) {

					if (!is_string($text)) {
						$text = var_export($text, true);
					}

					if ($text == '')
						$text = '(empty string)';

					
					if ($text == 'undefined')
						throw new Exception();
					
					//$username=GO::user() ? GO::user()->username : 'nobody';

					//$trace = debug_backtrace();

					//$prefix = "\n[".date("Ymd G:i:s")."][".$trace[0]['file'].":".$trace[0]['line']."]\n";

					//$lines = explode("\n", $text);

					//$text = $prefix.$text;

					$user = isset(GO::session()->values['username']) ? GO::session()->values['username'] : 'notloggedin';

					$text = "[$user] ".str_replace("\n","\n[$user] ", $text);

					file_put_contents(self::config()->file_storage_path . 'log/debug.log', $text . "\n", FILE_APPEND);
//				}
			}
		}
	}

	
	public static function debugCalledFrom($limit=1){
		
		GO::debug("--");
		$trace = debug_backtrace(); 
		for($i=0;$i<$limit;$i++){
			if(isset($trace[$i+1])){
				$call = $trace[$i+1];
				
				if(!isset($call["file"]))
								$call["file"]='unknown';
				if(!isset($call["function"]))
								$call["function"]='unknown';
				
				if(!isset($call["line"]))
								$call["line"]='unknown';
				
				GO::debug("Function: ".$call["function"]." called in file ".$call["file"]." on line ".$call["line"]);
			}
		}
		GO::debug("--");
	}
	
	private static $_language;

	/**
	 * Translates a language variable name into the local language
	 *
	 * @param String $name Name of the translation variable
	 * @param String $module Name of the module to find the translation
	 * @param String $basesection Only applies if module is set to 'base'
	 * @param boolean $found Pass by reference to determine if the language variable was found in the language file.
	 */
	public static function t($name, $module='base', $basesection='common', &$found=false){

		return self::language()->getTranslation($name, $module, $basesection, $found);
	}

	/**
	 *
	 * @return GO_Base_Language
	 */
	public static function language(){
		if(!isset(self::$_language)){
			self::$_language=new GO_Base_Language();
		}
		return self::$_language;
	}


	public static function memdiff() {
		static $int = null;

		$current = memory_get_usage();

		if ($int === null) {
			$int = $current;
		} else {
			print ($current - $int) . "\n";
			$int = $current;
		}
	}


	/**
	 * Get the static model object
	 *
	 * @param String $modelName
	 * @return GO_Base_Db_ActiveRecord
	 */
	public static function getModel($modelName){
		//$modelName::model() does not work on php 5.2! That's why we use this function.
		if(!class_exists($modelName))
			throw new Exception("Model class '$modelName' not found in GO::getModel()");

		return call_user_func(array($modelName, 'model'));
	}

	/**
	 * Create a URL for an outside application. The URL will open Group-Office and
	 * launch a function.
	 * 
	 * Controller external/index will be execured.
	 *
	 * @param string $module
	 * @param function $function
	 * @param array $params
	 * @return string
	 */
	public static function createExternalUrl($module, $function, $params,$toLoginDialog=false)
	{
		//$p = 'm='.urlencode($module).'&f='.urlencode($function).'&p='.urlencode(base64_encode(json_encode($params)));

		if(GO::config()->debug){
			if(!preg_match('/[a-z]+/', $module))
				throw new Exception('$module param may only contain a-z characters.');

			if(!preg_match('/[a-z]+/i', $function))
				throw new Exception('$function param may only contain a-z characters.');
		}

		$p = array('m'=>$module,'f'=>$function, 'p'=>$params);

		$r = $toLoginDialog ? '' : 'external/index';

		$url = GO::config()->orig_full_url.'?r='.$r.'&f='.urlencode(base64_encode(json_encode($p)));
		return $url;
	}

	/**
	 * Set the URL to redirect to after login.
	 *
	 * This is handled by the main index.php
	 *
	 * @param string $url
	 */
	public static function setAfterLoginUrl($url){
		GO::session()->values['after_login_url']=$url;
	}

	/**
	 * Generate a controller URL.
	 *
	 * @param string $path To controller. eg. addressbook/contact/submit
	 * @param array $params eg. array('id'=>1,'someVar'=>'someValue')
	 * @param boolean $relative Defaults to true. Set to false to return an absolute URL.
	 * @param boolean $htmlspecialchars Set to true to escape special html characters. eg. & becomes &amp.
	 * @return string
	 */
	public static function url($path='', $params=array(), $relative=true, $htmlspecialchars=false, $appendSecurityToken=true){
		$url = $relative ? GO::config()->host : GO::config()->full_url;

		if(empty($path) && empty($params)){
			return $url;
		}

		if(empty($path)){
			$amp = 'index.php?';
		}else
		{
			$url .= 'index.php?r='.$path;

			$amp = $htmlspecialchars ? '&amp;' : '&';
		}

		if(!empty($params)){
			if(is_array($params)){
				foreach($params as $name=>$value){
					$url .= $amp.$name.'='.urlencode($value);

					$amp = $htmlspecialchars ? '&amp;' : '&';
				}
			}else
			{
				$url .= $amp.$params;
			}
		}

		$amp = $htmlspecialchars ? '&amp;' : '&';

		if($appendSecurityToken && isset(GO::session()->values['security_token']))
			$url .= $amp.'security_token='.GO::session()->values['security_token'];

		return $url;
	}

	/**
	 * Find classes in a folder
	 *
	 * @param string $path Relative from go/base
	 * @return ReflectionClass[]
	 */
	public static function findClasses($subfolder){

		$classes=array();
		$folder = new GO_Base_Fs_Folder(GO::config()->root_path.'go/base/'.$subfolder);
		if($folder->exists()){

			$items = $folder->ls();

			foreach($items as $item){
				if($item instanceof GO_Base_Fs_File){
					$className = 'GO_Base_'.ucfirst($subfolder).'_'.$item->nameWithoutExtension();
					$classes[] = new ReflectionClass($className);
				}
			}
		}

		return $classes;
	}
	
	
	/**
	 * Find classes in a folder
	 *
	 * @param string $path Relative from $config['file_storage_path'].'php/'
	 * @return ReflectionClass[]
	 */
	public static function findFsClasses($subfolder, $subClassOf=null){

		$classes=array();
		$folder = new GO_Base_Fs_Folder(GO::config()->file_storage_path.'php/'.$subfolder);
		if($folder->exists()){

			$items = $folder->ls();

			foreach($items as $item){
				if($item instanceof GO_Base_Fs_File){
					$className = 'GOFS_';
					
					$subFolders = explode('/', $subfolder);
					
					foreach($subFolders as $sf){
						$className .= ucfirst($sf).'_';
					}
					
					$className .= $item->nameWithoutExtension();
					
					$rc = new ReflectionClass($className);
					
					if($subClassOf==null || $rc->isSubclassOf($subClassOf))
						$classes[] = $rc;
				}
			}
		}

		return $classes;
	}
	
	
	/**
	 * Checks if Group-Office is already installed. 
	 * 
	 * @return boolean
	 */
	public static function isInstalled(){
		return !empty(GO::config()->db_user);
	}

}

require_once('compat.php');
