<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 *
 */

/*
 * This script can be used to create users in Group-Office with a CSV file
 * and migrate the mail through IMAP.
 *
 * This only works if the postfixadmin module runs on the same installation
 * where the new Group-Office users will be created.
 *
 * The script was written for version 3.5.9
 */

//the domain for the mail
$maildomain='example.com';

//the new mail host
$local_host = 'localhost';

//the old mail host
$remote_host = 'mail.example.com';

//the path to the imapsync program
$imapsync = '/usr/bin/imapsync';

//CSV file in format:
//"Group in GO 0","Email address 1","First name 2","Middle name 3","Last name 4","Password 5","GroupOffice User name 6"
$csv_file='/home/mschering/Downloads/users.csv';


//end of variables section.



//otherwise log module will log all items as added.
define('NOLOG', true);

if (isset($argv[1])) {
	define('CONFIG_FILE', $argv[1]);
}

chdir(dirname(__FILE__));

require_once("../../Group-Office.php");
require_once($GO_MODULES->modules['postfixadmin']['class_path'].'postfixadmin.class.inc.php');
$postfixadmin = new postfixadmin();

require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc.php');
$e = new email();

require_once($GO_CONFIG->class_path.'base/groups.class.inc.php');
$GO_GROUPS = new GO_GROUPS();

require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

//make sure e-mail domain exists in database
$domain = $postfixadmin->get_domain_by_domain($maildomain);
if(!$domain)
{
	$domain['transport']='virtual';
	$domain['active']='1';
	$domain['domain']=$maildomain;
	$domain['user_id']=1;
	$domain['acl_id']=$GO_SECURITY->get_new_acl('domain');

	$domain_id=$postfixadmin->add_domain($domain);
}else
{
	$domain_id=$domain['id'];
}

$fp = fopen($csv_file, "r");
if (!$fp) {
	die('Could not read CSV file');
}

//headings, skip one row
$record = fgetcsv($fp, 4096, ',', '"');

while ($record = fgetcsv($fp, 4096, ',', '"')) {
	

	$remote_user = $record[1];
	$remote_pass = $record[5];
	$groupoffice_user = $record[6];
	$email = $record[1];

	//Check if the user exists in Group-Office and if it doesn't create it.
	$user = $GO_USERS->get_user_by_username($groupoffice_user);
	if(!$user){
		$user['first_name']=$record[2];
		$user['middle_name']=$record[3];
		$user['last_name']=$record[4];
		$user['email']=$email;
		$user['username']=$groupoffice_user;
		$user['password']=$remote_pass;
		$user['enabled']='1';

		$group = $GO_GROUPS->get_group_by_name($record[0]);
		if(!$group){
			$group_id=$GO_GROUPS->add_group(1, $record[0]);
		}else
		{
			$group_id=$group['id'];
		}

		$user_id = $GO_USERS->add_user($user, array($group_id),array($GO_CONFIG->group_everyone,$group_id));
	}else
	{
		$user_id = $user['id'];
	}
	
	$pa_user=$groupoffice_user.'@'.$maildomain;

	//Create a mailbox in postfixadmin
	$mailbox = $postfixadmin->get_mailbox_by_username($pa_user);
	if(!$mailbox){
		$mailbox['domain_id']=$domain_id;
		$mailbox['username']=$pa_user;
		$mailbox['password']=md5($remote_pass);
		$mailbox['maildir']=$maildomain.'/'.$groupoffice_user.'/';
		$mailbox['active']='1';
		$mailbox_id= $postfixadmin->add_mailbox($mailbox);

		$alias['domain_id']=$mailbox['domain_id'];
		$alias['address']=$mailbox['username'];
		$alias['active']=$mailbox['active'];
		$alias['goto']=$mailbox['username'];

		$postfixadmin->add_alias($alias);

		if($email!=$pa_user){
			$alias['goto']=$email;

			$postfixadmin->add_alias($alias);
		}
	}

	//create an e-mail account for the user
	$account = $e->get_account_by_username($pa_user, $user_id);
	if(!$account){
		$account['user_id']=$user_id;
		$account['mbroot'] = '';
		$account['use_ssl'] = 0;
		$account['type']='imap';
		$account['host']='localhost';
		$account['port']=143;
		$account['username']=$pa_user;
		$account['password']=$remote_pass;

		$account['smtp_host']='localhost';
		$account['smtp_port']=25;
		$account['smtp_encryption']='';
		$account['smtp_username']='';
		$account['smtp_password']='';
		$account['name']=String::format_name($record[4],$record[2],$record[3],'first_name');
		$account['email']=$email;
		$account['signature']='';

		$e->add_account($account);
	}


	echo "Syncing " . $record[0] . "\n\n";

	//--skipsize --useheader Message-Id

	$cmd = $imapsync . ' --syncinternaldates --authmech1 LOGIN --authmech2 LOGIN ' .
					'--host1="' . $remote_host . '" --user1="' . $pa_user . '" --password1="' . $remote_pass . '" ' .
					'--user2="' . $remote_user . '" --host2="' . $local_host . '" --password2="' . $remote_pass . '"';

	echo $cmd . "\n\n";

	system($cmd);
}

