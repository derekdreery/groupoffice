<?php
header('Content-Type: text/html; charset=UTF-8');

require('../../Group-Office.php');

require_once(GO::modules()->modules['cms']['class_path'].'cms.class.inc.php');
require_once(GO::modules()->modules['cms']['class_path'].'output.class.inc.php');
require_once(GO::modules()->modules['cms']['class_path'].'cms_smarty.class.inc.php');
$cms = new cms();
$co = new cms_output();

if(isset($_REQUEST['task']) && $_REQUEST['task']=='logout')
	GO::security()->logout();

if(isset($_REQUEST['path']))
{
	$site_id = isset($_REQUEST['site_id']) ? $_REQUEST['site_id'] : 0;
	$path = $co->special_decode($_REQUEST['path']);
	$co->set_by_path($site_id, $path);
	
}else
{
	$file_id = isset($_REQUEST['file_id']) ? ($_REQUEST['file_id']) : 0;
	$folder_id = isset($_REQUEST['folder_id']) ? ($_REQUEST['folder_id']) : 0;
	
	if($folder_id==0 && $file_id==0)
	{
		$site = $cms->get_site($_REQUEST['site_id']);	
		$folder_id=$site['root_folder_id'];
	}	
	$co->set_by_id($file_id, $folder_id);
}
$smarty = new cms_smarty($co);

//hide on screen errors in smarty
GO::config()->debug_display_errors=false;

// create site map
if (empty($site)) {
	if (empty($folder_id))
		$folder = $cms->get_folder($file_id);
	else
		$folder = $cms->get_folder($folder_id);
	$site = $cms->get_site($folder['site_id']);
}
$site_name_simplified = $cms->to_permalink_style($site['name']);
$_SESSION['GO_SESSION']['cms'][$site_name_simplified]['site_map'] = $cms->get_XML_sitemap($site['id']);

echo $co->replace_urls($smarty->fetch('index.tpl'));
?>