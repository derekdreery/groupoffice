<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * This file holds global functions for use inside Group-Office
 *
 * @package Framework
 * @author   Merijn Schering <mschering@intermesh.nl>
 * @since    Group-Office 1.0
 */



function __autoload($class_name) {
		global $GO_CONFIG;
		
		/*if(!file_exists($GO_CONFIG->class_path. $class_name.'.class.inc.php'))
		{
			debug_print_backtrace();
		}*/
    require_once $GO_CONFIG->class_path. $class_name.'.class.inc.php';
}

function utf8_basename($path)
{
	if(!function_exists('mb_substr'))
	{
		return basename($path);
	}
	$path = trim($path);
	if(substr($path,-1,1)=='/')
	{
		$path = substr($path,0,-1);
	}
	return mb_substr($path, mb_strrpos($path, '/')+1);
}

function resize_image($src, $maxsize)
{
	//De hoogte en breedte ophalen van het plaatje
	$dimensions = getimagesize($src);


	//Hoogte en breedte toekennnen aan nieuwe variabelen
	$old_width = $dimensions[0];
	$old_height  = $dimensions[1];

	if($old_width<$maxsize && $old_height<$maxsize)
	{
		return true;
	}

	if($old_width>$old_height)
	{
		$new_width=$maxsize;
		//De nieuwe hoogte berekenen aan de gegevens van het oude plaatje en de doel breedte
		$new_height = ($old_height * $new_width) / $old_width;

		//De hoogte, als het nodig is, afronden
		$new_height = round($new_height, 0);
	}else {
		$new_height=$maxsize;
		$new_width = ($old_width * $new_height) / $old_height;
		$new_width = round($new_width, 0);
	}


	//Het plaatje inlezen in de variabele $image
	$image = imagecreatefromjpeg($src);

	//een nieuw klein plaatje maken met de gewenste grootte
	$destination = imagecreatetruecolor($new_width, $new_height);

	//Het nieuwe plaatje vullen met verkleinde plaatje
	imagecopyresampled($destination, $image, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);

	//Het plaatje weergeven
	imagejpeg($destination, $src);

	//Het bronplaatje verwijderen
	imagedestroy($image);

	//Het doelplaatje verwijderen
	imagedestroy($destination);

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

function set_debug_log($file)
{
	$_SESSION['GO_SESION']['debug_log']=$file;
}


function debug($text)
{
	global $GO_CONFIG;
	
	if($GO_CONFIG->log)
	{
		if(!is_string($text))
		{
			$text = var_export($text, true);
		}
		
		if(!isset($_SESSION['GO_SESION']['debug_log']))
			$_SESSION['GO_SESION']['debug_log']=$GO_CONFIG->file_storage_path.'debug.log';
	
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