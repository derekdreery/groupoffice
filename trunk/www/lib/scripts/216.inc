<?php
/*
This script will update the databse fopr version 2.03
*/

$db = new db();
$db->Halt_On_Error = 'no';

$db2 = new db();

$sql = "SELECT cal_events.todo AS istodo, sync_events_maps.* FROM sync_events_maps LEFT JOIN cal_events ON sync_events_maps.event_id=cal_events.id";

$db->query($sql);
while($db->next_record())
{
	if($db->f('istodo')=='1')
	{
		$sql = "UPDATE sync_events_maps SET todo='1' WHERE device_id='".addslashes($db->f('device_id'))."' AND  event_id='".addslashes($db->f('event_id'))."'";
		$db2->query($sql);
	}
}
