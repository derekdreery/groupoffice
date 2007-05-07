<?php
$db1 = new db();

require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem(true);
$sql = "SELECT * FROM pmProjects";
$db1->query($sql);

while($db1->next_record())
{
	$old_path = $GO_CONFIG->file_storage_path.'projects/'.$db1->f('name');
	$new_path = $GO_CONFIG->file_storage_path.'projects/'.$db1->f('id');
	
	if(file_exists($old_path))
	{
		$fs->move($old_path, $new_path);			
	}
}
