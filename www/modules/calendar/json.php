<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

require('../../Group-Office.php');

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');



require_once ($GO_MODULES->class_path."calendar.class.inc");
$cal = new calendar();

$calendar_id=isset($_REQUEST['calendar_id']) ? smart_addslashes($_REQUEST['calendar_id']) : 0;
$view_id=isset($_REQUEST['view_id']) ? smart_addslashes($_REQUEST['view_id']) : 0;
$start_time=isset($_REQUEST['start_time']) ? strtotime($_REQUEST['start_time']) : 0;
$end_time=isset($_REQUEST['end_time']) ? strtotime($_REQUEST['end_time']) : 0;


$events = $cal->get_events_in_array($calendar_id, $view_id, 0, 
			local_to_gmt_time($start_time), local_to_gmt_time($end_time), false,false,true,false,true);
$results=array();
foreach($events as $event)
{
	$results[] = array(
		'id'=> $event['id'],
		'name'=> $event['name'],
		'start_time'=> date('Y-m-d H:i', gmt_to_local_time($event['start_time'])),
		'end_time'=> date('Y-m-d H:i', gmt_to_local_time($event['end_time'])),
		'tooltip'=>'Dit is een beschrijving'
	);
}
			
echo json_encode($results);