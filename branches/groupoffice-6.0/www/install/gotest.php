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

$product_name = class_exists('GO') ? \GO::config()->product_name : 'Group-Office';

/**
* Format a size to a human readable format.
* 
* @param	int $size The size in bytes
* @param	int $decimals Number of decimals to display
* @access public
* @return string
*/

if(!function_exists('format_size'))
{
	function format_size($size, $decimals = 1) {
		switch ($size) {
			case ($size > 1073741824) :
				$size = number_format($size / 1073741824, $decimals, '.', ' ');
				$size .= " GB";
				break;
	
			case ($size > 1048576) :
				$size = number_format($size / 1048576, $decimals, '.', ' ');
				$size .= " MB";
				break;
	
			case ($size > 1024) :
				$size = number_format($size / 1024, $decimals, '.', ' ');
				$size .= " KB";
				break;
	
			default :
				number_format($size, $decimals, '.', ' ');
				$size .= " bytes";
				break;
		}
		return $size;
	}
}

function ini_is_enabled($name){
	$v = ini_get($name);
	
	return $v==1 || strtolower($v)=='on';
}

function test_system(){

	global $product_name;
	
	$tests=array();
	
	
	$test['name']='Expose PHP';
	$test['pass']=!ini_is_enabled('expose_php');
	$test['feedback']='Warning. You should set expose php to off to prevent version information to be public';
	$test['fatal']=false;
	$tests[]=$test;
	
	
	$test['name']='PHP version';
	$test['pass']=function_exists('version_compare') && version_compare( phpversion(), "5.3", ">=");
	$test['feedback']='Fatal error: Your PHP version is too old to run '.$product_name.'. PHP 5.3 or higher is required';
	$test['fatal']=true;

//	$tests[]=$test;
//	$test['name']='Session Cookies';
//	$test['pass']=ini_get('session.cookie_httponly')!=1;
//	$test['feedback']='Warning: session.cookie_httponly is set to 1. We recommend setting it to 0 because this can give problems with file uploads.';
//	$test['fatal']=false;

	$tests[]=$test;

	$test['name']='Output buffering';
	$test['pass']=!ini_is_enabled('output_buffering');
	$test['feedback']='Warning: output_buffering is enabled. This will increase memory usage might cause memory errors';
	$test['fatal']=false;

	$tests[]=$test;

	//echo ini_get('mbstring.func_overload');

	$test['name']='mbstring function overloading';
	$test['pass']=ini_get('mbstring.func_overload')<1;
	$test['feedback']='Warning: mbstring.func_overload is enabled in php.ini. Encrypting e-mail passwords will be disabled with this feature enabled. Disabling this feature is recommended';
	$test['fatal']=false;

	$tests[]=$test;


	$test['name']='Magic quotes setting';
	$test['pass']=!get_magic_quotes_gpc();
	$test['feedback']='Warning: magic_quotes_gpc is enabled. You will get better performance if you disable this setting.';
	$test['fatal']=false;

	$tests[]=$test;

	$test['name']='MySQL support';
	$test['pass']=function_exists('mysqli_connect');
	$test['feedback']='Fatal error: The improved MySQL (MySQLi) extension is required. So is the MySQL server.';
	$test['fatal']=true;

	$tests[]=$test;
	
	$test['name']='PDO support';
	$test['pass']=  class_exists('PDO') && extension_loaded('pdo_mysql');
	$test['feedback']='Fatal error: The PHP PDO extension with MySQL support is required.';
	$test['fatal']=true;

	$tests[]=$test;

	$test['name']='Mcrypt support';
	$test['pass']=extension_loaded('mcrypt');
	$test['feedback']='Warning: No Mcrypt extension for PHP found. Without mcrypt Group-Office has to save e-mail passwords in plain text.';
	$test['fatal']=false;

	$tests[]=$test;

	$test['name']='GD support';
	$test['pass']=function_exists('getimagesize');
	$test['feedback']='Warning: No GD extension for PHP found. Without GD Group-Office can\'t create thumbnails.';
	$test['fatal']=false;

	$tests[]=$test;

	$test['name']='pspell support';
	$test['pass']=function_exists('pspell_new');
	$test['feedback']='Warning: No pspell extension for PHP found. The spellchecker in the e-mail composer won\'t work.';
	$test['fatal']=false;

	$tests[]=$test;


	$test['name']='File upload support';
	$test['pass']=ini_is_enabled('file_uploads');
	$test['feedback']='Warning: File uploads are disabled. Please set file_uploads=On in php.ini.';
	$test['fatal']=false;

	$tests[]=$test;

	$test['name']='Safe mode';
	$test['pass']=!ini_is_enabled('safe_mode');
	$test['feedback']='Warning: safe_mode is enabled in php.ini. This may cause trouble with the filesystem module and Synchronization. If you can please set safe_mode=Off in php.ini';
	$test['fatal']=false;

	$tests[]=$test;

	$test['name']='Open base_dir';
	$test['pass']=ini_get('open_basedir')=='';
	$test['feedback']='Warning: open_basedir is enabled. This may cause trouble with the filesystem module and Synchronization.';
	$test['fatal']=false;

	$tests[]=$test;

//	$test['name']='URL fopen';
//	$test['pass']=ini_is_enabled('allow_url_fopen');
//	$test['feedback']='Warning: allow_url_fopen is disabled in php.ini. RSS feeds on the start page will not work.';
//	$test['fatal']=false;

//	$tests[]=$test;
	
	$test['name']='Register globals';
	$test['pass']=!ini_is_enabled('register_globals');
	$test['feedback']='Warning: register_globals is enabled in php.ini. This causes a problem in the spell checker and probably in some other parts. It\'s recommended to disable this.';
	$test['fatal']=false;

	$tests[]=$test;	

	$test['name']='zlib compression';
	$test['pass']=extension_loaded('zlib');
	$test['feedback']='Warning: No zlib output compression support. You can increase the initial load time by installing this php extension.';
	$test['fatal']=false;

	$tests[]=$test;
	
	$test['name']='Calendar functions';
	$test['pass']=function_exists('easter_date');
	$test['feedback']='Warning: Calendar functions not available. The '.$product_name.' calendar won\'t be able to generate all holidays for you. Please compile PHP with --enable-calendar.';
	$test['fatal']=false;

	$memory_limit = return_bytes(ini_get('memory_limit'));
	$tests[]=$test;
	$test['name']='Memory limit';
	$test['pass']=$memory_limit>=64*1024*1024;
	$test['feedback']='Warning: Your memory limit setting ('.format_size($memory_limit).') is less than 64MB. It\'s recommended to allow at least 64 MB.';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='Error logging';
	$test['pass']=ini_is_enabled('log_errors');
	$test['feedback']='Warning: PHP error logging is disabled in php.ini. It\'s recommended that this feature is enabled in a production environment.';
	$test['fatal']=false;

	/*$tests[]=$test;
	$test['name']='Error display';
	$test['pass']=ini_get('display_errors')!='1';
	$test['feedback']='Warning: PHP error display is enabled in php.ini. It\'s recommended that this feature is disabled because it can cause unnessecary interface crashes.';
	$test['fatal']=false;*/

	$tests[]=$test;
	$test['name']='libwbxml';
	if(class_exists('GO'))
	{
		$wbxml2xml = whereis('wbxml2xml') ? whereis('wbxml2xml') : \GO::config()->cmd_wbxml2xml;
		$xml2wbxml = whereis('xml2wbxml') ? whereis('xml2wbxml') : \GO::config()->cmd_xml2wbxml;
	}else
	{
		$wbxml2xml = whereis('wbxml2xml') ? whereis('wbxml2xml') : '/usr/bin/wbxml2xml';
		$xml2wbxml = whereis('xml2wbxml') ? whereis('xml2wbxml') : '/usr/bin/xml2wbxml';
	}
	$test['pass']=@is_executable($wbxml2xml) && @is_executable($xml2wbxml);
	$test['feedback']='Warning: libwbxml2 is not installed. SyncML sync will not work!';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='DOM functions';
	$test['pass']=class_exists('DOMDocument', false);
	$test['feedback']='Warning: DOM functions are not installed. Synchronization with SyncML will not work. Install php-xml';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='MultiByte string functions';
	$test['pass']=function_exists('mb_detect_encoding');
	$test['feedback']='Warning: php-mbstring is not installed. Problems with non-ascii characters in e-mails and filenames might occur.';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='TAR Compression';
	if(class_exists('GO'))
	{
		$tar = whereis('tar') ? whereis('tar') : \GO::config()->cmd_tar;
	}else
	{
		$tar = whereis('tar') ? whereis('tar') : '/bin/tar';
	}

	$test['pass']=@is_executable($tar);
	$test['feedback']='Warning: tar is not installed or not executable.';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='ZIP Compression';
	if(class_exists('GO'))
	{
		$zip = whereis('zip') ? whereis('zip') : \GO::config()->cmd_zip;
	}else
	{
		$zip = whereis('zip') ? whereis('zip') : '/usr/bin/zip';
	}
	$test['pass']=@is_executable($zip);
	$test['feedback']='Warning: zip is not installed or not executable. Unpacking zip archives and using document templates for Microsoft Word and Open-Office.org won\'t be possible.';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='TNEF';
	if(class_exists('GO'))
	{
		$tnef = whereis('tnef') ? whereis('tnef') : \GO::config()->cmd_tnef;
	}else
	{
		$tnef = whereis('tnef') ? whereis('tnef') : '/usr/bin/tnef';
	}
	$test['pass']=@is_executable($tnef);
	$test['feedback']='Warning: tnef is not installed or not executable. you can\'t view winmail.dat attachments in the email module.';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='Ioncube';
	$test['pass']=$ioncubeWorks = ioncube_tester();
	$test['feedback']='Warning: Ioncube is not installed. The professional modules will not be enabled.';
	$test['fatal']=false;

	$tests[]=$test;
	
	
	$test['name']='JSON functions';
	$test['pass']=function_exists('json_encode');
	$test['feedback']='Fatal error: json_encode and json_decode functions are not available. Try apt-get install php5-json on Debian or Ubuntu.';
	$test['fatal']=true;

	$tests[]=$test;


	$ze1compat=ini_get('zend.ze1_compatibility_mode');

	$test['name']='zend.ze1_compatibility_mode';
	$test['pass']=empty($ze1compat);
	$test['feedback']='Fatal error: zend.ze1_compatibility_mode is enabled. '.$product_name.' can\'t run with this setting enabled';
	$test['fatal']=true;

	$tests[]=$test;	
	
	
	$url = "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['HTTP_HOST'];
	
	$headers = @get_headers($url.'/caldav');	
	$test['name']='CalDAV alias';
	$test['pass']=$headers && (strpos($headers[0], '401')!==false || strpos($headers[0], '200')!==false);
	$test['feedback']="Note: The alias /caldav was not detected. Please create: Alias /caldav /groupoffice/modules/caldav/calendar.php.";
	$test['fatal']=false;

	$tests[]=$test;	
	
	
	$headers = @get_headers($url.'/.well-known/caldav');	
	
	$test['name']='CalDAV autodiscovery';
	$test['pass']=$headers && (strpos($headers[0], '301')!==false|| strpos($headers[0], '200')!==false);
	$test['feedback']="Note: The redirect /.well-known/caldav was not detected. Please create a redirect: Redirect 301 /.well-known/caldav /caldav";
	$test['fatal']=false;

	$tests[]=$test;	
	
	
	$headers = @get_headers($url.'/carddav');	
	$test['name']='CardDAV alias';
	$test['pass']=$headers && (strpos($headers[0], '401')!==false || strpos($headers[0], '200')!==false);;
	$test['feedback']="Note: The alias /carddav was not detected. Please create: Alias /carddav /groupoffice/modules/carddav/addressbook.php.";
	$test['fatal']=false;

	$tests[]=$test;	
	
	
	$headers = @get_headers($url.'/.well-known/carddav');	
	$test['name']='CardDAV autodiscovery';
	$test['pass']=$headers && (strpos($headers[0], '301')!==false || strpos($headers[0], '200')!==false);
	$test['feedback']="Note: The redirect /.well-known/carddav was not detected. Please create a redirect: Redirect 301 /.well-known/carddav /carddav";
	$test['fatal']=false;

	$tests[]=$test;	
	
	
	$headers = @get_headers($url.'/Microsoft-Server-ActiveSync');	
	
//	var_dump($headers);
	$test['name']='Microsoft-Server-ActiveSync alias';
	$test['pass']=$headers && (strpos($headers[0], '401')!==false || strpos($headers[0], '200')!==false);
	$test['feedback']="Note: The alias /Microsoft-Server-ActiveSync was not detected. Please create: Alias /Microsoft-Server-ActiveSync /groupoffice/modules/z-push21/index.php.";
	$test['fatal']=false;

	$tests[]=$test;	
	
	
	
	if(class_exists('GO')){
		
		$test['name']='Writable license file';
		$test['pass']=GO::getLicenseFile()->exists() && GO::getLicenseFile()->isWritable();					
		$test['feedback']="Fatal: the license file ".GO::getLicenseFile()->path()." is not writable. Please make it writable for the webserver.";
		$test['fatal']=true;

		$tests[]=$test;	
		
		$root = dirname(dirname(__FILE__));

		if($ioncubeWorks && is_dir($root.'/modules/professional'))
		{

			$test['name']='Professional license';

	//		if(!file_exists(GO::config()->root_path.'groupoffice-pro-'.\GO::config()->getMajorVersion().'-license.txt')){
	//			$test['feedback']='Warning: There\'s no license file "groupoffice-pro-'.\GO::config()->getMajorVersion().'-license.txt" in the root of Group-Office. The professional modules will not be enabled.';
	//			$test['fatal']=false;
	//			$test['pass']=false;
	//		}else
			if(!\GO::scriptCanBeDecoded($root.'/modules/professional/License.php'))
			{
				$test['feedback']='Warning: Your professional license is invalid. The professional modules will not be enabled. Please contact Intermesh about this problem and supply the output of this page.';
				$test['fatal']=false;
				$test['pass']=false;
			}else
			{
				$test['feedback']='';
				$test['fatal']=false;
				$test['pass']=true;
			}	

			$tests[]=$test;
		}



		if(\GO::isInstalled())
		{		
			$test['name']='Protected files path';
			$test['pass']=is_writable(\GO::config()->file_storage_path);
			$test['feedback']='Fatal error: the file_storage_path setting in config.php is not writable. You must correct this or '.$product_name.' will not run.';
			$test['fatal']=false;
			$tests[]=$test;	

			$test['name']='Cronjob';
			$test['pass']=GO::cronIsRunning();
			$test['feedback']="Warning: The main cron job doesn't appear to be running. Please add a cron job: \n\n* * * * * www-data php ".\GO::config()->root_path."groupofficecli.php -c=".\GO::config()->get_config_file()." -r=core/cron/run -q > /dev/null";
			$test['fatal']=false;
			$tests[]=$test;	
		}	
	}
	
	return $tests;
}

