<?php
$fs = new filesystem();

require_once(GO::modules()->modules['cms']['class_path'].'cms.class.inc.php');
$cms = new cms();

if(is_dir(GO::config()->local_path.'cms'))
{
	if(!is_dir(GO::config()->file_storage_path.'public'))
		mkdir(GO::config()->file_storage_path.'public');
	
	$fs->move(GO::config()->local_path.'cms', GO::config()->file_storage_path.'public/cms');
	
	
	
	//$cms->__on_check_database();
        $cms->check_database();
}
if(isset(GO::modules()->modules['files']))
{
	$sql = "UPDATE cms_files SET content=replace(content, '".GO::config()->local_url."','".GO::modules()->modules['files']['url']."download.php?path=public/');";
	$cms->query($sql);
}
?>