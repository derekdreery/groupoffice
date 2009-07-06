<?php
//otherwise log module will log all items as added.
define('NOLOG', true);

if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}

require('../../Group-Office.php');


if(php_sapi_name()!='cli')
{
	$GO_SECURITY->html_authenticate('tools');
}

$db1 = new db();
$db2 = new db();
$deleted=0;

function delete_duplicate_folders(){
	global $db1,$db2,$deleted;


	$sql ="SELECT id, parent_id,name FROM fs_folders ORDER BY parent_id ASC, name ASC, ctime ASC";
	$db1->query($sql);



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

			$deleted++;
		}else
		{
			$lastrecord=$record;
		}
	}
}

delete_duplicate_folders();

echo 'Deleted '.$deleted.' duplicate folders';

?>
