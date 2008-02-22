<?php
/**
 * @copyright Intermesh 2004
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.218 $ $Date: 2006/11/28 10:42:42 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once($GO_CONFIG->root_path.'functions_compat.inc');

/**
 * This file holds global functions for use inside Group-Office
 *
 * @package Framework
 * @author   Merijn Schering <mschering@intermesh.nl>
 * @since    Group-Office 1.0
 */



/* function quoted_printable_encode($str)
 {
 global $GO_CONFIG;
 require_once($GO_CONFIG->class_path."mail/phpmailer/class.phpmailer.php");
 $mail = new PHPMailer();

 return trim($mail->EncodeQP ($str));

 }
 */

function trim_lines($text)
{
	go_log(LOG_DEBUG, $text);
	str_replace("\r\n","\n", $text);

	$trimmed='';

	$lines = explode("\n", $text);
	foreach($lines as $line)
	{
		if($trimmed=='')
		{
			$trimmed .= $line."\n";
		}elseif(empty($line))
		{
			$trimmed .= "\n";
		}elseif($line[0]!=' ')
		{
			return $text;
		}else{
			$trimmed .= substr($line,1)."\n";
		}
	}

	return $trimmed;
}

function record_to_keywords($record)
{
	$keywords=array();

	foreach($record as $field)
	{
		if(!empty($field) && !is_numeric($field) && !in_array($field,$keywords))
		{
			$keywords[]=$field;
		}
	}
	return implode(',',$keywords);
}

function load_control($name)
{
	global $GO_CONFIG;
	require_once($GO_CONFIG->class_path.'/base/controls/'.$name.'.class.inc');
}

function load_basic_controls()
{
	load_control('table');
	load_control('form');
	load_control('button_menu');
	load_control('image');
	load_control('hyperlink');
	load_control('select');
	load_control('button');
	load_control('checkbox');
	load_control('tabstrip');
	load_control('radiogroup');
	load_control('textarea');
}

