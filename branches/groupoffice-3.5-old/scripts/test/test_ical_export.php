<?php
require('../../www/Group-Office.php');

require_once ($GO_MODULES->modules['calendar']['class_path']."calendar.class.inc.php");
require_once ($GO_MODULES->modules['calendar']['class_path']."go_ical.class.inc");
$cal = new calendar();

$event_id=56;

$go_ical = new go_ical('1.0', true);
$ical_event = $go_ical->export_event($event_id);

var_dump($ical_event);