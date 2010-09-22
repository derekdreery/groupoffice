<?php
require_once($GO_CONFIG->root_path.'install/updatescripts/functions.inc.php');
$db->query("SELECT * FROM go_modules");
while($r=$db->next_record()){
	apply_write_acl($r['acl_read'], $r['acl_write']);
}
$db->query("ALTER TABLE `go_modules` CHANGE `acl_read` `acl_id` INT( 11 ) NOT NULL");

