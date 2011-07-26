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
	$GO_SECURITY->html_authenticate('tools');
}

session_write_close();

$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";
//$GO_SECURITY->html_authenticate('tools');

ini_set('max_execution_time', 3600);
echo 'Clearing search cache'.$line_break;

require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
$search = new search();

$search->reset();
flush();

echo 'Building search cache'.$line_break;

$GO_EVENTS->fire_event('build_search_index');

/*
 * Dangerous if search cache is not built correctly.

$db = new db();
echo 'Removing dead links'.$line_break;

for($i=1;$i<=13;$i++)
{
	$sql = "CREATE TABLE IF NOT EXISTS `go_links_$i` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`)
)  DEFAULT CHARSET=utf8;";
	$db->query($sql);

	$sql = "SELECT * FROM `go_links_$i` l WHERE NOT EXISTS (SELECT id FROM go_search_cache c WHERE c.id=l.id AND c.link_type=$i);";
	$search->query($sql);
	$count = $search->num_rows();

	while($search->next_record())
	{
		$GO_LINKS->delete_link($search->f('id'), $i);
	}

	echo 'Removed '.$count.' from table go_links_'.$i.$line_break;
}*/

echo 'Done'.$line_break.$line_break;