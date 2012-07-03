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
	 * If you set this to true then all acl's will allow all actions. Useful
	 * for maintenance scripts.
	 *
	 * It returns the old value.
	 *
	 * @param string $ignore
	 * @return boolean Old value
	 */
	public static function setIgnoreAclPermissions($ignore=true){
		$oldValue = GO::$ignoreAclPermissions;
		GO::$ignoreAclPermissions=$ignore;

		return $oldValue;
	}

	/**
	 * Get a unique ID for Group-Office. It's mainly used for the javascript window id.
	 * @return type 
	 */
	public static function getId(){
		
		$serverName = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "unknown";
		
		return GO::config()->id.'AT'.$serverName;
	}
	
	/**
	 * This GO_Base_Model_ModelCache.php mechanism can consume a lot of memory 
	 * when running large batch scripts. That's why it can be disabled.
	 *
	 * @var boolean
	 */
	public static $disableModelCache=false;

	private static $_classesIsDirty=false;
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

	public static function setDbConnection($dbname=false, $dbuser=false, $dbpass=false, $dbhost=false){

		self::$db=null;

		if($dbname===false)
			$dbname=GO::config()->db_name;

		if($dbuser===false)
			$dbuser=GO::config()->db_user;

		if($dbpass===false)
			$dbpass=GO::config()->db_pass;

		if($dbhost===false)
			$dbhost=GO::config()->db_host;

//		GO::debug("Connect: mysql:host=$dbhost;dbname=$dbname, $dbuser, ***");

		self::$db = new GO_Base_Db_PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
//		self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
//		self::$db->setAttribute( PDO::ATTR_STATEMENT_CLASS, array( 'GO_Base_Db_ActiveStatement', array() ) );
//
//		//todo needed for foundRows
//		self::$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true); 
//
//		self::$db->query("SET NAMES utf8");
//
//		if(GO::config()->debug){
//			//GO::debug("Setting MySQL sql_mode to TRADITIONAL");
//			self::$db->query("SET sql_mode='TRADITIONAL'");
//		}

	}

	/**
	 * Clears the GO::config()->file_storage_path/cache folder. This folder contains mainly cached javascripts.
	 */
	public static function clearCache(){
		$folder = new GO_Base_Fs_Folder(GO::config()->file_storage_path.'cache');

		//make sure it exists
		$folder->create();

		$items = $folder->ls();
		foreach($items as $item)
			$item->delete();
		GO::cache()->flush();

		GO_Base_Model::clearCache();
	}

	/**
	 *
	 * @return string Returns the currently selected theme.
	 *
	 * TODO SHould be changed to theme().
	 */
	public static function view(){
		return isset(GO::session()->values['view']) ? GO::session()->values['view'] : GO::config()->defaultView;
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
		if(empty(GO::session()->values['user_id'])){
			return false;
		}else{
			if(!isset(self::$_user))
				self::$_user = GO_Base_Model_User::model()->findByPk(GO::session()->values['user_id'], array(), true);

			return self::$_user;
		}
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
	 * @todo implement memcached driver
	 * @return GO_Base_Cache_Interface
	 */
	public static function cache(){

		if (!isset(self::$_cache)) {
			if(GO::config()->debug)
				self::$_cache=new GO_Base_Cache_Apc();
			elseif(function_exists("apc_store"))
				self::$_cache=new GO_Base_Cache_Apc();
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
			GO::debug("Autoloading: ".$className);
			//For SabreDAV
			if(strpos($className,'Sabre_')===0) {
        include self::config()->root_path . 'go/vendor/SabreDAV/lib/Sabre/' . str_replace('_','/',substr($className,6)) . '.php';
			}

			if(substr($className,0,7)=='GO_Base'){
				$arr = explode('_', $className);
				$file = array_pop($arr).'.php';

				$path = strtolower(implode('/', $arr));
				$location =$path.'/'.$file;
				$baseClassFile = dirname(dirname(__FILE__)) . '/'.$location;
				require($baseClassFile);

				//store it in the classes array so we can cache that for faster loading next time.
				self::$_classesIsDirty=true;
				self::$_classes[$className]=$location;

			}  else {
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

					if(!file_exists($fullPath) || is_dir($fullPath)){
						//throw new Exception('Class '.$orgClassName.' not found! ('.$file.')');
						return false;
					}
					
					//store it in the classes array so we can cache that for faster loading next time.
					self::$_classes[$className]=$file;
					self::$_classesIsDirty=true;

					require($fullPath);
				}
			}
		}
	}

	private static $initialized=false;
	
	
	/**
	 * Called by GO_Base_Config::__destruct() so we can do stuff at the end 
	 */
	public static function endRequest(){
		if(self::$_classesIsDirty)
			GO::cache()->set('autoload_classes', self::$_classes);
	}

	/**
	 * This function inititalizes Group-Office.
	 *
	 */
	public static function init() {

		if(self::$initialized){
			throw new Exception("Group-Office was already initialized");
		}
		self::$initialized=true;
				
		spl_autoload_register(array('GO', 'autoload'));	

		GO::session();
		
		//get cached autoload classes
		$classes = GO::cache()->get('autoload_classes');
		if($classes)
			self::$_classes=$classes;

		date_default_timezone_set(GO::user() ? GO::user()->timezone : GO::config()->default_timezone);

		if (self::config()->firephp) {
			if (self::requireExists('FirePHPCore/fb.php')) {
				require_once 'FirePHPCore/fb.php';
			}
		}

//		if (!defined('GO_NO_SESSION')) {
//			//start session
//			session_name('groupoffice');
//			if (isset($_REQUEST['session_id']) && isset($_REQUEST['auth_token'])) {
//				session_id($_REQUEST['session_id']);
//			}
//			session_start();
//			if (isset($_REQUEST['auth_token'])) {
//				if ($_REQUEST['auth_token'] != GO::session()->values['auth_token']) {
//					session_destroy();
//					die('Invalid auth_token supplied');
//				} else {
//					GO::session()->values['auth_token'] = GO_Base_Util_String::random_password('a-z,1-9', '', 30);
//					//redirect to URL without session_id
//					header('Location: ' . $_SERVER['PHP_SELF']);
//					exit();
//				}
//			}
//		}

		if(!defined('GO_LOADED')){ //check if old Group-Office.php was loaded

			if(GO::config()->debug || GO::config()->debug_log){
				$username = GO::user() ? GO::user()->username : 'nobody';

				$log = '['.date('Y-m-d G:i').']['.$username.'] Start of new request: ';
				if(isset($_SERVER['REQUEST_URI']))
					$log .= $_SERVER['REQUEST_URI'];

				GO::debug($log);

				if(PHP_SAPI!='cli')
					GO::debug("User agent: ".(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "unknown")." IP: ".$_SERVER['REMOTE_ADDR']);
				else
					GO::debug("User agent: CLI");

				GO::debug("Config file: ".GO::config()->get_config_file());
			}
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
				if(isset($_FILES))
					$_FILES = stripslashes_array($_FILES);
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


			//require($GLOBALS['GO_LANGUAGE']->get_base_language_file('common'));

			if (GO::config()->log) {
				$username = isset(GO::session()->values['username']) ? GO::session()->values['username'] : 'notloggedin';
				openlog('[Group-Office][' . date('Ymd G:i') . '][' . $username . ']', LOG_PERROR, LOG_USER);
			}

			if (function_exists('mb_internal_encoding'))
				mb_internal_encoding("UTF-8");
		}

		if (!empty(self::session()->values['user_id'])) {
			self::config()->tmpdir = self::config()->tmpdir . self::session()->values['user_id'] . '/';
		}

		if (GO::config()->debug) {

//			$_SESSION['connect_count'] = 0;
//			$_SESSION['query_count'] = 0;

			//Don't do this for old lib
			if(!isset($GLOBALS['GO_CONFIG']))
				error_reporting(E_ALL | E_STRICT);

			ini_set('display_errors','on');
			ini_set('log_errors','on');
			//ini_set('memory_limit','32M');
			//ini_set('max_execution_time',10);
			//set_error_handler(array('GO','errorHandler'), E_ALL | E_STRICT);
		}
	}


	public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {

		$err_str = "PHP error: $errfile:$errline $errstr ($errno)";
//		if(GO::config()->debug)
//			echo $err_str."\n";

	GO::debug($err_str);
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
		if (self::config()->log) {
			$messages = str_split($message, 500);
			for ($i = 0; $i < count($messages); $i++) {
				syslog($level, $messages[$i]);
			}
		}
	}

	public static function infolog($message) {

		if (self::config()->log) {

			if (empty(GO::session()->values["logdircheck"])) {
				$folder = new GO_Base_Fs_Folder(dirname(self::config()->info_log));
				$folder->create();
				GO::session()->values["logdircheck"] = true;
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

			if (self::config()->firephp) {
				if (class_exists('FB')) {
					ob_start();
					FB::send($text);
				}
			}

			if (self::config()->debug_log) {

				if (!is_string($text)) {
					$text = var_export($text, true);
				}

				if ($text == '')
					$text = '(empty string)';

				//$username=GO::user() ? GO::user()->username : 'nobody';

				//$trace = debug_backtrace();

				//$prefix = "\n[".date("Ymd G:i:s")."][".$trace[0]['file'].":".$trace[0]['line']."]\n";

				//$lines = explode("\n", $text);

				//$text = $prefix.$text;
				
				$user = isset(GO::session()->values['username']) ? GO::session()->values['username'] : 'notloggedin';
				
				$text = "[$user] ".str_replace("\n","\n[$user] ", $text);

				file_put_contents(self::config()->file_storage_path . 'debug.log', $text . "\n", FILE_APPEND);
			}
		}
	}

	
	public static function debugCalledFrom($limit=1){
		$trace = debug_backtrace(); 
		for($i=0;$i<$limit;$i++){
			if(isset($trace[$i+1])){
				$call = $trace[$i+1];
				GO::debug("Funtion: ".$call["function"]." called in file ".$call["file"]." line ".$call["line"]);
			}
		}
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

		$url = GO::config()->orig_full_url.'?r='.$r.'&f='.urlencode(GO_Base_Util_Crypt::encrypt($p));
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
	public static function url($path='', $params=array(), $relative=true, $htmlspecialchars=false){
		$url = $relative ? GO::config()->host : GO::config()->full_url;

		if(empty($path) && empty($params)){
			return $url;
		}

		if(empty($path)){
			$amp = '?';
		}else
		{
			$url .= '?r='.$path;

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

		if(isset(GO::session()->values['security_token']))
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

}

require_once('compat.php');
