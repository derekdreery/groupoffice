<?php
if(isset($argv[1]))
{
    define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));

require('../../../../Group-Office.php');

if(php_sapi_name()!='cli')
{
	GO::security()->authenticate();
	if(!GO::security()->has_admin_permission(GO::security()->user_id))
	{
		die('You must be logged in as admin or run it from the command line');
	}
}


$db = new db();
$db->halt_on_error='report';
//suppress duplicate and drop errors
$db->suppress_errors=array(1060, 1091);

ini_set('max_execution_time', '3600');

require('3_convert_old_paths.inc.php');