<?php
$calendar = GO_Calendar_CalendarModule::getDefaultCalendar(GO::user()->id);

if($calendar)
	$GO_SCRIPTS_JS .= 'GO.calendar.defaultCalendar = '.json_encode($calendar->getAttributes()).';';

$GO_SCRIPTS_JS .='GO.calendar.categoryRequired="'.GO_Calendar_CalendarModule::commentsRequired().'";';
