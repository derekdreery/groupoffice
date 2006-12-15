<?php
$db1 = new db();
$db2 = new db();

$sql = "SELECT * FROM pmFees";
$db1->query($sql);

while($db1->next_record())
{
	$sql = "UPDATE pmHours SET ext_fee_time='".addslashes($db1->f('time'))."', ext_fee_value='".addslashes($db1->f('value'))."' WHERE fee_id=".$db1->f('id');
	$db2->query($sql);
	
	$acl_id = $GO_SECURITY->get_new_acl();
	
	$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_everyone, $acl_id);
	
	$sql = "UPDATE pmFees SET acl_id=".$acl_id." WHERE id=".$db1->f('id');
	$db2->query($sql);
}
?>
