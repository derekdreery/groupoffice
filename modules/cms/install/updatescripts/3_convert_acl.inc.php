<?php
require_once($GO_CONFIG->root_path.'install/updatescripts/functions.inc.php');

$db->query("SELECT * FROM cms_folders WHERE acl>0");
while($folder=$db->next_record()){
	apply_write_acl($folder['acl_id']);
}


$db->query("SELECT * FROM cms_sites WHERE acl_write>0");
while($folder=$db->next_record()){
	apply_write_acl($folder['acl_write']);
}