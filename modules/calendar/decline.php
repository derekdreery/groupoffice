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

load_basic_controls();

require_once($GO_MODULES->path."classes/calendar.class.inc");
$cal = new calendar();
require_once($GO_LANGUAGE->get_language_file('calendar'));

$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : "";

$member = isset($_REQUEST['member']) ? $_REQUEST['member'] : 'false';
$event_id = isset($_REQUEST['event_id']) ? $_REQUEST['event_id'] : 0;

if($member == 'true')
{
	$GO_SECURITY->authenticate();
}

$event_link = new hyperlink($GO_CONFIG->full_url.
		'?return_to='.urlencode($GO_MODULES->modules['calendar']['url'].
		'event.php?event_id='.$event_id), $cal_event_mail_open);

require_once($GO_THEME->theme_path.'header.inc');

$div = new html_element('div');

$div->add_html_element(new html_element('h1', $sc_decline_title));
	
if(!$event = $cal->get_event($event_id))
{
	$div->add_html_element(new html_element('p',$sc_bad_event));
}elseif (!$cal->set_event_status($event_id, '2', $email))
{
	$div->add_html_element(new html_element('p',$strSaveError));
}else
{
	$div->add_html_element(new html_element('p',$sc_decline_confirm));
	
	if($member == 'true')
	{
		$body = sprintf($cal_decline_mail_body,htmlspecialchars($_SESSION['GO_SESSION']['name']),$event['name']);	
		$body .= "<br /><br />".$event_link->get_html();							
		$subject = sprintf($cal_decline_mail_subject, $event['name']);

		$user = $GO_USERS->get_user($event['user_id']);	
		sendmail($user['email'], $_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name'], $subject, $body, '3', 'text/HTML');
	}else
	{
		$body = sprintf($cal_decline_mail_body,$email,$event['name']);	
		$body .= "<br /><br />".$event_link->get_html();							
		$subject = sprintf($cal_decline_mail_subject, $event['name']);

		$user = $GO_USERS->get_user($event['user_id']);	
		sendmail($user['email'], $email, $email, $subject, $body, '3', 'text/HTML');
	}
}
echo $div->get_html();
require_once($GO_THEME->theme_path.'footer.inc');
