<?php
//This script will reset user passwords and send the invitation configured in
//the users module.
//uncomment the following line to enable this script. Don't leave it because
//anyone can reset your passwords with this script.
exit();

//modify this so not everyone will get a new password!
//Admins group will always be skipped
$exclude_groups="Some group, Another group";



//don't modify stuff below

if(isset($argv[1]))
{
    define('CONFIG_FILE', $argv[1]);
}

require('../../Group-Office.php');

require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

require_once($GO_MODULES->modules['users']['class_path'].'users.class.inc.php');
$users = new users();

$_email = $users->get_register_email();

require_once($GO_CONFIG->class_path.'base/groups.class.inc.php');
$GO_GROUPS = new GO_GROUPS();

$exclude_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$exclude_groups)));

$exclude_groups[]=1;

$GO_USERS2 = new GO_USERS();
$count = $GO_USERS2->get_users();

echo $count." users\n";
	

while($user = $GO_USERS2->next_record()) {
	$skip=false;
	foreach($exclude_groups as $group_id) {
		if($GO_GROUPS->is_in_group($user['id'], $group_id)) {
			echo "Skipping ".$user['username']."\n";
			$skip=true;
		}
	}
	if(!$skip) {
		$email = $_email;

		$up_user['id']=$user['id'];
		$up_user['password']=$GO_USERS->random_password();
		$GO_USERS->update_profile($up_user);


		unset($user['password']);

		require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
		$swift = new GoSwift($user['email'], $email['register_email_subject']);
		foreach($user as $key=>$value) {
			$email['register_email_body'] = str_replace('{'.$key.'}', $value, $email['register_email_body']);
		}

		$email['register_email_body']= str_replace('{url}', $GO_CONFIG->full_url, $email['register_email_body']);
		$email['register_email_body']= str_replace('{title}', $GO_CONFIG->title, $email['register_email_body']);
		$email['register_email_body']= str_replace('{password}', $up_user['password'], $email['register_email_body']);

		echo "Sending e-mail to ".$user['email']." ".$up_user['password']."\n";

		$swift->set_body($email['register_email_body'],'plain');
		$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
		$swift->sendmail();
	}
}
