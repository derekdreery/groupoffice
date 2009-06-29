<?php
//otherwise log module will log all items as added.
define('NOLOG', true);

if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));

require_once("../../Group-Office.php");

$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";
//$GO_SECURITY->html_authenticate('tools');

ini_set('max_execution_time', 360);
echo 'Clearing search cache'.$line_break;

require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
$search = new search();

$search->reset();
flush();

echo 'Building search cache'.$line_break;

$GO_EVENTS->fire_event('build_search_index');

//$search->update_search_cache(true);

echo 'Done'.$line_break.$line_break;