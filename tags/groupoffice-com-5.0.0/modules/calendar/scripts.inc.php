<?php
$calendar = GO_Calendar_Model_Calendar::model()->getDefault(GO::user());

if($calendar)
	$GO_SCRIPTS_JS .= 'GO.calendar.defaultCalendar = '.json_encode($calendar->getAttributes()).';';

$GO_SCRIPTS_JS .='GO.calendar.categoryRequired="'.GO_Calendar_CalendarModule::commentsRequired().'";';
