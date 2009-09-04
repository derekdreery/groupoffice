<?php
require_once("../../Group-Office.php");
$GO_SECURITY->html_authenticate('calendar');

require_once ($GO_MODULES->modules['calendar']['class_path']."calendar.class.inc.php");
require_once ($GO_MODULES->modules['calendar']['class_path']."go_ical.class.inc");
require_once ($GO_MODULES->modules['calendar']['class_path'].'pdf.class.inc.php');

require_once ($GO_LANGUAGE->get_language_file('calendar'));
$cal = new calendar();

//$date = getdate();

/*
$calendar_id=1;
$start_time = mktime(0,0,0,$date['mon'], $date['mday']-$date['wday']+1,$date['year']);
$end_time = Date::date_add($start_time,7);
*/

$start_time = strtotime($_REQUEST['start_time']);
$end_time = strtotime($_REQUEST['end_time']);

$pdf = new PDF();

if(!empty($_REQUEST['view_id']))
{
	
	$view = $cal->get_view($_REQUEST['view_id']);
	$title=$view['name'];
	$pdf->setParams($view['name'], $start_time, $end_time);
	$pdf->AddPage();
	$cal->get_view_calendars($view['id']);
	$cal2 = new calendar();
	$first = true;
	$even=false;
	while($calendar=$cal->next_record())
	{
		$pdf->setCurrentCalendar($calendar);
		
		$events = $cal2->get_events_in_array(array($cal->f('id')), 0, $start_time, $end_time);
		//$pdf->H3($cal->f('name'));
		$pdf->addCalendar($events, false,$first, $cal->f('name'));
		$first=false;
	}
}else
{
	$calendar = $cal->get_calendar($_REQUEST['calendar_id']);
	$pdf->setCurrentCalendar($calendar);
	$title=$calendar['name'];
	$pdf->setParams($calendar['name'], $start_time, $end_time);
	$events = $cal->get_events_in_array(array($_REQUEST['calendar_id']), 0, $start_time, $end_time);
	//var_dump($events);
	$pdf->AddPage();
	$pdf->addCalendar($events);
}

$filename = File::strip_invalid_chars($lang['calendar']['name'].' '.$title);


$browser = detect_browser();


//header('Content-Length: '.strlen($file));
header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
if ($browser['name'] == 'MSIE')
{
	header('Content-Type: application/download');
	header('Content-Disposition: attachment; filename="'.rawurlencode($filename).'.pdf";');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}else
{
	header('Content-Type: application/pdf');
	header('Pragma: no-cache');
	header('Content-Disposition: attachment; filename="'.$filename.'.pdf"');
}
header('Content-Transfer-Encoding: binary');

echo $pdf->Output($filename.'.pdf', 'S');
?>