<?php
if(!isset($argv[1]))
{
	$argv[1]='/etc/groupoffice/config.php';
}

define('CONFIG_FILE', $argv[1]);
require($argv[1]);

require_once($config['root_path']."Group-Office.php");

require_once($GLOBALS['GO_MODULES']->modules['ldapauth']['class_path'].'ldapauth.class.inc.php');

require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

require_once($GLOBALS['GO_CONFIG']->class_path.'base/groups.class.inc.php');
$GO_GROUPS = new GO_GROUPS();

$la = new ldapauth();

$ldap = $la->connect();

$db = new db();
$db->query("CREATE TABLE IF NOT EXISTS `ldap_sync` (  `user_id` int(11) NOT NULL,  PRIMARY KEY (`user_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
$db->query("TRUNCATE TABLE `ldap_sync`");

//admin user is not in ldap but should not be removed.
$rec['user_id']=1;
$db->insert_row('ldap_sync',$rec);

echo "Sending query for all users to LDAP server\n";
$search_id=$ldap->search('uid=*', $ldap->PeopleDN);

echo "Query finished\n";

$count=0;
for ($entryID=ldap_first_entry($ldap->Link_ID,$search_id);
            $entryID!=false;
            $entryID=ldap_next_entry($ldap->Link_ID,$entryID))
{

	#echo $count++;
	#echo ': ';

	//if($count==100)
		//break;


	$entry = ldap_get_attributes ($ldap->Link_ID,$entryID);



	$user = $la->convert_ldap_entry_to_groupoffice_record($entry);

	$gouser = $GO_USERS->get_user_by_username($user['username']);


	if($gouser){
		$user_id=$gouser['id'];
		echo "User ".$gouser['username']." already exists\n";

//		if($gouser['enabled']=='1' && $user['enabled']=='0'){
//			$args=array($gouser);
//
//			//for later
//			//$GLOBALS['GO_EVENTS']->fire_event('user_delete', $args);
//			//echo 'Disabling user: '.$gouser['username']."\n";
//		}

		if(!isset($entry['UniHGW-ServiceAgreement']) || $entry['UniHGW-ServiceAgreement']!="groupware"){
			echo 'Disabling user: '.$gouser['username']."\n";

//			require($GLOBALS['GO_MODULES']->modules['calendar']['class_path'].'calendar.class.inc.php');
//			$cal = new calendar();
//			$cal->user_delete($gouser);
//
//			require($GLOBALS['GO_MODULES']->modules['tasks']['class_path'].'tasks.class.inc.php');
//			$t = new tasks();
//			$t->user_delete($gouser);
//
//			require($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
//			$files = new files();
//			$folder = $files->resolve_path('users/'.$gouser['username']);
//			if($folder) {
//				$files->delete_folder($folder);
//
//				$files->resolve_path('users/'.$gouser['username'], true);
//			}


			
		}

	}else
	{
		try{
			if (!$user_id = $GO_USERS->add_user($user,
			$GO_GROUPS->groupnames_to_ids(explode(',',$GLOBALS['GO_CONFIG']->register_user_groups)),
			$GO_GROUPS->groupnames_to_ids(explode(',',$GLOBALS['GO_CONFIG']->register_visible_user_groups)),
			explode(',',$GLOBALS['GO_CONFIG']->register_modules_read),
			explode(',',$GLOBALS['GO_CONFIG']->register_modules_write))) {
				echo "Failed creating user ".$user['username']."\n";
			}
		}catch(Exception $e){
			echo $e->getMessage()."\n";

			var_dump($user);
			//exit();
		}
	}

	if($user_id>1){
		$rec['user_id']=$user_id;
		$db->replace_row('ldap_sync',$rec);
	}

}

$db_count = $GO_USERS->get_users();
$db->query("SELECT count(*) AS count FROM ldap_sync");
$db->next_record();
$ldap_count = $db->f('count');



echo "Deleting ".($db_count-$ldap_count)." users\n\n";

$div = $db_count/$ldap_count;

echo $div."\n";

if($div>1.05)
{
	exit("Aborted because script was about to delete more then 5% of the users");
}

$sql = "SELECT id,username FROM go_users u LEFT JOIN ldap_sync l ON u.id=l.user_id WHERE ISNULL(l.user_id) ORDER BY username ASC";
$db->query($sql);
while($r = $db->next_record()){
	echo "Deleting ".$r['username']." (id: ".$r['id'].")\n";
	$GO_USERS->delete_user($r['id']);
}





echo "Setting calendar entries older then one month to private\n";

$sql = <<<EOF
UPDATE cal_events INNER JOIN cal_calendars ON cal_calendars.id=cal_events.calendar_id
SET private =  "1"
WHERE cal_calendars.name NOT LIKE 'gr\_%' AND ((
start_time < UNIX_TIMESTAMP( NOW( ) - INTERVAL 1 MONTH ) AND rrule =  ''
) OR (
repeat_end_time >0 AND repeat_end_time < UNIX_TIMESTAMP( NOW( ) - INTERVAL 1 MONTH )
))
EOF;

$db->query($sql);

echo "Done!\n";
