<?php
require_once($GO_CONFIG->root_path.'install/updatescripts/functions.inc.php');

$db->query("SELECT acl_id FROM go_users WHERE acl_id>0");
while($folder=$db->next_record()){
	apply_write_acl($folder['acl_id']);
}