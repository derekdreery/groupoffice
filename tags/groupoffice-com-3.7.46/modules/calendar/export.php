<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */


require_once("../../Group-Office.php");

require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc.php');
require_once($GO_MODULES->modules['calendar']['class_path'].'go_ical.class.inc');
$ical = new go_ical('2.0');
$ical->line_break="\r\n";
$ical->dont_use_quoted_printable=true;
//$ical->line_break="\r\n";

$months_in_past = isset($_REQUEST['months_in_past']) ? intval($_REQUEST['months_in_past']) : 0;

if (isset($_REQUEST['calendar_id']) && $calendar = $ical->get_calendar($_REQUEST['calendar_id']))
{
	$event = false;
	$filename = $calendar['name'].'.ics';
}elseif(isset($_REQUEST['event_id']) && $event = $ical->get_event($_REQUEST['event_id']))
{
	$calendar = false;
	$filename = $event['name'].'.ics';
}
if(!$calendar || !$calendar['public']){
	$GO_SECURITY->authenticate();
	$GO_MODULES->authenticate('calendar');

	if($calendar && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_id'])){
		die('Access denied');
	}
}

if (!isset($filename))
{
	die($strDataError);
}else
{
	$browser = detect_browser();

	header('Content-Type: text/plain;charset=UTF-8');
	//header('Content-Length: '.filesize($path));
	header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
	if ($browser['name'] == 'MSIE')
	{
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	}else
	{
		header('Pragma: no-cache');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
	}
	header('Content-Transfer-Encoding: binary');

	if ($calendar)
	{
		echo $ical->export_calendar($_REQUEST['calendar_id'], $months_in_past);
	}elseif($event)
	{
		echo $ical->export_event($_REQUEST['event_id']);
	}
}
