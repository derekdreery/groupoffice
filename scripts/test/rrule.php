<?php
require('../../www/GO.php');

$rruleString = 'FREQ=MONTHLY;INTERVAL=2;BYDAYS:MO,WE;BYSETPOS=2';

$rruleString = 'FREQ=MONTHLY;INTERVAL=2;BYDAYS:2MO,2WE';
$start = '02-09-2011 00:00:00';

$rrule = new GO_Base_Util_Icalendar_Rrule();
$rrule->readRruleString(strtotime($start), $rruleString);

$fromTime=GO_Base_Util_Date::clear_time(time());

$next = $rrule->getNextRecurrence($fromTime);

echo date('d-m-Y G:i', $next)."\n";

$second = $rrule->getNextRecurrence($next);

echo date('d-m-Y G:i', $second)."\n";