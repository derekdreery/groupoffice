#!/usr/bin/php
<?php
if(isset($argv[1]))
	define('CONFIG_FILE', $argv[1]);

require('/etc/groupoffice/servermanager.inc.php');
require_once('/etc/groupoffice/config.php');

require($config['root_path'].'Group-Office.php');

//require('/etc/groupoffice/servermanager/config.inc.php');

if(!isset($GLOBALS['GO_MODULES']->modules['postfixadmin'])) {
	die('Fatal error: postfixadmin module must be installed');
}

if(!isset($GLOBALS['GO_CONFIG']->postfixadmin_vmail_root)) {
	if(is_dir('/home/vmail')) {
		$GLOBALS['GO_CONFIG']->postfixadmin_vmail_root='/home/vmail/';
	}elseif(is_dir('/vmail')) {
		$GLOBALS['GO_CONFIG']->postfixadmin_vmail_root='/vmail/';
	}
}

require_once($GLOBALS['GO_MODULES']->modules['postfixadmin']['class_path'].'postfixadmin.class.inc.php');
$pa = new postfixadmin();
$pa2 = new postfixadmin();

$pa->get_mailboxes();
while($pa->next_record()) {
	$path = $GLOBALS['GO_CONFIG']->postfixadmin_vmail_root.$pa->f('maildir');

	echo 'Calculating size of '.$path."\n";
	$mailbox['id']=$pa->f('id');
	$mailbox['usage']=File::get_directory_size($path);
	echo $mailbox['usage']."\n";
	$pa2->update_mailbox($mailbox);
}
?>
