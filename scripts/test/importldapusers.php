<?php
if(!isset($argv[1]))
{
	die('No config! :: The first argument is empty!');
}

define('CONFIG_FILE', $argv[1]);
require($argv[1]);

require_once($config['root_path']."Group-Office.php");

require_once($GO_MODULES->modules['ldapauth']['class_path'].'ldapauth.class.inc.php');

require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

require_once($GO_CONFIG->class_path.'base/groups.class.inc.php');
$GO_GROUPS = new GO_GROUPS();

$la = new ldapauth();

$ldap = $la->connect();

$search_id=$ldap->search('uid=testa', $ldap->PeopleDN);

$count=0;
for ($entryID=ldap_first_entry($ldap->Link_ID,$search_id);
            $entryID!=false;
            $entryID=ldap_next_entry($ldap->Link_ID,$entryID))
{
	$entry = ldap_get_attributes ($ldap->Link_ID,$entryID);
	//var_dump($entry);

	$user = $la->convert_ldap_entry_to_groupoffice_record($entry);

	//var_dump($entry);

	$gouser = $GO_USERS->get_user_by_username($entry['uid']);

	
	if($gouser){
		echo "User ".$gouser['username']." already exists\n";
	}else
	{
		if (!$user_id = $GO_USERS->add_user($user,
		$GO_GROUPS->groupnames_to_ids(explode(',',$GO_CONFIG->register_user_groups)),
		$GO_GROUPS->groupnames_to_ids(explode(',',$GO_CONFIG->register_visible_user_groups)),
		explode(',',$GO_CONFIG->register_modules_read),
		explode(',',$GO_CONFIG->register_modules_write))) {
			echo "Failed creating user ".$user['username']."\n";
		}
	}
	

	$count++;

	if($count==10)
		break;
}