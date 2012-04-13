<?php
define('CONFIG_FILE', '/etc/groupoffice/servermanager.group-office.com/config.php');
require('../../../Group-Office.php');

require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

$doreal=false;
$movefolders=true;

$virtual_file='/etc/postfix/virtual';
$uid='vmail';
$gid='mail';

$root = '/vmail/';

require('../classes/postfixadmin.class.inc.php');

class postfiximport extends postfixadmin
{

	function replace_mailbox($old_username, $new_username, $new_password)
	
	{
		global $doreal;
		$this->query("SHOW DATABASES");

		$db = new postfixadmin();
		
		while($this->next_record())
		{			
			$database = $this->f('Database');			
			$db->query("SHOW TABLES FROM `$database`;");			
			while($db->next_record(DB_BOTH))
			{
				if($db->f(0)=='emAccounts' || $db->f(0)=='em_accounts')
				{
					$table = $db->f(0);
					
					$db->query("USE `$database`");

					
					$sql = "UPDATE `$table` SET username='$new_username', password='$new_password' WHERE username='$old_username'";					
					//echo $database."\n";
					//echo $sql."\n\n";
					
					if($doreal)
						$db->query($sql);
					
					break;
				}
			}
		}	

		$db->query("USE ".$this->Database);
	}
	
}

$pa = new postfiximport();

$data = file_get_contents($virtual_file);
$lines = explode("\n", $data);

$mailboxes = array();

$new_admins=array();
$new_mailboxes=array();
$new_aliases=array();
$new_paths=array();

$undo=array();
foreach($lines as $line)
{
	//strip excess white-space
	$line = preg_replace('/\t/', ' ', trim($line));
	$line = preg_replace('/\s\s+/', ' ', $line);
	
	
	if(!empty($line))
	{

	
		$values = explode(' ', $line);
		
		
		$alias_email = $email = $values[0];
		$system_mailbox = str_replace(',', ' ', $values[1]);
		$system_mailboxes_pre = explode(' ', $system_mailbox); 
		
		
				
		$system_mailboxes=array();
		foreach($system_mailboxes_pre as $sm)
		{			
			if(!empty($sm) && !strpos($sm,'@'))
			{
					//echo $sm."\n";
				$system_mailboxes[]=$sm;				
			}
		}
		
		//continue;
		

		
		if(!count($system_mailboxes))
			continue;
		
		$email_arr = explode('@', $email);
		
		$personal = $email_arr[0];
		$domain = $email_arr[1];
		
		if(empty($personal))
		{
			$personal = $system_mailboxes[0];
			$email = $system_mailboxes[0].$email;
		}
			
		
			
		if(!$db_domain = $pa->get_domain_by_domain($domain))
		{
			
			$admin['username']=$domain;
			
			$pass = $GO_USERS->random_password();
			$admin['password']=md5($pass);
			$admin['first_name']=$domain;
			$admin['last_name']='admin';
			$admin['email']='admin@'.$domain;
			
			if($doreal)
			{
				
				$user_id=$GO_USERS->add_user($admin);
				
			}
			
			$new_admins[] = $admin['username'].':'.$pass;
			
			
			$db_domain['user_id']=$user_id;
			$db_domain['domain']=$domain;
			$db_domain['transport']='virtual';
			if($doreal)
			{
				$db_domain['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('domain');
				$GLOBALS['GO_SECURITY']->add_user_to_acl($db_domain['acl_id'], $user_id, GO_SECURITY::WRITE_PERMISSION);
			}

			if($doreal)
				$db_domain['id'] = $pa->add_domain($db_domain);		
		}
		
		//don't use the current system name but use the first alias as mailbox name
		foreach($system_mailboxes as $system_mailbox)
		{
			if(!isset($mailboxes[$system_mailbox]))
			{
				$pass = $GO_USERS->random_password();
				$db_mailbox['domain_id']=$db_domain['id'];
				$db_mailbox['username']=$mailboxes[$system_mailbox]=$email;
				$db_mailbox['maildir']=$domain.'/'.$personal.'/';			
				$db_mailbox['password']=md5($pass);
				$db_mailbox['name']=$personal;
				$db_mailbox['active']=1;
				
				if($doreal)
					$pa->add_mailbox($db_mailbox);
				
				$new_mailboxes[]=$db_mailbox['username'].':'.$pass;
				
				$pa->replace_mailbox($system_mailbox, $email, $pass);
				
			
				if(!is_dir('/home/'.$system_mailbox))
				{
					echo "Error: /home/$system_mailbox doesn't exist!\n";
				}else
				{
					//now move the mailbox
					$domain_path = $root.$domain;
					$new_paths[]=$domain_path.'/'.$personal;
					if(!is_dir($domain_path))
						mkdir($domain_path,700, true);

					$undo[]='mv '.$domain_path.'/'.$personal.' /home/'.$system_mailbox;
	
					if($movefolders)
					{
						system("mv /home/$system_mailbox ".$domain_path.'/'.$personal);
					}						
				}			
			}
			
			$alias = $pa->get_alias_by_address($alias_email);
			
			if(!$alias)		
			{					
				if($doreal)
				{
					$alias['domain_id']=$db_domain['id'];
					$alias['goto']=$mailboxes[$system_mailbox];		
					$alias['address']=$alias_email;				
					$alias['active']=1;
					
					$pa->add_alias($alias);
				}
			}else
			{
				$up_alias['id']=$alias['id'];
				$up_alias['goto'].=','.$mailboxes[$system_mailbox];
				
				$pa->update_alias($up_alias);
			}
			$new_aliases[]=$alias_email.' -> '.$mailboxes[$system_mailbox];
			
			
					
		}
	}
}

system("chown $uid:$gid /vmail -R");

$report .= "New paths created: \n\n";
$report .= implode("\n", $new_paths);


$report .= "New domain admins created: \n\n";
$report .= implode("\n", $new_admins);

$report .= "\n\n\nNew mailboxes created: \n\n";
$report .= implode("\n", $new_mailboxes);

$report .= "\n\n\nNew aliases created: \n\n";
$report .= implode("\n", $new_aliases);

file_put_contents('log.txt', $report);

file_put_contents('undo.txt', implode("\n", $undo));

//echo $report;

?>
