#!/usr/bin/php
<?php
require('/etc/groupoffice/config.php');
require($config['root_path'].'Group-Office.php');



if(!empty(GO::config()->serverclient_domains))
{
	global $GO_CONFIG, $GO_MODULES;

	require_once(GO::config()->class_path.'base/users.class.inc.php');
	$GO_USERS = new GO_USERS();

	$domains = explode(',', GO::config()->serverclient_domains);

	foreach($domains as $domain)
	{
		if(!empty($domain))
		{
			if(isset(GO::modules()->modules['email']))
			{
				require_once(GO::modules()->modules['email']['class_path'].'email.class.inc.php');

				$email = new email();

				$user = $GO_USERS->get_user(1);

				$sql = "SELECT * FROM em_accounts WHERE user_id=1 AND username=?";
				$email->query($sql, 's', 'admin@'.$domain);

				if(!$email->num_rows())
				{
					$account['user_id']=1;
					$account['mbroot'] = GO::config()->serverclient_mbroot;
					$account['use_ssl'] = GO::config()->serverclient_use_ssl;
					$account['novalidate_cert'] = GO::config()->serverclient_novalidate_cert;
					$account['type']=GO::config()->serverclient_type;
					$account['host']=GO::config()->serverclient_host;
					$account['port']=GO::config()->serverclient_port;
					$account['username']='admin@'.$domain;
					$account['password']='admin';
					$account['name']=String::format_name($user);
					$account['email']='admin@'.$domain;
					$account['smtp_host']=GO::config()->serverclient_smtp_host;
					$account['smtp_port']=GO::config()->serverclient_smtp_port;
					$account['smtp_encryption']=GO::config()->serverclient_smtp_encryption;
					$account['smtp_username']=GO::config()->serverclient_smtp_username;
					$account['smtp_password']=GO::config()->serverclient_smtp_password;
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
