<?php
/**
 * This file holds global functions for use inside Group-Office
 *
 * @package go.global
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @since    Group-Office 1.0
 */


/**
 * Attempts to autoload class files
 *
 * @param string $class_name
 */

function go_autoload($class_name) {
	global $GO_CONFIG;

	/*if(!file_exists($GO_CONFIG->class_path. $class_name.'.class.inc.php'))
		{
		debug_print_backtrace();
		}*/
	$cls = $GO_CONFIG->class_path. $class_name.'.class.inc.php';
	if(file_exists($cls))
		require_once $cls;
}
spl_autoload_register("go_autoload");


function is_windows(){
	return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}

/**
 * Get the current server time in microseconds
 *
 * @access public
 * @return int
 */
function getmicrotime() {
	list ($usec, $sec) = explode(" ", microtime());
	return ((float) $usec + (float) $sec);
}

/**
 * Get's the last file or directory name of a filesystem path and works
 * with UTF-8 too unlike the basename function in PHP.
 *
 * @param string $path
 * @return string basename
 */

function utf8_basename($path)
{
	if(!function_exists('mb_substr'))
	{
		return basename($path);
	}
	//$path = trim($path);
	if(substr($path,-1,1)=='/')
	{
		$path = substr($path,0,-1);
	}
	if(empty($path))
	{
		return '';
	}
	$pos = mb_strrpos($path, '/');
	if($pos===false)
	{
		return $path;
	}else
	{
		return mb_substr($path, $pos+1);
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
function go_log($level, $message) {
	global $GO_CONFIG;
	if ($GO_CONFIG->log) {
		$messages = str_split($message, 500);
		for ($i = 0; $i < count($messages); $i ++) {
			syslog($level, $messages[$i]);
		}
	}
}

/**
 * Set's the debug log location
 *
 * @param string $file
 */

function set_debug_log($file)
{
	$_SESSION['GO_SESION']['debug_log']=$file;
}

/**
 * Write's to a debug log.
 *
 * @param string $text log entry
 */

function debug($text, $config=false)
{

	if(!$config)
	$config=$GLOBALS['GO_CONFIG'];

	if($config->debug)
	{
		if(!is_string($text))
		{
			$text = var_export($text, true);
		}

		if(!isset($_SESSION['GO_SESION']['debug_log']))
		$_SESSION['GO_SESION']['debug_log']=$config->file_storage_path.'debug.log';

		$text = '['.date('Y-m-d G:i').'] '.$text;


		file_put_contents($_SESSION['GO_SESION']['debug_log'], $text."\n\n", FILE_APPEND);

		//go_log(LOG_DEBUG, $text);
	}
}


/**
 * Returns an array with browser information
 *
 * @access public
 * @return array Array contains keys name, version and subversion
 */
function detect_browser() {
	if (eregi('msie ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
		$browser['version'] = $log_version[1];
		$browser['name'] = 'MSIE';
	}
	elseif (eregi('opera/([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
		$browser['version'] = $log_version[1];
		$browser['name'] = 'OPERA';
	}
	elseif (eregi('mozilla/([0-9].[0-9]{1,2}).*gecko/([0-9]+)', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
		$browser['version'] = $log_version[1];
		$browser['name'] = 'MOZILLA';
		$browser['subversion'] = $log_version[2];
	}
	elseif (eregi('netscape/([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
		$browser['version'] = $log_version[1];
		$browser['name'] = 'NETSCAPE';
	}
	elseif (eregi('safari/([0-9]+.[0-9]+)', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
		$browser['version'] = $log_version[1];
		$browser['name'] = 'SAFARI';
	} else {
		$browser['version'] = 0;
		$browser['name'] = 'OTHER';
	}
	return $browser;
}