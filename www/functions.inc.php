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


function create_direct_url($module, $function, $params){
	global $GO_CONFIG;

	return $GO_CONFIG->full_url.'dialog.php?module='.$module.'&function='.$function.'&params='.urlencode(base64_encode(json_encode($params)));

}


/**
 * This file holds global functions for use inside Group-Office
 *
 * @package go.global
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @since    Group-Office 1.0
 */


 function google_maps_link($address, $address_no, $city, $country){
	 $l='';

	if(!empty($address) && !empty($city))
	{
		$l .= $address;
		if(!empty($address_no)){
			$l .= ' '.$address_no.', '.$city;
		}else
		{
			$l .= ', '.$city;
		}

		if(!empty($country)){
			$l .= ', '.$country;
		}

		return 'http://maps.google.com/maps?q='.urlencode($l);
	}else
	{
		return false;
	}
 }


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
	$_SESSION['GO_SESSION']['debug_log']=$file;
}

/**
 * Write's to a debug log.
 *
 * @param string $text log entry
 */

function go_debug($text, $config=false)
{

	if(!$config)
	$config=$GLOBALS['GO_CONFIG'];

	if($config->debug)
	{
		if(!is_string($text))
		{
			$text = var_export($text, true);
		}

		if(!isset($_SESSION['GO_SESSION']['debug_log']))
		$_SESSION['GO_SESSION']['debug_log']=$config->file_storage_path.'debug.log';

		//$text = '['.date('Y-m-d G:i').'] '.$text;


		file_put_contents($_SESSION['GO_SESSION']['debug_log'], $text."\n", FILE_APPEND);

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
	if (preg_match("'msie ([0-9].[0-9]{1,2})'i", $_SERVER['HTTP_USER_AGENT'], $log_version)) {
		$browser['version'] = $log_version[1];
		$browser['name'] = 'MSIE';
	}
	elseif (preg_match("'opera/([0-9].[0-9]{1,2})'i", $_SERVER['HTTP_USER_AGENT'], $log_version)) {
		$browser['version'] = $log_version[1];
		$browser['name'] = 'OPERA';
	}
	elseif (preg_match("'mozilla/([0-9].[0-9]{1,2}).*gecko/([0-9]+)'i", $_SERVER['HTTP_USER_AGENT'], $log_version)) {
		$browser['version'] = $log_version[1];
		$browser['name'] = 'MOZILLA';
		$browser['subversion'] = $log_version[2];
	}
	elseif (preg_match("'netscape/([0-9].[0-9]{1,2})'i", $_SERVER['HTTP_USER_AGENT'], $log_version)) {
		$browser['version'] = $log_version[1];
		$browser['name'] = 'NETSCAPE';
	}
	elseif (preg_match("'safari/([0-9]+.[0-9]+)'i", $_SERVER['HTTP_USER_AGENT'], $log_version)) {
		$browser['version'] = $log_version[1];
		$browser['name'] = 'SAFARI';
	} else {
		$browser['version'] = 0;
		$browser['name'] = 'OTHER';
	}
	return $browser;
}


function get_thumb_url($path, $w=100,$h=100,$zc=1) {
		global $GO_THEME, $GO_CONFIG;

		$extension = File::get_extension($path);

		switch($extension) {
			case 'jpg':
			case 'jpeg';
			case 'png';
			case 'gif';
				return $GO_CONFIG->control_url.'thumb.php?src='.urlencode($path).'&w='.$w.'&h='.$h.'&zc='.$zc.'&filemtime='.filemtime($GO_CONFIG->file_storage_path.$path);
				break;

			case 'pdf':
				return $GO_THEME->image_url.'128x128/filetypes/pdf.png';
				break;

			case 'tar':
			case 'tgz':
			case 'gz':
			case 'bz2':
			case 'zip':
				return $GO_THEME->image_url.'128x128/filetypes/zip.png';
				break;
			case 'odt':
			case 'docx':
			case 'doc':
				return $GO_THEME->image_url.'128x128/filetypes/doc.png';
				break;

			case 'odc':
			case 'ods':
			case 'xls':
			case 'xlsx':
				return $GO_THEME->image_url.'128x128/filetypes/spreadsheet.png';
				break;

			case 'odp':
			case 'pps':
			case 'pptx':
			case 'ppt':
				return $GO_THEME->image_url.'128x128/filetypes/pps.png';
				break;

			case 'htm':
				return $GO_THEME->image_url.'128x128/filetypes/doc.png';
				break;

			default:
				if(file_exists($GO_THEME->theme_path.'images/128x128/filetypes/'.$extension.'.png')) {
					return $GO_THEME->image_url.'128x128/filetypes/'.$extension.'.png';
				}else {
					return $GO_THEME->image_url.'128x128/filetypes/unknown.png';
				}
				break;

		}
}