function date_add($time,$days=0,$months=0,$years=0)
{
	$date=adodb_getdate($time);
	return adodb_mktime($date['hours'],$date['minutes'], $date['seconds'],$date['mon']+$months,$date['mday']+$days,$date['year']+$years);
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
/*
 function quoted_printable_encode( $str ) {
 return imap_8bit($str);
 }

 */
function quoted_printable_encode($sText,$bEmulate_imap_8bit=true) {
	// split text into lines
	$aLines=explode(chr(13).chr(10),$sText);

	for ($i=0;$i<count($aLines);$i++) {
		$sLine =& $aLines[$i];
		if (strlen($sLine)===0) continue; // do nothing, if empty

		$sRegExp = '/[^\x09\x20\x21-\x3C\x3E-\x7E]/e';

		// imap_8bit encodes x09 everywhere, not only at lineends,
		// for EBCDIC safeness encode !"#$@[\]^`{|}~,
		// for complete safeness encode every character :)
		if ($bEmulate_imap_8bit)
		$sRegExp = '/[^\x20\x21-\x3C\x3E-\x7E]/e';

		$sReplmt = 'sprintf( "=%02X", ord ( "$0" ) ) ;';
		$sLine = preg_replace( $sRegExp, $sReplmt, $sLine );

		// encode x09,x20 at lineends
		{
			$iLength = strlen($sLine);
			$iLastChar = ord($sLine{$iLength-1});

			//              !!!!!!!!
			// imap_8_bit does not encode x20 at the very end of a text,
			// here is, where I don't agree with imap_8_bit,
			// please correct me, if I'm wrong,
			// or comment next line for RFC2045 conformance, if you like
			if (!($bEmulate_imap_8bit && ($i==count($aLines)-1)))

			if (($iLastChar==0x09)||($iLastChar==0x20)) {
				$sLine{$iLength-1}='=';
				$sLine .= ($iLastChar==0x09)?'09':'20';
			}
		}    // imap_8bit encodes x20 before chr(13), too
		// although IMHO not requested by RFC2045, why not do it safer :)
		// and why not encode any x20 around chr(10) or chr(13)
		if ($bEmulate_imap_8bit) {
			$sLine=str_replace(' =0D','=20=0D',$sLine);
			//$sLine=str_replace(' =0A','=20=0A',$sLine);
			//$sLine=str_replace('=0D ','=0D=20',$sLine);
			//$sLine=str_replace('=0A ','=0A=20',$sLine);
		}
		 
		//merijn$sLine  = str_replace(' ','=20',$sLine);

		// finally split into softlines no longer than 76 chars,
		// for even more safeness one could encode x09,x20
		// at the very first character of the line
		// and after soft linebreaks, as well,
		// but this wouldn't be caught by such an easy RegExp
		preg_match_all( '/.{1,73}([^=]{0,2})?/', $sLine, $aMatch );
		$sLine = implode( '=' . chr(13).chr(10), $aMatch[0] ); // add soft crlf's
	}

	// join lines into text
	return implode(chr(13).chr(10),$aLines);
}


function array_to_form($array, $prefix='', $suffix='')
{
	$form = '';
	foreach($array as $key => $value)
	{
		if(is_array($value))
		{
			$form .= array_to_form($value, $prefix.$key.$suffix.'[',']');
		}else
		{
			$input = new input('hidden', $prefix.smart_stripslashes($key).$suffix, smart_stripslashes($value),false);
			$form .= $input->get_html();
		}
	}
	return $form;
}

/**
 * Creates a directory recursively
 *
 * @param	string $path
 * @access public
 * @return bool True on success
 */

function mkdir_recursive($path) {
	global $GO_CONFIG;

	$dirs_to_create = array ();
	while (!is_writable($path)) {
		$dirs_to_create[] = $path;
		if(dirname($path) == $path)
		{
			exit('Failed to create folder(s) '.var_export($dirs_to_create, true));
		}
		$path = dirname($path);
	}
	$old_umask = umask(000);
	while ($dir = array_pop($dirs_to_create)) {
		if (@ !mkdir($dir, $GO_CONFIG->create_mode)) {
			die("Failed creating required directory ".$dir);
		}
	}
	umask($old_umask);
	return true;
}

/**
 * Creates a JUpload java applet that can upload multiple files using HTTP
 *
 * @param	string $post_url The URL to the script that handles the uploaded files
 * @access public
 * @return void
 */

function jupload($post_url, $width, $height) {
	global $GO_CONFIG, $jupload_post_url, $jupload_width, $jupload_height;
	$jupload_post_url = $post_url;
	$jupload_width = $width;
	$jupload_height = $height;

	require_once ($GO_CONFIG->control_path.'JUpload/jupload.inc');
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
 * Grab an e-mail address out of a string
 *
 * @param	int $level The log level. See sys_log() of the PHP docs
 * @param	string $message The log message
 * @access public
 * @return void
 */
function get_email_from_string($email) {
	if (preg_match("/(\b)([\w\.\-]+)(@)([\w\.-]+)([A-Za-z]{2,4})\b/i", $email, $matches)) {
		return $matches[0];
	} else {
		return false;
	}
}

function get_name_from_string($string) {
	if (preg_match('/([\D]*|[\D]*[\040][\D]*)/i', $string, $matches)) {
		$matches[0] = str_replace('"', '', $matches[0]);
		return $matches[0];
	} else {
		return $string;
	}
}

function get_filetype_image($extension) {
	global $GO_THEME;

	if (isset ($GO_THEME->filetypes[$extension])) {
		return $GO_THEME->filetypes[$extension];
	} else {
		return $GO_THEME->filetypes['unknown'];
	}
}

function get_filetype_description($extension) {
	global $filetypes;

	if (isset ($filetypes[$extension])) {
		return $filetypes[$extension];
	} else {
		return $filetypes['unknown'];
	}
}



function get_timezone_offset($time = 0) {
	if ($time == 0)
	$time = time();

	if (date('I', $time) == 0) {
		return $_SESSION['GO_SESSION']['timezone'];
	} else {
		return $_SESSION['GO_SESSION']['timezone'] + $_SESSION['GO_SESSION']['DST'];
	}
}

/**
 * Adds paramaters to an URL
 *
 * @param	string $url
 * @param	string $params
 * @access public
 * @return string
 */

function add_params_to_url($url, $params) {
	if (strpos($url, '?') === false) {
		$url .= '?'.$params;
	} else {
		$url .= '&'.$params;
	}
	return $url;
}

/**
 * Get's all queries from an SQL dump file in an array
 *
 * @param	string $file The absolute path to the SQL file
 * @access public
 * @return array An array of SQL strings
 */

function get_sql_queries($file) {
	$sql = '';
	$queries = array ();
	if ($handle = fopen($file, "r")) {
		while (!feof($handle)) {
			$buffer = trim(fgets($handle, 4096));
			if ($buffer != '' && substr($buffer, 0, 1) != '#' && substr($buffer, 0, 1) != '-') {
				$sql .= $buffer;
			}
		}
		fclose($handle);
	} else {
		die("Could not read SQL dump file $file!");
	}
	$length = strlen($sql);
	$in_string = false;
	$start = 0;
	$escaped = false;
	for ($i = 0; $i < $length; $i ++) {
		$char = $sql[$i];
		if ($char == '\'' && !$escaped) {
			$in_string = !$in_string;
		}
		if ($char == ';' && !$in_string) {
			$offset = $i - $start;
			$queries[] = substr($sql, $start, $offset);

			$start = $i +1;
		}
		if ($char == '\\') {
			$escaped = true;
		} else {
			$escaped = false;
		}
	}
	return $queries;
}

/**
 * Converts a Group-Office date to unix time.
 *
 * A Group-Office date is formated by user preference.
 *
 * @param	string $date_string The date string formated in the user's preference
 * @access public
 * @return int unix timestamp
 */

function date_to_unixtime($date_string, $date_seperator=null, $date_format=null) {

	if(!isset($date_format))
	$date_format=$_SESSION['GO_SESSION']['date_format'];

	if(!isset($date_seperator))
	$date_seperator=$_SESSION['GO_SESSION']['date_seperator'];

	$date_string = trim($date_string);
	if ($date_string != '') {

		$datetime_array = explode(' ', $date_string);

		$date = isset ($datetime_array[0]) ?
		$datetime_array[0] :
    	'0000'.$date_seperator.
    	'00'.$date_seperator.'00';

		$date_array = explode($date_seperator, $datetime_array[0]);
		//$year = isset ($date_array[2]) ? $date_array[2] : date('Y');

		$format = str_replace($date_seperator,'',$date_format);

		$year_pos = strpos($format, 'Y');
		$month_pos = strpos($format, 'm');
		$day_pos = strpos($format, 'd');

		$year = isset ($date_array[$year_pos]) ? $date_array[$year_pos] : date('Y');
		$month = isset ($date_array[$month_pos]) ? $date_array[$month_pos] : date('m');
		$day = isset ($date_array[$day_pos]) ? $date_array[$day_pos] : 0;

		$time = isset ($datetime_array[1]) ? $datetime_array[1] : '00:00:00';
		$time_array = explode(':', $time);

		$hour = isset ($time_array[0]) ? $time_array[0] : 0;
		$min = isset ($time_array[1]) ? $time_array[1] : 0;
		$sec = isset ($time_array[2]) ? $time_array[2] : 0;

		return adodb_mktime($hour, $min, $sec, $month, $day, $year);
	}
	return false;
}


function get_dateformat($format, $seperator)
{
	$newformat = '';
	$end = strlen($format)-1;
	for($i=0;$i<$end;$i++)
	{
		$newformat .= $format[$i].$seperator;
	}
	$newformat .= $format[$i];
	return $newformat;
}

/**
 * Convert a Group-Office date to MySQL date format
 *
 * A Group-Office date is formated by user preference.
 *
 * @param	string $date_string The Group-Office date string
 * @param	bool $with_time The output sting should contain time too
 * @access public
 * @return int unix timestamp
 */

function date_to_db_date($date_string, $with_time = false) {
	if(empty($date_string))
	{
		return '';
	}
	$time = date_to_unixtime($date_string);
	$date_format = $with_time ? 'Y-m-d H:i' : 'Y-m-d';
	return adodb_date($date_format, $time);
}


/**
 * Convert a MySQL date to Group-Office date.
 *
 * A Group-Office date is formated by user preference.
 *
 * @param	string 	$date_string The MySQL date string
 * @access public
 * @return string Group-Office date string
 */

function db_date_to_date($date_string) {
	if ($date_string == '' || $date_string == '0000-00-00' || $date_string == '0000-00-00 00:00') {
		return '';
	}
	$datetime_array = explode(' ', $date_string);

	$date = isset ($datetime_array[0]) ? $datetime_array[0] : '0000-00-00';

	$date_array = explode('-', $datetime_array[0]);

	$year = isset ($date_array[0]) ? $date_array[0] : 0;
	$month = isset ($date_array[1]) ? $date_array[1] : 0;
	$day = isset ($date_array[2]) ? $date_array[2] : 0;

	$date = adodb_date($_SESSION['GO_SESSION']['date_format'], adodb_mktime(0, 0, 0, $month, $day, $year));

	if(isset ($datetime_array[1]))
	{
		$time_array = explode(':', $datetime_array[1]);

		$hour = isset ($time_array[0]) ? $time_array[0] : 0;
		$min = isset ($time_array[1]) ? $time_array[1] : 0;
		$sec = isset ($time_array[2]) ? $time_array[2] : 0;

		$date .= ' '.adodb_date($_SESSION['GO_SESSION']['time_format'], adodb_mktime($hour, $min, $sec));
	}

	return $date;
}
/*
 function php_to_pear_date_format($date_format)
 {
 $date_format = str_replace('G', 'H', $date_format);
 $date_format = str_replace('i', 'm', $date_format);

 $pear_date_format = '';
 for($i=0;$i<strlen($date_format);$i++)
 {
 if($date_format[$i] != $_SESSION['GO_SESSION']['date_seperator'] && $date_format[$i]!= ' ' && $date_format[$i]!= ':')
 {
 $pear_date_format .= '%';
 }
 $pear_date_format .= $date_format[$i];
 }
 return $pear_date_format;
 }

 function db_date_to_date($date_string) {
 if ($date_string == '') {
 return '';
 }

 $date_format = (strlen($date_string) > 10) ?
 $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'] :
 $_SESSION['GO_SESSION']['date_format'];


 $date = new go_date($date_string);
 return $date->format(php_to_pear_date_format($date_format));
 }
 */
/**
 * Convert a MySQL date to unix time format
 *
 * @param	int			$date_string The MySQL date string
 * @access public
 * @return int unix timestamp
 */

function db_date_to_unixtime($date_string) {
	$datetime_array = explode(' ', $date_string);

	$date = isset ($datetime_array[0]) ? $datetime_array[0] : '0000-00-00';

	$date_array = explode('-', $datetime_array[0]);

	$year = isset ($date_array[0]) ? $date_array[0] : 0;
	$month = isset ($date_array[1]) ? $date_array[1] : 0;
	$day = isset ($date_array[2]) ? $date_array[2] : 0;

	$time = isset ($datetime_array[1]) ? $datetime_array[1] : '00:00:00';
	$time_array = explode(':', $time);

	$hour = isset ($time_array[0]) ? $time_array[0] : 0;
	$min = isset ($time_array[1]) ? $time_array[1] : 0;
	$sec = isset ($time_array[2]) ? $time_array[2] : 0;

	return adodb_mktime($hour, $min, $sec, $month, $day, $year);
}

function get_timestamp($gmt_time)
{
	return date($_SESSION['GO_SESSION']['date_format'].' '.
	$_SESSION['GO_SESSION']['time_format'],
	gmt_to_local_time($gmt_time));
}

/**
 * Convert a unix time stamp to MySQL date format
 *
 * @param	int 		$date_string The MySQL date string
 * @access public
 * @return string MySQL timestamp
 */

function unixtime_to_db_date($unixtime) {
	return adodb_date('Y-m-d');
}

/**
 * Return only the contents of the body tag from a HTML page
 *
 * @param	string $html A HTML formatted string
 * @access public
 * @return string HTML formated string
 */

function get_html_body($html) {
	$to_removed_array = array ("'<html[^>]*>'si", "'</html>'si", "'<body[^>]*>'si", "'</body>'si", "'<head[^>]*>.*?</head>'si", "'<style[^>]*>.*?</style>'si", "'<object[^>]*>.*?</object>'si",);

	//$html = str_replace("\r", "", $html);
	//$html = str_replace("\n", "", $html);

	$html = preg_replace($to_removed_array, '', $html);
	return $html;

}

/**
 * Get the user's local time using the timezone offset preference.
 *
 * @param	string $server_time The time according to the server.
 *					If empty time() is used.
 * @access public
 * @return int unix timestamp
 */

function get_time($server_time = 0) {
	if ($server_time == '0') {
		$server_time = time();
	}

	$server_timezone_offset = date('Z', $server_time);

	$time = $server_time + ((get_timezone_offset($server_time) * 3600) - $server_timezone_offset);
	return $time;
}

/**
 * Get current GMT time.
 *
 * @param	string $server_time The server time
 *
 * @access public
 * @return int unix timestamp
 */

function server_to_gmt_time($server_time)
{
	$server_time -= get_server_timezone_offset($server_time);
	return $server_time;
}

function get_server_timezone_offset($time=0)
{
	if($time == 0) $time = time();
	return adodb_date('Z', $time);
}

/**
 * Get current GMT time.
 *
 * @param	string $local_time The local time of the logged in user.
 *					If empty the current time in GMT will be returned.
 * @access public
 * @return int unix timestamp
 */
function get_gmt_time($local_time = 0) {
	if ($local_time == 0) {
		$time = time() - get_server_timezone_offset($local_time);
	} else {
		$timezone_offset = get_timezone_offset($local_time);

		//echo 'Local time: '.date('Y-m-d G:i', $local_time).'<br />';
		//echo 'Timezone offset: '.$timezone_offset.'<br />';
			
		$time = $local_time - ($timezone_offset * 3600);
		//echo 'GMT time: '.date('Y-m-d G:i', $time).'<hr />';
	}
	return $time;
}

function gmt_to_local_time($gmt_time) {
	$timezone_offset = get_timezone_offset($gmt_time);
	$local_time = $gmt_time + ($timezone_offset * 3600);

	return $local_time;
}

function local_to_gmt_time($local_time) {
	$timezone_offset = get_timezone_offset($local_time);
	$gmt_time = $local_time - ($timezone_offset * 3600);

	return $gmt_time;
}


/**
 * Give it a full name and it tries to determine the First, Middle and Lastname
 *
 * @param	string $full_name A full name
 * @access public
 * @return array array with keys first, middle and last
 */

function split_name($full_name) {
	$name_arr = explode(' ', $full_name);

	$name['first'] = $full_name;
	$name['middle'] = '';
	$name['last'] = '';
	$count = count($name_arr);
	$last_index = $count -1;
	for ($i = 0; $i < $count; $i ++) {
		switch ($i) {
			case 0 :
				$name['first'] = $name_arr[$i];
				break;

			case $last_index :
				$name['last'] = $name_arr[$i];
				break;

			default :
				$name['middle'] .= $name_arr[$i].' ';
				break;
		}
	}
	$name['middle'] = trim($name['middle']);
	return $name;
}

/**
 * Prints an iframe containing a dialog to modify an Access Control List
 *
 * @param	int $acl_id The acl_id to modify
 * @access public
 * @return void
 */
function print_acl($acl_id) {
	echo get_acl($acl_id);
}

function get_acl($acl_id)
{
	global $GO_CONFIG;
	return  '<iframe style="display:block;" width="350"; height="340;" border="0" frameborder="0" '.'scrolling="no" src="'.$GO_CONFIG->control_url.'acl.php?acl_id='.$acl_id.'"></iframe>';
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
 * Get the regex used for validating an email address
 * Requires the Top Level Domain to be between 2 and 6 alphanumeric chars
 *
 * @param	none
 * @access	public
 * @return	string
 */
function get_email_validation_regex() {
	return "^([a-z0-9]+)([._-]([a-z0-9]+))*[@]([a-z0-9]+)([._-]([a-z0-9]+))*[.]([a-z0-9]){2,6}$";
}

/**
 * Check if an email adress is in a valid format
 *
 * @param	string $email E-mail address
 * @access public
 * @return bool
 */
/*function validate_email($email) {
 return eregi(get_email_validation_regex(), $email);
 }*/

function validate_email($email)
{
	// First, we check that there's one @ symbol,
	// and that the lengths are right.
	if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
		// Email invalid because wrong number of characters
		// in one section or wrong number of @ symbols.
		return false;
	}
	// Split it into sections to make life easier
	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	for ($i = 0; $i < sizeof($local_array); $i++) {
		if
		(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&
↪'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",
		$local_array[$i])) {
			return false;
		}
	}
	// Check if domain is IP. If not,
	// it should be valid domain name
	if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
		$domain_array = explode(".", $email_array[1]);
		if (sizeof($domain_array) < 2) {
			return false; // Not enough parts to domain
		}
		for ($i = 0; $i < sizeof($domain_array); $i++) {
			if
			(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|
↪([A-Za-z0-9]+))$",
			$domain_array[$i])) {
				return false;
			}
		}
	}
	return true;
}

