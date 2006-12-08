<?php
require('../../Group-Office.php');

require_once ($GO_MODULES->modules['calendar']['class_path']."calendar.class.inc");
require_once ($GO_MODULES->modules['calendar']['class_path']."ical2array.class.inc");
$ical = new ical2array();
$cal = new calendar();

$vcalendar = $ical->parse_file('vcal.icf');
//var_dump($vcalendar);

while($object = array_shift($vcalendar[0]['objects']))
{
	if($object['type'] == 'VEVENT' || $object['type'] == 'VTODO' )
	{
		var_dump($object);
		$event = $cal->get_event_from_ical_object($object);
		
		echo date('Ymd G:i', $event['start_time']);
		
		var_dump($event);
	}
}
?>
