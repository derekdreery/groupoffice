<?php 
if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));
$line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";



require('../../../../Group-Office.php');
$fs = new filesystem();

require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
$files = new files();

$files->query("TRUNCATE `fs_files` ;");

$files->query("TRUNCATE `fs_folders` ;");

$GO_SECURITY->logged_in(1);


$files->import_folder($GO_CONFIG->file_storage_path.'users', 0);


$GO_USERS->get_users();

while($GO_USERS->next_record())
{
	$home_dir = 'users/'.$GO_USERS->f('username');
		
	$folder = $files->resolve_path($home_dir);

	if(empty($folder['acl_read']))
	{
		echo "Sharing users/".$GO_USERS->f('username').$line_break;

		$up_folder['id']=$folder['id'];
		$up_folder['acl_read']=$GO_SECURITY->get_new_acl('files', $GO_USERS->f('id'));
		$up_folder['acl_write']=$GO_SECURITY->get_new_acl('files', $GO_USERS->f('id'));

		$files->update_folder($up_folder);
	}
}

?>