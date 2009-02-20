<?php
require_once("../../Group-Office.php");
$GO_SECURITY->html_authenticate('calendar');

require_once ($GO_MODULES->modules['calendar']['class_path']."calendar.class.inc");
require_once ($GO_MODULES->modules['calendar']['class_path']."go_ical.class.inc");
require_once ($GO_MODULES->modules['calendar']['class_path'].'pdf.class.inc.php');

require_once ($GO_LANGUAGE->get_language_file('calendar'));
$cal = new calendar();

$date = getdate();

/*
$calendar_id=1;
$start_time = mktime(0,0,0,$date['mon'], $date['mday']-$date['wday']+1,$date['year']);
$end_time = Date::date_add($start_time,7);
*/



$calendar = $cal->get_calendar($_REQUEST['calendar_id']);
$events = $cal->get_events_in_array(array(1), 0, $_REQUEST['start_time'], $_REQUEST['end_time']);


$pdf = new PDF();

//$pdf->H1($lang['calendar']['name'].' '.date($_SESSION['GO_SESSION']['date_format'], $_REQUEST['start_time']).' - '.date($_SESSION['GO_SESSION']['date_format'], $_REQUEST['end_time']));
//$pdf->H2($calendar['name']);

//$pdf->setTitle($calendar['name']);
$pdf->addDays($calendar['name'], $_REQUEST['start_time'], $_REQUEST['end_time'], $events);

$filename = $lang['calendar']['name'];


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