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

function test_system(){

	global $GO_CONFIG;
	
	$tests=array();

	$test['name']='PHP version';
	$test['pass']=function_exists('version_compare') && version_compare( phpversion(), "5.2", ">=");
	$test['feedback']='Fatal error: Your PHP version is too old to run Group-Office. PHP 5.2 or higher is required';
	$test['fatal']=true;

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

	$test['name']='IMAP support';
	$test['pass']=function_exists('imap_open');
	$test['feedback']='Warning: IMAP extension not installed, E-mail module will not work.';
	$test['fatal']=false;

	$tests[]=$test;


	$test['name']='File upload support';
	$test['pass']=ini_get('file_uploads') == '1';
	$test['feedback']='Warning: File uploads are disabled. Please set file_uploads=On in php.ini.';
	$test['fatal']=false;

	$tests[]=$test;

	$test['name']='Safe mode';
	$test['pass']=ini_get('safe_mode') != '1';
	$test['feedback']='Warning: Safe mode is enabled. This may cause trouble with the filesystem module and Synchronization. If you can please set safe_mode=Off in php.ini';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='Open base_dir';
	$test['pass']=ini_get('open_basedir')=='';
	$test['feedback']='Warning: open_basedir is enabled. This may cause trouble with the filesystem module and Synchronization.';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='Calendar functions';
	$test['pass']=function_exists('easter_date');
	$test['feedback']='Warning: Calendar functions not available. The Group-Office calendar won\'t be able to generate all holidays for you. Please compile PHP with --enable-calendar.';
	$test['fatal']=false;

	$memory_limit = return_bytes(ini_get('memory_limit'));
	$tests[]=$test;
	$test['name']='Memory limit';
	$test['pass']=$memory_limit>=32*1024*1024;
	$test['feedback']='Warning: Your memory limit setting ('.format_size($memory_limit).') is less then 32MB. It\'s recommended to allow at least 32 MB.';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='Error logging';
	$test['pass']=ini_get('log_errors')=='1';
	$test['feedback']='Warning: PHP error logging is disabled in php.ini. It\'s recommended that this feature is enabled in a production environment.';
	$test['fatal']=false;

	/*$tests[]=$test;
	$test['name']='Error display';
	$test['pass']=ini_get('display_errors')!='1';
	$test['feedback']='Warning: PHP error display is enabled in php.ini. It\'s recommended that this feature is disabled because it can cause unnessecary interface crashes.';
	$test['fatal']=false;*/

	$tests[]=$test;
	$test['name']='libwbxml';
	if(isset($GO_CONFIG))
	{
		$wbxml2xml = whereis('wbxml2xml') ? whereis('wbxml2xml') : $GO_CONFIG->cmd_wbxml2xml;
		$xml2wbxml = whereis('xml2wbxml') ? whereis('xml2wbxml') : $GO_CONFIG->cmd_xml2wbxml;
	}else
	{
		$wbxml2xml = whereis('wbxml2xml') ? whereis('wbxml2xml') : '/usr/bin/wbxml2xml';
		$xml2wbxml = whereis('xml2wbxml') ? whereis('xml2wbxml') : '/usr/bin/xml2wbxml';
	}
	$test['pass']=is_executable($wbxml2xml) && is_executable($xml2wbxml);
	$test['feedback']='Warning: libwbxml2 is not installed. Synchronization will not work!';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='DOM functions';
	$test['pass']=class_exists('DOMDocument', false);
	$test['feedback']='Warning: DOM functions are not installed. Synchronization will not work. Install php-xml';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='MultiByte string functions';
	$test['pass']=function_exists('mb_detect_encoding');
	$test['feedback']='Warning: php-mbstring is not installed. Problems with non-ascii characters in e-mails and filenames might occur.';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='TAR Compression';
	if(isset($GO_CONFIG))
	{
		$tar = whereis('tar') ? whereis('tar') : $GO_CONFIG->cmd_tar;
	}else
	{
		$tar = whereis('tar') ? whereis('tar') : '/bin/tar';
	}

	$test['pass']=is_executable($tar);
	$test['feedback']='Warning: tar is not installed or not executable.';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='ZIP Compression';
	if(isset($GO_CONFIG))
	{
		$zip = whereis('zip') ? whereis('zip') : $GO_CONFIG->cmd_zip;
	}else
	{
		$zip = whereis('zip') ? whereis('zip') : '/usr/bin/zip';
	}
	$test['pass']=is_executable($zip);
	$test['feedback']='Warning: zip is not installed or not executable.';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='TNEF';
	if(isset($GO_CONFIG))
	{
		$tnef = whereis('tnef') ? whereis('tnef') : $GO_CONFIG->cmd_tnef;
	}else
	{
		$tnef = whereis('tnef') ? whereis('tnef') : '/usr/bin/tnef';
	}
	$test['pass']=is_executable($tnef);
	$test['feedback']='Warning: tnef is not installed or not executable. you can\'t view winmail.dat attachments in the email module.';
	$test['fatal']=false;

	$tests[]=$test;
	$test['name']='Ioncube';
	$test['pass']=ioncube_tester();
	$test['feedback']='Warning: Ioncube is not installed. The professional version will not run.';
	$test['fatal']=false;

	$tests[]=$test;
	
	$test['name']='JSON functions';
	$test['pass']=function_exists('json_encode');
	$test['feedback']='Fatal error: json_encode and json_decode functions are not available.';
	$test['fatal']=true;

	$tests[]=$test;
	
	
	if(!empty($GO_CONFIG->db_name))
	{
		$test['name']='Public files path';
		$test['pass']=is_writable($GO_CONFIG->local_path);
		$test['feedback']='Fatal error: the local_path setting in config.php is not writable. You must correct this or Group-Office will not run.';
		$test['fatal']=true;
		$tests[]=$test;
		
		$test['name']='Protected files path';
		$test['pass']=is_writable($GO_CONFIG->file_storage_path);
		$test['feedback']='Fatal error: the file_storage_path setting in config.php is not writable. You must correct this or Group-Office will not run.';
		$test['fatal']=true;
		$tests[]=$test;
	}	
	
	return $tests;
}

