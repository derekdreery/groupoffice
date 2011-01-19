<?php
require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc.php');
$email = new email();
$email2 = new email();
$email2->halt_on_error='no';

if(isset($GO_MODULES->modules['mailings']))
{
	$db->query("ALTER TABLE `ml_mailings` ADD `alias_id` INT NOT NULL AFTER `account_id`");
}
$email->query("SELECT * FROM em_accounts");
while($account = $email->next_record())
{
	$alias['email']=$account['email'];
	$alias['name']=$account['name'];
	$alias['signature']=$account['signature'];
	$alias['default']='1';
	$alias['account_id']=$account['id'];
	
	$alias_id = $email2->add_alias($alias);
	
	if(isset($GO_MODULES->modules['mailings']))
	{
		$email2->query("UPDATE ml_mailings SET alias_id=".$alias_id." WHERE account_id=".$account['id']);
	}
}
?>