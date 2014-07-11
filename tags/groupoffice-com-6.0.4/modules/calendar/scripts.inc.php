<?php
$calendar = \GO\Calendar\Model\Calendar::model()->getDefault(\GO::user());

$settings = \GO\Calendar\Model\Settings::model()->getDefault(\GO::user());

if($calendar)
	$GO_SCRIPTS_JS .= 'GO.calendar.defaultCalendar = '.json_encode($calendar->getAttributes()).';';

$GO_SCRIPTS_JS .='GO.calendar.categoryRequired="'.\GO\Calendar\CalendarModule::commentsRequired().'";';

if($settings)
	$GO_SCRIPTS_JS .='GO.calendar.showStatuses='.($settings->show_statuses ? 'true;' : 'false;');
