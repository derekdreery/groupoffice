<?php
require_once( $GLOBALS['GO_CONFIG']->class_path.'mail/imap.class.inc' );

class imapauth extends db
{
	
	var $config;
	
	public function __construct(){
		
		global $GO_CONFIG;
		
		parent::__construct();
		
		$conf = str_replace('config.php', 'imapauth.config.php', $GO_CONFIG->get_config_file());
		
		if(file_exists($conf))
		{
			require($conf);
			$this->config=$config;
		}else
		{
			$this->config = array();
		}
	}
	
	private function get_domain_config($domain)
	{
		if(!empty($domain))
		{			
			foreach($this->config as $config)
			{
				if($config['domains']=='*')
				{
					return $config;
				}
				$domains = explode(',', $config['domains']);
				$domains = array_map('trim', $domains);
				
				if(in_array($domain, $domains));
				{
					return $config;
				}
			}
		}
		return false;
		
	}
	
	
	public function __on_before_login($arguments)
	{
		$arr = explode('@', $arguments['username']);
		
		$email = trim($arguments['username']);
		$mailbox = trim($arr[0]);
		$domain = isset($arr[1]) ? trim($arr[1]) : '';
		
		if($config = $this->get_domain_config($domain))
		{
			global $GO_CONFIG, $GO_SECURITY, $GO_LANGUAGE, $GO_USERS, $GO_GROUPS,
			$GO_MODULES;
						
	
			$GO_SECURITY->user_id = 0;
	
			require_once($GO_CONFIG->class_path.'mail/imap.class.inc');
			$imap = new imap();
	
			$go_username=$mail_username=$email;
			if ($config['remove_domain_from_username']) {
				$mail_username = $mailbox;
			}
			
			if ($imap->open(
				$config['host'], 
				$config['proto'], 
				$config['port'],
				$mail_username, 
				$arguments['password'], 
				'INBOX', 
				null, 
				$config['ssl'], 
				$config['novalidate_cert']))
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
							$email_client->update_password($config['host'], $mail_username, $arguments['password']);
						}
					}
	
				} else {
					//user doesn't exist. create it now
					
					$user['email'] =$email;
					$user['username'] = $go_username;
					//$user['first_name']=$go_username;
					//$user['last_name']='-';
					$user['password'] = $arguments['password'];
					$user['sex'] = 'M';
					// the user does not exist, so we have to add him.
					if ( !$user_id = $GO_USERS->add_user(
							$user, 
							$GO_GROUPS->groupnames_to_ids($config['groups']), 
							$GO_GROUPS->groupnames_to_ids($config['visible_groups']), 
							$config['modules_read'], 
							$config['modules_write']))
					{
						go_log(LOG_DEBUG, 'ERROR: Failed adding mail user to Group-Office. The user probably already existed. Try changing go_username_without_domain to true or false in imapauth config.');
	
					} else {
					
						$old_umask = umask( 000 );
						@mkdir( $GO_CONFIG->file_storage_path.'users/'.$email_address, $GO_CONFIG->create_mode );
						umask($old_umask);
	
						if ($config['create_email_account'])
						{
							if(isset($GO_MODULES->modules['email']))
							{
								require_once($GO_MODULES->modules['email']['class_path']."email.class.inc");
								require_once($GO_LANGUAGE->get_language_file('email'));
								$email_client = new email();					
								
								$account['user_id']=$user_id;
								$account['type']=$config['proto'];
								$account['host']=$config['host'];
								$account['port']=$config['port'];
								$account['use_ssl']=$config['ssl'];
								$account['novalidate_cert']=$config['novalidate_cert'];
								$account['mbroot']=$config['mbroot'];
								$account['username']=$mail_username;
								$account['password']=$arguments['password'];
								$account['name']=$email;
								$account['email']=$email;
								//$account['auto_check']=$config['auto_check_email'];
								
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
			}else
			{
				$imap->clear_errors();
			}
		}
	}
}