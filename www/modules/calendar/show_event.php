<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/
require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');

require_once($GO_LANGUAGE->get_language_file('calendar'));

require_once($GO_MODULES->path.'classes/calendar.class.inc');
$cal = new calendar();

load_basic_controls();


//get the local times
$local_time = get_time();

$calendar_id = isset($_REQUEST['calendar_id']) ? $_REQUEST['calendar_id'] : 0;
$event_id = isset($_REQUEST['event_id']) ? $_REQUEST['event_id'] : 0;
$gmt_start_time = isset($_REQUEST['gmt_start_time']) ? $_REQUEST['gmt_start_time'] : 0;
$task = isset($_POST['task']) ? $_POST['task'] : '';
$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back=$_SERVER['PHP_SELF'].'?event_id='.$event_id.'&gmt_start_time='.$gmt_start_time.'&return_to='.urlencode($return_to);

$event = $cal->get_event($event_id);
if(!$event['read_permission'])
{
	exit($strAccessDenied);
}


$form = new form('event_form');

$tabstrip = new tabstrip('event_tabstrip', $cal_event);
$tabstrip->set_return_to($return_to);
$tabstrip->set_attribute('style','width:100%');



$table = new table();
$table->set_attribute('style','width:100%');

$row = new table_row();
$cell = new table_cell($cal->event_to_html($event));
$cell->set_attribute('style','vertical-align:top');

$cell->add_html_element(new button($cmdClose, "javascript:document.location='$return_to';"));
if($event['write_permission'])
{
	$cell->add_html_element(new button($cmdEdit, "javascript:document.location='event.php?gmt_start_time=".$gmt_start_time."&event_id=".$event_id."&return_to=".urlencode($return_to)."';"));	
}
if($event['write_permission'] || ($cal->event_is_subscribed($event_id, $calendar_id) && $GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_write'])))
{
	$cell->add_html_element(new button($cmdDelete,
		"document.location='delete_event.php?event_id=$event_id&calendar_id=".
		$calendar_id."&return_to=".urlencode($link_back)."';"));
}

$row->add_cell($cell);


$cell = new table_cell();
$cell->set_attribute('style','vertical-align:top');


load_control('links_list');
$links_list = new links_list($event['link_id'], 'event_form', $link_back);
$GO_HEADER['head'] = $links_list->get_header();
$cell->add_html_element($links_list);

$row->add_cell($cell);

$table->add_row($row);
$tabstrip->add_html_element($table);

$form->add_html_element($tabstrip);



require_once($GO_THEME->theme_path.'header.inc');
echo $form->get_html();
require_once($GO_THEME->theme_path.'footer.inc');
