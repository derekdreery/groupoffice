<?php
function apply_write_acl($acl_id, $old_acl_write=0, $delete=true){
	global $GO_SECURITY;
	
	$selectdb = new db();
	$updatedb = new db();

	if($acl_id>0){

		$sql = "UPDATE go_acl SET level=1 WHERE acl_id=".$acl_id." AND level=-1";
		$updatedb->query($sql);

		if($old_acl_write>0){
			$selectdb->query("SELECT * FROM go_acl WHERE acl_id=".$old_acl_write);
			while($record = $selectdb->next_record()){
				$r['acl_id']=$acl_id;
				$r['user_id']=$record['user_id'];
				$r['group_id']=$record['group_id'];
				$r['level']=3;

				$updatedb->replace_row('go_acl', $r);
			}
			if($delete)
				$GO_SECURITY->delete_acl($old_acl_write);
		}

		$sql = "SELECT * FROM go_acl_items WHERE id=".$acl_id;
		$selectdb->query($sql);
		$acl = $selectdb->next_record();

		if(!empty($acl['user_id']))
			$GO_SECURITY->add_user_to_acl($acl['user_id'], $acl['id'], 4);

		$GO_SECURITY->add_group_to_acl(1, $acl_id, 4);
	}

}