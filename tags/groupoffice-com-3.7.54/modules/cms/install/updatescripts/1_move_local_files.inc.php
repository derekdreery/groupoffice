<?php
$fs = new filesystem();

require_once($GO_MODULES->modules['cms']['class_path'].'cms.class.inc.php');
$cms = new cms();

if(is_dir($GO_CONFIG->local_path.'cms'))
{
	if(!is_dir($GO_CONFIG->file_storage_path.'public'))
		mkdir($GO_CONFIG->file_storage_path.'public');
	
	$fs->move($GO_CONFIG->local_path.'cms', $GO_CONFIG->file_storage_path.'public/cms');
	
	
	
	//$cms->__on_check_database();
        $cms->check_database();
}
if(isset($GO_MODULES->modules['files']))
{
	$sql = "UPDATE cms_files SET content=replace(content, '".$GO_CONFIG->local_url."','".$GO_MODULES->modules['files']['url']."download.php?path=public/');";
	$cms->query($sql);
}
?>