<?php
require('../../www/GO.php');

//$rruleString = 'FREQ=MONTHLY;INTERVAL=2;BYDAYS:MO,WE;BYSETPOS=2';
//
//$rruleString = 'FREQ=MONTHLY;INTERVAL=2;BYDAYS:2MO,2WE';
//$start = '02-09-2011 00:00:00';
//
//$rrule = new GO_Base_Util_Icalendar_Rrule();
//$rrule->readIcalendarRruleString(strtotime($start), $rruleString);
//
//$fromTime=GO_Base_Util_Date::clear_time(time());
//
//$next = $rrule->getNextRecurrence($fromTime);
//
//echo date('d-m-Y G:i', $next)."\n";
//
//$second = $rrule->getNextRecurrence($next);
//
//echo date('d-m-Y G:i', $second)."\n";






//$params = array(
//		'byday' => '',
//		'bymonth' => '',
//		'bymonthday' => '',
//		'byday' => '',
//		'freq' => '',
//		'interval' => '',
//		'eventStartTime' => '',
//		'bysetpos' => '',
//		'until' => '',
//);
//
//$Recurrence_pattern = new GO_Base_Util_Date_RecurrencePattern($params);


$date1 = new DateTime('2011-09-01 15:00');
$date2 = new DateTime('2011-11-01 16:00');

$diff = $date1->diff($date2);

var_dump($diff);