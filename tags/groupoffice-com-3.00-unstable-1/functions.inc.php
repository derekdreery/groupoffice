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
 * @version $Id: functions.inc.php 2952 2008-09-03 09:47:49Z mschering $
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


/* function quoted_printable_encode($str)
 {
 global $GO_CONFIG;
 require_once($GO_CONFIG->class_path."mail/phpmailer/class.phpmailer.php");
 $mail = new PHPMailer();

 return trim($mail->EncodeQP ($str));

 }
 */






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



/**
 * Unescapes a slashed string if magic_quotes_gpc is on
 *
 * @param	string $string
 * @access public
 * @return string Stripped of slashes
 */
function smart_stripslashes($string) {
	if (get_magic_quotes_gpc()) {
		$string = stripslashes($string);
	}
	return $string;
}

/**
 * Escapes a string with slashes if magic_quotes_gpc is off
 *
 * @param	string $string
 * @access public
 * @return string Stripped of slashes
 */
function smart_addslashes($string) {
	if (!get_magic_quotes_gpc()) {
		$string = addslashes($string);
	}
	return $string;
}
