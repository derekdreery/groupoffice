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

if($calendar_module = $GO_MODULES->get_module('calendar'))
{
	require_once($calendar_module['path']."classes/calendar.class.inc");
}
$cal = new calendar();
require_once($GO_LANGUAGE->get_language_file('calendar'));

$calendar_id = isset($_REQUEST['calendar_id']) ? $_REQUEST['calendar_id'] : 0;
$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : "";
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : "";
$event_id = isset($_REQUEST['event_id']) ? $_REQUEST['event_id'] : 0;
$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : 0;



$form = new form('accept_form');
$form->add_html_element(new input('hidden', 'email', $email));
$form->add_html_element(new input('hidden', 'event_id', $event_id));
$form->add_html_element(new input('hidden', 'user_id', $user_id));
$form->add_html_element(new input('hidden', 'task', $task, false));

$event_link = new hyperlink($GO_CONFIG->full_url.
				'?return_to='.urlencode($GO_MODULES->modules['calendar']['url'].
				'event.php?event_id='.$event_id), $cal_event_mail_open);
				
$form->add_html_element(new html_element('h1', $sc_accept_title));

if(!$event = $cal->get_event($event_id))
{
	$form->add_html_element(new html_element('p',$sc_bad_event));
}elseif ($user_id>0)
{
	$GO_SECURITY->authenticate(false);
		
	if($GO_SECURITY->user_id!=$user_id)
	{
		$GO_SECURITY->logout();
		$GO_SECURITY->authenticate(false);
	}
		
	$calendar_count = $cal->get_user_calendars($GO_SECURITY->user_id,0);
	if($calendar_count > 1)
	{	
		$calendars = isset($_POST['calendars']) ? $_POST['calendars'] : array();
	}elseif($cal->next_record())
	{
			$calendars = array($cal->f('id'));
			$task = 'subscribe';
	}else
	{	
		$calendar = $cal->get_calendar();
		$calendars = array($calendar['id']);
		$task = 'subscribe';
	}
	
	if ($task == 'subscribe')
	{
		if (count($calendars) > 0)
		{
			$cal->set_event_status($_REQUEST['event_id'], '1', $email);
			while($calendar_id = array_shift($calendars))
			{
				if (!$cal->event_is_subscribed($event_id, $calendar_id))
				{
					if ($cal->subscribe_event($event_id, $calendar_id))
					{
						$cal->delete_reminder($GO_SECURITY->user_id, $event_id);
						if ($event['reminder'] > 0) {
							$next_recurrence_time = $cal->get_next_recurrence_time($event_id);
							$remind_time = $next_recurrence_time - $event['reminder'];
							
							$reminder['user_id'] = $GO_SECURITY->user_id;
							$reminder['event_id'] = $event_id;
							$reminder['remind_time'] = $remind_time;
							$reminder['occurence_time'] = $next_recurrence_time;							
							$cal->add_reminder($reminder);

						}
			
						if (!$cal->set_event_status($event_id, '1', $email))
						{
							$feedback = $strSaveError;
						}else
						{
							$body = sprintf($cal_accept_mail_body,htmlspecialchars($_SESSION['GO_SESSION']['name']),$event['name']);	
							$body .= "<br /><br />".$event_link->get_html();							
							$subject = sprintf($cal_accept_mail_subject, $event['name']);
						
							$user = $GO_USERS->get_user($event['user_id']);							
							sendmail($user['email'], $_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name'], $subject, $body, '3', 'text/HTML');
						}
					}else
					{
						$feedback = $strSaveError;
					}
				}
			}		
			

			if (isset($feedback))
			{
				$p = new html_element('p', $feedback);
				$p->set_attribute('class','Error');
				$form->add_html_element($p);
			}else
			{
				$form->add_html_element(new html_element('p',$sc_accept_confirm));
				$button = new button($cal_event_mail_open, 
					"javascript:document.location='event.php?event_id=".$event_id.
					"&return_to=".urlencode($GO_MODULES->modules['calendar']['url'])."';");
					
				$form->add_html_element($button);
			}
		}else
		{
			$feedback = $sc_select_calendar_please;
		}
	}else
	{
			
		if ($calendar_module && 
			$GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar_module['acl_read']) || 
			$GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar_module['acl_write']))
		{	
			if (isset($feedback))
			{
				$p = new html_element('p', $feedback);
				$p->set_attribute('class','Error');
				$form->add_html_element($p);
			}
				
			$form->add_html_element(new html_element('p',$sc_select_calendar.':'));
			
			
			while ($cal->next_record())
			{
				$calendars_check = (isset($_POST['calendars']) && in_array($cal->f('id'), $_POST['calendars'])) ? 'checked' : '';
				$checkbox = new checkbox('cal_'.$cal->f('id'), 'calendars[]', $cal->f('id'), $cal->f('name'), in_array($cal->f('id'), $calendars));
				$form->add_html_element($checkbox);
				$form->add_html_element(new html_element('br'));
			}
			$button = new button($cmdOk, "javascript:document.forms[0].task.value='subscribe';document.forms[0].submit();");
			$form->add_html_element($button);			
		}	
	}	
}else
{
	if (!$cal->set_event_status($event_id, '1', $email))
	{
		$form->add_html_element(new html_element('p',$strSaveError));
	}else
	{
		$form->add_html_element(new html_element('p',$sc_accept_confirm));
		
		$body = sprintf($cal_accept_mail_body,$email,$event['name']);	
		$body .= "<br /><br />".$event_link->get_html();							
		$subject = sprintf($cal_accept_mail_subject, $event['name']);
	
		$user = $GO_USERS->get_user($event['user_id']);
		sendmail($user['email'], $email, $email, $subject, $body, '3', 'text/HTML');
	}
}
require_once($GO_THEME->theme_path.'header.inc');
echo $form->get_html();
require_once($GO_THEME->theme_path.'footer.inc');
