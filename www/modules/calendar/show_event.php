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

$tabstrip->innerHTML = $cal->event_to_html($event);

load_control('links_list');
$links_list = new links_list($event['link_id'], 'event_form', $link_back);
$GO_HEADER['head'] .= $links_list->get_header();
$tabstrip->add_html_element($links_list);

$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='$return_to';"));
if($event['write_permission'])
{
	$tabstrip->add_html_element(new button($cmdEdit, "javascript:document.location='event.php?gmt_start_time=".$gmt_start_time."&event_id=".$event_id."&return_to=".urlencode($return_to)."';"));
}




$form->add_html_element($tabstrip);



require_once($GO_THEME->theme_path.'header.inc');
echo $form->get_html();
require_once($GO_THEME->theme_path.'footer.inc');
