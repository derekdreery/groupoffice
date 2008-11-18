<?php
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


/*$CONFIG_KEYS =
 array(
 'id',
 'debug',
 'log',
 'language',
 'default_country',
 'default_timezone',
 'default_currency',
 'default_date_format',
 'default_date_seperator',
 'default_time_format',
 'default_first_weekday',
 'default_decimal_seperator',
 'default_thousands_seperator',
 'theme',
 'allow_themes',
 'allow_password_change',
 'allow_registration',
 'allow_duplicate_email',
 'auto_activate_accounts',
 'notify_admin_of_registration',
 'registration_fields',
 'required_registration_fields',
 'register_modules_read',
 'register_modules_write',
 'register_user_groups',
 'register_visible_user_groups',
 'host',
 'full_url',
 'title',
 'webmaster_email',
 'root_path',
 'tmpdir',
 'local_path',
 'local_url',
 'max_users',
 //'refresh_rate',
 //'login_screen',
 'db_type',
 'db_host',
 'db_name',
 'db_user',
 'db_pass',
 //'sync_auth_source_key',
 'file_storage_path',
 'max_file_size',
 //'mailer',
 'smtp_server',
 'smtp_port',
 'smtp_username',
 'smtp_password',
 'email_connectstring_options',
 'max_attachment_size',
 'cmd_zip',
 'cmd_unzip',
 'cmd_tar',
 //'cmd_chpasswd',
 'cmd_sudo',
 //'cmd_quota',
 //'cmd_alias',
 //'cmd_edquota',
 'cmd_wbxml2xml',
 'cmd_xml2wbxml',
 'cmd_tnef',
 'quota_protouser',
 'phpMyAdminUrl'
 );*/

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


