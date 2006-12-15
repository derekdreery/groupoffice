<?php
$db1 = new db();
$db2 = new db();

$sql = "SELECT * FROM pmProjects";
$db1->query($sql);

while($db1->next_record())
{
	if($db1->f('acl_book')==0)
	{
		$acl_book=$GO_SECURITY->get_new_acl('project', $db1->f('user_id'));
		
		$sql = "UPDATE pmProjects SET acl_book='$acl_book' WHERE id='".$db1->f('id')."'";
		$db2->query($sql);
	}
}
?>
