<?php
if(isset($argv[1]))
	define('CONFIG_FILE', $argv[1]);
	
$root_path = dirname(dirname(dirname(__FILE__)));

require($root_path.'/Group-Office.php');

//require('/etc/groupoffice/servermanager/config.inc.php');

if(!isset($GO_MODULES->modules['postfixadmin']))
{
	die('Fatal error: postfixadmin module must be installed');
}



require_once($GO_MODULES->modules['postfixadmin']['class_path'].'postfixadmin.class.inc.php');
$pa = new postfixadmin();
$pa2 = new postfixadmin();

$pa->get_mailboxes();
while($pa->next_record())
{
	$arr = explode('@', $pa->f('username'));
	$path = $GO_CONFIG->postfixadmin_vmail_root.$arr[1].'/'.$arr[0];	
	
	
	$mailbox['id']=$pa->f('id');
	$mailbox['usage']=File::get_directory_size($path);
	
	$pa2->update_mailbox($mailbox);
}
?>