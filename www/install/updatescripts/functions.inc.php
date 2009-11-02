<?php
function apply_write_acl($acl_id, $old_acl_write){
	global $GO_SECURITY;
	
	$selectdb = new db();
	$updatedb = new db();

	

	$sql = "UPDATE go_acl SET level=1 WHERE acl_id=".$acl_id." AND level=-1";
	$updatedb->query($sql);

	$selectdb->query("SELECT * FROM go_acl WHERE acl_id=".$old_acl_write);
	while($record = $selectdb->next_record()){
		$sql = "UPDATE go_acl SET level=3 WHERE acl_id=".$acl_id." AND user_id=".$record['user_id']." AND group_id=".$record['group_id'];
		$updatedb->query($sql);
	}

	$GO_SECURITY->delete_acl($old_acl_write);

}