<?php

if(!empty(GO::config()->serverclient_domains))
{
	global $GO_CONFIG, $GO_MODULES;

	$domains = explode(',', GO::config()->serverclient_domains);

	require_once(GO::config()->class_path.'base/users.class.inc.php');
	$GO_USERS = new GO_USERS();
	
	foreach($domains as $domain)
	{
		if(!empty($domain))
		{
			require_once (GO::config()->root_path."modules/postfixadmin/classes/postfixadmin.class.inc.php");
			$postfixadmin = new postfixadmin();

			$d['domain']=$domain;
			$d['user_id']=1;
			$d['transport']='virtual';
			$d['active']='1';
			$d['acl_id']=GO::security()->get_new_acl('domain');

			$mailbox['domain_id']=$postfixadmin->add_domain($d);
			$mailbox['maildir']=$domain.'/admin/';
			$mailbox['username']='admin@'.$domain;
			$mailbox['active']='1';
			$mailbox['password']=md5('admin');

			$postfixadmin->add_mailbox($mailbox);

			$alias['active']='1';
			$alias['goto']=$mailbox['username'];
			$alias['address']=$mailbox['username'];
			$alias['domain_id']=$mailbox['domain_id'];

			$postfixadmin->add_alias($alias);


			if(isset(GO::modules()->modules['email']))
			{
				require_once(GO::modules()->modules['email']['class_path'].'email.class.inc.php');

				$email = new email();

				$user = $GO_USERS->get_user(1);

				$account['user_id']=1;
				$account['mbroot'] = GO::config()->serverclient_mbroot;
				$account['use_ssl'] = GO::config()->serverclient_use_ssl;
				$account['novalidate_cert'] = GO::config()->serverclient_novalidate_cert;
				$account['type']=GO::config()->serverclient_type;
				$account['host']=GO::config()->serverclient_host;
				$account['port']=GO::config()->serverclient_port;
				$account['username']=$mailbox['username'];
				$account['password']='admin';
				require_once(GO::config()->class_path.'cryptastic.class.inc.php');
				$c = new cryptastic();

				$encrypted = $c->encrypt($account['password']);
				if($encrypted){
					$account['password']=$encrypted;
					$account['password_encrypted']=2;
				}
				
				$account['name']=String::format_name($user);
				$account['email']=$mailbox['username'];
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
					}
				}
				catch(Exception $e){
					go_debug('POSTFIXADMIN: '.$e->getMessage());
				}
			}
		}
	}
}
