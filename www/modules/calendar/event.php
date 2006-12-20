<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1.243 $ $Date: 2006/11/28 13:20:38 $
 * 
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
      
 * @package Calendar
 * @category Calendar
 */
require_once ("../../Group-Office.php");

load_basic_controls();
load_control('dynamic_tabstrip');
load_control('date_picker');
load_control('color_selector');
load_control('datatable');

require_once ($GO_CONFIG->class_path.'mail/RFC822.class.inc');
$RFC822 = new RFC822();

//check for the addressbook module
$ab_module = isset ($GO_MODULES->modules['addressbook']) ? $GO_MODULES->modules['addressbook'] : false;
if ($ab_module) {
	require_once ($ab_module['class_path'].'addressbook.class.inc');
	$ab = new addressbook();
}

$projects_module = isset ($GO_MODULES->modules['projects']) ? $GO_MODULES->modules['projects'] : false;
if ($projects_module) {
	require_once ($projects_module['class_path'].'projects.class.inc');
	$projects = new projects();
}

$fs_module = isset ($GO_MODULES->modules['filesystem']) ? $GO_MODULES->modules['filesystem'] : false;
if ($fs_module) {
	require_once ($GO_CONFIG->class_path.'filesystem.class.inc');
	$fs = new filesystem();
}

//get the local times
$local_time = get_time();
$year = isset ($_REQUEST['year']) ? $_REQUEST['year'] : date("Y", $local_time);
$month = isset ($_REQUEST['month']) ? $_REQUEST['month'] : date("n", $local_time);
$day = isset ($_REQUEST['day']) ? $_REQUEST['day'] : date("j", $local_time);
$hour = isset ($_REQUEST['hour']) ? $_REQUEST['hour'] : date("H", $local_time);
$min = isset ($_REQUEST['min']) ? $_REQUEST['min'] : date("i", $local_time);

$hours = array ("00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23");
$mins = array ("00", "05", "10", "15", "20", "25", "30", "35", "40", "45", "50", "55");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');
require_once ($GO_LANGUAGE->get_language_file('calendar'));

require_once ($GO_MODULES->class_path.'calendar.class.inc');
$cal = new calendar();

$cal_settings = $cal->get_settings($GO_SECURITY->user_id);

$event_id = isset ($_REQUEST['event_id']) ? $_REQUEST['event_id'] : 0;
$send_invitation = isset ($_POST['send_invitation']) ? true : false;

