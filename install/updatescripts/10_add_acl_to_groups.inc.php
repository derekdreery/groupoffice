<?php
$db2 = new db();

$db->query("SELECT * FROM go_groups");
while($r=$db->next_record()){
	$acl_id = $GO_SECURITY->get_new_acl('group', 1);

	$record['id']=$r['id'];
	$record['acl_id']=$acl_id;

	$db2->update_row('go_groups','id',$record);
}