function output_system_test(){
	global $product_name;

	$tests = test_system();
	
	$fatal = false;
	
	
	foreach($tests as $test)
	{
		if(!$test['pass'])
		{
			echo '<p style="color:red">'.$test['feedback'].'</p>';
			
			if($test['fatal'])
				$fatal=true;
		}
	}	

	if($fatal)
	{
		echo '<p style="color:red">Fatal errors occured. '.$product_name.' will not run properly with current system setup!</p>';
	}else
	{
		echo '<p><b>Passed!</b> '.$product_name.' should run on this machine</p>';
	}
	
	
	echo '<table style="font:12px Arial"><tr>
	<td colspan="2">
	<br />
	<b>Use this information for your '.$product_name.' Professional license:</b>
	</td>
</tr>

<tr>
	<td valign="top">Server name:</td>
	<td>'.$_SERVER['SERVER_NAME'].'</td>
</tr>
<tr>
	<td valign="top">Server IP:</td>
	<td>'.gethostbyname($_SERVER['SERVER_NAME']).'</td>
</tr></table>';
	
	return !$fatal;
	
}


//
// Detect some system parameters
//
function ic_system_info()
{
	$thread_safe = false;
	$debug_build = false;
	$cgi_cli = false;
	$php_ini_path = '';

	ob_start();
	phpinfo(INFO_GENERAL);
	$php_info = ob_get_contents();
	ob_end_clean();

	foreach (explode("\n",$php_info) as $line) {
		if (stripos($line, 'command')!==false) {
			continue;
		}

		if (preg_match('/thread safety.*(enabled|yes)/Ui',$line)) {
			$thread_safe = true;
		}

		if (preg_match('/debug.*(enabled|yes)/Ui',$line)) {
			$debug_build = true;
		}

		if (preg_match("/configuration file.*(<\/B><\/td><TD ALIGN=\"left\">| => |v\">)([^ <]*)(.*<\/td.*)?/",$line,$match)) {
			$php_ini_path = $match[2];

			//
			// If we can't access the php.ini file then we probably lost on the match
			//
			if (!@file_exists($php_ini_path)) {
				$php_ini_path = '';
			}
		}

		$cgi_cli = ((strpos(php_sapi_name(),'cgi') !== false) ||
		(strpos(php_sapi_name(),'cli') !== false));
	}

	return array('THREAD_SAFE' => $thread_safe,
	       'DEBUG_BUILD' => $debug_build,
	       'PHP_INI'     => $php_ini_path,
	       'CGI_CLI'     => $cgi_cli);
}


