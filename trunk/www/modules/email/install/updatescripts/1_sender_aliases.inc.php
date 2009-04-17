<?php
require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc.php');
$email = new email();
$email2 = new email();

$email->query("SELECT * FROM em_accounts");
while($account = $email->next_record())
{
	$alias['email']=$account['email'];
	$alias['name']=$account['name'];
	$alias['signature']=$account['signature'];
	$alias['default']='1';
	$alias['account_id']=$account['id'];
	
	$email2->add_alias($alias);
}
?>