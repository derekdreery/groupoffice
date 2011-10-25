#!/usr/bin/php
<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */
if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
	define('NOTINSTALLED', true);
}

chdir(dirname(__FILE__));

require('../Group-Office.php');

require_once(dirname(dirname(__FILE__)).'/classes/filesystem.class.inc');

$db = new db();

$db->query("SHOW TABLES");
if($db->num_rows()>0)
exit("Automatic installation of Group-Office aborted because database is not empty");

$queries = String::get_sql_queries($GLOBALS['GO_CONFIG']->root_path."install/sql/groupoffice.sql");
//$queries = get_sql_queries($GLOBALS['GO_CONFIG']->root_path."lib/sql/groupoffice.sql");
while ($query = array_shift($queries))
{
	$db->query($query);
}

require($GLOBALS['GO_CONFIG']->root_path."install/sql/updates.inc.php");
//store the version number for future upgrades
$GLOBALS['GO_CONFIG']->save_setting('version', count($updates));

$GLOBALS['GO_LANGUAGE']->set_language($GLOBALS['GO_CONFIG']->language);

require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

$user['id'] = $GO_USERS->nextid("go_users");

require_once($GLOBALS['GO_CONFIG']->class_path.'base/groups.class.inc.php');
$GO_GROUPS = new GO_GROUPS();

$GO_GROUPS->query("DELETE FROM go_db_sequence WHERE seq_name='groups'");
$GO_GROUPS->query("DELETE FROM go_groups");

$admin_group_id = $GO_GROUPS->add_group($user['id'], $lang['common']['group_admins']);
$everyone_group_id = $GO_GROUPS->add_group($user['id'], $lang['common']['group_everyone']);
$internal_group_id = $GO_GROUPS->add_group($user['id'], $lang['common']['group_internal']);

$user_groups = array($admin_group_id, $everyone_group_id, $internal_group_id);



$fs = new filesystem();

//install all modules
if(isset($argv[2]))
{
	$modules = explode(',', $argv[2]);
}else
{
	$modules = array();
	$module_folders = $fs->get_folders($GLOBALS['GO_CONFIG']->root_path.'modules/');

	$available_modules=array();
	foreach($module_folders as $folder)
	{
		if(!file_exists($folder['path'].'/install/noautoinstall'))
		{
			$available_modules[]=$folder['name'];
		}
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
	$GLOBALS['GO_MODULES']->add_module($module);
}


$GLOBALS['GO_MODULES']->load_modules();

$user['language'] = $GLOBALS['GO_LANGUAGE']->language;
$user['first_name']=$lang['common']['system'];
$user['middle_name']='';
$user['last_name']=$lang['common']['admin'];
$user['username'] = 'admin';
$user['password'] = 'admin';
$user['email'] = $GLOBALS['GO_CONFIG']->webmaster_email;
$user['sex'] = 'M';
$user['country']=$GLOBALS['GO_CONFIG']->default_country;
$user['work_country']=$GLOBALS['GO_CONFIG']->default_country;
$user['enabled']='1';

$GO_USERS->add_user($user,$user_groups,array($GLOBALS['GO_CONFIG']->group_everyone));


$GLOBALS['GO_CONFIG']->save_setting('upgrade_mtime', $GLOBALS['GO_CONFIG']->mtime);
