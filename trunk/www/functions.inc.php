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

function __autoload($class_name) {
	global $GO_CONFIG;

	/*if(!file_exists($GO_CONFIG->class_path. $class_name.'.class.inc.php'))
		{
		debug_print_backtrace();
		}*/
	require_once $GO_CONFIG->class_path. $class_name.'.class.inc.php';
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


function export_query($fp){


	$db = new db();

	$q = $_SESSION['GO_SESSION']['export_queries'][$_REQUEST['query']];

	$params = array();
	$types='';


	if(is_array($q))
	{
		$extra_sql=array();
		$sql = $q['query'];
		if(isset($q['extra_params']))
		{
			foreach($q['extra_params'] as $param=>$sqlpart)
			{
				if(!empty($_REQUEST[$param]))
				{
					$params[] = $_REQUEST[$param];
					$extra_sql[]=$sqlpart;
				}
			}
		}
		if(count($params))
		{
			$insert = ' ';
			if(!strpos($sql, 'WHERE'))
			{
				$insert .= 'WHERE ';
			}else
			{
				$insert .= ' AND ';
			}
			$insert .= implode(' AND ', $extra_sql);

			$pos = strpos($sql, 'ORDER');

			if(!$pos)
			{
				$sql .= $insert;
			}else
			{
				$sql = substr($sql, 0, $pos).$insert.' '.substr($sql, $pos);
			}

			$types=str_repeat('s',count($params));
		}
	}else
	{
		$sql = $q;

		$params=array();

	}

	$db->query($sql,$types,$params);


	

	$columns=array();
	$headers=array();
	if(isset($_REQUEST['columns']))
	{
		$indexesAndHeaders = explode(',', $_REQUEST['columns']);

		foreach($indexesAndHeaders as $i)
		{
			$indexAndHeader = explode(':', $i);

			$headers[]=$indexAndHeader[1];
			$columns[]=$indexAndHeader[0];
		}

		fwrite($fp, $_SESSION['GO_SESSION']['text_separator'].implode($_SESSION['GO_SESSION']['text_separator'].$_SESSION['GO_SESSION']['list_separator'].$_SESSION['GO_SESSION']['text_separator'], $headers).$_SESSION['GO_SESSION']['text_separator']."\r\n");
	}


	while($record = $db->next_record())
	{
		if(!count($columns))
		{

			foreach($record as $key=>$value)
			{
				$columns[]=$key;
				$headers[]=$key;
			}

			fwrite($fp,  $_SESSION['GO_SESSION']['text_separator'].implode($_SESSION['GO_SESSION']['text_separator'].$_SESSION['GO_SESSION']['list_separator'].$_SESSION['GO_SESSION']['text_separator'], $headers).$_SESSION['GO_SESSION']['text_separator']."\r\n");

		}

		if(is_array($q))
		{
			if(!empty($q['require']))
			{
				require_once($q['require']);
			}
			call_user_func_array(array($q['class'], $q['method']),array(&$record));
		}

		if(isset($record['user_id']) && isset($columns['user_id']))
		{
			$user = $GO_USERS->get_user($record['user_id']);
			$record['user_id']=$user['username'];
		}
		$values=array();
		foreach($columns as $index)
		{
			$values[] = $record[$index];
		}
		fwrite($fp, $_SESSION['GO_SESSION']['text_separator'].implode($_SESSION['GO_SESSION']['text_separator'].$_SESSION['GO_SESSION']['list_separator'].$_SESSION['GO_SESSION']['text_separator'], $values).$_SESSION['GO_SESSION']['text_separator']."\r\n");
	}

}

