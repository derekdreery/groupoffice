<?php
//otherwise log module will log all items as added.
define('NOLOG', true);

//event firing will cause problems with Ioncube
define('NO_EVENTS',true);

if(isset($argv[2]))
{
	define('CONFIG_FILE', $argv[2]);
}

chdir(dirname(__FILE__));

require_once("../../Group-Office.php");

if(php_sapi_name()!='cli')
{
	$GO_SECURITY->html_authenticate('tools');
}
if(isset($argv[1]))
	$module = $argv[1];
else
	$module=$_REQUEST['module'];

require_once($GO_MODULES->modules[$module]['class_path'].$module.'.class.inc.php');

$cls = new $module;
$cls->check_database();
