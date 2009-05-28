<?php
if(@file_exists('/etc/groupoffice/config-mailserver.inc.php')){

	global $GO_CONFIG;
	
	require('/etc/groupoffice/config-mailserver.inc.php');

	if(!empty($domain))
	{
		require_once ($GO_CONFIG->root_path."modules/postfixadmin/classes/postfixadmin.class.inc.php");
		$postfixadmin = new postfixadmin();

		$d['domain']=$domain;
		$d['user_id']=1;
		$d['transport']='virtual';
		$d['active']='1';
		$d['acl_read']=$GO_SECURITY->get_new_acl('domain');
		$d['acl_write']=$GO_SECURITY->get_new_acl('domain');

		$mailbox['domain_id']=$postfixadmin->add_domain($d);
		$mailbox['maildir']=$domain.'/admin/';
		$mailbox['username']='admin@'.$domain;
		$mailbox['active']='1';

		$postfixadmin->add_mailbox($mailbox);

		$alias['active']='1';
		$alias['goto']=$mailbox['username'];
		$alias['address']=$mailbox['username'];
		$alias['domain_id']=$mailbox['domain_id'];

		$postfixadmin->add_alias($alias);
	}
}