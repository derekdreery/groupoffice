<?php

require_once("../../Group-Office.php");
$GLOBALS['GO_SECURITY']->html_authenticate('calendar');

require_once ($GO_MODULES->modules['calendar']['class_path'] . "calendar.class.inc.php");
require_once ($GO_MODULES->modules['calendar']['class_path'] . "go_ical.class.inc");
require_once ($GO_MODULES->modules['calendar']['class_path'] . 'pdf.class.inc.php');


require($GLOBALS['GO_LANGUAGE']->get_language_file('calendar'));
$cal = new calendar();
$cal2 = new calendar();

//$date = getdate();

/*
  $calendar_id=1;
  $start_time = mktime(0,0,0,$date['mon'], $date['mday']-$date['wday']+1,$date['year']);
  $end_time = Date::date_add($start_time,7);
 */

$start_time = strtotime($_REQUEST['start_time']);
$end_time = strtotime($_REQUEST['end_time']);

$pdf = new PDF();

$calendars = isset($_REQUEST['calendars']) ? json_decode($_REQUEST['calendars']) : array();

function insert_events($calendars, $start_time, $end_time) {
	global $cal, $cal2, $lang, $pdf, $calendar_names, $view, $GO_SECURITY;
	require_once('../../classes/base/users.class.inc.php');
	$GO_USERS = new GO_USERS();

	$events = $cal->get_events_in_array($calendars, 0, $start_time, $end_time);

	if (empty($_REQUEST['view_id']) || !empty($view['owncolor'])) {
		/* Default colors for merged calendars */
		$default_colors = array('F0AE67', 'FFCC00', 'FFFF00', 'CCFF00', '66FF00',
				'00FFCC', '00CCFF', '0066FF', '95C5D3', '6704FB',
				'CC00FF', 'FF00CC', 'CC99FF', 'FB0404', 'FF6600',
				'C43B3B', '996600', '66FF99', '999999', '00FFFF');
		//$default_bg = array();

		$default_colors_count = count($default_colors);
		$i = 0;
		foreach ($calendars as $k => $v) {
			$color = $cal2->getCalendarColor($v, $GO_SECURITY->user_id);

			if (!$color) {
				$color = $default_colors[$i];
				$i++;
				if($i > $default_colors_count)
					$i = 0;
			}

			$default_bg[$v] = $color;
		}
		//$default_bg[$v] = $default_colors[$k];
		//$output_events[$event_nr]['background'] =
		//$default_bg[$event['calendar_id']];
	}
	//require_once('merge_events.php');
	$output_events = array();
	$event_nr = 0;
	$uuid_array = array();
	foreach ($events as $event) {
		if ($cal->merge_events($output_events, $event, $uuid_array, $event_nr, $calendar_names, $GO_USERS))
			continue;

		if ($event['all_day_event'] == '1') {
			$date_format = $_SESSION['GO_SESSION']['date_format'];
		} else {
			if (date($_SESSION['GO_SESSION']['date_format'], $event['start_time']) != date($_SESSION['GO_SESSION']['date_format'], $event['end_time'])) {
				$date_format = $_SESSION['GO_SESSION']['date_format'] . ' ' . $_SESSION['GO_SESSION']['time_format'];
			} else {
				$date_format = $_SESSION['GO_SESSION']['time_format'];
			}
		}

		// If background is empty then select the default color.
		if(empty($event['background']))
			$event['background'] = 'FFFFCC';
		
		$background = $event['background'];
		
		if(!empty($default_bg) && count($default_bg) > 1 && !empty($default_bg[$event['calendar_id']])) {
			$background = $default_bg[$event['calendar_id']];
		}

		//$background = !empty($default_bg) && !empty($default_bg[$event['calendar_id']]) ? $default_bg[$event['calendar_id']] : $event['background'];

		$name = $event['name'];

		$username = $GO_USERS->get_user_realname($event['user_id']);


		if (count($calendars) > 1) {
			$name .= ' (' . String::get_first_letters($calendar_names[$event['calendar_id']]) . ')';
		}

		$output_events[] = array(
				'id' => $event_nr++,
				'event_id' => $event['id'],
				//'link_count'=>$GO_LINKS->count_links($event['id'], 1),
				'name' => $name,
				'time' => date($date_format, $event['start_time']),
				'calendar_id' => $event['calendar_id'],
				'calendar_name' => isset($calendar_names[$event['calendar_id']]) ? $calendar_names[$event['calendar_id']] : '',
				'start_time' => $event['start_time'], //date('Y-m-d H:i', $event['start_time']),
				'end_time' => $event['end_time'], //date('Y-m-d H:i', $event['end_time']),
				'location' => $event['location'],
				'description' => $event['description'],
				'background' => $background,
				//'background'=>$default_colors[$response['count']-1],
				'private' => ($event['private'] == '1' && $GO_SECURITY->user_id != $event['user_id']),
				'repeats' => !empty($event['rrule']),
				'all_day_event' => $event['all_day_event'],
				'day' => $lang['common']['full_days'][date('w', $event['start_time'])] . ' ' . date($_SESSION['GO_SESSION']['date_format'], $event['start_time']),
				'read_only' => $event['read_only'] ? true : false,
				'username' => $username,
				//'duration' => $duration,
				'has_other_participants' => 1
		);
	}
	$pdf->addCalendar($output_events);
}

