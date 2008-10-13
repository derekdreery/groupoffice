<?php
require_once( $GLOBALS['GO_CONFIG']->class_path.'mail/imap.class.inc' );

class imapauth extends db
{
	
	var $config;
	
	public function __construct(){
		
		global $GO_MODULES;
		
		$this->db();
		
		if(file_exists($GO_MODULES->modules['imapauth']['path'].'config.inc.php'))
		{
			require($GO_MODULES->modules['imapauth']['path'].'config.inc.php');
			$this->config=$config;
		}
	}

	public function __on_before_login($arguments)
	{
		
		if(isset($this->config))
		{
			global $GO_CONFIG, $GO_SECURITY, $GO_LANGUAGE, $GO_USERS, $GO_GROUPS,
			$GO_MODULES;
	
			$GO_SECURITY->user_id = 0;
	
			require_once($GO_CONFIG->class_path.'mail/imap.class.inc');
			$imap = new imap();
	
			$mail_username=$arguments['username'];
			$go_username=$arguments['username'];
			
			$email_address = $arguments['username'].'@'.$this->config['domain'];
			if ($this->config['add_domain_to_username']) {
				$mail_username = $email_address;
			}
			
			if (!isset($this->config['go_username_without_domain']) || !$this->config['go_username_without_domain']) {
				$go_username = $email_address;
			}else{
				$go_username = str_replace('@'.$this->config['domain'], '', $go_username);
			}
			
	
			if ($imap->open(
				$this->config['host'], 
				$this->config['proto'], 
				$this->config['port'],
				$mail_username, 
				$arguments['password'], 
				'INBOX', 
				0, 
				$this->config['ssl'], 
				$this->config['novalidate_cert']))
			{
				$imap->close();
	
				if ($user = $GO_USERS->get_user_by_username( $go_username ) ) {
					
					//user exists. See if the password is accurate				
					if(md5($arguments['password']) != $user['password'])
					{
						$GO_USERS->update_password($user_id, $arguments['password']);	
						if(isset($GO_MODULES->modules['email']))
						{
							require_once($GO_MODULES->modules['email']['class_path']."email.class.inc");
							$email_client = new email();
							$email_client->update_password($this->config['host'], $mail_username, $arguments['password']);
						}
					}
	
				} else {
					//user doesn't exist. create it now
					
					$user['email'] =$email_address;
					$user['username'] = $go_username;
					$user['password'] = $arguments['password'];
					$user['sex'] = 'M';
					// the user does not exist, so we have to add him.
					if ( !$user_id = $GO_USERS->add_user(
							$user, 
							$GO_GROUPS->groupnames_to_ids($this->config['groups']), 
							$GO_GROUPS->groupnames_to_ids($this->config['visible_groups']), 
							$this->config['modules_read'], 
							$this->config['modules_write']))
					{
						go_log(LOG_DEBUG, 'ERROR: Failed adding mail user to Group-Office. The user probably already existed. Try changing go_username_without_domain to true or false in imapauth config.');
	
					} else {
					
						$old_umask = umask( 000 );
						@mkdir( $GO_CONFIG->file_storage_path.'users/'.$email_address, $GO_CONFIG->create_mode );
						umask($old_umask);
	
						if ($this->config['create_email_account'])
						{
							if(isset($GO_MODULES->modules['email']))
							{
								require_once($GO_MODULES->modules['email']['class_path']."email.class.inc");
								require_once($GO_LANGUAGE->get_language_file('email'));
								$email_client = new email();					
								
								$account['user_id']=$user_id;
								$account['type']=$this->config['proto'];
								$account['host']=$this->config['host'];
								$account['port']=$this->config['port'];
								$account['use_ssl']=$this->config['ssl'];
								$account['novalidate_cert']=$this->config['novalidate_cert'];
								$account['mbroot']=$this->config['mbroot'];
								$account['username']=addslashes($arguments['username']);
								$account['password']=addslashes($arguments['password']);
								$account['name']=addslashes($email_address);
								$account['email']=addslashes($email_address);
								$account['auto_check']=$this->config['auto_check_email'];
								
								if (!$account_id = $email_client->add_account($account))
								{								
									go_log(LOG_DEBUG, 'ERROR: Failed to create e-mail account in imapauth module.');
								}else
								{
									$account = $email_client->get_account($account_id);
									$email_client->synchronize_folders($account);
								}
							}
						}
					}
				}
			}	
		}
	}
}