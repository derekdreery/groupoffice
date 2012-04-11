#!/usr/bin/php
<?php
require('/etc/groupoffice/config.php');
require($config['root_path'].'Group-Office.php');



if(!empty($GLOBALS['GO_CONFIG']->serverclient_domains))
{
	global $GO_CONFIG, $GO_MODULES, $GO_SECURITY;

	require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
	$GO_USERS = new GO_USERS();

	$domains = explode(',', $GLOBALS['GO_CONFIG']->serverclient_domains);

	foreach($domains as $domain)
	{
		if(!empty($domain))
		{
			if(isset($GLOBALS['GO_MODULES']->modules['email']))
			{
				require_once($GLOBALS['GO_MODULES']->modules['email']['class_path'].'email.class.inc.php');

				$email = new email();

				$user = $GO_USERS->get_user(1);

				$sql = "SELECT * FROM em_accounts WHERE user_id=1 AND username=?";
				$email->query($sql, 's', 'admin@'.$domain);

				if(!$email->num_rows())
				{
					$account['user_id']=1;
					$account['mbroot'] = $GLOBALS['GO_CONFIG']->serverclient_mbroot;
					$account['use_ssl'] = $GLOBALS['GO_CONFIG']->serverclient_use_ssl;
					$account['novalidate_cert'] = $GLOBALS['GO_CONFIG']->serverclient_novalidate_cert;
					$account['type']=$GLOBALS['GO_CONFIG']->serverclient_type;
					$account['host']=$GLOBALS['GO_CONFIG']->serverclient_host;
					$account['port']=$GLOBALS['GO_CONFIG']->serverclient_port;
					$account['username']='admin@'.$domain;
					$account['password']='admin';
					$account['name']=String::format_name($user);
					$account['email']='admin@'.$domain;
					$account['smtp_host']=$GLOBALS['GO_CONFIG']->serverclient_smtp_host;
					$account['smtp_port']=$GLOBALS['GO_CONFIG']->serverclient_smtp_port;
					$account['smtp_encryption']=$GLOBALS['GO_CONFIG']->serverclient_smtp_encryption;
					$account['smtp_username']=$GLOBALS['GO_CONFIG']->serverclient_smtp_username;
					$account['smtp_password']=$GLOBALS['GO_CONFIG']->serverclient_smtp_password;
					$account['acl_id']=$GO_SECURITY->get_new_acl('email');
					
					$GO_SECURITY->add_user_to_acl(1, $account['acl_id'],GO_SECURITY::MANAGE_PERMISSION);
					
					try{
						$account['id'] = $email->add_account($account);

						if($account['id']>0)
						{
							//get the account because we need special folder info
							$account = $email->get_account($account['id']);
							$email->synchronize_folders($account);

							echo 'Added admin e-mail accounts for '.$account['username']."\n";
						}
					}
					catch(Exception $e){

						//ignore errors, user probably removed the admin account manually.
						//echo 'Failed adding admin account: '.$e->getMessage()."\n";
					}
				}
			}
		}
	}
}
