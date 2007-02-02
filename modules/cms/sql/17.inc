<?php
require_once($GO_MODULES->modules['cms']['class_path'].'cms.class.inc');
$cms = new cms();


//get the language file
require_once($GO_LANGUAGE->get_language_file('cms'));

$db1 = new db();
$db2 = new db();

$sql = "SELECT * FROM cms_languages";
$db1->query($sql);
while($db1->next_record())
{
	$site = $cms->get_site($db1->f('site_id'));
	if($site['root_folder_id']==0)
	{
		$sql = "UPDATE cms_sites SET root_folder_id=".$db1->f('root_folder_id')." WHERE id=".$db1->f('site_id');
		
	}else
	{
		$sql = "UPDATE cms_folders SET parent_id=".$site['root_folder_id']." WHERE id=".$db1->f('root_folder_id');
	}
	$db2->query($sql);
}
