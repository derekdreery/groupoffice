<?php
require_once($GO_CONFIG->root_path.'install/updatescripts/functions.inc.php');

$db->query("SELECT * FROM pa_domains WHERE acl_read>0");
while($folder=$db->next_record()){
	apply_write_acl($folder['acl_read'], $folder['acl_write']);
}
$db->query("ALTER TABLE `pa_domains` CHANGE `acl_read` `acl_id` INT( 11 ) NOT NULL DEFAULT '0'");

