<?php
//otherwise log module will log all items as added.
define('NOLOG', true);

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

require_once($GO_MODULES->modules[$argv[1]]['class_path'].$argv[1].'.class.inc.php');

$cls = new $argv[1];

$cls->check_database();
?>
