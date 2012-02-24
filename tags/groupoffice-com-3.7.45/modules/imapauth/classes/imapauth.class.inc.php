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
 */

require_once( $GLOBALS['GO_CONFIG']->class_path.'mail/imap.class.inc' );

class imapauth
{
	var $config;

	public function __on_load_listeners($events){
		$events->add_listener('before_login', __FILE__, 'imapauth', 'before_login');
		$events->add_listener('add_user', __FILE__, 'imapauth', 'add_user');
	}

	protected function get_domain_config($domain)
	{
		global $GO_CONFIG;

		if(!empty($domain))
		{
			$conf = str_replace('config.php', 'imapauth.config.php', $GO_CONFIG->get_config_file());

			if(file_exists($conf))
			{
				require($conf);
				$this->config=$config;
			}else
			{
				$this->config = array();
			}
			foreach($this->config as $config)
			{
				if($config['domains']=='*')
				{
					return $config;
				}
				$domains = explode(',', $config['domains']);
				$domains = array_map('trim', $domains);

				if(in_array($domain, $domains))
				{
					return $config;
				}
			}
		}
		return false;
	}


	public static function before_login($username, $password, $count_login)
	{
		$ia = new imapauth();

		go_debug('IMAPAUTH: module active');
		$arr = explode('@', $username);

		$email = trim($username);
		$mailbox = trim($arr[0]);
		$domain = isset($arr[1]) ? trim($arr[1]) : '';

		$config = $ia->get_domain_config($domain);
		if(!$config)
		{
			go_debug('IMAPAUTH: No config for domain found');
		}else
		{
			go_debug($config);

			global $GO_CONFIG, $GO_SECURITY, $GO_LANGUAGE, $GO_MODULES, $GO_EVENTS;


			$GO_SECURITY->user_id = 0;

			require_once($GO_CONFIG->class_path.'mail/imap.class.inc');
			$imap = new imap();

			$go_username=$mail_username=$email;
			if ($config['remove_domain_from_username']) {
				$mail_username = $mailbox;
			}

			go_debug('IMAPAUTH: Attempt IMAP login');

			try{
				if ($imap->connect(
				$config['host'],
				$config['port'],
				$mail_username,
				$password,
				$config['ssl']))
				{
					go_debug('IMAPAUTH: IMAP login succesful');
					$imap->disconnect();

					require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
					$GO_USERS = new GO_USERS();

          require_once($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
          $email_client = new email();

					$user = $GO_USERS->get_user_by_username($go_username);
					if ($user) {

						go_debug("IMAPAUTH: Group-Office user already exists.");
            if(!$email_client->get_account_by_username($mail_username, $user['id'],$config['host'])){
              go_debug('IMAPAUTH: E-mail account not found. Creating it now.');
              $ia->create_email_account($config, $user['id'], $mail_username, $password, $email);
            }

						//user exists. See if the password is accurate
						if(crypt($password, $user['password']) != $user['password'])
						{
							go_debug('IMAPAUTH: IMAP password has changed. Updating Group-Office database');

							$GO_USERS->update_profile(array('id'=>$user['id'], 'password'=>$password));

							if(isset($GO_MODULES->modules['email']))
							{
								
								$email_client->update_password($config['host'], $mail_username, $password, $config['smtp_use_login_credentials']);
							}
						}

					} else {

            go_debug("IMAPAUTH: Group-Office user doesn't exists.");
						//user doesn't exist. create it now
						$user['email'] =$email;
						$user['username'] = $go_username;
						$user['password'] = $password;

						require_once($GO_CONFIG->class_path.'base/groups.class.inc.php');
						$GO_GROUPS = new GO_GROUPS();

            
						if ( !$user_id = $GO_USERS->add_user(
						$user,
						$GO_GROUPS->groupnames_to_ids($config['groups']),
						$GO_GROUPS->groupnames_to_ids($config['visible_groups']),
						$config['modules_read'],
						$config['modules_write']))
						{
							trigger_error('IMAPAUTH: Failed creating user '.$go_username.' and e-mail '.$email.' with imapauth. The e-mail address probably already existed at another user.', E_USER_WARNING);
						} else {
							$ia->create_email_account($config, $user_id, $mail_username, $password, $email);
						}
					}
				}else
				{
					go_debug('IMAPAUTH: Authentication to IMAP server failed '.$imap->last_error());
					$imap->clear_errors();
					
					$GLOBALS['GO_SECURITY']->logout(); //for clearing remembered password cookies
					
					$args = array(&$username, &$password, $count_login);
					$GO_EVENTS->fire_event('bad_login', $args);
					
					go_infolog("LOGIN FAILED for user: \"".$username."\" from IP: ".$_SERVER['REMOTE_ADDR']);
			
				
					throw new Exception($GLOBALS['lang']['common']['badLogin']);
				}
			}catch(Exception $e){
				go_debug('IMAPAUTH: Authentication to IMAP server failed with Exception: '.$e->getMessage().' IMAP error:'.$imap->last_error());
				$imap->clear_errors();
				
				$args = array(&$username, &$password, $count_login);
				$GO_EVENTS->fire_event('bad_login', $args);
				
				$GLOBALS['GO_SECURITY']->logout(); //for clearing remembered password cookies
				
				go_infolog("LOGIN FAILED for user: \"".$username."\" from IP: ".$_SERVER['REMOTE_ADDR']);
			
				throw new Exception($GLOBALS['lang']['common']['badLogin']);
			}
		}
	}

	protected function create_email_account($config, $user_id, $username, $password, $email){
		global $GO_MODULES, $GO_LANGUAGE, $GO_SECURITY;
		if ($config['create_email_account'])
		{
			if(isset($GO_MODULES->modules['email']))
			{
				require_once($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
				require_once($GO_LANGUAGE->get_language_file('email'));
				$email_client = new email();

				$account['user_id']=$user_id;
				$account['type']='imap';//$config['proto'];
				$account['host']=$config['host'];
				$account['smtp_host']=$config['smtp_host'];
				$account['smtp_port']=$config['smtp_port'];
				$account['smtp_encryption']=$config['smtp_encryption'];

				if(!empty($config['smtp_use_login_credentials'])){
					$account['smtp_username']=$username;
					$account['smtp_password']=$password;
				}else
				{
					$account['smtp_username']=$config['smtp_username'];
					$account['smtp_password']=$config['smtp_password'];
				}

				$account['port']=$config['port'];
				$account['use_ssl']=$config['ssl'];
				//$account['novalidate_cert']=$config['novalidate_cert'];
				$account['mbroot']=$config['mbroot'];
				$account['username']=$username;
				$account['password']=$password;
				$account['name']=$email;
				$account['email']=$email;
				$account['acl_id']=$GO_SECURITY->get_new_acl('email', $account['user_id']);

				if (!$account_id = $email_client->add_account($account))
				{
					go_debug('IMAPAUTH: Failed creating e-mail account for user '.$username.' in imapauth module.');
					trigger_error('IMAPAUTH: Failed creating e-mail account for user '.$username.' in imapauth module.', E_USER_WARNING);
				}else
				{
					go_debug('IMAPAUTH: Created IMAP account successfully');
					$_SESSION['GO_SESSION']['imapauth']['new_account_id']=$account_id;
					$account = $email_client->get_account($account_id);
					$account = $email_client->decrypt_account($account);
					$email_client->synchronize_folders($account);
				}
			}
		}
	}

	public static function add_user($user)
	{
		global $GO_MODULES;

		require_once($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
		$email_client = new email();

		if(!empty($_SESSION['GO_SESSION']['imapauth']['new_account_id']))
		{
			go_debug('IMAPAUTH: updating e-mail account from user profile');

			$up_account['id']=$_SESSION['GO_SESSION']['imapauth']['new_account_id'];
			$up_account['name']=String::format_name($user);
			$email_client->_update_account($up_account);

			unset($_SESSION['GO_SESSION']['imapauth']['new_account_id']);
		}
	}
}
