<?php
require('../../../Group-Office.php');
$db = new db();
$db2 = new db();
$sql = "SELECT id, content FROM cms_files WHERE extension='html'";

$db->query($sql);

while($db->next_record())
{
	$content = utf8_encode($db->f('content'));
	
	$sql = "UPDATE cms_files SET content='".addslashes($content)."' WHERE id=".$db->f('id');
	$db2->query($sql);
}
