<?php
//otherwise log module will log all items as added.
define('NOLOG', true);

//event firing will cause problems with Ioncube
define('NO_EVENTS',true);

if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));

require('../../../Group-Office.php');

$line_break = php_sapi_name()=='cli' ? "\n" : '<br />';

if(php_sapi_name()!='cli' && !$GLOBALS['GO_SECURITY']->has_admin_permission($GLOBALS['GO_SECURITY']->user_id))
{
	die('You must be admin or on the command line');
}

$db1 = new db();
$db2 = new db();
$deleted=0;

function delete_duplicate_folders(){
	global $db1,$db2,$deleted;


	$sql ="SELECT id, parent_id,name FROM fs_folders ORDER BY parent_id ASC, name ASC, ctime ASC";
	$db1->query($sql);

	$deleted_this_time=false;

	$lastrecord['name']='';
	$lastrecord['parent_id']=-1;
	$lastrecord['id']=-1;
	while($record = $db1->next_record())
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
	global $db1,$db2,$deleted;


	$sql ="SELECT id, folder_id,name FROM fs_files ORDER BY folder_id ASC, name ASC, ctime ASC";
	$db1->query($sql);

	$deleted_this_time=false;

	$lastrecord['name']='';
	$lastrecord['folder_id']=-1;
	$lastrecord['id']=-1;
	while($record = $db1->next_record())
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

?>
