<?php
$db1 = new db();

$db1->query("SELECT * FROM fs_shares");

while($db1->next_record())
{
	if(!$GO_SECURITY->user_in_acl($db1->f('user_id'), $db1->f('acl_write')))
	{
		$GO_SECURITY->add_user_to_acl($db1->f('user_id'), $db1->f('acl_write'));
	}
}
?>
