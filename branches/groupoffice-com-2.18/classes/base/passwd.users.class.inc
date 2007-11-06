<?php
/**
* @copyright Intermesh 2005
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.13 $ $Date: 2006/10/20 12:36:43 $3
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
 require_once($GO_CONFIG->class_path.'base/sql.users.class.inc');
 
 /**
 * This is the passwd class of the user management class family
 * This class totaly relies on the SQL classes but only changes the system
 * password with: sudo /usr/sbin/chpasswd
 * 
 * You need to give the Apache user permissions for chpasswd with visudo for
 * this to work.
 * 
 * Example entry in /etc/sudoers in case the Apache username is apache:
 * 
 * apache  ALL=NOPASSWD:/usr/sbin/chpasswd
 * apache  ALL=NOPASSWD:/usr/sbin/useradd
 * apache  ALL=NOPASSWD:/usr/sbin/userdel
 * 
 * NOPASSWD is required for this to work.
 *
 * @package Framework
 * @author   Merijn Schering <mschering@intermesh.nl>
 * @since    Group-Office 2.05
 */
 
class passwd_users extends sql_users
{
	function passwd_users()
	{
		$this->sql_users();
	}
	
	function update_password($user_id, $password)
	{
		global $GO_CONFIG;
		
		$sql = "UPDATE users SET password='".md5($password)."' WHERE id='$user_id'";
		if ($this->query($sql))
		{
			if($user = $this->get_user($user_id))
			{			
				if(isset($_SESSION['auth_source']) && 
					$_SESSION['auth_source']['type'] == 'email')
				{
					$username = str_replace('@'.$_SESSION['auth_source']['domain'],'', $user['username']);
					
					global $GO_MODULES;
					
					if($_SESSION['auth_source']['user_manager'] == 'passwd' && $GO_MODULES->modules['email'])
					{
						if($_SESSION['auth_source']['add_domain_to_username'])
						{
							$mail_username = $user['username'];
						}else
						{
							$mail_username = $username;
						}
						require_once($GO_MODULES->modules['email']['class_path']."email.class.inc");
						$email = new email();
						$email->update_password($_SESSION['auth_source']['host'], $mail_username, addslashes($password));
					}					
				}else
				{
					$username = $user['username'];
				}				
				exec('echo '.$username.':'.$password.' | '.$GO_CONFIG->cmd_sudo.' '.$GO_CONFIG->cmd_chpasswd);
				return true;
			}			
		}
		return false;
	}
	
	function add_user(
		$user, 
		$user_groups=array(), 
		$visible_user_groups=array(), 
		$modules_read=array(), 
		$modules_write=array(),
		$acl=array())
	{
		
		global $GO_CONFIG, $GO_MODULES, $GO_LANGUAGE;
		
		if(empty($user['password']))
		{
			$password=$this->random_password();
		}else {
			$password=$user['password'];
		}
		
		exec($GO_CONFIG->cmd_sudo.' useradd -m '.$GO_CONFIG->id.'_'.$user['username']);
		exec('echo '.$GO_CONFIG->id.'_'.$user['username'].':'.$password.' | '.$GO_CONFIG->cmd_sudo.' '.$GO_CONFIG->cmd_chpasswd);
		
		if(!empty($GO_CONFIG->cmd_edquota) && !empty($GO_CONFIG->quota_protouser))
		{
				exec($GO_CONFIG->cmd_sudo.' '.$GO_CONFIG->cmd_edquota.' -p '.$GO_CONFIG->quota_protouser.' '.$GO_CONFIG->id.'_'.$user['username']);
		}
		
		if(isset($_SESSION['auth_source']['postfix_virtual']) && file_exists($_SESSION['auth_source']['postfix_virtual']))
		{
			$this->add_email_alias($GO_CONFIG->id.'_'.$user['username'], $user['email'], $_SESSION['auth_source']['postfix_virtual']);
		}
		
		$user_id = parent::add_user($user, $user_groups, $visible_user_groups, $modules_read, $modules_write, $acl);
		
		if(!$user_id)
		{
			return false;
		}
		
		if($_SESSION['auth_source']['create_email_account'] && isset($GO_MODULES->modules['email']))
		{
			require_once($GO_MODULES->modules['email']['class_path']."email.class.inc");
			$email = new email();					
			if(!$account_id = $email->add_account(
				$user_id,
				$_SESSION['auth_source']['proto'],
				$_SESSION['auth_source']['host'],
				$_SESSION['auth_source']['port'],
				$_SESSION['auth_source']['ssl'],
				$_SESSION['auth_source']['novalidate_cert'],
				$_SESSION['auth_source']['mbroot'],
				$GO_CONFIG->id.'_'.$user['username'],
				$password,
				format_name($user['last_name'], $user['first_name'], $user['middle_name'],'first_name'),
				$user['email'],
				"",
				$_SESSION['auth_source']['auto_check_email']
				))
			{
				require_once($GO_LANGUAGE->get_language_file('email'));
				echo "<p class=\"Error\">".$registration_email_error."</p>";
				echo "<p class=\"Error\">".$email->last_error."</p>";
			}else
			{
				$account = $email->get_account($account_id);
				$email->synchronize_folders($account);
			
			}
		}
		
		return $user_id;
	}
	
