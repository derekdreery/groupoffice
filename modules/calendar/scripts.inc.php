<?php
$calendar = GO_Calendar_CalendarModule::getDefaultCalendar(GO::user()->id);

$GO_SCRIPTS_JS .= 'GO.calendar.defaultCalendar = '.json_encode($calendar->getAttributes()).';';
