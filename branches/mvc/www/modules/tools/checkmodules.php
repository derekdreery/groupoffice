<?php
//otherwise log module will log all items as added.
define('NOLOG', true);

if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));

require_once("../../Group-Office.php");

if(php_sapi_name()!='cli')
{
	GO::security()->html_authenticate('tools');
}

$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";
//GO::security()->html_authenticate('tools');

ini_set('max_execution_time', 360);


echo 'Start of module checks'.$line_break;

GO::events()->fire_event('check_database');

echo 'Done'.$line_break.$line_break;
?>
