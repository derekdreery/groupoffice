<?php
require('../../www/Group-Office.php');

require_once ($GO_MODULES->modules['calendar']['class_path']."calendar.class.inc.php");
require_once ($GO_CONFIG->class_path."ical2array.class.inc");
$ical = new ical2array();
$cal = new calendar();

$vcalendar = $ical->parse_file('/home/mschering/jos.ics');
//var_dump($vcalendar);

while($object = array_shift($vcalendar[0]['objects']))
{
	if($object['type'] == 'VEVENT' || $object['type'] == 'VTODO' )
	{
		//var_dump($object);
		$event = $cal->get_event_from_ical_object($object);
		
		echo 'Name: '.$event['name']."\n";
		echo 'Start time: '.date('Ymd G:i', $event['start_time'])."\n";
		echo 'End time: '.date('Ymd G:i', $event['end_time'])."\n";
		echo "\n------------\n\n";
		
		//var_dump($event);
	}
}