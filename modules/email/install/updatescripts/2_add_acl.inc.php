<?php
require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc.php');
$email = new email();

$email->query("SELECT id,user_id FROM em_accounts WHERE acl_id=0");
while($account = $email->next_record())
{
	$account['acl_id']=$GO_SECURITY->get_new_acl('email', $account['user_id']);
	$db->update_row('em_accounts', 'id', $account);
}
?>