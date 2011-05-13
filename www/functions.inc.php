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


function ini_return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}


function load_standard_info_panel_items(&$response, $link_type) {
	global $GO_CONFIG, $GO_MODULES, $GO_SECURITY;

	$hidden_sections = json_decode($_POST['hidden_sections'], true);
	
	require_once($GO_CONFIG->class_path . '/base/search.class.inc.php');
	$search = new search();

	if (/*!in_array('links', $hidden_sections) && */!isset($response['data']['links'])) {
		$links_json = $search->get_latest_links_json($GO_SECURITY->user_id, $response['data']['id'], $link_type);
		$response['data']['links'] = $links_json['results'];
	}

	if (/*isset($GO_MODULES->modules['tasks']) && !in_array('tasks', $hidden_sections) &&*/ !isset($response['data']['tasks'])) {
		require_once($GO_MODULES->modules['tasks']['class_path'] . 'tasks.class.inc.php');
		$tasks = new tasks();

		$response['data']['tasks'] = $tasks->get_linked_tasks_json($response['data']['id'], $link_type);
	}

	if (isset($GO_MODULES->modules['calendar'])/* && !in_array('events', $hidden_sections)*/) {
		require_once($GO_MODULES->modules['calendar']['class_path'] . 'calendar.class.inc.php');
		$cal = new calendar();

		$response['data']['events'] = $cal->get_linked_events_json($response['data']['id'], $link_type);
	}

	if (/*!in_array('files', $hidden_sections) && */!isset($response['data']['files'])) {
		if (isset($GO_MODULES->modules['files'])) {
			require_once($GO_MODULES->modules['files']['class_path'] . 'files.class.inc.php');
			$files = new files();

			$response['data']['files'] = $files->get_content_json($response['data']['files_folder_id']);
		} else {
			$response['data']['files'] = array();
		}
	}


	if (/*!in_array('comments', $hidden_sections) && */isset($GO_MODULES->modules['comments']) && !isset($response['data']['comments'])) {
		require_once ($GO_MODULES->modules['comments']['class_path'] . 'comments.class.inc.php');
		$comments = new comments();

		$response['data']['comments'] = $comments->get_comments_json($response['data']['id'], $link_type);
	}

	if($GO_MODULES->has_module('customfields') && !isset($response['data']['customfields']))
	{
		require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
		$cf = new customfields();
		$response['data']['customfields']=$cf->get_all_fields_with_values($GO_SECURITY->user_id, $link_type, $response['data']['id']);
	}

}

/**
 * Function array_insert().
 *
 * Returns the new number of the elements in the array.
 *
 * @param array $array Array (by reference)
 * @param mixed $value New element
 * @param int $offset Position
 * @return int
 */
function array_insert(&$array, $value, $offset)
{
    if (is_array($array)) {
        $array  = array_values($array);
        $offset = intval($offset);
        if ($offset < 0 || $offset >= count($array)) {
            array_push($array, $value);
        } elseif ($offset == 0) {
            array_unshift($array, $value);
        } else {
            $temp  = array_slice($array, 0, $offset);
            array_push($temp, $value);
            $array = array_slice($array, $offset);
            $array = array_merge($temp, $array);
        }
    } else {
        $array = array($value);
    }
    return count($array);
}

function set_multiselectgrid_selections($key, $ids, $user_id){
	global $GO_CONFIG;
	$value = is_array($ids) ? implode(',', $ids) : $ids;

	$GO_CONFIG->save_setting('msg_'.$key,$value, $user_id);
}

/*
 * Get's and saves the selected id's from a MultiSelectGrid
 */

function get_multiselectgrid_selections($key){
	global $GO_CONFIG, $GO_SECURITY;

	
	if(isset($_POST[$key]))
	{
		$ids = json_decode($_POST[$key], true);
		$GO_CONFIG->save_setting('msg_'.$key,implode(',', $ids), $GO_SECURITY->user_id);
	}else
	{
		$ids = $GO_CONFIG->get_setting('msg_'.$key, $GO_SECURITY->user_id);
		$ids = $ids ? explode(',',$ids) : array();
	}
	return $ids;
}

function in_multiselectgrid_selection($target,$key) {
	global $GO_CONFIG, $GO_SECURITY;
	$array = get_multiselectgrid_selections($key);
	return in_array($target,$array);
}

