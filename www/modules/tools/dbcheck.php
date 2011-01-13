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

//otherwise log module will log all items as added.
define('NOLOG', true);

if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));

require_once("../../Group-Office.php");

session_write_close();

if(php_sapi_name()!='cli')
{
	$GO_SECURITY->html_authenticate('tools');
}


$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";
//$GO_SECURITY->html_authenticate('tools');

ini_set('max_execution_time', 3600);

header('Content-Type: text/html; charset=UTF-8');

$db2 = new db();
$db3 = new db();

$db = new db();
$db->halt_on_error = 'no';

if($GO_CONFIG->quota>0)
{
	require_once($GO_CONFIG->class_path.'base/quota.class.inc.php');
	$quota = new quota();
			
	echo 'Recalculating quota'.$line_break;
	$quota->reset();
	$GO_CONFIG->save_setting('usage_date',time());
	echo 'Done'.$line_break.$line_break;
}
flush();

echo "Correcting timezone$line_break";

$db->query("update go_users set timezone='".$db->escape($GO_CONFIG->default_timezone)."' where length(timezone)<3");


flush();
echo 'Adding everyone to the everyone group'.$line_break;

$db->query("DELETE FROM go_users_groups where user_id NOT IN (SELECT id FROM go_users)");

require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

$GO_USERS->get_users();


require_once($GO_CONFIG->class_path.'base/groups.class.inc.php');
$GO_GROUPS = new GO_GROUPS();

while($GO_USERS->next_record())
{
	if(!$GO_GROUPS->is_in_group($GO_USERS->f('id'), $GO_CONFIG->group_everyone))
		$GO_GROUPS->add_user_to_group($GO_USERS->f('id'), $GO_CONFIG->group_everyone);
}
echo 'Done'.$line_break.$line_break;



if(!$GO_GROUPS->is_in_group(1, $GO_CONFIG->group_root))
{
	echo 'Adding admin to admins group'.$line_break;
	$GO_GROUPS->add_user_to_group(1, $GO_CONFIG->group_root);
}




flush();


$acls=array();

$db->query("SELECT acl_id FROM `go_modules` GROUP BY acl_id HAVING count( * )>1");
while($record = $db->next_record())
{
	$acls[]=$record['acl_id'];
}

if(count($acls))
{
	echo "Correcting module permissions...$line_break";
	foreach($acls as $acl_id)
	{
		$sql = "SELECT * FROM go_modules WHERE acl_id='$acl_id'";
		$db->query($sql);
		$first = $db->next_record();
		while($record = $db->next_record())
		{
			$mod['id']=$record['id'];
			$mod['acl_id']=$GO_SECURITY->copy_acl($first['acl_id']);
			
			$db2->update_row('go_modules', 'id', $mod);
		}
	}
	$GO_MODULES->load_modules();
	echo "Done$line_break$line_break";
}


echo 'Checking ACL...'.$line_break;

$sql = "DELETE FROM go_acl WHERE acl_id=0;";
$db->query($sql);

$sql = "SELECT * FROM go_acl_items";
$db->query($sql);
while($db->next_record())
{
	if($GO_SECURITY->group_in_acl($GO_CONFIG->group_root, $db->f('id'))<GO_SECURITY::MANAGE_PERMISSION)
	{
		echo 'Adding admin group to '.$db->f('id').$line_break;
		$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_root, $db->f('id'), GO_SECURITY::MANAGE_PERMISSION);
	}
	if($GO_SECURITY->user_in_acl($db->f('user_id'), $db->f('id'))<GO_SECURITY::MANAGE_PERMISSION)
	{
		echo 'Adding owner to '.$db->f('id').$line_break;
		$GO_SECURITY->add_user_to_acl($db->f('user_id'), $db->f('id'), GO_SECURITY::MANAGE_PERMISSION);
	}
}


//special acl where admin does not have write permission
$GO_SECURITY->set_read_only_acl_permissions();


echo 'Done'.$line_break.$line_break;

flush();

echo 'Resetting DB sequence...'.$line_break;

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
//echo $table.':'.$max.$line_break;	
	if(!empty($max))
	{
		$sql = "REPLACE INTO go_db_sequence VALUES ('".$db->escape($table)."', '".$db->escape($max)."');";
		$db->query($sql);

		echo 'Setting '.$table.'='.$max.$line_break;
	}
}
echo 'Done'.$line_break.$line_break;

flush();




echo 'Optimizing tables'.$line_break;

$db->query("SHOW TABLES");

$tables = array();

while($record = $db->next_record(DB_BOTH))
{
	echo 'Optimizing: '.$db->f(0).$line_break;
	$db2->query('OPTIMIZE TABLE `'.$db->f(0).'`');
}
echo 'Done'.$line_break.$line_break;



echo 'Checking for duplicate folders and files'.$line_break;


function delete_duplicate_folders(){
	global $db,$db2,$deleted;


	$sql ="SELECT id, parent_id,name FROM fs_folders ORDER BY parent_id ASC, name ASC, ctime ASC";
	$db->query($sql);

	$deleted_this_time=false;

	$lastrecord['name']='';
	$lastrecord['parent_id']=-1;
	$lastrecord['id']=-1;
	while($record = $db->next_record())
	{
		if($record['name']==$lastrecord['name'] && $record['parent_id']==$lastrecord['parent_id'])
		{
			$sql = "UPDATE fs_folders SET parent_id=".$lastrecord['id']." WHERE parent_id=".$record['id'];
			$db2->query($sql);

			$sql = "UPDATE fs_files SET folder_id=".$lastrecord['id']." WHERE folder_id=".$record['id'];
			$db2->query($sql);

			$sql = "DELETE FROM fs_folders WHERE id=".$record['id'];
			$db2->query($sql);

			$deleted_this_time=true;
			$deleted++;
		}else
		{
			$lastrecord=$record;
		}
	}
	if($deleted_this_time)
	{
		delete_duplicate_folders();
	}
}

delete_duplicate_folders();

echo 'Deleted '.$deleted.' duplicate folders'.$line_break;


$deleted=0;

function delete_duplicate_files(){
	global $db,$db2,$deleted;


	$sql ="SELECT id, folder_id,name FROM fs_files ORDER BY folder_id ASC, name ASC, ctime ASC";
	$db->query($sql);

	$deleted_this_time=false;

	$lastrecord['name']='';
	$lastrecord['folder_id']=-1;
	$lastrecord['id']=-1;
	while($record = $db->next_record())
	{
		if($record['name']==$lastrecord['name'] && $record['folder_id']==$lastrecord['folder_id'])
		{
			$sql = "DELETE FROM fs_files WHERE id=".$record['id'];
			$db2->query($sql);

			$deleted_this_time=true;
			$deleted++;
		}else
		{
			$lastrecord=$record;
		}
	}
	if($deleted_this_time)
	{
		delete_duplicate_files();
	}
}

delete_duplicate_files();
echo 'Deleted '.$deleted.' duplicate files'.$line_break;

echo $line_break;

echo 'Starting with module checks '.$line_break;

$GO_EVENTS->fire_event('check_database');

echo 'All Done!'.$line_break;