if (!empty($_REQUEST['view_id'])) {//!empty($_REQUEST['view_id'])){
	$view = $cal->get_view($_REQUEST['view_id']);
	$title = $view['name'];
	$pdf->setParams($title, $start_time, $end_time);

	$cal->get_view_calendars($view['id']);
	if (empty($view['merge'])) {
		$cal2 = new calendar();
		$first = true;
		$even = false;
		while ($calendar = $cal->next_record()) {
			$pdf->setCurrentCalendar($calendar);
			$events = $cal2->get_events_in_array(array($cal->f('id')), 0, $start_time, $end_time);
			$pdf->addCalendar($events, false, $first, $cal->f('name'));
			$first = false;
		}
	} else {
		// Calendar.js doesn't pass $_POST['calendars'] if $_POST['view_id'] is passed
		$calendars = array();
		$calendar_names = array();
		while ($calendar = $cal->next_record()) {
			$calendars[] = $calendar['id'];
			$calendar_names[$calendar['id']] = $calendar['name'];
		}

		insert_events($calendars, $start_time, $end_time);
	}
} elseif (count($calendars) > 1) {
	$calendar_names = array();
	foreach ($calendars as $calendar_id) {
		$calendar = $cal->get_calendar($calendar_id);
		$calendar_names[$calendar_id] = $calendar['name'];
	}
	$title = 'Multiple calendars: ' . implode(', ', $calendar_names);
	$pdf->setParams($title, $start_time, $end_time);

	insert_events($calendars, $start_time, $end_time);
} else {
	$calendar = $cal->get_calendar($calendars[0]);
	$pdf->setCurrentCalendar($calendar);
	$title = $calendar['name'];
	$pdf->setParams($calendar['name'], $start_time, $end_time);
	//$events = $cal->get_events_in_array(array($calendars[0]), 0, $start_time, $end_time);
	//go_debug($events);

	insert_events($calendars, $start_time, $end_time);

	//$pdf->addCalendar($events);
}

$filename = File::strip_invalid_chars($lang['calendar']['name'] . ' ' . $title);


$browser = detect_browser();


//header('Content-Length: '.strlen($file));
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
if ($browser['name'] == 'MSIE') {
	header('Content-Type: application/download');
	header('Content-Disposition: attachment; filename="' . rawurlencode($filename) . '.pdf";');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
} else {
	header('Content-Type: application/pdf');
	header('Pragma: no-cache');
	header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
}
header('Content-Transfer-Encoding: binary');

echo $pdf->Output($filename . '.pdf', 'S');
?>