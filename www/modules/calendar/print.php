<?php
require_once("../../Group-Office.php");
$GO_SECURITY->html_authenticate('calendar');

require_once ($GO_MODULES->modules['calendar']['class_path']."calendar.class.inc");
require_once ($GO_MODULES->modules['calendar']['class_path']."go_ical.class.inc");
require_once ($GO_MODULES->modules['calendar']['class_path'].'pdf.class.inc.php');

require_once ($GO_LANGUAGE->get_language_file('calendar'));
$cal = new calendar();

$date = getdate();

$start_time = mktime(0,0,0,$date['mon'], $date['mday']-$date['wday']+1,$date['year']);
$end_time = Date::date_add($start_time,5);

$calendar = $cal->get_calendar(1);
$events = $cal->get_events_in_array(array(1), 0, $start_time, $end_time);


$pdf = new PDF();
$pdf->AddPage();
$pdf->H1('Agenda '.date($_SESSION['GO_SESSION']['date_format'], $start_time).' - '.date($_SESSION['GO_SESSION']['date_format'], $end_time));
$pdf->H2($calendar['name']);
$pdf->addDays($start_time, $end_time, $events);

$filename = 'Calendar';


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