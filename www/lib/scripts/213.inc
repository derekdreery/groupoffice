<?php
/*
This script will update the databse fopr version 2.03
*/

$db = new db();
$db->Halt_On_Error = 'no';

$sql = "SELECT * FROM acl";

$db->query($sql);
while($db->next_record())
{
	if(!$GO_SECURITY->group_in_acl($GO_CONFIG->group_root, $db->f('acl_id')))
	{
		$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_root, $db->f('acl_id'));
	}
}

@rename($GO_CONFIG->file_storage_path, $GO_CONFIG->file_storage_path.'users/');
if(!is_dir($GO_CONFIG->file_storage_path.'users/'))
{
	echo '<b>Warning:</b> Unable to move the user data into a subdirectory \'users\'. Please do this manually.<br />';
}
?>
