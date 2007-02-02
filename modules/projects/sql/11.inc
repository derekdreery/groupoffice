<?php
$db1 = new db();
$db2 = new db();

$sql = "SELECT * FROM pmProjects";
$db1->query($sql);

while($db1->next_record())
{
	$sql = "UPDATE pmHours SET fee_id='".$db1->f('fee_id')."' WHERE project_id=".$db1->f('id');
	$db2->query($sql);
}
$db1->query("ALTER TABLE `pmProjects` DROP `fee_id`");
