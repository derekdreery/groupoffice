<?php
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


