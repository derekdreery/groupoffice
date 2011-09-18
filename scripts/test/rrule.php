<?php
require('../../www/GO.php');

//$rruleString = 'FREQ=MONTHLY;INTERVAL=2;BYDAY=MO,WE;BYSETPOS=2';

//$rruleString = 'FREQ=MONTHLY;INTERVAL=2;BYDAY=2MO,2WE';

$rruleString = 'FREQ=WEEKLY;INTERVAL=3;BYDAY=FR,SU';
//$rruleString = 'FREQ=DAILY;INTERVAL=2';

$start = '02-09-2011 19:00:00';

$rrule = new GO_Base_Util_Icalendar_Rrule();
$rrule->readIcalendarRruleString(strtotime($start), $rruleString);

//$fromTime=GO_Base_Util_Date::clear_time(time());
$next = $rrule->getNextRecurrence();
for($i=0;$i<10;$i++){
	

	echo date('Y-m-d', $next)."\n";
	$next = $rrule->getNextRecurrence();
	//echo "---\n\n";
	
}





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