function ioncube_tester()
{
	if(extension_loaded('ionCube Loader'))
	{
		return true;
	}

	//
	// Test some system info
	//
	$sys_info = ic_system_info();

	if ($sys_info['THREAD_SAFE'] && !$sys_info['CGI_CLI']) {
		return false;
	}

	if ($sys_info['DEBUG_BUILD']) {
		return false;
	}
	//
	// Check safe mode and for a valid extensions directory
	//
	if (ini_get('safe_mode') == '1') {
		return false;
	}


	// Old style naming should be long gone now
	$test_old_name = false;

	$_u = php_uname();
	$_os = substr($_u,0,strpos($_u,' '));
	$_os_key = strtolower(substr($_u,0,3));

	$_php_version = phpversion();
	$_php_family = substr($_php_version,0,3);

	$_loader_sfix = (($_os_key == 'win') ? '.dll' : '.so');

	$_ln_old="ioncube_loader.$_loader_sfix";
	$_ln_old_loc="/ioncube/$_ln_old";

	$_ln_new="ioncube_loader_${_os_key}_${_php_family}${_loader_sfix}";
	$_ln_new_loc="/ioncube/$_ln_new";


	$_extdir = ini_get('extension_dir');
	if ($_extdir == './') {
		$_extdir = '.';
	}

	$_oid = $_id = realpath($_extdir);

	$_here = dirname(__FILE__);
	if ((@$_id[1]) == ':') {
		$_id = str_replace('\\','/',substr($_id,2));
		$_here = str_replace('\\','/',substr($_here,2));
	}
	$_rd=str_repeat('/..',substr_count($_id,'/')).$_here.'/';

	if ($_oid === false) {
		return false;
	}


	$_ln = '';
	$_i=strlen($_rd);
	while($_i--) {
		if($_rd[$_i]=='/') {
			if ($test_old_name) {
				// Try the old style Loader name
				$_lp=substr($_rd,0,$_i).$_ln_old_loc;
				$_fqlp=$_oid.$_lp;
				if(@file_exists($_fqlp)) {
			  $_ln=$_lp;
			  break;
				}
			}
			// Try the new style Loader name
			$_lp=substr($_rd,0,$_i).$_ln_new_loc;
			$_fqlp=$_oid.$_lp;
			if(@file_exists($_fqlp)) {
				$_ln=$_lp;
				break;
			}
		}
	}

	//
	// If Loader not found, try the fallback of in the extensions directory
	//
	if (!$_ln) {
		if ($test_old_name) {
			if (@file_exists($_id.$_ln_old_loc)) {
				$_ln = $_ln_old_loc;
			}
		}
		if (@file_exists($_id.$_ln_new_loc)) {
			$_ln = $_ln_new_loc;
		}
	}

	if ($_ln) {
		@dl($_ln);
		if(extension_loaded('ionCube Loader')) {
			return true;
		} else {
			return false;
		}
	}
	return false;
}

