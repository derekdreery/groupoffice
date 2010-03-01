<?php
require_once($GO_CONFIG->root_path.'install/updatescripts/functions.inc.php');

$db->query("SELECT * FROM fs_folders WHERE acl_read>0");
while($folder=$db->next_record()){
	apply_write_acl($folder['acl_read'], $folder['acl_write'], false);
}
$db->query("ALTER TABLE `fs_folders` CHANGE `acl_read` `acl_id` INT( 11 ) NOT NULL DEFAULT '0'");

$db->query("SELECT * FROM fs_templates");
while($folder=$db->next_record()){
	apply_write_acl($folder['acl_read'], $folder['acl_write']);
}
$db->query("ALTER TABLE `fs_templates` CHANGE `acl_read` `acl_id` INT( 11 ) NOT NULL DEFAULT '0'");