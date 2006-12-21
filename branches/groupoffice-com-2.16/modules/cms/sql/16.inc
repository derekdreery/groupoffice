<?php
require_once($GO_MODULES->modules['cms']['class_path'].'cms.class.inc');
$cms = new cms();


//get the language file
require_once($GO_LANGUAGE->get_language_file('cms'));

$db1 = new db();
$db2 = new db();
$db2->Halt_On_Error = 'no';

$sql = "SELECT * FROM cms_languages";
$db1->query($sql);
while($db1->next_record())
{	
	$sql = "UPDATE cms_sites SET template_id='".$db1->f('template_item_id')."' WHERE site_id='".$db1->f('site_id')."'";
	$db2->query($sql);
}

$sql = "SELECT * FROM cms_templates";
$db1->query($sql);
while($db1->next_record())
{	
	$sql = "UPDATE cms_languages SET template_item_id='".$db1->f('main_template_item_id')."' WHERE site_id='".$db1->f('site_id')."'";
	$db2->query($sql);
}
