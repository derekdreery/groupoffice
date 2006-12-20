<?php
$db = new db();
$db2=new db();

$sql = "SELECT id, timezone, DST FROM users";
$db->query($sql);
while($db->next_record())
{
	$sql = "UPDATE cal_events SET timezone='".$db->f('timezone')."', DST='".$db->f('DST')."' WHERE user_id=".$db->f('id');
	$db2->query($sql);
}
