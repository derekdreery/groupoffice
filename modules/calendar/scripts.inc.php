<?php
$calendar = GO_Calendar_Model_Calendar::model()->getDefault(GO::user());

$settings = GO_Calendar_Model_Settings::model()->getDefault(GO::user());

if($calendar)
	$GO_SCRIPTS_JS .= 'GO.calendar.defaultCalendar = '.json_encode($calendar->getAttributes()).';';

$GO_SCRIPTS_JS .='GO.calendar.categoryRequired="'.GO_Calendar_CalendarModule::commentsRequired().'";';

if($settings)
	$GO_SCRIPTS_JS .='GO.calendar.showStatuses='.($settings->show_statuses ? 'true;' : 'false;');