	function delete_user($user_id)
	{
		global $GO_CONFIG;
		
		$user = $this->get_user($user_id);
		
		exec($GO_CONFIG->cmd_sudo.' userdel '.$GO_CONFIG->id.'_'.$user['username']);
				
		if(isset($_SESSION['auth_source']['postfix_virtual']) && file_exists($_SESSION['auth_source']['postfix_virtual']))
		{
			$this->delete_aliases($GO_CONFIG->id.'_'.$user['username'], $_SESSION['auth_source']['postfix_virtual']);
		}
		return parent::delete_user($user_id);
	}
	
	function delete_aliases($username, $postfix_virtual_file)
	{
		global $GO_CONFIG;
		
		$virtual = file_get_contents($postfix_virtual_file);
		
		if($virtual === false)
		{
			return false;
		}
		
		$lines = explode("\n", $virtual);
		$virtual = '';
		foreach($lines as $line)
		{
			if(!strpos($line, ' '.$username))
			{
				$virtual .= $line."\n";
			}
		}
		
		if(!$fp = fopen($GO_CONFIG->tmpdir.'virtual','w+'))
		{
			return false;
		}
		
		if(!fwrite($fp, $virtual)) {
     		return false;
		}
		fclose($fp);
		
		system('sudo '.$GO_CONFIG->cmd_alias.' '.$postfix_virtual_file.' '.$GO_CONFIG->tmpdir.'virtual');
		return true;		
	}
	
	function add_email_alias($username, $email, $postfix_virtual_file)
	{
		global $GO_CONFIG;
		
		$virtual = file_get_contents($postfix_virtual_file);
		
		if($virtual === false)
		{
			return false;
		}
		$virtual .="\n".$email.' '.$username;
		
		if(!$fp = fopen($GO_CONFIG->tmpdir.'virtual','w+'))
		{
			return false;
		}
		
		if(!fwrite($fp, $virtual)) {
     		return false;
		}
		fclose($fp);
   
		
		/*exec('sudo mv '.$postfix_virtual_file.' '.$postfix_virtual_file.'.bak');
		exec('sudo mv '.$GO_CONFIG->tmpdir.'virtual '.$postfix_virtual_file);
		exec('chown root '.$postfix_virtual_file);
		exec('sudo postmap '.$postfix_virtual_file);*/

		system('sudo '.$GO_CONFIG->root_path.'lib/scripts/alias.sh '.$postfix_virtual_file.' '.$GO_CONFIG->tmpdir.'virtual');
		return true;		
	}
}