/**
 *
 * @global <type> $GO_CONFIG
 * @param <type> $module The name of the module
 * @param <type> $function the name of the function GO.<module>.<function> must be created
 * @param <type> $params Array of parameters. The javascript function will be called like foo(arr[0],arr[1],arr[2], etc){}
 * @param <type> $loadevent Call this function before Group-Office renders with ready or after with render
 * @return <type> An URL that can call a Group-Office function directly
 */


function create_direct_url($module, $function, $params, $loadevent='render')
{
	global $GO_CONFIG;

	return $GO_CONFIG->orig_full_url.'dialog.php?e='.$loadevent.'&m='.$module.'&f='.$function.'&p='.urlencode(base64_encode(json_encode($params)));
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
	if(isset($GO_CONFIG)){
		$cls = $GO_CONFIG->class_path. $class_name.'.class.inc.php';
		if(file_exists($cls))
			require_once $cls;
	}
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

function go_infolog($message){
	global $GO_CONFIG;
	if ($GO_CONFIG->log) {
		
		if(empty($_SESSION['GO_SESSION']['logdircheck'])){
			File::mkdir($GO_CONFIG->file_storage_path.'log');
			$_SESSION['GO_SESSION']['logdircheck']=true;
		}
		
		$msg = '['.date('Y-m-dm G:i').']';
		
		if(!empty($_SESSION['GO_SESSION']['username'])){
			$msg .= '['.$_SESSION['GO_SESSION']['username'].'] ';
		}
		
		$msg.= $message;
		
		@file_put_contents($GO_CONFIG->file_storage_path.'log/info.log', $msg."\n", FILE_APPEND);
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

	if($config->debug || $config->debug_log)
	{
		if(!is_string($text))
		{
			$text = var_export($text, true);
		}

		if($text=='')
			$text = '(empty string)';

//		if(PHP_SAPI=='cli')
//		{
//			echo 'DEBUG: '.$text."\n\n";
//			return;
//		}

		if(!isset($_SESSION['GO_SESSION']['debug_log']))
		$_SESSION['GO_SESSION']['debug_log']=$config->file_storage_path.'debug.log';

		file_put_contents($_SESSION['GO_SESSION']['debug_log'], $text."\n", FILE_APPEND);
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

function get_thumb_dir(){
	global $GO_CONFIG;

	require_once($GO_CONFIG->class_path.'base/theme.class.inc.php');
	$GO_THEME = new GO_THEME();


	if(is_dir($GO_THEME->theme_path.'images/128x128/filetypes/')){
		$dir = $GO_THEME->image_path.'128x128/filetypes/';
		$url = $GO_THEME->image_url.'128x128/filetypes/';
	}else{
		$dir = $GO_CONFIG->theme_path.'Default/images/128x128/filetypes/';
		$url = $GO_CONFIG->theme_url.'Default/images/128x128/filetypes/';
	}

	return array('url'=>$url,'dir'=>$dir);
}

function get_thumb_url($path, $w=100,$h=100,$zc=1) {
		global $GO_CONFIG;

		require_once($GO_CONFIG->class_path.'base/theme.class.inc.php');
		$GO_THEME = new GO_THEME();


		$extension = File::get_extension($path);

		$arr = get_thumb_dir();

		$url = $arr['url'];
		$dir = $arr['dir'];
		

		switch($extension) {
			case 'ico':
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			case 'xmind':
				return $GO_CONFIG->control_url.'thumb.php?src='.urlencode($path).'&w='.$w.'&h='.$h.'&zc='.$zc.'&filemtime='.@filemtime($GO_CONFIG->file_storage_path.$path);
				break;

			case 'pdf':
				return $url.'pdf.png';
				break;

			case 'tar':
			case 'tgz':
			case 'gz':
			case 'bz2':
			case 'zip':
				return $url.'zip.png';
				break;
			case 'odt':
			case 'docx':
			case 'doc':
				return $url.'doc.png';
				break;

			case 'odc':
			case 'ods':
			case 'xls':
			case 'xlsx':
				return $url.'spreadsheet.png';
				break;

			case 'odp':
			case 'pps':
			case 'pptx':
			case 'ppt':
				return $url.'pps.png';
				break;
			case 'eml':
				return $url.'message.png';
				break;

			case 'htm':
				return $url.'doc.png';
				break;

			case 'log':
				return $url.'txt.png';
				break;

			default:
				if(file_exists($dir.$extension.'.png')) {
					return $url.$extension.'.png';
				}else {
					return $url.'unknown.png';
				}
				break;

		}
}