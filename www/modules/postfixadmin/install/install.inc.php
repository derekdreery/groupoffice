<?php

if(!empty($GLOBALS['GO_CONFIG']->serverclient_domains))
{
	global $GO_CONFIG, $GO_MODULES;

	$domains = explode(',', $GLOBALS['GO_CONFIG']->serverclient_domains);

	require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
	$GO_USERS = new GO_USERS();
	
	foreach($domains as $domain)
	{
		if(!empty($domain))
		{
			require_once ($GLOBALS['GO_CONFIG']->root_path."modules/postfixadmin/classes/postfixadmin.class.inc.php");
			$postfixadmin = new postfixadmin();

			$d['domain']=$domain;
			$d['user_id']=1;
			$d['transport']='virtual';
			$d['active']='1';
			$d['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('domain');

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


			if(isset($GLOBALS['GO_MODULES']->modules['email']))
			{
				require_once($GLOBALS['GO_MODULES']->modules['email']['class_path'].'email.class.inc.php');

				$email = new email();

				$user = $GO_USERS->get_user(1);

				$account['user_id']=1;
				$account['mbroot'] = $GLOBALS['GO_CONFIG']->serverclient_mbroot;
				$account['use_ssl'] = $GLOBALS['GO_CONFIG']->serverclient_use_ssl;
				$account['novalidate_cert'] = $GLOBALS['GO_CONFIG']->serverclient_novalidate_cert;
				$account['type']=$GLOBALS['GO_CONFIG']->serverclient_type;
				$account['host']=$GLOBALS['GO_CONFIG']->serverclient_host;
				$account['port']=$GLOBALS['GO_CONFIG']->serverclient_port;
				$account['username']=$mailbox['username'];
				$account['password']='admin';
				require_once($GLOBALS['GO_CONFIG']->class_path.'cryptastic.class.inc.php');
				$c = new cryptastic();

				$encrypted = $c->encrypt($account['password']);
				if($encrypted){
					$account['password']=$encrypted;
					$account['password_encrypted']=2;
				}
				
				$account['name']=String::format_name($user);
				$account['email']=$mailbox['username'];
				$account['smtp_host']=$GLOBALS['GO_CONFIG']->serverclient_smtp_host;
				$account['smtp_port']=$GLOBALS['GO_CONFIG']->serverclient_smtp_port;
				$account['smtp_encryption']=$GLOBALS['GO_CONFIG']->serverclient_smtp_encryption;
				$account['smtp_username']=$GLOBALS['GO_CONFIG']->serverclient_smtp_username;
				$account['smtp_password']=$GLOBALS['GO_CONFIG']->serverclient_smtp_password;

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