function output_system_test(){
	
	$tests = test_system();
	
	$fatal = false;
	
	echo '<table border="0" style="font-family: Arial, Helvetica;font-size:12px;">';
	
	foreach($tests as $test)
	{
		if(!$test['pass'])
		{
			echo '<tr><td valign="top">'.$test['name'].'</td>'.
				'<td style="color:red">: '.$test['feedback'].'</td></tr>';
			
			if($test['fatal'])
				$fatal=true;
		}
	}	

	if($fatal)
	{
		echo '<tr><td colspan="2" style="color:red"><br />Fatal errors occured. Group-Office will not run properly with current system setup!</td></tr>';
	}else
	{
		echo '<tr><td colspan="2"><br /><b>Passed!</b> Group-Office should run on this machine</td></tr>';
	}
	
	
	echo '<tr>
	<td colspan="2">
	<br />
	<b>Use this information for your Group-Office Professional license:</b>
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

	foreach (split("\n",$php_info) as $line) {
		if (eregi('command',$line)) {
			continue;
		}

		if (preg_match('/thread safety.*(enabled|yes)/Ui',$line)) {
			$thread_safe = true;
		}

		if (preg_match('/debug.*(enabled|yes)/Ui',$line)) {
			$debug_build = true;
		}

		if (eregi("configuration file.*(</B></td><TD ALIGN=\"left\">| => |v\">)([^ <]*)(.*</td.*)?",$line,$match)) {
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
	exec('whereis '.$cmd, $return);

	if(isset($return[0]))
	{
		$locations = explode(' ', $return[0]);
		if(isset($locations[1]))
		{
			return $locations[1];
		}
		return false;
	}
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
if(!isset($GO_CONFIG))
{
	echo '<h1 style="font-family: Arial, Helvetica;font-size: 18px;">Group-Office test script</h1>';
	output_system_test();
}
?>
