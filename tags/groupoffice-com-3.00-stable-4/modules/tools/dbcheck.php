<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 *
 * @package Tools
 * @subpackage DB check
 */

require_once("../../Group-Office.php");
//$GO_SECURITY->html_authenticate('tools');

ini_set('max_execution_time', 360);

$db2 = new db();
$db3 = new db();

$db = new db();
$db->halt_on_error = 'no';

$acls=array();

$db->query("SELECT acl_read FROM `go_modules` GROUP BY acl_read HAVING count( * )>1");
while($record = $db->next_record())
{
	$acls[]=$record['acl_read'];
}

if(count($acls))
{
	echo "Correcting module permissions...<br />";
	foreach($acls as $acl_read)
	{
		$sql = "SELECT * FROM go_modules WHERE acl_read='$acl_read'";
		$db->query($sql);
		$first = $db->next_record();
		while($record = $db->next_record())
		{
			$mod['id']=$record['id'];
			$mod['acl_read']=$GO_SECURITY->copy_acl($first['acl_read']);
			$mod['acl_write']=$GO_SECURITY->copy_acl($first['acl_write']);
			
			$db2->update_row('go_modules', 'id', $mod);
		}
	}
	$GO_MODULES->load_modules();
	echo "Done<br /><br />";
}


echo 'Checking ACL...<br />';

$sql = "SELECT * FROM go_acl_items";
$db->query($sql);
while($db->next_record())
{
	if(!$GO_SECURITY->group_in_acl($GO_CONFIG->group_root, $db->f('id')))
	{
		echo 'Adding admin group to '.$db->f('id').'<br />';
		$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_root, $db->f('id'));
	}
	if(!$GO_SECURITY->user_in_acl($db->f('user_id'), $db->f('id')))
	{
		echo 'Adding owner to '.$db->f('id').'<br />';
		$GO_SECURITY->add_user_to_acl($db->f('user_id'), $db->f('id'));
	}
}
echo 'Done<br /><br />';

flush();

echo 'Resetting DB sequence...<br />';

$db->query("SHOW TABLES");

$tables = array();

while($db->next_record(DB_BOTH))
{
	if($db->f(0) != 'go_db_sequence')
	{
		$db2->query("SHOW FIELDS FROM `".$db->f(0)."`");
		while($db2->next_record())
		{
			if($db2->f('Field')=='id')
			{
				$tables[]=$db->f(0);
				break;
			}
		}
	}
}

foreach($tables as $table)
{
	$max=0;
	$sql = "SELECT max(id) FROM `$table`";
	$db->query($sql);
	$db->next_record(DB_BOTH);
	$max = $db->f(0);
//echo $table.':'.$max.'<br />';	
	if(!empty($max))
	{
		$sql = "REPLACE INTO go_db_sequence VALUES ('".$db->escape($table)."', '".$db->escape($max)."');";
		$db->query($sql);

		echo 'Setting '.$table.'='.$max.'<br />';
	}
}
echo 'Done<br /><br />';

flush();




echo 'Optimizing tables<br />';

$db->query("SHOW TABLES");

$tables = array();

while($record = $db->next_record(DB_BOTH))
{
	echo 'Optimizing: '.$db->f(0).'<br />';
	$db2->query('OPTIMIZE TABLE `'.$db->f(0).'`');
}
echo 'Done<br /><br />';



require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
$search = new search();


echo 'Removing dead links<br />';

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
	
	echo 'Removed '.$count.' from table go_links_'.$i.'<br />';
}

echo 'Done<br /><br />';

echo 'Start of module checks<br />';

$GO_MODULES->fire_event('check_database');


echo 'Clearing search cache<br />';


$search->reset();
flush();

echo 'Building search cache<br />';

$search->update_search_cache(true);

echo 'Done<br /><br />';



echo 'All Done!<br />';