/**
 * Checks for empty string and returns stripe when empty
 *
 * @param	string $input Any string
 * @access public
 * @return string
 */
function empty_to_stripe($input) {
	if ($input == "") {
		return "-";
	} else {
		return $input;
	}
}

/**
 * Build mailto link based on user's preference
 *
 * creates a mail to link based on the users settings to use
 * his own mail client or the Group-Office mail client
 *
 * @param	string $email The reciever's address
 * @param	string $name The reciever's name
 * @param	string $class The CSS class of the link
 * @param	bool $full_link Return a full link or just the href attribute
 * @param	int $contact_id The contact id of the reciever
 * @access public
 * @return string URL to open e-mail client
 */
function mail_to($email, $name = "", $class = "normal", $full_link = true, $contact_id = 0, $company_id = 0) {
	global $GO_CONFIG, $GO_SECURITY, $GO_MODULES, $strEmailTo, $charset;

	$email = validate_email(get_email_from_string($email)) ? $email : '';

	$module = isset ($GO_MODULES->modules['email']) ? $GO_MODULES->modules['email'] : false;

	$email = htmlspecialchars($email, ENT_QUOTES);

	if ($name == "")
	$name = $email;

	if ($module['read_permission']) {
		if ($full_link == true) {
			return "<a class=\"".$class."\" title=\"".$strEmailTo.stripslashes($email)."\" href='javascript:popup(\"".$module['url']."send.php?mail_to=".$email."&contact_id=".$contact_id."&company_id=".$company_id."\",\"".$GO_CONFIG->composer_width."\",\"".$GO_CONFIG->composer_height."\")'>".$name."</a>";
		} else {
			return 'javascript:openPopup("_blank", "'.$module['url'].'send.php?mail_to='.$email.'&contact_id='.$contact_id.'&company_id='.$company_id.'","'.$GO_CONFIG->composer_width.'","'.$GO_CONFIG->composer_height.'", "no")';
		}

	} else {
		$email = get_email_from_string($email);
		if ($full_link == true) {
			return "<a class=\"".$class."\" href=\"mailto:".$email."\" title=\"".$strEmailTo.$email."\">".$name."</a>";
		} else {
			return 'mailto:'.$email;
		}
	}
}

function get_mail_to_href($email='', $contact_id = 0, $company_id = 0)
{
	global $GO_MODULES, $GO_CONFIG;

	if (isset($GO_MODULES->modules['email']) && $GO_MODULES->modules['email']['read_permission']) {
		return "javascript:openPopup('_blank', '".$GO_MODULES->modules['email']['url'].
			"send.php?mail_to=".htmlspecialchars($email, ENT_QUOTES)."&contact_id=".
		$contact_id."&company_id=".$company_id."','".
		$GO_CONFIG->composer_width."','".$GO_CONFIG->composer_height."','no')";
	}else
	{
		return 'mailto:'.get_email_from_string($email);
	}
}
/**
 * Return a formatted address string
 *
 * @param	array $object User or contact
 * @access public
 * @return string Address formatted
 */
