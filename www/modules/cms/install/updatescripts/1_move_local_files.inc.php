<?php
$fs = new filesystem();

require_once($GLOBALS['GO_MODULES']->modules['cms']['class_path'].'cms.class.inc.php');
$cms = new cms();

if(is_dir($GLOBALS['GO_CONFIG']->local_path.'cms'))
{
	if(!is_dir($GLOBALS['GO_CONFIG']->file_storage_path.'public'))
		mkdir($GLOBALS['GO_CONFIG']->file_storage_path.'public');
	
	$fs->move($GLOBALS['GO_CONFIG']->local_path.'cms', $GLOBALS['GO_CONFIG']->file_storage_path.'public/cms');
	
	
	
	//$cms->__on_check_database();
        $cms->check_database();
}
if(isset($GLOBALS['GO_MODULES']->modules['files']))
{
	$sql = "UPDATE cms_files SET content=replace(content, '".$GLOBALS['GO_CONFIG']->local_url."','".$GLOBALS['GO_MODULES']->modules['files']['url']."download.php?path=public/');";
	$cms->query($sql);
}
?>