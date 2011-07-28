<?php

for($link_type=1;$link_type<13;$link_type++)
{
	$db->query("ALTER TABLE `go_links_$link_type` ADD `ctime` INT NOT NULL ;");
	$db->query("ALTER TABLE `go_links_$link_type` ADD INDEX ( `ctime` ) ;");
	$db->query("UPDATE `go_links_$link_type` SET ctime=UNIX_TIMESTAMP()");
}

$sql = "SELECT * FROM go_search_cache";

$db->query($sql);
$db2 = new db();
while($db->next_record())
{
	$table = 'go_links_'.$db->f('link_type');
	
	$row['id']=$db->f('id');
	$row['ctime']=$db->f('mtime');
	
	$db2->update_row($table, 'id', $row);
}
?>