function address_format($object, $linebreak = '<br />') {
	if (isset ($object['name'])) {
		$name = $object['name'];
	} else {
		$middle_name = $object['middle_name'] == '' ? '' : $object['middle_name'].' ';

		if ($object['title'] != '' && $object['initials'] != '') {
			$name = $object['title'].' '.$object['initials'].' '.$middle_name.$object['last_name'];
		} else {
			$name = $object['first_name'].' '.$middle_name.$object['last_name'];
		}
	}

	$address = $name.$linebreak;

	if ($object['address'] != '') {
		$address .= $object['address'];
		if (isset ($object['address_no'])) {
			$address .= ' '.$object['address_no'];
		}
		$address .= $linebreak;
	}
	if ($object['zip'] != '') {
		$address .= $object['zip'].' ';
	}
	if ($object['city'] != '') {
		$address .= $object['city'].$linebreak;
	}
	if ($object['country'] != '') {
		$address .= $object['country'].$linebreak;
	}
	return $address;

}

/**
 * Return an URL to a user profile page
 *
 * @param	int $user_id The user_id
 * @param	string $linktext The text to display
 * @param	string $class The CSS class of the link
 * @param	string $return_to The URL to return to when Back is clicked
 * @access public
 * @return string URL to a user profile page
 */
function show_profile($user_id, $linktext = '', $class = 'normal', $return_to = '') {
	global $strShowProfile, $strProtectedUser, $GO_CONFIG, $GO_USERS, $go_security_class;

	if ($return_to == '') {
		$return_to = $_SERVER['REQUEST_URI'];
	}

	$GO_SECURITY = new $go_security_class();
	$GO_MODULES = new GO_MODULES();

	if ($linktext == '') {
		$profile = $GO_USERS->get_user($user_id);
		$middle_name = $profile['middle_name'] == '' ? '' : $profile['middle_name'].' ';
		$linktext = htmlspecialchars($profile['first_name'].' '.$middle_name.$profile['last_name']);
	}

	if ($GO_SECURITY->user_is_visible($user_id)) {
		if(isset($GO_MODULES->modules['users']) && $GO_MODULES->modules['users']['read_permission'])
		{
			$link = '<a href="'.$GO_MODULES->modules['users']['url'].'user.php?user_id='.$user_id.'&return_to='.urlencode($return_to).'" class="'.$class.'" title="'.$strShowProfile.'">';
			$link .= $linktext."</a>\n";
		}else
		{
			$link = '<a href="'.$GO_CONFIG->control_url.'user.php?id='.$user_id.'&return_to='.urlencode($return_to).'" class="'.$class.'" title="'.$strShowProfile.'">';
			$link .= $linktext."</a>\n";
		}
	} else {
		$link = $linktext;
	}


	return $link;
}

/**
 * Return an URL to a project
 *
 * @param	int $project_id The project_id
 * @param	string $linktext The text to display
 * @param	string $return_to The URL to return to when Back is clicked
 * @access public
 * @return string URL to project page
 */
function show_project($project_id, $linktext = '', $return_to = '') {
	global $GO_CONFIG, $GO_SECURITY, $GO_MODULES;

	if ($return_to == '') {
		$return_to = $_SERVER['REQUEST_URI'];
	}

	$link = '';

	$projects_module = isset ($GO_MODULES->modules['projects']) ? $GO_MODULES->modules['projects'] : false;
	if ($projects_module && $projects_module['read_permission']) {
		require_once ($GO_MODULES->modules['projects']['class_path'].'projects.class.inc');
		$projects = new projects();

		if ($project = $projects->get_project($project_id)) {
			if ($linktext == '') {
				$linktext = $project['name'];
			}

			$link = "<a ".$style." href=\"".$projects_module['url']."project.php?project_id=".$project_id."&return_to=".urlencode($return_to)."\" class=\"normal\">";
			$link .= $linktext."</a>\n";
		}
	}
	return $link;
}

/**
 * Return an URL to a contact profile page
 *
 * @param	int $contact_id The contact_id
 * @param	string $linktext The text to display
 * @param	string $return_to The URL to return to when Back is clicked
 * @access public
 * @return string URL to Contact profile page
 */