$task = isset ($_REQUEST['task']) ? smart_addslashes($_REQUEST['task']) : '';
$return_to = isset ($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $GO_MODULES->modules['calendar']['url'];
$link_back = isset ($_REQUEST['link_back']) ? $_REQUEST['link_back'] : $_SERVER['PHP_SELF'].'?event_id='.$event_id.'&return_to='.urlencode($return_to);
$is_resouce=false;


if($event_id == 0)
{
	$calendar_id =
	(isset ($_REQUEST['calendar_id']) && $_REQUEST['calendar_id'] > 0) ?
	smart_addslashes($_REQUEST['calendar_id']) : $cal_settings['default_cal_id'];

	$calendar = $cal->get_calendar($calendar_id);

	if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_write']))
	{
		$calendar_id= $cal_settings['default_cal_id'];
		$calendar = $cal->get_calendar($calendar_id);
	}
}else
{
	$cal->get_event_subscribtions($event_id);
	if($cal->next_record())
	{
		$calendar_id = $cal->f('calendar_id');
	}else
	{
		$calendar_id = $cal_settings['default_cal_id'];
	}
	$calendar = $cal->get_calendar($calendar_id);
}



$event_link = new hyperlink($GO_CONFIG->full_url.
'?return_to='.urlencode($GO_MODULES->modules['calendar']['url'].
'event.php?event_id='.$event_id), $cal_event_mail_open);

if($task == 'accept')
{
	$accept=true;
	$task='save_event';
}elseif($task=='decline')
{
	$event = $cal->get_event($event_id);
	if($event['user_id'] != $GO_SECURITY->user_id)
	{
		$body = sprintf($cal_your_resource_declined_mail_body,htmlspecialchars($_SESSION['GO_SESSION']['name']),$calendar['name']).'<br /><br />';
		$body .= $cal->event_to_html($event);

		$subject = sprintf($cal_your_resource_declined_mail_subject, $calendar['name']);

		$user = $GO_USERS->get_user($event['user_id']);
		sendmail($user['email'], $_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name'], $subject, $body, '3', 'text/HTML');
	}
	$cal->delete_event($event_id);
	header('Location: '.$GO_MODULES->modules['calendar']['url']);
	exit();
}

switch($task)
{
	case 'activate_linking':
		$link_event = $cal->get_event($event_id);
		$link_id = $link_event['link_id'];
		if(empty($link_event['link_id']))
		{
			$update_event['id'] = $event_id;
			$update_event['link_id'] = $link_id = $GO_LINKS->get_link_id();
			$cal->update_event($update_event);
		}
		$GO_LINKS->activate_linking($link_id, 1, $link_event['name'], $link_back);

		header('Location: '.$GO_CONFIG->host.'link.php');
		exit();
		break;

	case 'create_link':
		if($link = $GO_LINKS->get_active_link())
		{
			$link_event = $cal->get_event($event_id);
			$link_id = $link_event['link_id'];
			if(empty($link_event['link_id']))
			{
				$update_event['id'] = $event_id;
				$update_event['link_id'] = $link_id = $GO_LINKS->get_link_id();
				$cal->update_event($update_event);
			}
			$GO_LINKS->add_link($link['id'], $link['type'], $link_id, 1);
			$GO_LINKS->deactivate_linking();
			header('Location: '.$link['return_to']);
			exit();
		}
		break;

	case 'save_event':
		$is_resource=false;
		$event['name'] = smart_addslashes(trim($_POST['name']));
		$event['description'] = smart_addslashes(trim($_POST['description']));
		$event['location'] = smart_addslashes(trim($_POST['location']));
		$event['permissions'] = isset($_POST['permissions']) ? $_POST['permissions'] : '1';

		$event['timezone'] = $_SESSION['GO_SESSION']['timezone'];
		$event['DST'] = $_SESSION['GO_SESSION']['DST'];
		$event['reminder'] = isset($_POST['reminder_multiplier']) ? $_POST['reminder_multiplier'] * $_POST['reminder_value'] : 0;

		if ($event['name'] == '') {
			$feedback = $error_missing_field;
			$activetab='properties';
		}
		elseif (!isset ($_POST['calendars']) || count($_POST['calendars']) == 0) {
			$feedback = $sc_select_calendar_please;
			$activetab='calendars';
		}else
		{
			$timezone_offset = get_timezone_offset(date_to_unixtime($_POST['start_date']));
			$event['user_id']=$_POST['user_id'];
			$event['repeat_forever'] = isset ($_POST['repeat_forever']) ? '1' : '0';
			$event['repeat_every'] = isset ($_POST['repeat_every']) ? $_POST['repeat_every'] : '0';
			$event['month_time'] = isset ($_POST['month_time']) ? $_POST['month_time'] : '0';

			$event['todo'] = $_POST['todo'];

			if($event['todo'] == '1')
			{
				$event['status_id'] = $_POST['todo_status_id'];
			}else
			{
				$event['status_id'] = $_POST['event_status_id'];
			}

			if($calendar['group_id']>1)
			{
				$event['background']=$event['status_id']==2 ? 'CCFFCC' : 'FF6666';
			}else
			{
				$event['background'] = $_POST['background'];
			}




			if ($event['status_id'] == 11)
			{
				$event['completion_time'] = date_to_unixtime($_POST['completion_date'].' '.$_POST['completion_hour'].':'.$_POST['completion_min']);
				$timezone_offset = get_timezone_offset($event['completion_time'])*3600;
				$event['completion_time'] -= $timezone_offset;
			}else
			{
				$event['completion_time'] = 0;
			}


			if (isset ($_POST['all_day_event'])) {
				$event['all_day_event'] = '1';
				$start_hour = 0 - $timezone_offset;
				$start_min = '0';
				$end_hour = 23 - $timezone_offset;
				$end_min = 59;

				$event['start_time'] = date_to_unixtime($_POST['start_date'].' '.$start_hour.':'.$start_min);
				$event['end_time'] = date_to_unixtime($_POST['end_date'].' '.$end_hour.':'.$end_min);
			} else {
				$event['all_day_event'] = '0';
				$start_min = $_POST['start_min'];
				$start_hour = $_POST['start_hour'];
				$end_hour = $_POST['end_hour'];
				$end_min = $_POST['end_min'];

				$event['start_time'] = get_gmt_time(date_to_unixtime($_POST['start_date'].' '.$start_hour.':'.$start_min));
				$event['end_time'] = get_gmt_time(date_to_unixtime($_POST['end_date'].' '.$end_hour.':'.$end_min));

			}

			$timezone_offset = get_timezone_offset($event['start_time']);

			$event['repeat_type'] = $_POST['repeat_type'];
			if ($event['repeat_type'] != REPEAT_NONE) {
				$event['repeat_end_time'] = isset ($_POST['repeat_forever']) ? '0' : server_to_gmt_time(date_to_unixtime($_POST['repeat_end_date']))+86400;
			} else {
				$event['repeat_end_time'] = 0;
			}

			$shift_day = 0;


			$shifted_start_hour = $start_hour - $timezone_offset;
			if ($shifted_start_hour > 23) {
				$shifted_start_hour = $shifted_start_hour -24;
				$shift_day = 1;
			}
			elseif ($shifted_start_hour < 0) {
				$shifted_start_hour = 24 + $shifted_start_hour;
				$shift_day = -1;
			}


			switch ($shift_day) {
				case 0 :
					$event['mon'] = isset ($_POST['repeat_days_1']) ? '1' : '0';
					$event['tue'] = isset ($_POST['repeat_days_2']) ? '1' : '0';
					$event['wed'] = isset ($_POST['repeat_days_3']) ? '1' : '0';
					$event['thu'] = isset ($_POST['repeat_days_4']) ? '1' : '0';
					$event['fri'] = isset ($_POST['repeat_days_5']) ? '1' : '0';
					$event['sat'] = isset ($_POST['repeat_days_6']) ? '1' : '0';
					$event['sun'] = isset ($_POST['repeat_days_0']) ? '1' : '0';
					break;

				case 1 :
					$event['mon'] = isset ($_POST['repeat_days_0']) ? '1' : '0';
					$event['tue'] = isset ($_POST['repeat_days_1']) ? '1' : '0';
					$event['wed'] = isset ($_POST['repeat_days_2']) ? '1' : '0';
					$event['thu'] = isset ($_POST['repeat_days_3']) ? '1' : '0';
					$event['fri'] = isset ($_POST['repeat_days_4']) ? '1' : '0';
					$event['sat'] = isset ($_POST['repeat_days_5']) ? '1' : '0';
					$event['sun'] = isset ($_POST['repeat_days_6']) ? '1' : '0';
					break;

				case -1 :
					$event['mon'] = isset ($_POST['repeat_days_2']) ? '1' : '0';
					$event['tue'] = isset ($_POST['repeat_days_3']) ? '1' : '0';
					$event['wed'] = isset ($_POST['repeat_days_4']) ? '1' : '0';
					$event['thu'] = isset ($_POST['repeat_days_5']) ? '1' : '0';
					$event['fri'] = isset ($_POST['repeat_days_6']) ? '1' : '0';
					$event['sat'] = isset ($_POST['repeat_days_0']) ? '1' : '0';
					$event['sun'] = isset ($_POST['repeat_days_1']) ? '1' : '0';
					break;
			}


			$custom_fields=isset($_POST['custom_fields']) ? array_map('smart_addslashes', $_POST['custom_fields']) : array();
			$event['custom_fields'] = $cal->group_values_to_xml($custom_fields, $calendar['group_id']);

			if($event['todo'] == '1' || isset($_POST['ignore_conflicts']))
			{
				$conflicts = array();
			}else
			{
				$calendars = $_POST['calendars'];
				if(isset($_POST['resources']))
				{
					$calendars = array_merge($calendars, $_POST['resources']);
				}

				$conflicts = $cal->get_conflicts($event['start_time'], $event['end_time'], $calendars, $_POST['to']);
				unset($conflicts[$event_id]);
			}


			if($event['repeat_type'] != REPEAT_NONE && $cal->get_next_recurrence_time(0,$event['start_time'], $event) < $event['end_time'])
			{
				//Event will cumulate
				$feedback = $cal_cumulative;
			}elseif(count($conflicts))
			{
				$feedback = $cal_conflict;
			}else
			{
				$cal2 = new calendar();
				if ($event_id > 0) {
					$event['id'] = $event_id;

					$old_event = $cal->get_event($event_id);

					if (!$cal->update_event($event)) {
						$feedback = $strSaveError;
					}else
					{
						if(	$old_event['start_time'] != $event['start_time'] ||
						$old_event['end_time'] != $event['end_time'] ||
						$old_event['custom_fields'] != $event['custom_fields'])
						{
							$modified=true;
						}
					}
				} else {

					$event['user_id']=$GO_SECURITY->user_id;
					if($link = $GO_LINKS->get_active_link())
					{
						$event['link_id'] = $GO_LINKS->get_link_id();
					}

					if (!$event_id = $cal->add_event($event)) {
						$feedback = $strSaveError;
					} else {
						$link_back = add_params_to_url($link_back, 'event_id='.$event_id);

						if(isset($_REQUEST['create_exception']) && $_REQUEST['exception_event_id'] > 0)
						{
							$exception['event_id'] = $_REQUEST['exception_event_id'];
							$exception['time'] = $_REQUEST['exception_time'];
							$cal->add_exception($exception);

							$update_event['id']=$_REQUEST['exception_event_id'];
							$cal->update_event($update_event);

							$cal->get_event_resources($exception['event_id']);
							while($cal->next_record())
							{
								$exception['event_id'] = $cal->f('id');
								$cal2->add_exception($exception);

								$update_event['id']= $cal->f('id');
								$cal2->update_event($update_event);
							}
						}
					}
				}
			}

			if (!isset ($feedback)) {
				//add the event to all selected calendars

				//add group admins to acl if this is a resource (Group_id > 1)
				if($calendar['group_id'] > 1)
				{
					if($cal->get_resource_group_admins($calendar['group_id']))
					{
						while($cal->next_record())
						{
							//Send e-mail to admin about resource.
							if($cal->f('user_id') != $GO_SECURITY->user_id)
							{
								$resource_link = new hyperlink($GO_MODULES->modules['calendar']['full_url'].
								'event.php?event_id='.$event_id, $cal_open_resource);

								$body = sprintf($cal_resource_mail_body,htmlspecialchars($_SESSION['GO_SESSION']['name']),$calendar['name']).'<br /><br />';
								$body .= $cal2->event_to_html($event);
								$body .= "<br />".$resource_link->get_html();

								$subject = sprintf($cal_resource_mail_subject,$calendar['name']);

								$user = $GO_USERS->get_user($cal->f('user_id'));
								sendmail($user['email'], $_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name'], $subject, $body, '3', 'text/HTML');
							}
						}
					}
				}

				if ($event['reminder'] > 0) {
					$next_recurrence_time = $cal->get_next_recurrence_time($event_id);
				}

				$event = array_map('stripslashes', $event);

				foreach($_POST['writable_calendars'] as $writable_calendar_id)
				{
					if (in_array($writable_calendar_id, $_POST['calendars'])) {

						if (!$cal->event_is_subscribed($event_id, $writable_calendar_id)) {
							$cal->subscribe_event($event_id, $writable_calendar_id);
							$modified=true;
							$subscribtion_modified=true;
						}

						$subscribed_calendar = $cal->get_calendar($writable_calendar_id);
						if($subscribed_calendar['group_id']>1)
						{
							$is_resource=true;
						}

						$cal->delete_reminder($subscribed_calendar['user_id'], $event_id);
						//set the reminder

						if ($event['reminder'] > 0 && $next_recurrence_time > 0) {
							$remind_time = $next_recurrence_time - $event['reminder'];

							$reminder['user_id'] = $subscribed_calendar['user_id'];
							$reminder['event_id'] = $event_id;
							$reminder['remind_time'] = $remind_time;
							$reminder['occurence_time'] = $next_recurrence_time;

							$cal->add_reminder($reminder);
						}
					} else {
						if ($cal->event_is_subscribed($event_id, $writable_calendar_id)) {
							$cal->unsubscribe_event($event_id, $writable_calendar_id);
							$modified=true;
							$subscribtion_modified=true;
						}
					}
				}

				//copy event properties
				$event_copy = array_map('addslashes',$event);
				unset($event_copy['id'], $event_copy['acl_read'],$event_copy['acl_write'], $event_copy['reminder']);
				$event_copy['status_id'] = 1;
				$event_copy['todo'] = '0';
				if(isset($_POST['writable_resources']))
				{
					foreach($_POST['writable_resources'] as $writable_resource_id)
					{
						$existing_resource = $cal->get_event_resource($event_id, $writable_resource_id);
						if (isset($_POST['resources']) && in_array($writable_resource_id, $_POST['resources']))
						{
							$resource_calendar = $cal->get_calendar($writable_resource_id);

							$resource = $event_copy;
							$resource['event_id'] = $event_id;

							$custom_fields=isset($_POST['resource_options'][$writable_resource_id]) ? array_map('smart_addslashes', $_POST['resource_options'][$writable_resource_id]) : array();
							$resource['custom_fields'] = $cal->group_values_to_xml($custom_fields, $resource_calendar['group_id']);

							//echo htmlspecialchars($resource['custom_fields']);

							if($existing_resource)
							{
								$resource['id'] = $existing_resource['id'];

								if(	$existing_resource['start_time'] != $resource['start_time'] ||
								$existing_resource['end_time'] != $resource['end_time'] ||
								$existing_resource['custom_fields'] != $resource['custom_fields'])
								{
									$admin_count = $cal2->get_resource_group_admins($resource_calendar['group_id']);
									if($admin_count>0 && $cal->is_resource_group_admin($existing_resource['user_id'], $resource_calendar['group_id']))
									{
										$resource['status_id']=2;
									}else
									{
										//No admins so default to accepted
										$resource['status_id']=1;
									}
									$resource['background']=$resource['status_id']==2 ? 'CCFFCC' : 'FF6666';

									$resource_link = new hyperlink($GO_MODULES->modules['calendar']['full_url'].
									'event.php?event_id='.$resource['id'], $cal_open_resource);
									while($cal2->next_record())
									{
										if($cal2->f('user_id') != $GO_SECURITY->user_id)
										{
											$body = sprintf($cal_resource_modified_mail_body,htmlspecialchars($_SESSION['GO_SESSION']['name']),$resource_calendar['name']).'<br /><br />';
											$body .= $cal->event_to_html($resource);
											$body .= "<br />".$resource_link->get_html();
											$subject = sprintf($cal_resource_modified_mail_subject, $resource_calendar['name']);
											$user = $GO_USERS->get_user($cal2->f('user_id'));
											sendmail($user['email'], $_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name'], $subject, $body, '3', 'text/HTML');
										}
									}
								}
								$cal->update_event($resource);
							}else
							{
								//$cal2=new calendar();
								$admin_count = $cal2->get_resource_group_admins($resource_calendar['group_id']);
								if($admin_count>0 && $cal->is_resource_group_admin($event['user_id'], $resource_calendar['group_id']))
								{
									$resource['status_id']=2;
								}else
								{
									//No admins so default to accepted
									$resource['status_id']=1;
								}

								$resource['background']=$resource['status_id']==2 ? 'CCFFCC' : 'FF6666';

								$resource_id = $cal->add_event($resource);

								$cal->subscribe_event($resource_id, $writable_resource_id);
								if($admin_count)
								{
									while($cal2->next_record())
									{
										if($cal2->f('user_id') != $GO_SECURITY->user_id)
										{
											$resource_link = new hyperlink($GO_MODULES->modules['calendar']['full_url'].
											'event.php?event_id='.$resource_id, $cal_open_resource);

											$body = sprintf($cal_resource_mail_body,$_SESSION['GO_SESSION']['name'],$resource_calendar['name']).'<br /><br />';
											$body .= $cal->event_to_html($resource);
											$body .= "<br />".$resource_link->get_html();

											$subject = sprintf($cal_resource_mail_subject,$resource_calendar['name']);

											$user = $GO_USERS->get_user($cal2->f('user_id'));
											sendmail($user['email'], $_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name'], $subject, $body, '3', 'text/HTML');
										}
									}
								}
							}

							$resource['custom_fields'] = stripslashes($resource['custom_fields']);
						}elseif($existing_resource)
						{
							$cal->delete_event($existing_resource['id']);
						}
					}
				}

				$participants = $RFC822->explode_address_list(smart_stripslashes($_POST['to']));
				$existing_participants = array ();
				$cal->get_participants($event_id);
				while ($cal->next_record()) {
					$existing_participants[] = $cal->f("email");
				}

				//if ((count($participants) > 0 && $send_invitation) || count($participants) > count($existing_participants)) {
				if (count($participants) > 0)
				{
					//send an invitation mail to all participants
					$invite_text = $event['todo'] == '1'  ? sprintf($cal_invited, strtolower($cal_todo)) : sprintf($sc_invited, strtolower($cal_event));

					$mail_body = '<html><body>'.$invite_text .'<br /><br />';
					$mail_body .= $cal->event_to_html($event);
					$mail_body .= '<br />'.$sc_accept_question.'<br />';

					require_once ($GO_CONFIG->class_path."mail/phpmailer/class.phpmailer.php");
					require_once ($GO_CONFIG->class_path."mail/phpmailer/class.smtp.php");
					$mail = new PHPMailer();
					$mail->PluginDir = $GO_CONFIG->class_path.'mail/phpmailer/';
					$mail->SetLanguage($php_mailer_lang, $GO_CONFIG->class_path.'mail/phpmailer/language/');


					switch ($GO_CONFIG->mailer) {
						case 'smtp' :
							$mail->Host = $GO_CONFIG->smtp_server;
							$mail->Port = $GO_CONFIG->smtp_port;
							$mail->IsSMTP();
							if(!empty($GO_CONFIG->smtp_username))
							{
								$mail->SMTPAuth=true;
								$mail->Username=$GO_CONFIG->smtp_username;
								$mail->Password = $GO_CONFIG->smtp_password;
							}
							break;
						case 'qmail' :
							$mail->IsQmail();
							break;
						case 'sendmail' :
							$mail->IsSendmail();
							break;
						case 'mail' :
							$mail->IsMail();
							break;
					}

					$mail->Sender = $_SESSION['GO_SESSION']["email"];
					$mail->From = $_SESSION['GO_SESSION']["email"];
					$mail->FromName = $_SESSION['GO_SESSION']["name"];
					$mail->AddReplyTo($_SESSION['GO_SESSION']["email"], $_SESSION['GO_SESSION']["name"]);
					$mail->WordWrap = 76;
					$mail->IsHTML(true);
					$mail->Subject = $event['name'];

					require_once ($GO_MODULES->class_path.'go_ical.class.inc');
					$ical = new go_ical();

					$ics_string = $ical->export_event($event_id);


					if(isset($GO_MODULES->modules['email']))
					{
						require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc');
						$email = new email();
						$em_settings = $email->get_settings($GO_SECURITY->user_id);

						if(function_exists('iconv') && $em_settings['charset'] != $charset)
						{
							$mail->CharSet=$em_settings['charset'];
						}
					}

					if($mail->CharSet!=$charset)
					{
						$mail->recode($charset);
					}


					for ($i = 0; $i < sizeof($participants); $i ++) {
						$mail->ClearAllRecipients();
						$mail->ClearAttachments();

						$addresses = $RFC822->parse_address_list($participants[$i]);

						if ($send_invitation || !in_array($addresses[0]['email'], $existing_participants)) {

							$id = 0;
							if ($user_profile = $GO_USERS->get_user_by_email($addresses[0]['email'])) {
								$id = $user_profile["id"];
							}
							elseif (!$user_profile && $ab_module) {
								$user_profile = $ab->get_contact_by_email($addresses[0]['email'], $GO_SECURITY->user_id);
								$id = $user_profile["source_id"];
							}

							if ($id == 0) {
								if ($ics_string) {
									$mail->AddStringAttachment($ics_string, $event['name'].'.ics', 'base64', 'text/calendar');
								}
								$nouser_accept_link = new hyperlink($GO_MODULES->full_url.
								'accept.php?event_id='.$event_id.'&email='.
								urlencode($addresses[0]['email']), $sc_accept);

								$nouser_decline_link = new hyperlink($GO_MODULES->full_url.
								'decline.php?event_id='.$event_id.'&email='.
								urlencode($addresses[0]['email']), $sc_decline);

								$p = new html_element('p');
								$p->add_html_element($nouser_accept_link);
								$p->innerHTML .= '&nbsp;|&nbsp;';
								$p->add_html_element($nouser_decline_link);


								if($mail->CharSet!=$charset)
								{
									$mail->Body=$mail_body.iconv($charset, $mail->CharSet, $p->get_html());
								}else
								{
									$mail->Body=$mail_body.$p->get_html();
								}



								$mail->AddAddress($addresses[0]['email'],$addresses[0]['personal']);

								if($mail->CharSet!=$charset)
								{
									$mail->recode_addresses($charset);
								}

								if ($mail->Send() && !in_array($addresses[0]['email'], $existing_participants)) {
									$cal->add_participant($event_id, addslashes($addresses[0]['personal']), $addresses[0]['email']);
								}
							} else {

								$user_accept_link = new hyperlink($GO_MODULES->full_url.
								'accept.php?event_id='.$event_id.'&user_id='.
								$id.'&email='.urlencode($addresses[0]['email']), $sc_accept);

								$user_decline_link = new hyperlink($GO_MODULES->full_url.
								'decline.php?event_id='.$event_id.'&user_id='.
								$id.'&email='.urlencode($addresses[0]['email']), $sc_decline);

								$p = new html_element('p');
								$p->add_html_element($user_accept_link);
								$p->innerHTML .= '&nbsp;|&nbsp;';
								$p->add_html_element($user_decline_link);

								if($mail->CharSet!=$charset)
								{
									$mail->Body=$mail_body.iconv($charset, $mail->CharSet, $p->get_html());
								}else
								{
									$mail->Body=$mail_body.$p->get_html();
								}



								if ($GO_SECURITY->user_id != $id) {

									$mail->AddAddress($addresses[0]['email'],$addresses[0]['personal']);

									if($mail->CharSet!=$charset)
									{
										$mail->recode_addresses($charset);
									}

									if ($mail->Send() && !in_array($addresses[0]['email'], $existing_participants)) {
										$cal->add_participant($event_id, addslashes($addresses[0]['personal']), $addresses[0]['email'], $id);
									}
								} else {
									if(!in_array($addresses[0]['email'], $existing_participants))
									{
										$cal->add_participant($event_id, addslashes($addresses[0]['personal']), $addresses[0]['email'], $id);
										$cal->set_event_status($event_id, '1', $addresses[0]['email']);
									}
								}
							}
						}
					}
				}
				$participants_emails = array();
				for($i=0;$i<count($participants);$i++)
				{
					$addresses = $RFC822->parse_address_list($participants[$i]);
					$participants_emails[] = $addresses[0]['email'];
				}

				for($i=0;$i<count($existing_participants);$i++)
				{
					if(!in_array($existing_participants[$i], $participants_emails))
					{
						$cal->delete_participant($event_id, addslashes($existing_participants[$i]));
					}
				}


				//finally send modification notice if this is a resource.
				if($is_resource && isset($old_event) && $old_event['user_id'] != $GO_SECURITY->user_id)
				{
					if(isset($modified))
					{
						$resource_link = new hyperlink($GO_MODULES->modules['calendar']['full_url'].
						'event.php?event_id='.$event_id, $cal_open_resource);

						$body = sprintf($cal_your_resource_modified_mail_body,htmlspecialchars($_SESSION['GO_SESSION']['name']),$calendar['name']).'<br /><br />';
						$body .= $cal->event_to_html($event);

						if(isset($subscribtion_modified))
						{
							$body .= '<br />'.sprintf($cal_another_resource, $subscribed_calendar['name']).'<br />';
						}

						//$body .= "<br />".$resource_link->get_html();
						$subject = sprintf($cal_resource_modified_mail_subject, $calendar['name']);
						$user = $GO_USERS->get_user($old_event['user_id']);
						sendmail($user['email'], $_SESSION['GO_SESSION']['email'], htmlspecialchars($_SESSION['GO_SESSION']['name']), $subject, $body, '3', 'text/HTML');
					}elseif(isset($accept))
					{
						$body = sprintf($cal_your_resource_accepted_mail_body,htmlspecialchars($_SESSION['GO_SESSION']['name']),$calendar['name']).'<br /><br />';
						$body .= $cal->event_to_html($event);
						//$body .= "<br />".$event_link->get_html();
						$subject = sprintf($cal_your_resource_accepted_mail_subject, $calendar['name']);

						$user = $GO_USERS->get_user($old_event['user_id']);
						sendmail($user['email'], $_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name'], $subject, $body, '3', 'text/HTML');
					}
				}



				if(isset($link) && $link)
				{
					$GO_LINKS->add_link($link['id'], $link['type'], $event['link_id'], 1);
					$GO_LINKS->deactivate_linking();

				}

				$send_invitation = false;
				if ($_POST['close'] == 'true') {
					header('Location: '.$return_to);
					exit ();
				} else {
					//$task = '';
				}
			}
		}
		break;
}



if($event_id> 0)
{
	if (isset ($_POST['new_event']) && $_POST['new_event'] == 'true') {
		//reset all event related form fields
		unset ($event, $_POST['name'], $_POST['description'], $_POST['location'], $_POST['reminder'], $_POST['background'], $_POST['permissions'], $_POST['repeat_type'], $_POST['repeat_end_date'], $_POST['repeat_forever'], $_POST['repeat_every'], $_POST['all_day_event'], $_POST['month_time'], $_POST['repeat_days_0'], $_POST['repeat_days_1'], $_POST['repeat_days_2'], $_POST['repeat_days_3'], $_POST['repeat_days_4'], $_POST['repeat_days_5'], $_POST['repeat_days_6']);
		$event_id = 0;
	}else {
		$event = $cal->get_event($event_id);
	}

}

if ($event_id > 0 && $task != 'save_event' && $task != 'change_event') {


	$is_resource = ($event['event_id'] > 0 || $calendar['group_id'] > 1);
	//get the event

	if(!$event['write_permission']	&& !$event['read_permission'])
	{
		header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
		exit ();
	}elseif(!$is_resource && $event['repeat_type'] != REPEAT_NONE && !isset($_REQUEST['create_exception']) && isset($_REQUEST['gmt_start_time']))
	{
		require($GO_THEME->theme_path.'header.inc');

		$form = new form('event_form');

		$form->add_html_element(new input('hidden','event_id',$event_id, false));
		$form->add_html_element(new input('hidden','gmt_start_time',$_REQUEST['gmt_start_time'], false));
		$form->add_html_element(new input('hidden','return_to',$return_to, false));
		$form->add_html_element(new input('hidden','create_exception','false', false));


		//echo date('Ymd  G:i', $_REQUEST['gmt_start_time']);


		$p = new html_element('h2');

		$img = new image('questionmark');
		$img->set_attribute('align','middle');
		$img->set_attribute('style','border:0px;margin-right:10px;');
		$p->add_html_element($img);

		$p->innerHTML .= $cal_edit_series_or_single;
		$form->add_html_element($p);

		$form->add_html_element(new button($cal_single, "javascript:document.event_form.create_exception.value='true';document.event_form.submit();", '120'));
		$form->add_html_element(new button($cal_series, "javascript:document.event_form.create_exception.value='false';document.event_form.submit();", '120'));
		$form->add_html_element(new button($cmdCancel, "javascript:document.location='".htmlspecialchars($return_to)."';"));
		echo $form->get_html();
		require($GO_THEME->theme_path.'footer.inc');
		exit();

	}
}elseif (isset ($_REQUEST['ical_file']) && file_exists($_REQUEST['ical_file'])) {
	$event = $cal->get_event_from_ical_file($_REQUEST['ical_file']);
	$ical = true;
}

if ($task != 'save_event' && $task != 'change_event' && ($event_id > 0 || isset ($ical)) && isset ($event) && $event) {

	$event=$cal->shift_days_to_local($event);

	//populate an address string of the participants
	$event['to'] = '';
	$cal->get_participants($event_id);
	while ($cal->next_record()) {
		if ($event['to'] == '') {
			$event['to'] = $RFC822->write_address($cal->f("name"), $cal->f("email"));
		} else {
			$event['to'] .= ', '.$RFC822->write_address($cal->f("name"), $cal->f("email"));
		}
	}


	if(isset($_REQUEST['create_exception']) && $_REQUEST['create_exception'] == 'true')
	{
		$duration = $event['end_time'] - $event['start_time'];
		$event['start_time'] = $_REQUEST['exception_time'] = $_REQUEST['gmt_start_time'];
		$event['end_time'] = $event['start_time']+$duration;
		$event['repeat_type'] = REPEAT_NONE;
		$event['resources'] = array();
		$event_id=0;

		//$link_back = $_SERVER['PHP_SELF'].'?event_id=0&create_exception=true&exception_event_id='.
		//$_REQUEST['event_id'].'&exception_time='.$_REQUEST['exception_time'].'&return_to='.urlencode($return_to);
	}else
	{
		$_POST['exception_time'] = 0;
		$event['resources'] = array();
		$cal->get_event_resources($event_id);
		while ($cal->next_record()) {
			$event['resources'][] = $cal->f('calendar_id');
		}
	}

	$gmt_start_time = $event['start_time'];
	//if($event['all_day_event'] != '1')
	//{
	$event['start_time'] = gmt_to_local_time($event['start_time']);
	$event['end_time'] =gmt_to_local_time($event['end_time']);
	//}

	$event['start_hour'] = date('G', $event['start_time']);
	$event['start_min'] = date('i', $event['start_time']);

	$event['end_hour'] = date('G', $event['end_time']);
	$event['end_min'] = date('i', $event['end_time']);



	$event['start_date'] = date($_SESSION['GO_SESSION']['date_format'], $event['start_time']);
	$event['end_date'] = date($_SESSION['GO_SESSION']['date_format'], $event['end_time']);

	//$event['repeat_end_date'] = date($_SESSION['GO_SESSION']['date_format'], $event['repeat_end_time']);

	if ($event['repeat_type'] != REPEAT_NONE) {
		if ($event['repeat_forever'] == '0') {
			$event['repeat_end_date'] = date($_SESSION['GO_SESSION']['date_format'], gmt_to_local_time($event['repeat_end_time']-86400));
		} else {
			$event['repeat_end_date'] = date($_SESSION['GO_SESSION']['date_format'], $event['end_time']);
		}
	} else {
		$event['repeat_end_date'] = date($_SESSION['GO_SESSION']['date_format'], $event['start_time']);
	}

	//to what calendars is this event subscribed?
	$event['calendars'] = array ();
	$cal->get_event_subscribtions($event_id);
	while ($cal->next_record()) {
		$event['calendars'][] = $cal->f('calendar_id');
	}



	/*
	//shift the selected weekdays to local time
	$local_start_hour = date("G", $gmt_start_time) + get_timezone_offset($event['start_time']);
	if ($local_start_hour > 23) {
	$local_start_hour = $local_start_hour -24;
	$shift_day = 1;
	}
	elseif ($local_start_hour < 0) {
	$local_start_hour = 24 + $local_start_hour;
	$shift_day = -1;
	} else {
	$shift_day = 0;
	}

	switch ($shift_day) {
	case 1 :
	$mon = $event['sun'] == '1' ? '1' : '0';
	$tue = $event['mon'] == '1' ? '1' : '0';
	$wed = $event['tue'] == '1' ? '1' : '0';
	$thu = $event['wed'] == '1' ? '1' : '0';
	$fri = $event['thu'] == '1' ? '1' : '0';
	$sat = $event['fri'] == '1' ? '1' : '0';
	$sun = $event['sat'] == '1' ? '1' : '0';
	break;

	case -1 :
	$mon = $event['tue'] == '1' ? '1' : '0';
	$tue = $event['wed'] == '1' ? '1' : '0';
	$wed = $event['thu'] == '1' ? '1' : '0';
	$thu = $event['fri'] == '1' ? '1' : '0';
	$fri = $event['sat'] == '1' ? '1' : '0';
	$sat = $event['sun'] == '1' ? '1' : '0';
	$sun = $event['mon'] == '1' ? '1' : '0';
	break;

	}

	if ($shift_day != 0) {
	$event['sun'] = $sun;
	$event['mon'] = $mon;
	$event['tue'] = $tue;
	$event['wed'] = $wed;
	$event['thu'] = $thu;
	$event['fri'] = $fri;
	$event['sat'] = $sat;
	}
	*/
	if($calendar['group_id'] > 1 || $event['event_id'] > 0)
	{
		$title = sprintf($cal_booking, $calendar['name']);
	}else
	{
		$title = htmlspecialchars($event['name']);
	}

	if ($event['completion_time'] > 0)
	{
		$event['completion_time'] += (get_timezone_offset($event['completion_time'])*3600);
		$event['completed'] = true;
		$event['completion_date'] = date($_SESSION['GO_SESSION']['date_format'], $event['completion_time']);
		$event['completion_hour'] = date('G', $event['completion_time']);
		$event['completion_min'] = date('i', $event['completion_time']);

	}else
	{
		$event['completed'] = false;
		$event['completion_date'] = date($_SESSION['GO_SESSION']['date_format'], $local_time);
		$event['completion_hour'] = date('G', $local_time);
		$event['completion_min'] = date('i', $local_time);
	}

} else {

	$default_background = $calendar ? $calendar['background'] : 'FFFFCC';
	$event['background'] = isset ($_POST['background']) ? $_POST['background'] : $default_background;

	if(isset($_POST['single_calendar_id']) && $_POST['single_calendar_id']>0)
	{
		$event['calendars'] = array($_POST['single_calendar_id']);
		$calendar_id = smart_addslashes($_POST['single_calendar_id']);
		$calendar = $cal->get_calendar($calendar_id);
		$event['background'] = $calendar['background'];
	}elseif(isset($_POST['calendars']))
	{
		$event['calendars'] = $_POST['calendars'];
	}else
	{
		$event['calendars'] = array();
		if(isset($_REQUEST['view_id']) && $_REQUEST['view_id'] > 0)
		{
			$view_calendars = $cal->get_view_calendars($_REQUEST['view_id']);
			foreach($view_calendars as $view_calendar)
			{
				//if($view_calendar['user_id']==$GO_SECURITY->user_id)
				//{
				$event['calendars'][] = $view_calendar['id'];
				//break;
				//}
			}
		}
	}

	$event['todo'] = isset($_REQUEST['todo']) ? $_REQUEST['todo'] : '0';
	$event['user_id']=isset ($_POST['user_id']) ? smart_stripslashes($_POST['user_id']) : $GO_SECURITY->user_id;
	$event['name'] = isset ($_POST['name']) ? smart_stripslashes($_POST['name']) : '';
	if($calendar['group_id'] > 1)
	{
		$title = sprintf($cal_booking, $calendar['name']);
	}else
	{

		if(empty($event['name']))
		{
			$title = $event['todo'] == '1' ? $cal_add_todo : $sc_new_app;
		}else {
			$title = htmlspecialchars($event['name']);
		}

	}
	$requested_time = mktime($hour, $min, 0, $month, $day, $year);

	$timezone_offset = get_timezone_offset($requested_time);

	$requested_date = date($_SESSION['GO_SESSION']['date_format'], $requested_time);
	//new event declare all vars



	$event['resources'] = isset ($_POST['resources']) ? $_POST['resources'] : array ();
	$event['description'] = isset ($_POST['description']) ? smart_stripslashes($_POST['description']) : '';

	$event['to'] = isset ($_POST['to']) ? smart_stripslashes($_POST['to']) : '';
	$event['user_id'] = isset ($_REQUEST['user_id']) ? $_REQUEST['user_id'] : $GO_SECURITY->user_id;
	$event['event_id'] = 0;


	if($event['todo'] == '1')
	{
		$event['status_id'] = isset ($_REQUEST['todo_status_id']) ? $_REQUEST['todo_status_id'] : 1;
	}else
	{
		$event['status_id'] = isset ($_REQUEST['event_status_id']) ? $_REQUEST['event_status_id'] : 1;
	}


	$event['start_date'] = isset ($_POST['start_date']) ? smart_stripslashes($_POST['start_date']) : $requested_date;
	$tmp = (strlen($hour) == 1) ? '0'.$hour : $hour;

	$event['start_hour'] = isset ($_POST['start_hour']) ? $_POST['start_hour'] : $tmp;
	$event['start_min'] = isset ($_POST['start_min']) ? $_POST['start_min'] : $min;

	$event['end_date'] = isset ($_POST['end_date']) ? $_POST['end_date'] : $requested_date;
	$tmp = (strlen($hour +1) == 1) ? '0'.$hour +1 : $hour +1;
	$event['end_hour'] = isset ($_POST['end_hour']) ? $_POST['end_hour'] : $tmp;
	$event['end_min'] = isset ($_POST['end_min']) ? $_POST['end_min'] : $min;

	$event['repeat_end_date'] = isset ($_POST['repeat_end_date']) ? $_POST['repeat_end_date'] : $requested_date;

	$event['repeat_type'] = isset ($_POST['repeat_type']) ? $_POST['repeat_type'] : REPEAT_NONE;
	$event['all_day_event'] = isset ($_POST['all_day_event']) ? $_POST['all_day_event'] : '0';
	if($task == 'save_event' || $task == 'change_event')
	{
		$event['repeat_forever'] = isset ($_POST['repeat_forever']) ? $_POST['repeat_forever'] : '0';
	}else
	{
		$event['repeat_forever'] = '1';
	}
	$event['repeat_every'] = isset ($_POST['repeat_every']) ? $_POST['repeat_every'] : '0';
	$event['month_time'] = isset ($_POST['month_time']) ? $_POST['month_time'] : '0';


	$requested_weekday = ($event_id==0 && $task!='save_event' && $task!='change_event') ? date('w', $requested_time) : -1;

	$event['sun'] = (isset ($_POST['repeat_days_0']) || $requested_weekday==0) ? true : false;
	$event['mon'] = (isset ($_POST['repeat_days_1']) || $requested_weekday==1) ? true : false;
	$event['tue'] = (isset ($_POST['repeat_days_2']) || $requested_weekday==2) ? true : false;
	$event['wed'] = (isset ($_POST['repeat_days_3']) || $requested_weekday==3) ? true : false;
	$event['thu'] = (isset ($_POST['repeat_days_4']) || $requested_weekday==4) ? true : false;
	$event['fri'] = (isset ($_POST['repeat_days_5']) || $requested_weekday==5) ? true : false;
	$event['sat'] = (isset ($_POST['repeat_days_6']) || $requested_weekday==6) ? true : false;
	//$event['reminder'] = isset ($_POST['reminder']) ? $_POST['reminder'] : '0';


	$event['location'] = isset ($_POST['location']) ? smart_stripslashes($_POST['location']) : '';
	$event['permissions'] = isset ($_POST['permissions']) ? $_POST['permissions'] :$cal_settings['permissions'];



	$event['completed'] = isset($_POST['completed']) ? true : false;
	$event['completion_date'] = isset($_POST['completion_date']) ? $_POST['completion_date'] : $requested_date;
	$event['completion_hour'] = isset($_POST['completion_hour']) ? $_POST['completion_hour'] : date('G', $local_time);
	$event['completion_min'] = isset($_POST['completion_min']) ? $_POST['completion_min'] :  date('i', $local_time);

	$event['completion_time'] = date_to_unixtime($event['completion_date'].' '.$event['completion_hour'].':'.$event['completion_min']);;

}



$is_resource=($event['event_id'] > 0 || $calendar['group_id'] > 1);




$GO_HEADER['head'] = date_picker::get_header();
$GO_HEADER['head'] .= color_selector::get_header();

if($event_id>0)
{
	load_control('links_list');
	$links_list = new links_list($event['link_id'], 'event_form', $link_back);
	$GO_HEADER['head'] .= $links_list->get_header();
}

if($task == 'availability')
{
	load_control('tooltip');
	$GO_HEADER['head'] .= tooltip::get_header();
}else
{
	$GO_HEADER['body_arguments'] = 'onload="javascript:document.event_form.name.focus();"';
}

require_once ($GO_THEME->theme_path.'header.inc');

if ($ab_module) {
	echo $ab->enable_contact_selector();
}

$form = new form('event_form');

if($task == 'availability')
{
	require('check_availability.inc');
}else
{
	$form->add_html_element(new input('hidden', 'user_id', $event['user_id'], false));
	$form->add_html_element(new input('hidden', 'calendar_id', $calendar_id, false));
	$form->add_html_element(new input('hidden', 'event_id', $event_id, false));
	$form->add_html_element(new input('hidden', 'close', 'false', false));
	$form->add_html_element(new input('hidden', 'return_to', $return_to, false));
	$form->add_html_element(new input('hidden', 'link_back', $link_back, false));
	$form->add_html_element(new input('hidden', 'new_event', 'false', false));
	$form->add_html_element(new input('hidden','task','change_event'));
	$form->add_html_element(new input('hidden', 'goto_url', '', false));


	if($cal_settings['check_conflicts'] == '0' || (isset($conflicts) && count($conflicts)))
	{
		$form->add_html_element(new input('hidden', 'ignore_conflicts','true'));
	}

	if(isset($_REQUEST['create_exception']) )
	{
		$form->add_html_element(new input('hidden', 'create_exception', $_REQUEST['create_exception'], false));
		if($_REQUEST['create_exception'] =='true')
		{
			$form->add_html_element(new input('hidden', 'exception_event_id', $_REQUEST['event_id']));
			$form->add_html_element(new input('hidden', 'exception_time', $_REQUEST['exception_time']));
		}
	}

	$tabstrip = new dynamic_tabstrip('event_strip', $title, '120', 'event_form');
	$tabstrip->set_attribute('style','width:100%');
	$tabstrip->set_return_to($return_to);
	if(isset($activetab))
	{
		$tabstrip->set_active_tab($activetab);
	}

	if (isset($feedback))
	{
		$p = new html_element('p',$feedback);
		$p->set_attribute('class','Error');
		$form->add_html_element($p);
	}

	$table = new table();
	$table->set_attribute('style','width:100%');

	$row = new table_row();
	$cell = new table_cell($strName.'*:');
	$cell->set_attribute('style','width:200px;');
	$row->add_cell($cell);
	$input = new input('text','name',$event['name']);
	$input->set_attribute('maxlength','50');
	$input->set_attribute('style','width:100%');
	$cell = new table_cell($input->get_html());
	$cell->set_attribute('style','width:100%;');
	$row->add_cell($cell);
	$table->add_row($row);

	if($event_id > 0)
	{
		$form->add_html_element(new input('hidden','user_id',$event['user_id']));
		$row = new table_row();
		$row->add_cell(new table_cell($strOwner.':'));

		$subtable = new table();
		$subtable->set_attribute('style','width:100%;');
		$subtable->set_attribute('cellpadding','0');
		$subtable->set_attribute('cellspacing','0');
		$subrow = new table_row();
		$subrow->add_cell(new table_cell(show_profile($event['user_id'])));
		$subcell = new table_cell($strCreatedAt.': '.get_timestamp($event['ctime']));
		$subcell->set_attribute('style','text-align:right');
		$subrow->add_cell($subcell);
		$subcell = new table_cell($strModifiedAt.': '.get_timestamp($event['mtime']));
		$subcell->set_attribute('style','text-align:right');
		$subrow->add_cell($subcell);
		$subtable->add_row($subrow);

		$row->add_cell(new table_cell($subtable->get_html()));
		$table->add_row($row);
	}

	/*if($event['event_id'] > 0 || $calendar['group_id'] > 1)
	{
	$form->add_html_element(new input('hidden','todo','0'));
	}else
	{
	$row = new table_row();
	$row->add_cell(new table_cell($strType.':'));

	$radiogroup = new radiogroup('todo', $event['todo']);

	$event_button = new radiobutton('event_button', '0');
	$event_button->set_attribute('onclick', "javascript:toggle_statuses('VEVENT');");
	$todo_button = new radiobutton('todo_button', '1');
	$todo_button->set_attribute('onclick', "javascript:toggle_statuses('VTODO');");

	$row->add_cell(new table_cell($radiogroup->get_option($event_button, $cal_event).
	$radiogroup->get_option($todo_button, $cal_todo)));
	$table->add_row($row);
	}	*/

	$form->add_html_element(new input('hidden','todo', $event['todo']));


	$row = new table_row();
	$cell = new table_cell();
	$cell->set_attribute('valign','top');
	$cell->set_attribute('nowrap','true');

	if($is_resource)
	{
		$form->add_html_element(new input('hidden','send_invitation','false'));
		$form->add_html_element(new input('hidden','to',''));
		$form->add_html_element(new input('hidden','reminder_value','0'));
		$form->add_html_element(new input('hidden','reminder_multiplier','0'));
	}else
	{
		if ($ab_module && $ab_module['read_permission']) {

			$img = new image('addressbook_small');
			$img->set_attribute('style','border:0px;width:16px;height:16px;margin-right: 5px;');
			$img->set_attribute('align','absmiddle');

			$hyperlink = new hyperlink($ab->select_contacts('document.event_form.to', $GO_CONFIG->control_url.'select/add.php'), $img->get_html().$sc_participants.':');
			$hyperlink->set_attribute('class','normal');

			$cell->add_html_element($hyperlink);
		}else
		{
			$cell->innerHTML .= $sc_participants.':';
		}
		$row->add_cell($cell);
		$cell = new table_cell();

		require($GO_MODULES->modules['email']['class_path'].'email_autocomplete.class.inc');

		$autocomplete = new email_autocomplete(
		'to',
		'to',
		$event['to'],
		'0'
		);

		$autocomplete->set_attribute('style','width:100%;height:50px');


		$cell->add_html_element($autocomplete);

		
		$row->add_cell($cell);
		$table->add_row($row);
		
		if ($event_id > 0) {
			$row = new table_row();
			$row->add_cell(new table_cell());
			
			$checkbox =new checkbox('send_invitation', 'send_invitation', 'true', $cal_resend_invitation, $send_invitation);
			$row->add_cell(new table_cell($checkbox->get_html()));
			$table->add_row($row);
		} else {
			$form->add_html_element(new input('hidden','send_invitation','true'));
		}
	}


	$row = new table_row();
	$row->add_cell(new table_cell($sc_location.':'));
	$input = new input('text','location',$event['location']);
	$input->set_attribute('maxlength','50');
	$input->set_attribute('style','width:100%');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	$row = new table_row();
	$cell = new table_cell($sc_description.':');
	$cell->set_attribute('valign','top');
	$row->add_cell($cell);
	$input = new textarea('description',$event['description']);
	$input->set_attribute('style','width:100%;height: 70px;');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);

	/*$row = new table_row();
	$cell = new table_cell('&nbsp;');
	$cell->set_attribute('colspan','2');
	$row->add_cell($cell);
	$table->add_row($row);*/

	$row = new table_row();
	$row->add_cell(new table_cell($sc_start_at.':'));

	$subtable= new table();
	$subtable->set_attribute('cellpadding','0');
	$subtable->set_attribute('cellspacing','0');
	$subrow= new table_row();
	$datepicker = new date_picker('start_date', $_SESSION['GO_SESSION']['date_format'], $event['start_date'], '', '', 'onchange="javascript:check_date(\'start_date\');"');
	$subrow->add_cell(new table_cell($datepicker->get_html()));

	$select_hour = new select("start_hour", $event['start_hour']);
	$select_hour->set_attribute('onchange','javascript:update_end_hour();');
	for ($i = 0; $i < 24; $i ++) {
		$select_hour->add_value($i, str_replace(':00', '', date($_SESSION['GO_SESSION']['time_format'], mktime($i, 0, 0))));
	}

	$select_min = new select('start_min', $event['start_min']);
	$select_min->set_attribute('onchange','javascript:document.event_form.end_min.value=this.value;');
	$select_min->add_arrays($mins, $mins);

	$subrow->add_cell(new table_cell($select_hour->get_html().'&nbsp;:&nbsp;'.$select_min->get_html()));

	$button = new button($strAvailability, "javascript:check_availability();");
	$button->set_attribute('style','margin-top:0px;');
	$subrow->add_cell(new table_cell($button->get_html()));

	$subtable->add_row($subrow);

	$row->add_cell(new table_cell($subtable->get_html()));
	$table->add_row($row);



	$row = new table_row();
	$row->add_cell(new table_cell($sc_end_at.':'));

	$subtable= new table();
	$subtable->set_attribute('cellpadding','0');
	$subtable->set_attribute('cellspacing','0');
	$subrow= new table_row();
	$datepicker = new date_picker('end_date', $_SESSION['GO_SESSION']['date_format'], $event['end_date'], '', '', 'onchange="javascript:check_date(\'end_date\');"');
	$subrow->add_cell(new table_cell($datepicker->get_html()));

	$select_hour = new select("end_hour", $event['end_hour']);
	for ($i = 0; $i < 24; $i ++) {
		$select_hour->add_value($i, str_replace(':00', '', date($_SESSION['GO_SESSION']['time_format'], mktime($i, 0, 0))));
	}

	$select_min = new select('end_min', $event['end_min']);
	$select_min->add_arrays($mins, $mins);

	$subrow->add_cell(new table_cell($select_hour->get_html().'&nbsp;:&nbsp;'.$select_min->get_html()));

	$all_day_event = ($event['all_day_event'] == '1') ? true : false;
	$checkbox = new checkbox('all_day_event', 'all_day_event', '1', $sc_notime, $all_day_event);
	$checkbox->set_attribute('onclick', 'javascript:disable_time();');
	$subrow->add_cell(new table_cell($checkbox->get_html()));
	$subtable->add_row($subrow);

	$row->add_cell(new table_cell($subtable->get_html()));
	$table->add_row($row);


	if($is_resource)
	{
		if($event_id==0)
		{
			$admin_count = $cal->get_resource_group_admins($calendar['group_id']);
			if($admin_count>0 && $cal->is_resource_group_admin($event['user_id'], $calendar['group_id']))
			{
				$event['status_id']=2;
			}else
			{
				//No admins so default to accepted
				$event['status_id']=1;
			}
		}

		$row = new table_row();
		$row->add_cell(new table_cell($sc_status.':'));
		$status=$cal->get_status($event['status_id']);
		$row->add_cell(new table_cell($cal_statuses[$status['name']]));
		$table->add_row($row);


		$form->add_html_element(new input('hidden', 'event_status_id',$event['status_id']));
		if($cal->get_writable_calendars($GO_SECURITY->user_id, $calendar['group_id']))
		{
			//this is a resource. Only select resource calendar
			$row = new table_row();
			$row->add_cell(new table_cell($cal_resource.':'));

			if (count($event['calendars']) == 0) {
				$event['calendars'][] = $calendar['id'];
			}
			$select = new select('calendars[]', $event['calendars'][0]);
			while($cal->next_record())
			{
				$form->add_html_element(new input('hidden', 'writable_calendars[]', $cal->f('id')));

				$select->add_value($cal->f('id'), $cal->f('name'));
			}
			$row->add_cell(new table_cell($select->get_html()));
			$table->add_row($row);
			$form->add_html_element(new input('hidden', 'old_resource_calendars', $event['calendars'][0]));
		}else
		{
			$form->add_html_element(new input('hidden', 'calendars[]', $event['calendars'][0]));
		}
	}else
	{
		$row = new table_row();
		$row->add_cell(new table_cell($sc_status.':'));

		$event_select = new select('event_status_id',$event['status_id']);
		$event_select->set_attribute('id', 'event_status_id');
		if($event['todo'] == '1')
		{
			$event_select->set_attribute('style','display:none');
		}
		$cal->get_statuses('VEVENT');
		while($cal->next_record())
		{
			$event_select->add_value($cal->f('id'), $cal_statuses[$cal->f('name')]);
		}

		$todo_select = new select('todo_status_id',$event['status_id']);
		$todo_select->set_attribute('id', 'todo_status_id');
		if($event['todo'] == '0')
		{
			$todo_select->set_attribute('style','display:none');
		}
		$cal->get_statuses('VTODO');
		while($cal->next_record())
		{
			$todo_select->add_value($cal->f('id'), $cal_statuses[$cal->f('name')]);
		}


		if($event['todo'] == '1')
		{
			$cell = new table_cell();

			$subtable= new table();
			$subtable->set_attribute('cellpadding','0');
			$subtable->set_attribute('cellspacing','0');
			$subrow= new table_row();

			$todo_select->set_attribute('onchange', "javascript:disable_completion_time(this.value);");

			$subrow->add_cell(new table_cell($event_select->get_html().$todo_select->get_html()));

			$datepicker= new date_picker('completion_date', $_SESSION['GO_SESSION']['date_format'], $event['completion_date'], '', '', '', !$event['completed']);
			$subrow->add_cell(new table_cell($datepicker->get_html()));


			$select_hour = new select("completion_hour", $event['completion_hour']);
			if(!$event['completed'])
			{
				$select_hour->set_attribute('disabled','true');
			}
			$select_hour->add_arrays($hours, $hours);

			$select_min = new select("completion_min", $event['completion_min']);
			if(!$event['completed'])
			{
				$select_min->set_attribute('disabled','true');
			}
			$select_min->add_arrays($mins, $mins);

			$ct_cell = new table_cell('&nbsp;&nbsp;'.$select_hour->get_html().'&nbsp;:&nbsp;'.$select_min->get_html());


			$subrow->add_cell($ct_cell);
			$subtable->add_row($subrow);
			$row->add_cell(new table_cell($subtable->get_html()));
		}else
		{
			$row->add_cell(new table_cell($event_select->get_html().$todo_select->get_html()));
		}
		$table->add_row($row);
	}



	if($event_id > 0 && $task != 'save_event' && $task != 'change_event')
	{
		if(!empty($event['custom_fields']))
		{
			$fieldsNode =text_to_xml($event['custom_fields']);
			$fields = $fieldsNode->children();
		}else
		{
			$fields=false;
		}
	}else
	{
		$fields = $cal->get_custom_fields($calendar['group_id']);
	}

	if(count($fields) > 0 && isset($fields[0]->_name))
	{
		$row = new table_row();
		$cell = new table_cell('&nbsp;');
		$cell->set_attribute('colspan','2');
		$row->add_cell($cell);
		$table->add_row($row);
		foreach(	$fields as $inputNode)
		{
			$row = new table_row();
			$type = $inputNode->get_attribute('type') ? $inputNode->get_attribute('type') : 'text';
			switch($type)
			{
				case 'text':
					$cell = new table_cell($inputNode->get_attribute('name').':');
					$cell->set_attribute('style','white-space:nowrap');
					$row->add_cell($cell);
					if($task == 'save_event' || $task == 'change_event')
					{
						$value = isset($_POST['custom_fields'][$inputNode->get_attribute('name')]) ?
						$_POST['custom_fields'][$inputNode->get_attribute('name')] : '';
					}else
					{
						$value = $inputNode->get_attribute('value');
					}
					$input = new input($inputNode->get_attribute('type'),'custom_fields['.addslashes($inputNode->get_attribute('name')).']', $value);
					$row->add_cell(new table_cell($input->get_html()));
					break;

				case 'checkbox':
					if($task == 'save_event' || $task == 'change_event')
					{
						$value = isset($_POST['custom_fields'][$inputNode->get_attribute('name')]);
					}else
					{
						$value = $inputNode->get_attribute('value')=='1';
					}
					$options_checkbox = new checkbox($inputNode->get_attribute('name'),
					'custom_fields['.addslashes($inputNode->get_attribute('name')).']','1',$inputNode->get_attribute('name'),$value);
					$options_cell = new table_cell($options_checkbox->get_html());
					$options_cell->set_attribute('colspan','2');
					$row->add_cell($options_cell);
					break;

				case 'textarea':
					$cell = new table_cell($inputNode->get_attribute('name').':');
					$cell->set_attribute('style','white-space:nowrap;vertical-align:top');
					$row->add_cell($cell);
					if($task == 'save_event' || $task == 'change_event')
					{
						$value = isset($_POST['custom_fields'][$inputNode->get_attribute('name')]) ?
						$_POST['custom_fields'][$inputNode->get_attribute('name')] : '';
					}else
					{
						$value = $inputNode->get_attribute('value');
					}
					$input = new textarea('custom_fields['.addslashes($inputNode->get_attribute('name')).']', $value);
					$input->set_attribute('style', 'width:300px;height:50px;');
					$row->add_cell(new table_cell($input->get_html()));
					break;

				case 'date':
					$cell = new table_cell($inputNode->get_attribute('name').':');
					$cell->set_attribute('style','white-space:nowrap');
					$row->add_cell($cell);
					if($task == 'save_event' || $task == 'change_event')
					{
						$value = isset($_POST['custom_fields'][$inputNode->get_attribute('name')]) ?
						$_POST['custom_fields'][$inputNode->get_attribute('name')] : '';
					}else
					{
						$value = $inputNode->get_attribute('value');
					}
					$datepicker = new date_picker('custom_fields['.addslashes($inputNode->get_attribute('name')).']', $_SESSION['GO_SESSION']['date_format'], $value);
					$row->add_cell(new table_cell($datepicker->get_html()));
					break;
			}
			$table->add_row($row);
		}
	}


	$tabstrip->add_tab('properties', $strProperties, $table);


	//Begin recurrence tab

	$table = new table();
	$row = new table_row();
	$row->add_cell(new table_cell($sc_recur_every.':'));

	$cell = new table_cell();
	$select = new select('repeat_every', $event['repeat_every']);
	for ($i = 1; $i < 13; $i ++) {
		$select->add_value($i, $i);
	}
	$cell->add_html_element($select);

	$select = new select('repeat_type', $event['repeat_type']);
	$select->set_attribute('onchange','javascript:toggle_repeat(this.value);');
	$select->add_value('0', $sc_types1[REPEAT_NONE]);
	$select->add_value('1', $sc_types1[REPEAT_DAILY]);
	$select->add_value('2', $sc_types1[REPEAT_WEEKLY]);
	$select->add_value('3', $sc_types1[REPEAT_MONTH_DATE]);
	$select->add_value('4', $sc_types1[REPEAT_MONTH_DAY]);
	$select->add_value('5', $sc_types1[REPEAT_YEARLY]);
	$cell->add_html_element($select);

	$row->add_cell($cell);
	$table->add_row($row);



	$row = new table_row();
	$row->add_cell(new table_cell($sc_at_days.':'));

	$cell = new table_cell();


	$day_data_field[0] = 'sun';
	$day_data_field[1] = 'mon';
	$day_data_field[2] = 'tue';
	$day_data_field[3] = 'wed';
	$day_data_field[4] = 'thu';
	$day_data_field[5] = 'fri';
	$day_data_field[6] = 'sat';

	$day_number = $_SESSION['GO_SESSION']['first_weekday'];

	$subtable= new table();
	$subtable->set_attribute('cellpadding','0');
	$subtable->set_attribute('cellspacing','0');
	$subrow= new table_row();

	$select = new select("month_time", $event['month_time']);
	$select->add_arrays(array (1, 2, 3, 4), $month_times);
	$subrow->add_cell(new table_cell($select->get_html()));

	for ($i = 0; $i < 7; $i ++) {
		if ($day_number == 7)
		$day_number = 0;

		$checkbox = new checkbox('repeat_days_'.$day_number, 'repeat_days_'.$day_number, '1', $days[$day_number], $event[$day_data_field[$day_number]]);
		$subrow->add_cell(new table_cell($checkbox->get_html()));
		$day_number ++;
	}
	$subtable->add_row($subrow);
	$cell->add_html_element($subtable);

	$row->add_cell($cell);
	$table->add_row($row);


	$row = new table_row();
	$row->add_cell(new table_cell($sc_cycle_end.':'));

	$datepicker= new date_picker('repeat_end_date', $_SESSION['GO_SESSION']['date_format'], $event['repeat_end_date']);

	$repeat_forever = $event['repeat_forever'] == '1' ? true : false;
	$checkbox = new checkbox('repeat_forever','repeat_forever', '1', $sc_noend, $repeat_forever);
	$checkbox->set_attribute('onclick', 'javascript:toggle_repeat_end_info();');

	$row->add_cell(new table_cell($datepicker->get_html().$checkbox->get_html()));

	$table->add_row($row);

	$tabstrip->add_tab('recurrence', $sc_recur_section, $table);


	//Begin other options

	if(!$is_resource)
	{
		$table = new table();
		$row = new table_row();
		$row->add_cell(new table_cell($sc_reminder.':'));

		$cell = new table_cell();

		if(empty($event['id']) && empty($event['reminder'])){
			$event['reminder'] = $cal_settings['reminder'];
		}



		$multipliers[] = 604800;
		$multipliers[] = 86400;
		$multipliers[] = 3600;
		$multipliers[] = 60;

		$reminder_multiplier = 60;
		$reminder_value = 0;

		if($event['reminder'] != 0)
		{
			for ($i = 0; $i < count($multipliers); $i ++) {
				$devided = $event['reminder'] / $multipliers[$i];
				$match = (int) $devided;
				if ($match == $devided) {
					$reminder_multiplier = $multipliers[$i];
					$reminder_value = $devided;
					break;
				}
			}
		}
		$select = new select('reminder_value', $reminder_value);
		$select->add_value('0', $cal_no_reminder);
		for ($i = 1; $i < 60; $i ++) {
			$select->add_value($i, $i);
		}

		$cell->add_html_element($select);


		$select = new select('reminder_multiplier', $reminder_multiplier);
		$select->add_value('60', $sc_mins);
		$select->add_value('3600', $sc_hours);
		$select->add_value('86400', $sc_days);
		$select->add_value('604800', $sc_weeks);

		$cell->add_html_element($select);
		$row->add_cell($cell);
		$table->add_row($row);



		$row = new table_row();
		$cell = new table_cell('&nbsp;');
		$cell->set_attribute('colspan','2');
		$row->add_cell($cell);
		$table->add_row($row);


		$row = new table_row();
		$cell = new table_cell($sc_background.':');
		$row->add_cell($cell);
		$color_selector = new color_selector('background','background', $event['background'], 'event_form');
		$row->add_cell(new table_cell($color_selector->get_html()));
		$table->add_row($row);

		$row = new table_row();
		$cell = new table_cell($strPermissions.':');
		$cell->set_attribute('valign','top');
		$row->add_cell($cell);

		$radiogroup = new radiogroup('permissions', $event['permissions']);
		$rb1 = new radiobutton('rb_participants_write', PARTICIPANTS_WRITE);
		$rb2 = new radiobutton('rb_everybody_read', EVERYBODY_READ);
		$rb3 = new radiobutton('rb_everybody_write', EVERYBODY_WRITE);
		$rb4 = new radiobutton('rb_private', PRIVATE_EVENT);

		$row->add_cell(new table_cell(
		$radiogroup->get_option($rb1, $cal_participants_write).'<br />'.
		$radiogroup->get_option($rb2, $cal_everybody_read).'<br />'.
		$radiogroup->get_option($rb3, $cal_everybody_write).'<br />'.
		$radiogroup->get_option($rb4, $sc_private_event)));

		$table->add_row($row);

		$tabstrip->add_tab('options', $sc_options_section, $table);


		//Begin calendars


		if (count($event['calendars']) == 0) {
			$event['calendars'][] = $calendar['id'];
		}

		$div = new html_element('div');

		$count=0;
		$select_cals=array();

		if($count += $cal->get_writable_calendars($GO_SECURITY->user_id,0))
		{

			while($cal->next_record())
			{
				$form->add_html_element(new input('hidden', 'writable_calendars[]', $cal->f('id')));

				if (!isset ($first_writable_cal)) {
					$first_writable_cal = $cal->f('id');
				}
				$checkbox = new checkbox('cal_'.$cal->f('id'), 'calendars[]', $cal->f('id'), $cal->f('name'), in_array($cal->f('id'), $event['calendars']));
				$div->add_html_element($checkbox);
				$div->add_html_element(new html_element('br'));

				$select_cals[] = $cal->f('id');
			}
		}


		if ($count > 1) {
			for ($i = 0; $i < count($event['calendars']); $i ++) {
				if (!in_array($event['calendars'][$i], $select_cals)) {
					$input = new input('hidden', 'calendars[]', $event['calendars'][$i]);
					$form->add_html_element($input);
				}
			}
			$tabstrip->add_tab('calendars', $sc_calendars, $div);
		} else {
			for ($i = 0; $i < count($event['calendars']); $i ++) {
				$input = new input('hidden', 'calendars[]', $event['calendars'][$i]);
				$form->add_html_element($input);
			}
			//$form->add_html_element(new input('hidden', 'calendars[]', $calendar['id']));
		}


		//begin participants
		if(isset($_POST['status']) && $_POST['status'] > -1)
		{
			$cal->set_event_status($event_id, $_POST['status'], $_SESSION['GO_SESSION']['email']);

			if($event['user_id'] !=  $GO_SECURITY->user_id)
			{
				if($_POST['status'] == '1')
				{
					$body = sprintf($cal_accept_mail_body,htmlspecialchars($_SESSION['GO_SESSION']['name']),$event['name']);
					$body .= "<br /><br />".$event_link->get_html();
					$subject = sprintf($cal_accept_mail_subject, $event['name']);

					$user = $GO_USERS->get_user($event['user_id']);
					sendmail($user['email'], $_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name'], $subject, $body, '3', 'text/HTML');
				}else
				{
					$body = sprintf($cal_decline_mail_body,htmlspecialchars($_SESSION['GO_SESSION']['name']),$event['name']);
					$body .= "<br /><br />".$event_link->get_html();
					$subject = sprintf($cal_decline_mail_subject, $event['name']);

					$user = $GO_USERS->get_user($event['user_id']);
					sendmail($user['email'], $_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name'], $subject, $body, '3', 'text/HTML');
				}
			}
		}

		if($event_id > 0 && $cal->get_participants($event_id))
		{
			$div = new html_element('div');

			$form->add_html_element(new input('hidden', 'status','-1'));

			$datatable = new datatable('participants_table');
			$datatable->add_column(new table_heading($strName));
			$datatable->add_column(new table_heading($strEmail));
			$datatable->add_column(new table_heading($sc_status));

			while ($cal->next_record()) {

				$row = new table_row();

				if ($cal->f('user_id') > 0) {
					$row->add_cell(new table_cell(show_profile($cal->f('user_id'), '', 'normal', $link_back)));
				} else {
					$row->add_cell(new table_cell(show_profile_by_email(addslashes($cal->f('email')), '', $link_back)));
				}
				$full_email = '"'.$cal->f('name').'" <'.$cal->f('email').'>';
				$row->add_cell(new table_cell(mail_to(empty_to_stripe(addslashes($full_email)),$cal->f('email')), '', $link_back));

				switch ($cal->f('status')) {
					case '0' :
						$row->add_cell(new table_cell($sc_not_responded));
						break;

					case '1' :
						$row->add_cell(new table_cell($sc_accepted));
						break;

					case '2' :
						$row->add_cell(new table_cell($sc_declined));
						break;

				}
				$datatable->add_row($row);
			}


			$div->add_html_element($datatable);

			$status = $cal->get_event_status($event_id, $_SESSION['GO_SESSION']['email']);
			if ($status !== false) {
				switch ($status) {
					case '0';
					$div->add_html_element(new button($sc_accept, "javascript:document.event_form.status.value=1;document.event_form.submit();"));
					$div->add_html_element(new button($sc_decline, "javascript:document.event_form.status.value=2;document.event_form.submit();"));
					break;

					case '1';
					$div->add_html_element(new button($sc_decline, "javascript:document.event_form.status.value=2;document.event_form.submit();"));
					break;

					case '2';
					$div->add_html_element(new button($sc_accept, "javascript:document.event_form.status.value=1;document.event_form.submit();"));
					break;
				}
			}
			$tabstrip->add_tab('participants', $sc_participants, $div);
		}


		//Begin resources
		$cal2 = new calendar();
		$cal3 = new calendar();


		if($group_count = $cal2->get_resource_groups())
		{
			$div = new html_element('div');


			$count=0;
			$select_cals=array();

			$table = new datatable('resource_table');
			$table->add_column(new table_heading($strName));
			$table->add_column(new table_heading($strOwner));
			$table->add_column(new table_heading($sc_status));
			$table->add_column(new table_heading('&nbsp;'));
			$has_resources=false;
			while($cal2->next_record())
			{
				if($cal->get_writable_calendars($GO_SECURITY->user_id, $cal2->f('id')))
				{
					$has_resources=true;
					if($group_count > 1)
					{
						$row = new table_row();
						$cell = new table_cell($cal2->f('name'));
						$cell->set_attribute('colspan','4');
						$cell->set_attribute('class','groupRow');
						$row->add_cell($cell);
						$table->add_row($row);
					}
					while($cal->next_record())
					{
						$form->add_html_element(new input('hidden', 'writable_resources[]', $cal->f('id')));

						if($task == 'save_event' || $task == 'change_event')
						{
							$check = (isset($_POST['resources']) && in_array($cal->f('id'), $_POST['resources']));
							$resource = $cal3->get_event_resource($event_id, $cal->f('id'));
							$fields = $cal3->get_custom_fields($cal->f('group_id'));
						}elseif($event_id > 0 && $resource = $cal3->get_event_resource($event_id, $cal->f('id')))
						{
							if(!empty($resource['custom_fields']))
							{
								$fieldsNode = text_to_xml($resource['custom_fields']);
								$fields = $fieldsNode->children();
							}else
							{
								$fields = false;
							}
							$check = true;
						}else
						{
							$fields = $cal3->get_custom_fields($cal->f('group_id'));
							$check = false;
						}

						$checkbox = new checkbox('cal_'.$cal->f('id'), 'resources[]', $cal->f('id'), $cal->f('name'), $check);
						$row = new table_row();


						$select_recourses[] = $cal->f('id');



						if(count($fields) > 0 && isset($fields[0]->_name))
						{
							$checkbox->set_attribute('onclick','javascript:display_options(this);');
							$options_table = new table();
							$options_table->set_attribute('id','options_'.$cal->f('id'));
							$options_table->set_attribute('class','normalTable');
							if($check)
							{
								$options_table->set_attribute('style','margin-left:20px;display:block;');
							}else
							{
								$options_table->set_attribute('style','margin-left:20px;display:none;');
							}
							foreach(	$fields as $inputNode)
							{
								$options_row = new table_row();
								switch($inputNode->get_attribute('type'))
								{
									case 'text':
										$options_row->add_cell(new table_cell($inputNode->get_attribute('name').':'));
										if($task == 'save_event' || $task == 'change_event')
										{
											$value = isset($_POST['resource_options'][$cal->f('id')][$inputNode->get_attribute('name')]) ?
											smart_stripslashes($_POST['resource_options'][$cal->f('id')][$inputNode->get_attribute('name')]) : '';
										}else
										{
											$value = $inputNode->get_attribute('value');
										}
										$input = new input($inputNode->get_attribute('type'),'resource_options['.$cal->f('id').']['.addslashes($inputNode->get_attribute('name')).']', $value);
										$options_row->add_cell(new table_cell($input->get_html()));
										break;

									case 'checkbox':
										if($task == 'save_event' || $task == 'change_event')
										{
											$value = isset($_POST['resource_options'][$cal->f('id')][$inputNode->get_attribute('name')]);
										}else
										{
											$value = $inputNode->get_attribute('value');
										}
										$options_checkbox = new checkbox($inputNode->get_attribute('name'),
										'resource_options['.$cal->f('id').']['.addslashes($inputNode->get_attribute('name')).']','1',$inputNode->get_attribute('name'),$value);
										$options_cell = new table_cell($options_checkbox->get_html());
										$options_cell->set_attribute('colspan','2');
										$options_row->add_cell($options_cell);
										break;

									case 'textarea':
										$options_row->add_cell(new table_cell($inputNode->get_attribute('name').':'));
										if($task == 'save_event' || $task == 'change_event')
										{
											$value = isset($_POST['resource_options'][$cal->f('id')][$inputNode->get_attribute('name')]) ?
											smart_stripslashes($_POST['resource_options'][$cal->f('id')][$inputNode->get_attribute('name')]) : '';
										}else
										{
											$value = $inputNode->get_attribute('value');
										}
										$input = new textarea('resource_options['.$cal->f('id').']['.addslashes($inputNode->get_attribute('name')).']', $value);
										$input->set_attribute('style', 'width:300px;height:50px;');
										$options_row->add_cell(new table_cell($input->get_html()));
										break;

									case 'date':
										$options_row->add_cell(new table_cell($inputNode->get_attribute('name').':'));
										if($task == 'save_event' || $task == 'change_event')
										{
											$value = isset($_POST['resource_options'][$cal->f('id')][$inputNode->get_attribute('name')]) ?
											$_POST['resource_options'][$cal->f('id')][$inputNode->get_attribute('name')] : '';
										}else
										{
											$value = $inputNode->get_attribute('value');
										}
										$datepicker = new date_picker('resource_options['.$cal->f('id').']['.addslashes($inputNode->get_attribute('name')).']', $_SESSION['GO_SESSION']['date_format'], $value);
										$options_row->add_cell(new table_cell($datepicker->get_html()));
										break;
								}
								$options_table->add_row($options_row);
							}
							$cell = new table_cell($checkbox->get_html());
							$cell->add_html_element($options_table);
						}else
						{
							$cell = new table_cell($checkbox->get_html());
						}
						$row->add_cell($cell);
						$row->add_cell(new table_cell(show_profile($cal->f('user_id'))));

						if(isset($resource) && $resource)
						{
							$status = $cal3->get_status($resource['status_id']);
							$row->add_cell(new table_cell($cal_statuses[$status['name']]));

							$button = new button($cal_open_resource, "document.location='event.php?event_id=".$resource['id']."&return_to=".rawurlencode($link_back)."';");
							$button->set_attribute('style','margin:0px');
							$cell = new table_cell($button->get_html());
							$cell->set_attribute('style','text-align:right;');
							$row->add_cell($cell);
						}else
						{
							$cell = new table_cell('&nbsp;');
							$cell->set_attribute('colspan','2');
							$row->add_cell($cell);
						}
						$table->add_row($row);
					}
				}
			}
			$div->add_html_element($table);
			//var_dump($event['resources']);
			for ($i = 0; $i < count($event['resources']); $i ++) {
				if (!in_array($event['resources'][$i], $select_recourses)) {
					$form->add_html_element(new input('hidden', 'resources[]', $event['resources'][$i]));
				}
			}
			if($has_resources)
			{
				$tabstrip->add_tab('resources', $cal_resources, $div);
			}
		}



		if($event_id > 0)
		{
			$menu = new button_menu();

			if($GO_LINKS->linking_is_active())
			{
				if($GO_LINKS->get_active_link())
				{
					$menu->add_button('link', $strCreateLink, "javascript:document.event_form.task.value='create_link';document.event_form.submit();");
				}
			}else
			{
				$menu->add_button('link', $strCreateLink, "javascript:document.event_form.task.value='activate_linking';document.event_form.submit();");
			}

			$menu->add_button(
			'unlink',
			$cmdUnlink,
			$links_list->get_unlink_handler());

			$menu->add_button(
			'delete_big',
			$cmdDelete,
			$links_list->get_delete_handler());

			$form->add_html_element($menu);


			$tabstrip->add_tab('links', $strLinks, $links_list);

		}
	}


	$form->add_html_element($tabstrip);



	if($event_id  == 0 || $event['write_permission'])
	{
		$form->add_html_element(new button($cmdOk, "javascript:save_event('true', 'false');"));
		$form->add_html_element(new button($cmdSaveNew, "javascript:save_event('false', 'true');"));
		$form->add_html_element(new button($cmdApply, "javascript:save_event('false', 'false');"));
	}
	if ($event_id > 0) {
		$form->add_html_element(new button($cal_export, "document.location='export.php?event_id=$event_id';"));

		if($event['write_permission'] || ($cal->event_is_subscribed($event_id, $calendar_id) && $GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_write'])))
		{
			$form->add_html_element(new button($cmdDelete,
			"document.location='delete_event.php?event_id=$event_id&calendar_id=".
			$calendar['id']."&return_to=".urlencode($link_back)."';"));
		}
		if($is_resource)
		{

			$group_admin=false;
			if($cal->get_resource_group_admins($calendar['group_id']))
			{
				if($cal->is_resource_group_admin($GO_SECURITY->user_id, $calendar['group_id']))
				{
					$group_admin=true;
				}
			}elseif($event['write_permission'])
			{
				$group_admin=true;
			}
			if($group_admin)
			{
				if($event['status_id']!=2)
				{
					$form->add_html_element(new button($sc_accept, "javascript:accept_resource();"));
				}
				$form->add_html_element(new button($sc_decline, "javascript:decline_resource();"));
			}
		}
	}elseif(isset($_REQUEST['create_exception']) && $_REQUEST['create_exception'] == 'true')
	{
		$form->add_html_element(new button($cmdDelete,
		"document.location='delete_event.php?event_id=".$_REQUEST['event_id'].
		"&exception_time=".$_REQUEST['exception_time']."&return_to=".urlencode($link_back)."';"));
	}
	$form->add_html_element(new button($cmdClose, "javascript:document.location='$return_to'"));

}

echo $form->get_html();

//echo get_acl($event['acl_read']);
//echo get_acl($event['acl_write']);
?>
<script type="text/javascript" language="javascript">

<?php
if($task != 'availability')
{
	echo 'toggle_repeat(\''.$event['repeat_type'].'\');';
	if ($event['all_day_event'] == '1') {
		echo 'disable_time();';
	}

	if ($event['repeat_forever'] == '1') {
		echo 'toggle_repeat_end_info();';
	}
}
?>
function accept_resource()
{
	document.event_form.task.value='accept';
	document.event_form.event_status_id.value=2;
	document.event_form.submit();
}

function decline_resource()
{
	document.event_form.task.value='decline';
	document.event_form.submit();
}

function toggle_statuses(type)
{
	if(type=='VEVENT')
	{
		document.getElementById('event_status_id').style.display='block';
		document.getElementById('todo_status_id').style.display='none';
	}else
	{
		document.getElementById('event_status_id').style.display='none';
		document.getElementById('todo_status_id').style.display='block';
	}
}
function display_options(cb)
{
	if(cb.checked)
	{
		document.getElementById('options_'+cb.value).style.display="block";
	}else
	{
		document.getElementById('options_'+cb.value).style.display="none";
	}
}

function check_availability()
{
	document.event_form.task.value='availability';
	document.event_form.submit();
}
function update_end_hour()
{
	var start_hour = parseInt(document.event_form.start_hour.value);
	var end_hour = parseInt(document.event_form.end_hour.value);
	if (start_hour == 23)
	{
		document.event_form.end_hour.value='23';
		document.event_form.end_min.value='30';
	}else
	{
		if (start_hour >= end_hour)
		{
			end_hour = start_hour+1;
			document.event_form.end_hour.value=end_hour;
		}
	}
}


function check_date(changed_field)
{
	start_date = get_date(document.event_form.start_date.value, '<?php echo $_SESSION['GO_SESSION']['date_format']; ?>', '<?php echo $_SESSION['GO_SESSION']['date_seperator']; ?>');
	end_date = get_date(document.event_form.end_date.value, '<?php echo $_SESSION['GO_SESSION']['date_format']; ?>', '<?php echo $_SESSION['GO_SESSION']['date_seperator']; ?>');

	if(end_date < start_date)
	{
		if(changed_field == 'start_date')
		{
			document.event_form.end_date.value = document.event_form.start_date.value;
		}else
		{
			document.event_form.start_date.value = document.event_form.end_date.value;
		}
	}
}




function save_event(close, clear_form)
{
	start_date = get_date(document.event_form.start_date.value+' '+document.event_form.start_hour.value+':'+document.event_form.start_min.value, '<?php echo $_SESSION['GO_SESSION']['date_format']; ?>', '<?php echo $_SESSION['GO_SESSION']['date_seperator']; ?>');
	end_date = get_date(document.event_form.end_date.value+' '+document.event_form.end_hour.value+':'+document.event_form.end_min.value, '<?php echo $_SESSION['GO_SESSION']['date_format']; ?>', '<?php echo $_SESSION['GO_SESSION']['date_seperator']; ?>');
	repeat_end_date = get_date(document.event_form.repeat_end_date.value, '<?php echo $_SESSION['GO_SESSION']['date_format']; ?>', '<?php echo $_SESSION['GO_SESSION']['date_seperator']; ?>');

	if (start_date > end_date)
	{
		alert("<?php echo $sc_start_later; ?>");
		return;
	}
	if (document.event_form.repeat_type.value != '0')
	{
		if ((start_date >= repeat_end_date) && document.event_form.repeat_forever.checked == false)
		{
			alert("<?php echo $sc_cycle_start_later; ?>");
			return;
		}
	}

	if (document.event_form.repeat_type.value == '1' && (document.event_form.reminder_value.value*document.event_form.reminder_multiplier.value)> 43200)
	{
		alert("<?php echo $sc_reminder_set_to_early; ?>");
		return;
	}

	if (document.event_form.repeat_type.value == '2' && (document.event_form.reminder_value.value*document.event_form.reminder_multiplier.value) > 518400)
	{
		alert("<?php echo $sc_reminder_set_to_early; ?>");
		return;
	}

	if (document.event_form.repeat_type.value == '2' || document.event_form.repeat_type.value == '4')
	{
		if (document.event_form.repeat_days_0.checked == false && document.event_form.repeat_days_1.checked == false && document.event_form.repeat_days_2.checked == false && document.event_form.repeat_days_3.checked == false && document.event_form.repeat_days_4.checked == false && document.event_form.repeat_days_5.checked == false && document.event_form.repeat_days_6.checked == false)
		{
			alert("<?php echo $sc_never_happens; ?>");
			return;
		}
	}
	document.event_form.task.value = 'save_event';
	document.event_form.close.value = close;
	document.event_form.new_event.value = clear_form;

	document.event_form.submit();

}

function remove_client()
{
	document.event_form.contact_id.value = 0;
	document.event_form.contact_name.value = '';
	document.event_form.contact_name_text.value = '';
}

function toggle_repeat_end_info()
{
	document.event_form.repeat_end_date.disabled=document.event_form.repeat_forever.checked;
}

function disable_time()
{
	if (document.event_form.start_hour.disabled==false)
	{
		document.event_form.start_hour.disabled=true;
		document.event_form.start_min.disabled=true;
		document.event_form.end_hour.disabled=true;
		document.event_form.end_min.disabled=true;
	}else
	{
		document.event_form.start_hour.disabled=false;
		document.event_form.start_min.disabled=false;
		document.event_form.end_hour.disabled=false;
		document.event_form.end_min.disabled=false;
	}
}

function toggle_repeat(repeat)
{

	document.event_form.repeat_type.value = repeat;
	switch(repeat)
	{
		case '0':
		disable_days(true);
		document.event_form.month_time.disabled = true;
		disable_repeat_end_date(true);
		document.event_form.repeat_every.disabled = true;
		break;

		case '1':
		disable_days(true);
		document.event_form.month_time.disabled = true;
		disable_repeat_end_date(false);
		document.event_form.repeat_every.disabled = false;
		break;

		case '2':
		disable_days(false);
		document.event_form.month_time.disabled = true;
		disable_repeat_end_date(false);
		document.event_form.repeat_every.disabled = false;
		break;

		case '3':
		document.event_form.month_time.disabled = true;
		disable_days(true);
		disable_repeat_end_date(false);
		document.event_form.repeat_every.disabled = false;
		break;

		case '4':
		disable_days(false);
		document.event_form.month_time.disabled = false;
		disable_repeat_end_date(false);
		document.event_form.repeat_every.disabled = false;
		break;

		case '5':
		disable_days(true);
		document.event_form.month_time.disabled = true;
		disable_repeat_end_date(false);
		document.event_form.repeat_every.disabled = false;
		break;
	}
}

function disable_days(disable)
{
	document.event_form.repeat_days_0.disabled=disable;
	document.event_form.repeat_days_1.disabled=disable;
	document.event_form.repeat_days_2.disabled=disable;
	document.event_form.repeat_days_3.disabled=disable;
	document.event_form.repeat_days_4.disabled=disable;
	document.event_form.repeat_days_5.disabled=disable;
	document.event_form.repeat_days_6.disabled=disable;

}

function disable_repeat_end_date(disable)
{
	document.event_form.repeat_forever.disabled=disable;
	if (disable == true || (disable==false && document.event_form.repeat_forever.checked == false))
	{
		document.event_form.repeat_end_date.disabled=disable;
	}
}
function show_recur(bool)
{
	var button = get_object('button_recur');
	var section = get_object('section_recur');
	button.style.display='inline';
	section.style.display='none';
	if (bool)
	{
		button.style.display='none';
		section.style.display='inline';
	}
}

function show_option(bool)
{
	var button = get_object('button_option');
	var section = get_object('section_option');
	button.style.display='inline';
	section.style.display='none';
	if (bool)
	{
		button.style.display='none';
		section.style.display='inline';
	}
}

function disable_completion_time(value)
{
	if(value == '11')
	{
		var disabled = false;
	}else
	{
		var disabled = true;
	}
	document.event_form.completion_date.disabled=disabled;
	document.event_form.completion_date_button.disabled=disabled;
	document.event_form.completion_hour.disabled=disabled;
	document.event_form.completion_min.disabled=disabled;
}

function create_link()
{
	document.event_form.task.value='create_link';
	document.event_form.submit();
}
function activate_linking(goto_url)
{
	document.event_form.goto_url.value=goto_url;
	document.event_form.task.value='activate_linking';
	document.event_form.submit();
}


</script>
<?php
require_once ($GO_THEME->theme_path.'footer.inc');
