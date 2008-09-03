#!/usr/bin/php
<?php
if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
	define('NOTINSTALLED', true);
}

require('../Group-Office.php');

require_once(dirname(dirname(__FILE__)).'/classes/filesystem.class.inc');

$db = new db();

$db->query("SHOW TABLES");
if($db->num_rows()>0)
exit("Aborted because database is not empty!");

$queries = String::get_sql_queries($GO_CONFIG->root_path."install/sql/groupoffice.sql");
//$queries = get_sql_queries($GO_CONFIG->root_path."lib/sql/groupoffice.sql");
while ($query = array_shift($queries))
{
	$db->query($query);
}

require($GO_CONFIG->root_path."install/sql/updates.inc.php");
//store the version number for future upgrades
$GO_CONFIG->save_setting('version', count($updates));

$GO_LANGUAGE->set_language($GO_CONFIG->language);



$user['id'] = $GO_USERS->nextid("go_users");

$GO_GROUPS->query("DELETE FROM go_db_sequence WHERE seq_name='groups'");
$GO_GROUPS->query("DELETE FROM go_groups");

$admin_group_id = $GO_GROUPS->add_group($user['id'], addslashes($lang['common']['group_admins']));
$everyone_group_id = $GO_GROUPS->add_group($user['id'], addslashes($lang['common']['group_everyone']));
$internal_group_id = $GO_GROUPS->add_group($user['id'], addslashes($lang['common']['group_internal']));

$user_groups = array($admin_group_id, $everyone_group_id, $internal_group_id);



$fs = new filesystem();

//install all modules
if(isset($argv[2]))
{
	$modules = explode(',', $argv[2]);
}else
{
	$modules = array();
	$module_folders = $fs->get_folders($GO_CONFIG->root_path.'modules/');

	$available_modules=array();
	foreach($module_folders as $folder)
	{
		$available_modules[]=$folder['name'];
	}
	$priority_modules=array('summary','email','calendar','tasks','addressbook','files', 'notes', 'projects');

	for($i=0;$i<count($priority_modules);$i++)
	{
		if(in_array($priority_modules[$i], $available_modules))
		{
			$modules[]=$priority_modules[$i];
		}
	}
	for($i=0;$i<count($available_modules);$i++)
	{
		if(!in_array($available_modules[$i], $priority_modules))
		{
			$modules[]=$available_modules[$i];
		}
	}
}

foreach($modules as $module)
{
	$GO_MODULES->add_module($module);
}


$GO_MODULES->load_modules();

$user['language'] = $GO_LANGUAGE->language;
$user['first_name']='Group-Office';
$user['middle_name']='';
$user['last_name']=$lang['common']['admin'];
$user['username'] = 'admin';
$user['password'] = 'admin';
$user['email'] = $GO_CONFIG->webmaster_email;
$user['sex'] = 'M';
$user['country']=$GO_CONFIG->default_country;
$user['work_country']=$GO_CONFIG->default_country;

$GO_USERS->add_user($user,$user_groups,array($GO_CONFIG->group_everyone));
//filesystem::mkdir_recursive($GO_CONFIG->file_storage_path.'users/admin/');
