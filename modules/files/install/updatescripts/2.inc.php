<?php

require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

$GO_USERS->get_users();

$mod = new GO_MODULES();
$fs_module = $mod->get_module('files');

require($GO_LANGUAGE->get_language_file('files'));

require_once($fs_module['class_path'].'files.class.inc.php');
$files = new files();

while($GO_USERS->next_record())
{
	$home_dir = $GO_CONFIG->file_storage_path.'users/'.$GO_USERS->f('username');
	if(!is_dir($home_dir))
	{
		mkdir($home_dir, $GO_CONFIG->folder_create_mode,true);
	}
	
	$folder = $files->get_folder($home_dir);
	
	if(empty($folder['acl_read']))
	{
		$up_folder['id']=$folder['id'];
		$up_folder['user_id']=$GO_USERS->f('id');
		$up_folder['acl_read']=$GO_SECURITY->get_new_acl('files', $GO_USERS->f('id'));
		$up_folder['acl_write']=$GO_SECURITY->get_new_acl('files', $GO_USERS->f('id'));
		$up_folder['visible']='1';
		
		$files->update_folder($up_folder);
	}	
}

?>