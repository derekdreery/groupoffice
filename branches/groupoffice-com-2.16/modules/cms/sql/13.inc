<?php
require_once($GO_MODULES->modules['cms']['class_path'].'cms.class.inc');
$cms = new cms();


//get the language file
require_once($GO_LANGUAGE->get_language_file('cms'));

$db1 = new db();
$db2 = new db();
$db2->Halt_On_Error = 'no';

$db2->query("ALTER TABLE `cms_languages` ADD `template_id` INT NOT NULL AFTER `site_id` ");

$sql = "SELECT * FROM cms_sites";
$db1->query($sql);
while($db1->next_record())
{	
	$sql = "UPDATE cms_languages SET template_id='".$db1->f('template_id')."' WHERE site_id='".$db1->f('id')."'";
	$db2->query($sql);
}
$db2->query('ALTER TABLE `cms_sites` DROP `template_id`');

update_priority(0);

function update_priority($folder_id)
{
	//add a second cms object for simultanious select and delete from the db
	$cms1 = new cms();
	$cms2 = new cms();

	//get all folders
	$cms1->get_folders($folder_id);
	while($cms1->next_record())
	{
		$priority = $cms2->get_next_priority($folder_id);
		$cms2->query("UPDATE cms_folders SET priority='$priority' WHERE id='".$cms1->f('id')."'");
		update_priority($cms1->f('id'));
	}

	$cms1->get_files($folder_id);
	while ($cms1->next_record())
	{
		$priority = $cms2->get_next_priority($folder_id);
		$cms2->query("UPDATE cms_files SET priority='$priority' WHERE id='".$cms1->f('id')."'");
	}
}

$sql = "SELECT * FROM cms_folders";
$db1->query($sql);
while($db1->next_record())
{
	$new_multipage = $db1->f('multipage') == '1' ? '0' : '1';
	$db2->query("UPDATE cms_folders SET multipage='$new_multipage' WHERE id='".$db1->f('id')."'");
}
?>
