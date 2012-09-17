<?php
$module = $this->get_module('files');

global $GO_LANGUAGE, $lang, $GO_SECURITY, $GO_CONFIG;

require($GLOBALS['GO_LANGUAGE']->get_language_file('files'));

require_once($module['class_path'].'files.class.inc.php');
$files = new files();

$template['name']=$lang['files']['emptyFile'];
$template['user_id']=1;
$template['extension']='';
$template['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('files');

$GLOBALS['GO_SECURITY']->add_group_to_acl($GLOBALS['GO_CONFIG']->group_internal, $template['acl_id']);

$files->add_template($template);

$template['name']=$lang['files']['ootextdoc'];
$template['user_id']=1;
$template['extension']='odt';
$template['content']=file_get_contents($module['path'].'install/templates/empty.odt');
$template['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('files');

$GLOBALS['GO_SECURITY']->add_group_to_acl($GLOBALS['GO_CONFIG']->group_internal, $template['acl_id']);

$files->add_template($template);


$template['name']=$lang['files']['wordtextdoc'];
$template['user_id']=1;
$template['extension']='doc';
$template['content']=file_get_contents($module['path'].'install/templates/empty.doc');
$template['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('files');

$GLOBALS['GO_SECURITY']->add_group_to_acl($GLOBALS['GO_CONFIG']->group_internal, $template['acl_id']);

$files->add_template($template);

require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

$GO_USERS->get_users();

//$module = $GLOBALS['GO_MODULES']->get_module('files');

while($GO_USERS->next_record())
{
	$home_dir = $GLOBALS['GO_CONFIG']->file_storage_path.'users/'.$GO_USERS->f('username');
	File::mkdir($home_dir);

	$folder = $files->get_folder($home_dir);

	if(empty($folder['acl_id']))
	{
		$up_folder['id']=$folder['id'];
		$up_folder['user_id']=$GO_USERS->f('id');
		$up_folder['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('files', $GO_USERS->f('id'));
		$up_folder['visible']='1';
			
		$files->update_folder($up_folder);
	}
}


$share_dir = $GLOBALS['GO_CONFIG']->file_storage_path.'users/admin/'.$lang['files']['general'];
File::mkdir($share_dir);

$folder = $files->get_folder('users/admin/'.$lang['files']['general']);

if(empty($folder['acl_id']))
{
	$up_folder['id']=$folder['id'];
	$up_folder['user_id']=1;
	$up_folder['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('files', 1);
	$up_folder['visible']='1';
		
	$files->update_folder($up_folder);
	
	$GLOBALS['GO_SECURITY']->add_group_to_acl($GLOBALS['GO_CONFIG']->group_internal, $up_folder['acl_id']);
}

?>