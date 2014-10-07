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

$GLOBALS['GO_SECURITY']->authenticate();
$GLOBALS['GO_MODULES']->authenticate('tasks');

require_once($GLOBALS['GO_MODULES']->class_path.'tasks.class.inc.php');
$tasks = new tasks();

require_once($GLOBALS['GO_MODULES']->class_path.'export_tasks.class.inc.php');
$ical = new export_tasks();
$ical->dont_use_quoted_printable=true;
//$ical->line_break="\r\n";

$tasklist = $tasks->get_tasklist($_REQUEST['tasklist_id']);
$filename = $tasklist['name'].'.ics';


$browser = detect_browser();

header('Content-Type: text/calendar;charset=UTF-8');
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

echo $ical->export_tasklist($_REQUEST['tasklist_id']);