function show_contact($contact_id, $linktext = '', $return_to = '') {
	global $strShowProfile, $strProtectedUser, $GO_CONFIG, $GO_SECURITY, $GO_MODULES;

	if ($return_to == '') {
		$return_to = $_SERVER['REQUEST_URI'];
	}

	$link = '';

	$ab_module = isset ($GO_MODULES->modules['addressbook']) ? $GO_MODULES->modules['addressbook'] : false;
	if ($ab_module) {
		require_once ($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
		$ab = new addressbook();


		if ($contact = $ab->get_contact($contact_id)) {
			if ($ab->f('color') != '') {
				$style = ' style="color: #'.$ab->f('color').';"';
			} else {
				$style = '';
			}

			if ($linktext == '') {
				$middle_name = $contact['middle_name'] == '' ? '' : $contact['middle_name'].' ';
				$linktext = $contact['first_name'].' '.$middle_name.$contact['last_name'];
			}
				
			if($ab_module['read_permission'] &&
			($GO_SECURITY->has_permission($GO_SECURITY->user_id, $contact['acl_read']) ||
			$GO_SECURITY->has_permission($GO_SECURITY->user_id, $contact['acl_write'])))
			{
				$link = "<a ".$style." href=\"".$ab_module['url']."contact.php?contact_id=".$contact_id."&return_to=".urlencode($return_to)."\" class=\"normal\" title=\"".$strShowProfile."\">";
				$link .= $linktext."</a>\n";
			}else
			{
				$link .= '<span '.$style.'>'.$linktext.'</a>';
			}
		}
	}
	return $link;
}

/**
 * Return an URL to a company profile page
 *
 * @param	int $company_id The company_id
 * @param	string $linktext The text to display
 * @param	string $return_to The URL to return to when Back is clicked
 * @access public
 * @return string URL to company profile page
 */

function show_company($company_id, $linktext = '', $return_to = '') {
	global $strShowProfile, $GO_CONFIG, $GO_SECURITY, $GO_MODULES;

	if ($return_to == '') {
		$return_to = $_SERVER['REQUEST_URI'];
	}

	$link = '';
	$ab_module = isset ($GO_MODULES->modules['addressbook']) ? $GO_MODULES->modules['addressbook'] : false;
	if ($ab_module) {
		require_once ($ab_module['class_path'].'addressbook.class.inc');
		$ab = new addressbook();

		if ($company = $ab->get_company($company_id)) {
			if ($linktext == '') {
				$linktext = $company['name'];
			}

			if($ab_module['read_permission'] &&
			($GO_SECURITY->has_permission($GO_SECURITY->user_id, $company['acl_read']) ||
			$GO_SECURITY->has_permission($GO_SECURITY->user_id, $company['acl_write'])))
			{
				$link = "<a href=\"".$ab_module['url']."company.php?company_id=".$company_id."&return_to=".urlencode($return_to)."\" class=\"normal\" title=\"".$strShowProfile."\">";
				$link .= $linktext."</a>\n";
			}else
			{
				$link .= $linktext;
			}
		}
	}
	return $link;
}

/**
 * Return an URL to a user or contact profile page
 *
 * @param	string $email The e-mail address to search
 * @param	string $linktext The text to display
 * @param	string $return_to The URL to return to when Back is clicked
 * @access public
 * @return string URL to a user profile page
 */
function show_profile_by_email($email, $linktext = '', $return_to = '') {
	global $GO_CONFIG, $GO_SECURITY, $strShowProfile, $strAddContact, $GO_MODULES, $GO_USERS;

	$linktext = ($linktext == '') ? get_name_from_string($email) : $linktext;
	$email = get_email_from_string($email);

	if ($return_to == '') {
		$return_to = $_SERVER['REQUEST_URI'];
	}

	$ab_module = isset ($GO_MODULES->modules['addressbook']) ? $GO_MODULES->modules['addressbook'] : false;
	if ($ab_module) {
		require_once ($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
		$ab = new addressbook();
	}

	if ($ab_module && $contact = $ab->get_contact_by_email($email, $GO_SECURITY->user_id)) {
		if ($contact['color'] != '') {
			$style = ' style="color: #'.$contact['color'].';"';
		} else {
			$style = '';
		}
		if($ab_module['read_permission'] &&
		($GO_SECURITY->has_permission($GO_SECURITY->user_id, $contact['acl_read']) ||
		$GO_SECURITY->has_permission($GO_SECURITY->user_id, $contact['acl_write'])))
		{
			$link = "<a ".$style." href=\"".$GO_MODULES->modules['addressbook']['url']."contact.php?contact_id=".$contact['id']."&return_to=".urlencode($return_to)."\" class=\"normal\" title=\"".$strShowProfile."\">";
			$link .= $linktext."</a>\n";
		}else
		{
			$link = '<span '.$style.'>'.$linktext.'</a>';
		}
	} else {
		$user = $GO_USERS->get_user_by_email($email);

		if ($user && $GO_SECURITY->user_is_visible($GO_SECURITY->user_id)) {
			$link = '<a href="'.$GO_CONFIG->control_url.'user.php?id='.$user['id'].'&return_to='.urlencode($return_to).'" title="'.$strShowProfile.'">';
			$link .= $linktext."</a>\n";
		}	elseif ($ab_module && $ab_module['read_permission']) {
			$name = split_name($linktext);
			$link = "<a href=\"".$ab_module['url']."contact.php?email=".$email."&first_name=".urlencode($name['first'])."&middle_name=".urlencode($name['middle'])."&last_name=".urlencode($name['last'])."&return_to=".urlencode($return_to)."\" class=\"normal\" title=\"".$strAddContact."\">";
			$link .= $linktext."</a>\n";
		} else {
			$link = $linktext;
		}
	}

	return $link;
}

/**
 * Check precence of invalid characters
 *
 * @param	string $input The string to check
 * @param	array $invalid_chars An array of invalid charcters
 * @access public
 * @return bool
 */
function validate_input($input, $invalid_chars = "") {
	if ($invalid_chars == "") {
		$invalid_chars[] = "\"";
		$invalid_chars[] = "/";
		$invalid_chars[] = "?";
		$invalid_chars[] = "&";
	}

	for ($i = 0; $i < count($invalid_chars); $i ++) {
		if (strchr($input, $invalid_chars[$i])) {
			return false;
		}
	}
	return true;
}

/**
 * Format a number by using the user preferences
 *
 * @param	int $number The number
 * @param	int $decimals Number of decimals to display
 * @access public
 * @return string
 */

function format_number($number, $decimals=2, $precision=null)
{
	if(!isset($precision))
	{
		$precision=$decimals;
	}
	$formatted = number_format($number, $precision, $_SESSION['GO_SESSION']['decimal_seperator'], $_SESSION['GO_SESSION']['thousands_seperator']);
	$dec=$precision;
	while(substr($formatted,-1,1)=='0' && $dec>$decimals)
	{
		$dec--;
		$formatted = substr($formatted,0,-1);
	}
	return $formatted;
}

/**
 * Format a number by using the user preferences
 *
 * @param	int $number The number
 * @param	int $decimals Number of decimals to display
 * @access public
 * @return string
 */

function number_to_phpnumber($number)
{
	$number = str_replace($_SESSION['GO_SESSION']['thousands_seperator'],'', $number);
	return str_replace($_SESSION['GO_SESSION']['decimal_seperator'],'.',$number);
}

/**
 * Format a size to a human readable format.
 *
 * @param	int $size The size in bytes
 * @param	int $decimals Number of decimals to display
 * @access public
 * @return string
 */

function format_size($size, $decimals = 1) {
	switch ($size) {
		case ($size > 1073741824) :
			$size = number_format($size / 1073741824, $decimals, $_SESSION['GO_SESSION']['decimal_seperator'], $_SESSION['GO_SESSION']['thousands_seperator']);
			$size .= " GB";
			break;

		case ($size > 1048576) :
			$size = number_format($size / 1048576, $decimals, $_SESSION['GO_SESSION']['decimal_seperator'], $_SESSION['GO_SESSION']['thousands_seperator']);
			$size .= " MB";
			break;

		case ($size > 1024) :
			$size = number_format($size / 1024, $decimals, $_SESSION['GO_SESSION']['decimal_seperator'], $_SESSION['GO_SESSION']['thousands_seperator']);
			$size .= " KB";
			break;

		default :
			number_format($size, $decimals, $_SESSION['GO_SESSION']['decimal_seperator'], $_SESSION['GO_SESSION']['thousands_seperator']);
			$size .= " bytes";
			break;
	}
	return $size;
}

/**
 * Chop long strings with 3 dots
 *
 * Chops of the string after a given length and puts three dots behind it
 * function editted by Tyler Gee to make it chop at whole words
 *
 * @param	string $string The string to chop
 * @param	int $maxlength The maximum number of characters in the string
 * @access public
 * @return string
 */

function cut_string($string, $maxlength, $cut_whole_words = true) {
	if (strlen($string) > $maxlength) {
		$temp = substr($string, 0, $maxlength -3);
		if ($cut_whole_words) {
			if ($pos = strrpos($temp, ' ')) {
				return substr($temp, 0, $pos).'...';
			} else {
				return $temp = substr($string, 0, $maxlength -3).'...';
			}
		} else {
			return $temp.'...';
		}

	} else {
		return $string;
	}
}

/**
 * Convert an enriched formated string to HTML format
 *
 * @param	string $enriched Enriched formatted string
 * @access public
 * @return string HTML formated string
 */
function enriched_to_html($enriched, $convert_links=true) {
	global $GO_CONFIG, $GO_MODULES;

	// We add space at the beginning and end of the string as it will
	// make some regular expression checks later much easier (so we
	// don't have to worry about start/end of line characters)
	$enriched = ' '.$enriched.' ';

	// Get color parameters into a more useable format.
	$enriched = preg_replace('/<color><param>([\da-fA-F]+),([\da-fA-F]+),([\da-fA-F]+)<\/param>/Uis', '<color r=\1 g=\2 b=\3>', $enriched);
	$enriched = preg_replace('/<color><param>(red|blue|green|yellow|cyan|magenta|black|white)<\/param>/Uis', '<color n=\1>', $enriched);

	// Get font family parameters into a more useable format.
	$enriched = preg_replace('/<fontfamily><param>(\w+)<\/param>/Uis', '<fontfamily f=\1>', $enriched);

	// Single line breaks become spaces, double line breaks are a
	// real break. This needs to do <nofill> tracking to be
	// compliant but we don't want to deal with state at this
	// time, so we fake it some day we should rewrite this to
	// handle <nofill> correctly.
	$enriched = preg_replace('/([^\n])\r\n([^\r])/', '\1 \2', $enriched);
	$enriched = preg_replace('/(\r\n)\r\n/', '\1', $enriched);

	// We try to protect against bad stuff here.
	$enriched = @ htmlspecialchars($enriched, ENT_QUOTES);

	// Now convert the known tags to html. Try to remove any tag
	// parameters to stop people from trying to pull a fast one
	$enriched = preg_replace('/(?<!&lt;)&lt;bold.*&gt;(.*)&lt;\/bold&gt;/Uis', '<span style="font-weight: bold">\1</span>', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;italic.*&gt;(.*)&lt;\/italic&gt;/Uis', '<span style="font-style: italic">\1</span>', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;underline.*&gt;(.*)&lt;\/underline&gt;/Uis', '<span style="text-decoration: underline">\1</span>', $enriched);
	$enriched = preg_replace_callback('/(?<!&lt;)&lt;color r=([\da-fA-F]+) g=([\da-fA-F]+) b=([\da-fA-F]+)&gt;(.*)&lt;\/color&gt;/Uis', 'colorize', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;color n=(red|blue|green|yellow|cyan|magenta|black|white)&gt;(.*)&lt;\/color&gt;/Uis', '<span style="color: \1">\2</span>', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;fontfamily&gt;(.*)&lt;\/fontfamily&gt;/Uis', '\1', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;fontfamily f=(\w+)&gt;(.*)&lt;\/fontfamily&gt;/Uis', '<span style="font-family: \1">\2</span>', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;smaller.*&gt;/Uis', '<span style="font-size: smaller">', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;\/smaller&gt;/Uis', '</span>', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;bigger.*&gt;/Uis', '<span style="font-size: larger">', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;\/bigger&gt;/Uis', '</span>', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;fixed.*&gt;(.*)&lt;\/fixed&gt;/Uis', '<font face="fixed">\1</font>', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;center.*&gt;(.*)&lt;\/center&gt;/Uis', '<div align="center">\1</div>', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;flushleft.*&gt;(.*)&lt;\/flushleft&gt;/Uis', '<div align="left">\1</div>', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;flushright.*&gt;(.*)&lt;\/flushright&gt;/Uis', '<div align="right">\1</div>', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;flushboth.*&gt;(.*)&lt;\/flushboth&gt;/Uis', '<div align="justify">\1</div>', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;paraindent.*&gt;(.*)&lt;\/paraindent&gt;/Uis', '<blockquote>\1</blockquote>', $enriched);
	$enriched = preg_replace('/(?<!&lt;)&lt;excerpt.*&gt;(.*)&lt;\/excerpt&gt;/Uis', '<blockquote>\1</blockquote>', $enriched);

	// Now we remove the leading/trailing space we added at the
	// start.
	$enriched = preg_replace('/^ (.*) $/s', '\1', $enriched);

	$module = $GO_MODULES->modules['email'];

	if($convert_links)
	{
		$enriched = preg_replace("/(?:^|\b)(((http(s?):\/\/)|(www\.-))([\w\.-]+)([,:;%#&\/?=\w+\.\-@]+))(?:\b|$)/is", "<a href=\"http$4://$5$6$7\" target=\"_blank\" class=\"blue\">$1</a>", $enriched);
		if ($_SESSION['GO_SESSION']['mail_client'] == 1) {
			$enriched = preg_replace("/(\A|\s)([\w\.\-]+)(@)([\w\.-]+)([A-Za-z]{2,3})\b/i", "\\1<a href=\"javascript:top.main.popup('".$module['url']."send.php?mail_to=\\2\\3\\4\\5','".$GO_CONFIG->composer_width."','".$GO_CONFIG->composer_height."')\" class=\"blue\">\\2\\3\\4\\5</a>", $enriched);
		} else {
			$enriched = preg_replace("/(\A|\s)([\w\.\-]+)(@)([\w\.-]+)([A-Za-z]{2,3})\b/i", "\\1<a href=\"mailto:\\2\\3\\4\\5\" class=\"blue\">\\2\\3\\4\\5</a>", $enriched);
		}
	}

	$enriched = nl2br($enriched);
	$enriched = str_replace("\r", "", $enriched);
	$enriched = str_replace("\n", "", $enriched);

	return $enriched;

}

/**
 * Used by enriched_to_html() to create colors
 *
 * @param	array $colors
 * @access private
 * @return string HTML formatted string
 */
function colorize($colors) {
	for ($i = 1; $i < 4; $i ++) {
		$colors[$i] = sprintf('%02X', round(hexdec($colors[$i]) / 255));
	}
	return '<span style="color: #'.$colors[1].$colors[2].$colors[3].'">'.$colors[4].'</span>';
}

/**
 * Convert plain text to HTML
 *
 * @param	string $text Plain text string
 * @access public
 * @return string HTML formatted string
 */
function text_to_html($text, $convert_links=true) {
	global $GO_CONFIG, $GO_MODULES;

	$module = isset ($GO_MODULES->modules['email']) ? $GO_MODULES->modules['email'] : false;

	$text = htmlspecialchars($text);
	if($convert_links)
	{
		$text = preg_replace("/(?:^|\b)(((http(s?):\/\/)|(www\.-))([\w\.-]+)([,:;%#&\/?=\w+\.\-@]+))(?:\b|$)/is", "<a href=\"http$4://$5$6$7\" target=\"_blank\" class=\"blue\">$1</a>", $text);
		if ($_SESSION['GO_SESSION']['mail_client'] == 1) {
			$text = preg_replace("/(\A|\s)([\w\.\-]+)(@)([\w\.-]+)([A-Za-z]{2,3})\b/i", "\\1<a href=\"javascript:top.main.popup('".$module['url']."send.php?mail_to=\\2\\3\\4\\5','".$GO_CONFIG->composer_width."','".$GO_CONFIG->composer_height."')\" class=\"blue\">\\2\\3\\4\\5</a>", $text);
		} else {
			$text = preg_replace("/(\A|\s)([\w\.\-]+)(@)([\w\.-]+)([A-Za-z]{2,3})\b/i", "\\1<a href=\"mailto:\\2\\3\\4\\5\" class=\"blue\">\\2\\3\\4\\5</a>", $text);
		}
	}
	$text = nl2br($text);
	$text = str_replace("\r", "", $text);
	$text = str_replace("\n", "", $text);

	return ($text);

}

/**
 * Convert Dangerous HTML to safe HTML for display inside of Group-Office
 *
 * This also removes everything outside the body and replaces mailto links
 *
 * @param	string $text Plain text string
 * @access public
 * @return string HTML formatted string
 */
function convert_html($html) {

	$html = str_replace("\r", '', $html);
	$html = str_replace("\n",' ', $html);

	$to_removed_array = array (
	"'<html[^>]*>'si",
	"'</html>'si",
	"'<body[^>]*>'si",
	"'</body>'si",
	"'<meta[^>]*>'si",
	"'<head[^>]*>.*?</head>'si",
	"'<style[^>]*>.*?</style>'si",
	"'<script[^>]*>.*?</script>'si",
	"'<object[^>]*>.*?</object>'si",
	"'<embed[^>]*>.*?</embed>'si",
	"'<applet[^>]*>.*?</applet>'si",
	"'<form[^>]*>'si",
	"'<input[^>]*>'si",
	"'<select[^>]*>.*?</select>'si",
	"'<textarea[^>]*>.*?</textarea>'si",
	"'</form>'si"
	);

	$html = preg_replace($to_removed_array, '', $html);
	$html = preg_replace("/([\"']?)javascript:/i", "$1removed_script:", $html);

	return $html;
}

/**
 * Change HTML links to Group-Office links. For example mailto: links will call
 * the Group-Office e-mail module if installed.
 *
 *
 * @param	string $text Plain text string
 * @access public
 * @return string HTML formatted string
 */

function convert_links($html)
{
	global $GO_CONFIG, $GO_MODULES;

	$html = str_replace("\r", '', $html);
	$html = str_replace("\n",' ', $html);

	$regexp="/<a[^>]*href=([\"']?)(http|https|ftp|bf2)(:\/\/)(.+?)>/i";
	$html = preg_replace($regexp, "<a target=$1_blank$1 class=$1blue$1 href=$1$2$3$4>", $html);

	//$regexp="/<a.+?href=([\"']?)".str_replace('/','\\/', $GO_CONFIG->full_url)."(.+?)>/i";
	//$html = preg_replace($regexp, "<a target=$1main$1 class=$1blue$1 href=$1".$GO_CONFIG->host."$2$3>", $html);

	$html =str_replace($GO_CONFIG->full_url, $GO_CONFIG->host, $html);

	if ($GO_MODULES->modules['email'] && $GO_MODULES->modules['email']['read_permission']) {
		$html = preg_replace("/(href=([\"']?)mailto:)([\w\.\-]+)(@)([\w\.\-\"]+)\b/i",
			"href=\"javascript:top.main.popup('".$GO_MODULES->modules['email'] ['url'].
			"send.php?mail_to=$3$4$5','".$GO_CONFIG->composer_width."','".
		$GO_CONFIG->composer_height."')\" class=\"blue\"", $html);

	}

	return $html;
}

/**
 * Get all e-mail addresses inside a string
 *
 * @param	string $addr The comma seperated string
 * @access public
 * @return array Array of e-mail addresses
 */
function get_addresses_from_string($address_string) {
	$in_address = false;
	$address = '';
	$addresses = array ();
	$length = strlen($address_string);
	for ($i = 0; $i < $length; $i ++) {
		$char = $address_string[$i];
		if ($char == '>') {
			if ($in_address) {
				$in_address = false;
				$addresses[] = trim($address);
				$address = '';
			}
		}
		elseif ($char == '<') {
			$in_address = true;
		}
		elseif ($in_address) {
			$address .= $char;
		}
	}
	return $addresses;
}

/**
 * Get CRLF based on OS (Deprecated
 *
 * @param	string $smtp
 * @access public
 * @return string CRLF
 */
function get_crlf($smtp = "") {
	$crlf = stristr(PHP_OS, 'Windows') ? "\r\n" : "\n";

	if ($smtp != "")
	$crlf = $smtp ? "\r\n" : $crlf;

	//return ($crlf);
	return "\r\n";
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

/**
 * Quotes a string with >
 *
 * @param	string $text
 * @access public
 * @return string A string quoted with >
 */
function quote($text) {
	$text = "> ".ereg_replace("\n", "\n> ", trim($text));
	return ($text);
}

/**
 * Sends an e-mail using the global mail settings from Group-Office.php
 *
 * @param	string $email_to The e-mail reciepent
 * @param	string $email_from The e-mail sender
 * @param	string $subject The e-mail subject
 * @param	string $body The e-mail body
 * @param	string $priority The priotity of the e-mail message
 * @param	string $body_ctype The the mime type of the body
 (text/PLAIN or text/HTML)
 * @access public
 * @return bool Returns true if the message was successfully sent
 */
function sendmail($email_to, $email_from, $name_from, $subject, $body, $priority = '3', $body_ctype = 'text/PLAIN') {
	global $GO_CONFIG, $GO_MODULES, $GO_SECURITY, $charset, $php_mailer_lang;

	require_once ($GO_CONFIG->class_path."mail/phpmailer/class.phpmailer.php");
	require_once ($GO_CONFIG->class_path."mail/phpmailer/class.smtp.php");
	require_once ($GO_CONFIG->class_path."html2text.class.inc");

	$mail = new PHPMailer();
	$mail->PluginDir = $GO_CONFIG->class_path.'mail/phpmailer/';
	$mail->SetLanguage($php_mailer_lang, $GO_CONFIG->class_path.'mail/phpmailer/language/');

	switch ($GO_CONFIG->mailer) {
		case 'smtp' :
			$mail->Host = $GO_CONFIG->smtp_server;
			$mail->Port = $GO_CONFIG->smtp_port;
				
			if(!empty($GO_CONFIG->smtp_username))
			{
				$mail->SMTPAuth=true;
				$mail->Username=$GO_CONFIG->smtp_username;
				$mail->Password = $GO_CONFIG->smtp_password;
			}
				
			$mail->IsSMTP();
			break;
		case 'qmail' :
			$mail->IsQmail();
			break;
		case 'sendmail' :
			$mail->IsSendmail();
			break;
		case 'mail' :
			$mail->IsMail();
			break;
	}
	$mail->Priority = $priority;
	$mail->Sender = $email_from;
	$mail->From = $email_from;
	$mail->FromName = $name_from;
	$mail->AddReplyTo($email_from, $name_from);
	$mail->WordWrap = 76;
	$html_message = strtolower($body_ctype) == 'text/html' ? true : false;
	$mail->IsHTML($html_message);
	$mail->Subject = smart_stripslashes(trim($subject));

	require_once ($GO_CONFIG->class_path.'mail/RFC822.class.inc');
	$RFC822 = new RFC822();

	$mail_to_array = $RFC822->parse_address_list($email_to);

	foreach ($mail_to_array as $to_address) {
		$mail->AddAddress($to_address['email'], $to_address['personal']);
	}

	if(isset($GO_MODULES->modules['email']) && $GO_MODULES->modules['email']['read_permission'])
	{
		require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc');
		$email= new email();
		$em_settings = $email->get_settings($GO_SECURITY->user_id);
		$mail_charset = $em_settings['charset'];
	}else
	{
		$mail_charset = $charset;
	}

	if(function_exists('iconv') && $mail_charset != $charset)
	{
		$mail->CharSet=$mail_charset;
	}

	if ($html_message) {
		if($mail->CharSet!=$charset)
		{
			$html_mail_head = str_replace($charset, $mail->CharSet, $html_mail_head);
		}
		$mail->Body = smart_stripslashes($body);
		$h2t = & new html2text($body);
		$mail->AltBody = $h2t->get_text();

	} else {
		$mail->Body = smart_stripslashes($body);
	}

	if($mail->CharSet!=$charset)
	{
		$mail->recode($charset);
	}

	return $mail->Send();
}

/**
 * returns a link poping up a open file dialog
 *
 * A handler file needs to be created to do something with the file to open
 *
 * @param	string $GO_HANDLER The handler file to submit to
 * @param	string $GO_FILTER Filter certain file types to display
 * @param	string $link_text The text to display in the link
 * @param	string $class CSS class file to use in the link
 * @param	bool $full_link Return a full link or retrun only an URL.
 * @access public
 * @return string URL or link
 */
function show_open($GO_HANDLER, $GO_FILTER, $link_text = "", $class = "normal", $fulllink = true) {
	global $GO_CONFIG, $GO_MODULES;

	$module = $GO_MODULES->modules['filesystem'];

	if ($fulllink) {
		$link = "<a class=\"".$class."\" href=\"javascript:popup('".$module['url']."index.php?GO_HANDLER=".urlencode($GO_HANDLER)."&GO_FILTER=".$GO_FILTER."&mode=popup','600','400')\">".$link_text."</a>";
	} else {
		$link = "javascript:popup('".$module['url']."index.php?GO_HANDLER=".urlencode($GO_HANDLER)."&GO_FILTER=".$GO_FILTER."&mode=popup','600','400')";
	}
	return $link;
}

/**
 * returns a link poping up a save file dialog
 *
 * A temporarely file to save must be passed
 *
 * @param	string $tmp_file The temporary file location
 * @param	string $link_text The text to display in the link
 * @param	string $class CSS class file to use in the link
 * @param	bool $full_link Return a full link or retrun only an URL.
 * @access public
 * @return string URL or link
 */

function show_save($tmpfile, $link_text = "", $class = "normal", $fulllink = true) {
	global $GO_CONFIG, $GO_MODULES;

	$module = $GO_MODULES->modules['filesystem'];

	if ($fulllink) {
		$link = "<a class=\"".$class."\" href=\"javascript:popup('".$module['url']."save.php?SET_HANDLER=".urlencode($tmpfile)."','600','400')\">".$link_text."</a>";
	} else {
		$link = "javascript:popup('".$module['url']."save.php?GO_HANDLER=".urlencode($tmpfile)."','400','450')";
	}
	return $link;
}

/**
 * Return a filename without extension
 *
 * @param	string $filename The complete filename
 * @access public
 * @return string A filename without the extension
 */
function strip_extension($filename) {
	$pos = strrpos($filename, '.');
	if ($pos) {
		$filename = substr($filename, 0, $pos);
	}
	return $filename;
}

/**
 * Returns the extension of a filename
 *
 * @param	string $filename The complete filename
 * @access public
 * @return string  The extension of a filename
 */
function get_extension($filename) {
	$extension = '';
	$pos = strrpos($filename, '.');
	if ($pos) {
		$extension = substr($filename, $pos +1, strlen($filename));
	}
	return strtolower($extension);
}

/**
 * Returns an array with RGB color (e.g. R128 G126 B124) from a hex html color code (e.g. #F7F7F7)
 *
 * @param  string $html_color HTML color code: e.g. #FFFFFF
 * @access public
 * @return array RGB color code: e.g. R:102 G:102 B:102
 */
function hex2dec($html_color) {
	$R = hexdec(substr($html_color, 1, 2));
	$G = hexdec(substr($html_color, 3, 2));
	$B = hexdec(substr($html_color, 5, 2));
	$color = array ();
	$color['R'] = $R;
	$color['G'] = $G;
	$color['B'] = $B;
	return $color;
}


function mime_content_type_by_extension($extension) {
	global $GO_CONFIG;
	$file = file($GO_CONFIG->mime_types_file);
	foreach ($file as $line) {
		rtrim($line);
		if (preg_match('/^\#/', $line))
		continue;
		$elms = preg_split('/\s+/', $line);
		$mime = array_shift($elms);
		foreach ($elms as $elm) {
			if ($elm == $extension) {
				return $mime;
			}
		}
	}
	return 'application/OCTET-STREAM';
}

function enc_utf8($str) {
	//some mail clients create encoded strings such: =?iso-8859-1?Q? "Andr=E9=20Mc=20Intyre" ?=
	//containing space values inside, but they mustn't. The space values have to be removed before
	//they are going to be converted to utf8.

	if (preg_match("/=\?/", $str)) {
		//$str = str_replace(" ", "", $str);
		return imap_utf8($str);
	}else {
		if (function_exists('iconv'))
		{
			if($converted = @iconv('ISO-8859-15', $GLOBALS['charset'].'//IGNORE', $str))
			{
				return $converted;
			}
		}
	}
	return $str;

	/*
	 $text = '';
	 if($elements = imap_mime_header_decode($str))
	 {
		foreach($elements as $element)
		{
		$text .= $element->text;
		}
		return utf8_encode($text);
		}	*/
}
/**
 * Formats a name in Group-Office
 *
 * @param string $sort_name string Vlaue can be last_name or first_name
 * @return string base64 encoded string
 */
function format_name($last, $first = '', $middle = '', $sort_name='') {

	$sort_name = $sort_name == '' ? $_SESSION['GO_SESSION']['sort_name'] : $sort_name;

	if ($sort_name== 'last_name') {
		$name = 	!empty ($last) ? $last : '';
		if(!empty($last) && !empty($first))
		{
			$name .= ', ';
		}
		$name .= !empty ($first) ? $first : '';
		$name .= !empty ($middle) ? ' '.$middle : '';
	} else {
		$name = !empty ($first) ? $first : ' ';
		$name .= !empty ($middle) ? ' '.$middle.' ' : ' ';
		$name .= $last;
	}

	return $name;
}

/**
 * Converts string from base64
 *
 * @param string $s string
 * @return string base64 encoded string
 */
function decodeBASE64($string) {
	$B64Values = array(
            'A' =>  0, 'B' =>  1, 'C' =>  2, 'D' =>  3, 'E' =>  4, 'F' =>  5,
            'G' =>  6, 'H' =>  7, 'I' =>  8, 'J' =>  9, 'K' => 10, 'L' => 11,
            'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17,
            'S' => 18, 'T' => 19, 'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23,
            'Y' => 24, 'Z' => 25,
            'a' => 26, 'b' => 27, 'c' => 28, 'd' => 29, 'e' => 30, 'f' => 31,
            'g' => 32, 'h' => 33, 'i' => 34, 'j' => 35, 'k' => 36, 'l' => 37,
            'm' => 38, 'n' => 39, 'o' => 40, 'p' => 41, 'q' => 42, 'r' => 43,
            's' => 44, 't' => 45, 'u' => 46, 'v' => 47, 'w' => 48, 'x' => 49,
            'y' => 50, 'z' => 51,
            '0' => 52, '1' => 53, '2' => 54, '3' => 55, '4' => 56, '5' => 57,
            '6' => 58, '7' => 59, '8' => 60, '9' => 61, '+' => 62, ',' => 63
	);
	$p = 0;
	$d = '';
	$unicodeNullByteToggle = 0;
	for ($i = 0, $len = strlen($string); $i < $len; $i++) {
		$c = $string[$i];
		if ($p == 0) {
			$t = $B64Values[$c];
			$p = 1;
		} elseif ($p == 1) {
			if ($unicodeNullByteToggle) {
				$d = $d . chr(($t << 2) + (($B64Values[$c] & 48) >> 4));
				$unicodeNullByteToggle = 0;
			} else {
				$unicodeNullByteToggle = 1;
			}
			$t = ($B64Values[$c] & 15);
			$p = 2;
		} elseif ($p == 2) {
			if ($unicodeNullByteToggle) {
				$d = $d . chr(($t << 4) + (($B64Values[$c] & 60) >> 2));
				$unicodeNullByteToggle = 0;
			} else {
				$unicodeNullByteToggle = 1;
			}
			$t = ($B64Values[$c] & 3);
			$p = 3;
		} elseif ($p == 3) {
			if ($unicodeNullByteToggle) {
				$d = $d . chr(($t << 6) + $B64Values[$c]);
				$unicodeNullByteToggle = 0;
			} else {
				$unicodeNullByteToggle = 1;
			}
			$t = ($B64Values[$c] & 3);
			$p = 0;
		}
	}
	return $d;
}
/**
 * Converts string to base64
 * @param string $s string
 * @return string base64 encoded string
 */
function encodeBASE64($string) {
	$B64Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+,';
	$p = 0;     // phase: 1 / 2 / 3 / 1 / 2 / 3...
	$e = '';    // base64-encoded string
	//foreach($s as $c) {
	for ($i = 0; $i < strlen($string); $i++) {
		$c = $string[$i];
		if ($p == 0) {
			$e = $e . substr($B64Chars, ((ord($c) & 252) >> 2), 1);
			$t = (ord($c) & 3);
			$p = 1;
		} elseif ($p == 1) {
			$e = $e . $B64Chars[($t << 4) + ((ord($c) & 240) >> 4)];
			$t = (ord($c) & 15);
			$p = 2;
		} elseif ($p == 2) {
			$e = $e . $B64Chars[($t << 2) + ((ord($c) & 192) >> 6)];
			$e = $e . $B64Chars[ord($c) & 63];
			$p = 0;
		}
	}
	//
	// flush buffer
	//
	if ($p == 1) {
		$e = $e . $B64Chars[$t << 4];
	} elseif ($p == 2) {
		$e = $e . $B64Chars[$t << 2];
	}
	return $e;
}

function encode_imap_path($str, $delimiter='.')
{
	$encoded = array();
	$mailboxes = explode($delimiter, $str);
	foreach($mailboxes as $mailbox)
	{
		$encoded[]=$this->utf7_encode($mailbox);
	}
	return implode($delimiter, $encoded);
}

function decode_imap_path($str, $delimiter='.')
{
	$decoded = array();
	$mailboxes = explode($delimiter, $str);
	foreach($mailboxes as $mailbox)
	{
		$decoded[]=$this->utf7_decode($mailbox);
	}
	return implode($delimiter, $decoded);
}

function utf7_imap_encode($input)
{
	global $charset;
	if (extension_loaded('iconv')) {
		if($converted_input = iconv($charset, 'UTF-7', $input))
		{
			$input=preg_replace("/&/","&-",$converted_input);
			$input=preg_replace("/\+A/","&A",$input);
			$input=preg_replace("/&ACY /","&- ",$input);
			$input=preg_replace("/&ACY/","&",$input);
			$input=preg_replace("/&AF8(-)?/","_",$input);
			$input=preg_replace("/&ADs(-)?/",";",$input);
			return $input;
		}
	}
	return @imap_utf7_encode(utf8_decode($input));
}



function utf7_imap_decode($input)
{
	global $charset;

	if (extension_loaded('iconv')) {
		$input=preg_replace("/&A/","+A",$input);
		$input=preg_replace("/&-/","&",$input);
			
		if($converted_input = iconv('UTF-7', $charset, $input))
		{
			return $converted_input;
		}
	}
	return utf8_encode(@imap_utf7_decode($input));
}