function is__writable($path) {
	//will work in despite of Windows ACLs bug
	//NOTE: use a trailing slash for folders!!!
	//see http://bugs.php.net/bug.php?id=27609
	//see http://bugs.php.net/bug.php?id=30931

	if ($path{strlen($path)-1}=='/') // recursively return a temporary file path
	return is__writable($path.uniqid(mt_rand()).'.tmp');
	else if (is_dir($path))
	return is__writable($path.'/'.uniqid(mt_rand()).'.tmp');
	// check tmp file for read/write capabilities
	$rm = file_exists($path);
	$f = @fopen($path, 'a');
	if ($f===false)
	return false;
	fclose($f);
	if (!$rm)
	unlink($path);
	return true;
}

function escape_config_value($value)
{
	return str_replace('\\"', '"', addslashes($value));
}

function save_config($config_obj)
{
	global $CONFIG_FILE;

	require($CONFIG_FILE);

	$values = get_object_vars($config_obj);

	foreach($values as $key=>$value)
	{
		if($key == 'version')
		break;
			
			
		if(!is_object($value))
		{
			$config[$key]=$value;
		}
	}


	$config_data = "<?php\n";
	foreach($config as $key=>$value)
	{
		if($value===true)
		{
			$config_data .= '$config[\''.$key.'\']=true;'."\n";
		}elseif($value===false)
		{
			$config_data .= '$config[\''.$key.'\']=false;'."\n";
		}else
		{
			$config_data .= '$config[\''.$key.'\']="'.$value.'";'."\n";
		}
	}
	return file_put_contents($CONFIG_FILE, $config_data);
}



function whereis($cmd)
{
	if(strtoupper(substr(PHP_OS, 0, 3)) != 'WIN' && @is_executable('whereis'))
	{
		exec('whereis '.$cmd, $return);

		if(isset($return[0]))
		{
			$locations = explode(' ', $return[0]);
			if(isset($locations[1]))
			{
				return $locations[1];
			}
		}
	}
	return false;
}

function return_bytes($val) {
	$val = trim($val);
	$last = strtolower($val{strlen($val)-1});
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

//check if we are included
if(!class_exists('GO'))
{
	echo '<h1 style="font-family: Arial, Helvetica;font-size: 18px;">'.$product_name.' test script</h1><div style="font-family: Arial, Helvetica;font-size: 12px;"> ';
	output_system_test();
	echo "</div>";
}