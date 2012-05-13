<?php
require_once($GO_CONFIG->root_path.'install/updatescripts/functions.inc.php');

$db->query("SELECT * FROM cal_calendars WHERE acl_read>0");
while($folder=$db->next_record()){
	apply_write_acl($folder['acl_read'], $folder['acl_write']);
}
$db->query("ALTER TABLE `cal_calendars` CHANGE `acl_read` `acl_id` INT( 11 ) NOT NULL DEFAULT '0'");

$db->query("SELECT * FROM cal_views WHERE acl_read>0");
while($folder=$db->next_record()){
	apply_write_acl($folder['acl_read'], $folder['acl_write']);
}
$db->query("ALTER TABLE `cal_views` CHANGE `acl_read` `acl_id` INT( 11 ) NOT NULL DEFAULT '0'");

$db->query("SELECT * FROM cal_groups WHERE acl_admin>0");
while($folder=$db->next_record()){
	apply_write_acl($folder['acl_admin']);
}

