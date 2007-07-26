<?php
echo 'Removing all unnessecary event ACL entries...<br />';
$db = new db();

$sql = "SELECT cal_events.acl_read, acl_write FROM cal_events";
$db->query($sql);
$counter = 0;
while($db->next_record())
{
	echo '.';
	$counter=$counter+2;
	$GO_SECURITY->delete_acl($db->f('acl_read'));
	$GO_SECURITY->delete_acl($db->f('acl_write'));
}
echo '<br />';

$db->query("ALTER TABLE `cal_events` DROP `acl_read` , DROP `acl_write` ;");

$db->query("OPTIMIZE TABLE `acl` , `acl_items`");

echo $counter.' entries removed<br />';
