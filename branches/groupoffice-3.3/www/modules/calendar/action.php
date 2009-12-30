<?php
/** 
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('calendar');

require_once ($GO_MODULES->modules['calendar']['class_path']."calendar.class.inc.php");
require_once ($GO_MODULES->modules['calendar']['class_path']."go_ical.class.inc");
require_once ($GO_LANGUAGE->get_language_file('calendar'));
$cal = new calendar();

function get_posted_event() {
	$gmt_tz = new DateTimeZone('GMT');

	$event['id']=$_POST['event_id'];
	$event['calendar_id']=$_POST['calendar_id'];

	$event['private']=isset($_POST['private']) ? '1' : '0';
	$event['name'] = (trim($_POST['subject']));
	$event['description'] = (trim($_POST['description']));
	$event['location'] = (trim($_POST['location']));
	$event['status'] = ($_POST['status']);
	$event['background'] = ($_POST['background']);
	$event['busy']=isset($_POST['busy']) ? '1' : '0';
	$event['reminder'] = isset($_POST['reminder_multiplier']) ? $_POST['reminder_multiplier'] * $_POST['reminder_value'] : 0;
	//$event['background'] = $_POST['background'];

	$timezone_offset = Date::get_timezone_offset(Date::to_unixtime($_POST['start_date']));

	if (isset ($_POST['all_day_event'])) {
		$event['all_day_event'] = '1';
		$start_hour = 0 ;
		$start_min = '0';
		$end_hour = 23;
		$end_min = 59;
	} else {
		$event['all_day_event'] = '0';
		$start_min = $_POST['start_min'];
		$start_hour = $_POST['start_hour'];
		$end_hour = $_POST['end_hour'];
		$end_min = $_POST['end_min'];
	}

	$start_date = new DateTime(Date::to_input_format($_POST['start_date'].' '.$start_hour.':'.$start_min));
	$start_date->setTimezone($gmt_tz);
	$event['start_time'] = $start_date->format('U');

	$end_date = new DateTime(Date::to_input_format($_POST['end_date'].' '.$end_hour.':'.$end_min));
	$start_date->setTimezone($gmt_tz);
	$event['end_time'] = $end_date->format('U');

	$repeat_every = isset ($_POST['repeat_every']) ? $_POST['repeat_every'] : '1';
	$event['repeat_end_time'] = (isset ($_POST['repeat_forever']) || !isset($_POST['repeat_end_date'])) ? '0' : Date::to_unixtime($_POST['repeat_end_date'].' '.$end_hour.':'.$end_min);


	$month_time = isset ($_POST['month_time']) ? $_POST['month_time'] : '0';


	$days['mon'] = isset ($_POST['repeat_days_1']) ? '1' : '0';
	$days['tue'] = isset ($_POST['repeat_days_2']) ? '1' : '0';
	$days['wed'] = isset ($_POST['repeat_days_3']) ? '1' : '0';
	$days['thu'] = isset ($_POST['repeat_days_4']) ? '1' : '0';
	$days['fri'] = isset ($_POST['repeat_days_5']) ? '1' : '0';
	$days['sat'] = isset ($_POST['repeat_days_6']) ? '1' : '0';
	$days['sun'] = isset ($_POST['repeat_days_0']) ? '1' : '0';

	$days = Date::shift_days_to_gmt($days, date('G', $event['start_time']), Date::get_timezone_offset($event['start_time']));

	//debug(var_export($days, true));
	if($_POST['repeat_type']>0) {
		$event['rrule']=Date::build_rrule($_POST['repeat_type'], $repeat_every,$event['repeat_end_time'], $days, $month_time);
	}else {
		$event['rrule']='';
	}

	return $event;
}

function round_quarters($time) {
	$date = getdate($time);

	$mins = ceil($date['minutes']/15)*15;
	$time = mktime($date['hours'], $mins, 0, $date['mon'], $date['mday'], $date['year']);

	return $time;
}


//we are unsuccessfull by default
$response =array('success'=>false);

try {

	switch($_REQUEST['task']) {

		case 'import':

			ini_set('max_execution_time', 180);

			if (!file_exists($_FILES['ical_file']['tmp_name'][0])) {
				throw new Exception($lang['common']['noFileUploaded']);
			}else {
				$tmpfile = $GO_CONFIG->tmpdir.uniqid(time());
				move_uploaded_file($_FILES['ical_file']['tmp_name'][0], $tmpfile);
				File::convert_to_utf8($tmpfile);

				if($count = $cal->import_ical_file($tmpfile, $_POST['calendar_id'])) {
					$response['feedback'] = sprintf($lang['calendar']['import_success'], $count);
					$response['success']=true;
				}else {
					throw new Exception($lang['common']['saveError']);
				}
				unlink($tmpfile);
			}
			break;
		case 'delete_event':

			$event_id=$_POST['event_id'];

			$event = $cal->get_event($event_id);

			if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $event['acl_id'])<GO_SECURITY::WRITE_PERMISSION) {
				throw new AccessDeniedException();
			}			

			if(isset($_POST['create_exception']) && $_POST['create_exception'] =='true') {
				$exceptionDate = strtotime(($_POST['exception_date']));

				//an instance of a recurring event was modified. We must create an exception for the
				//recurring event.
				$exception['event_id'] = $event_id;

				$event_start_time = $event['start_time'];
				$exception['time'] = mktime(date('G', $event_start_time),date('i', $event_start_time), 0, date('n', $exceptionDate), date('j', $exceptionDate), date('Y', $exceptionDate));

				$cal->add_exception($exception);
			}else {
				$cal->delete_event($event_id);
			}

			$response['success']=true;


			break;

		case 'update_grid_event':

			if(isset($_POST['update_event_id'])) {
				$update_event_id=$_POST['update_event_id'];
				$old_event = $cal->get_event($update_event_id);
				$calendar = $cal->get_calendar($old_event['calendar_id']);

				//an event is moved or resized
				if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_event['acl_id'])<GO_SECURITY::WRITE_PERMISSION) {
					throw new AccessDeniedException();
				}

				if(isset($_POST['createException']) && $_POST['createException'] =='true') {

					$exceptionDate = strtotime(($_POST['exceptionDate']));

					//an instance of a recurring event was modified. We must create an exception for the
					//recurring event.
					$exception['event_id'] = $update_event_id;

					$event_start_time = $old_event['start_time'];
					$exception['time'] = mktime(date('G', $event_start_time),date('i', $event_start_time), 0, date('n', $exceptionDate), date('j', $exceptionDate), date('Y', $exceptionDate));

					//die(date('Ymd : G:i', $exception['time']));

					$cal->add_exception($exception);

					//now we copy the recurring event to a new single event with the new time
					$update_event['rrule']='';
					$update_event['start_time']=$exception['time'];
					$update_event['end_time']=$exception['time']+$old_event['end_time']-$old_event['start_time'];

					if(isset($_POST['offset'])) {
					//move an event
						$offset = ($_POST['offset']);


						$update_event['start_time']=round_quarters($update_event['start_time']+$offset);
						$update_event['end_time']=$update_event['end_time']+$offset;

					}


					if(isset($_POST['offsetDays'])) {
					//move an event
						$offsetDays = ($_POST['offsetDays']);
						$update_event['start_time'] = Date::date_add($update_event['start_time'], $offsetDays);
						$update_event['end_time'] = Date::date_add($update_event['end_time'], $offsetDays);

					}

					if(isset($_POST['duration'])) {
					//change duration
						$duration = ($_POST['duration']);
						$update_event['end_time']=round_quarters($update_event['start_time']+$duration);
					}

					if(isset($_POST['update_calendar_id'])) {
						$update_event['calendar_id']=$_POST['update_calendar_id'];
					}


					$response['new_event_id'] = $cal->copy_event($exception['event_id'], $update_event);

					//for sync update the timestamp
					$update_recurring_event=array();
					$update_recurring_event['id']=$exception['event_id'];
					$update_recurring_event['mtime']=time();
					$cal->update_row('cal_events', 'id', $update_recurring_event);

				}else {
					if(isset($_POST['offset'])) {
					//move an event
						$offset = ($_POST['offset']);


						$update_event['start_time']=round_quarters($old_event['start_time']+$offset);
						$update_event['end_time']=$old_event['end_time']+$offset;
					}

					if(isset($_POST['offsetDays'])) {
					//move an event
						$offsetDays = ($_POST['offsetDays']);
						$update_event['start_time'] = Date::date_add($old_event['start_time'], $offsetDays);
						$update_event['end_time'] = Date::date_add($old_event['end_time'], $offsetDays);
					}

					if(isset($_POST['duration'])) {
					//change duration
						$duration = ($_POST['duration']);

						$update_event['start_time']=$old_event['start_time'];
						$update_event['end_time']=round_quarters($old_event['start_time']+$duration);
					}

					if(isset($_POST['update_calendar_id'])) {
						$update_event['calendar_id']=$_POST['update_calendar_id'];

					}

					if(isset($update_event))
					{
						$update_event['id']=$update_event_id;
						$cal->update_event($update_event, $calendar, $old_event);
					}
/*
					//move the exceptions if a recurrent event is moved
					if(!empty($old_event['rrule']) && isset($offset))
					{
						$cal->move_exceptions(($_POST['update_event_id']), $offset);
					}*/
				}
				$response['success']=true;
			}



			break;




		case 'accept':

			$event_id = ($_REQUEST['event_id']);
			$calendar_id = isset($_REQUEST['calendar_id']) ? $_REQUEST['calendar_id'] : 0;

			$event_exists= $cal->has_participants_event($event_id, $calendar_id);


			if(!$cal->is_participant($event_id, $_SESSION['GO_SESSION']['email'])) {
				throw new Exception($lang['calendar']['not_invited']);
			}


			$event = $cal->get_event($event_id);

			if(!$event_exists && !empty($calendar_id) && $event['calendar_id']!=$calendar_id) {
				$new_event['user_id']=$GO_SECURITY->user_id;
				$new_event['calendar_id']=$calendar_id;
				$new_event['participants_event_id']=$event_id;

				$cal->copy_event($event_id, $new_event);
			}

			$cal->set_event_status($event_id, '1', $_SESSION['GO_SESSION']['email']);

			$owner = $GO_USERS->get_user($event['user_id']);

			require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
			$swift = new GoSwift($owner['email'], sprintf($lang['calendar']['accept_mail_subject'],$event['name']));

			$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);

			$body = sprintf($lang['calendar']['accept_mail_body'],$_SESSION['GO_SESSION']['email']);
			$body .= '<br /><br />'.$cal->event_to_html($event);

			$swift->set_body($body);
			$swift->sendmail();
			

			$response['success']=true;

			break;

		case 'save_event':
			
			$event = get_posted_event();
			$event_id=$event['id'];
			$group_id = isset($_POST['group_id']) ? $_POST['group_id'] : 0;
			$calendar_id = $event['calendar_id'];

			$date_format = $_SESSION['GO_SESSION']['date_format'];

			if(isset($_POST['resources'])) {
				foreach($_POST['resources'] as $key => $value) {
					if($value == 'on') {
						$resources[] = $key;
					}
				}
			}

			if(empty($event['calendar_id'])) {
				throw new Exception($lang['calendar']['exceptionNoCalendarID']);
			}

			$calendar = $cal->get_calendar($event['calendar_id']);

			if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_id'])<GO_SECURITY::WRITE_PERMISSION) {
				throw new AccessDeniedException();
			}

			if(empty($event['name']) || empty($event['start_time']) || empty($event['end_time'])) {
				throw new Exception($lang['common']['missingField']);
			}

			//throw new Exception(date('Ymd G:i', $cal->get_next_recurrence_time(0,$event['start_time'], $event)));
			if(!empty($event['rrule']) && Date::get_next_recurrence_time($event['start_time'],$event['start_time'], $event['rrule']) < $event['end_time']) {
			//Event will cumulate
				throw new Exception($lang['calendar']['cumulative']);
			}

			$insert = false;
			$modified = false;
			$accepted = false;
			$declined = false;
			
			if($event['id'] > 0)
			{
				$old_event = $cal->get_event($event_id);
				$update_related = (isset($_POST['resources'])) ? false : true;

				if(($old_event['status'] != 'ACCEPTED') && ($event['status'] == 'ACCEPTED'))
					$accepted = true;
				if(($old_event['status'] != 'DECLINED') && ($event['status'] == 'DECLINED'))
					$declined = true;
				if($old_event['start_time'] != $event['start_time'] || $old_event['end_time'] != $event['end_time'])
					$modified = true;

				$cal->update_event($event, $calendar, $old_event, $update_related, false);

				$response['files_folder_id']=$event['files_folder_id'];
				$response['success']=true;
			}else
			{
				$event_id= $cal->add_event($event, $calendar);
				$old_event = $cal->get_event($event_id);
				$insert = true;
				if($event_id) {

					$response['files_folder_id']=$event['files_folder_id'];
					/*$calendar_user = $GO_USERS->get_user($calendar['user_id']);
					
					if($calendar_user)
					{
						$participant['user_id']=$calendar_user['id'];
						$participant['event_id']=$event_id;
						$participant['name']=String::format_name($calendar_user);
						$participant['email']=$calendar_user['email'];
						$participant['status']=1;
						
						$cal->add_participant($participant);
					}*/

					if(!empty($_POST['link'])) {
						$link_props = explode(':', $_POST['link']);
						$GO_LINKS->add_link(
								($link_props[1]),
								($link_props[0]),
								$event_id,
								1);
					}

					if(isset($_REQUEST['exception_event_id']) && $_REQUEST['exception_event_id'] > 0) {
						$exception['event_id'] = ($_REQUEST['exception_event_id']);
						$exception['time'] = strtotime(($_POST['exceptionDate']));
						$cal->add_exception($exception);

						//for sync update the timestamp
						$update_recurring_event=array();
						$update_recurring_event['id']=$_REQUEST['exception_event_id'];
						$update_recurring_event['mtime']=time();
						$cal->update_row('cal_events', 'id', $update_recurring_event);
					}

					$response['event_id']=$event_id;
					$response['success']=true;
				}
			}

			if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission']) {
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();

				if(!$insert && $calendar['group_id'] > 1) {
					$values_old = array_values($cf->get_values($GO_SECURITY->user_id, 1, $event_id));
				}

				$cf->update_fields($GO_SECURITY->user_id, $event_id, 1, $_POST, $insert);								

				if(!$insert && $calendar['group_id'] > 1) {
					$values = array_values($cf->get_values($GO_SECURITY->user_id, 1, $event_id));
					for($i=0; $i<count($values_old); $i++) {
						if($values_old[$i] != $values[$i]) {
							$modified = true;
						}
					}
				}
			}

			if(!empty($_POST['tmp_files']) && $GO_MODULES->has_module('files')) {
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
				$files = new files();
				$fs = new filesystem();

				//event = $cal->get_event($event_id);
				$path = $files->build_path($event['files_folder_id']);

				$tmp_files = json_decode($_POST['tmp_files'], true);
				while($tmp_file = array_shift($tmp_files)) {
					$new_path = $GO_CONFIG->file_storage_path.$path.'/'.$tmp_file['name'];
					$fs->move($tmp_file['tmp_file'], $new_path);
					$files->import_file($new_path, $event['files_folder_id']);
				}
			}

			/*
			 * When the user adds events to participants calendar's directly it might be
			 * that the user is unauthorized. We report those at the end of this switch case.
			 */
			$unauthorized_participants=array();
			
			if(!empty($_POST['participants'])) {

				$ids=array();
				$participants = json_decode($_POST['participants'], true);
				foreach($participants as $p) {
					$participant['event_id']=$event_id;
					$participant['name']=$p['name'];
					$participant['email']=$p['email'];
					$participant['user_id']=(isset($p['user_id'])) ? $p['user_id'] : 0;
					$participant['status']=$p['status'] ;

					if(substr($p['id'], 0,4)=='new_') {
						if(isset($_POST['import']) && $participant['user_id'] > 0) {
							$calendar = $cal->get_default_import_calendar($participant['user_id']);

							if($calendar_id != $calendar['id']) {
								
								if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_id'])>=GO_SECURITY::WRITE_PERMISSION) {
									$response['cal'] = $calendar;

									$event['calendar_id'] = $calendar['id'];	
									if(!isset($event['participants_event_id'])) {
										$event['participants_event_id'] = $event_id;
									}
									unset($event['files_folder_id']);
									$cal->add_event($event, $calendar);
									$participant['status']=1;
								}else
								{
									$unauthorized_participants[] = $participant['name'];
								}
							}
						}
						$ids[]=$cal->add_participant($participant);
					}else {
						$ids[]=$p['id'];

						if(isset($_POST['import']) && $participant['user_id'] > 0) {
							$calendar = $cal->get_default_import_calendar($participant['user_id']);

							if($calendar_id != $calendar['id']) {
								if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_id'])>=GO_SECURITY::WRITE_PERMISSION) {
									if(!$cal->has_participants_event($event_id, $calendar['id'])){
										$event['calendar_id'] = $calendar['id'];
										if(!isset($event['participants_event_id'])) {
											$event['participants_event_id'] = $event_id;
										}
										unset($event['files_folder_id']);
										$cal->add_event($event, $calendar);										
									}
									$cal->set_event_status($event_id, 1, $participant['email']);
								}else
								{
									$unauthorized_participants[] = $participant['name'];
								}
							}
						}
					}
				}
				$response['event_id'] = $event_id;
				$response['id'] = $ids;
				$cal->delete_other_participants($event_id, $ids);
			}elseif(isset($response['event_id'])) {
				$calendar_user = $GO_USERS->get_user($calendar['user_id']);

				if($calendar_user) {
					$participant['user_id']=$calendar_user['id'];
					$participant['event_id']=$event_id;
					$participant['name']=String::format_name($calendar_user);
					$participant['email']=$calendar_user['email'];
					$participant['status']=1;

					$cal->add_participant($participant);
				}
			}


			if(!empty($_POST['invitation'])) {
				require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
				require_once $GO_CONFIG->class_path.'mail/swift/lib/classes/Swift/Plugins/DecoratorPlugin.php';
				require_once $GO_CONFIG->class_path.'mail/swift/lib/classes/Swift/Plugins/Decorator/Replacements.php';

				$RFC822 = new RFC822();
				$participants_event_id=empty($old_event['participants_event_id']) ? $event_id : $old_event['participants_event_id'];
				//debug($participants_event_id);
				$cal->clear_event_status($participants_event_id, $_SESSION['GO_SESSION']['email']);
			
				$participants=array();
				$cal->get_participants($participants_event_id);
				while($cal->next_record()) {
	
					if(/*$cal->f('status') !=1 && */$cal->f('email')!=$_SESSION['GO_SESSION']['email']) {
						$participants[] = $RFC822->write_address($cal->f('name'), $cal->f('email'));
					}
				}

				//debug($participants);
				if(count($participants)) {				
					$swift = new GoSwift(
							implode(',', $participants),
							$lang['calendar']['appointment'].$event['name']);


					class Replacements implements Swift_Plugins_Decorator_Replacements {
						function getReplacementsFor($address) {
							return array('%email%'=>$address);
						}
					}
					//Load the plugin with the extended replacements class
					$swift->registerPlugin(new Swift_Plugins_DecoratorPlugin(new Replacements()));

					$swift->set_body('<p>'.$lang['calendar']['invited'].'</p>'.
							$cal->event_to_html($event).
							'<p>'.$lang['calendar']['acccept_question'].'</p>'.
							'<a href="'.$GO_MODULES->modules['calendar']['full_url'].'invitation.php?event_id='.$event_id.'&task=accept&email=%email%">'.$lang['calendar']['accept'].'</a>'.
							'&nbsp;|&nbsp;'.
							'<a href="'.$GO_MODULES->modules['calendar']['full_url'].'invitation.php?event_id='.$event_id.'&task=decline&email=%email%">'.$lang['calendar']['decline'].'</a>');

					//create ics attachment
					require_once ($GO_MODULES->modules['calendar']['class_path'].'go_ical.class.inc');
					$ical = new go_ical();
					$ics_string = $ical->export_event($participants_event_id);

					$name = File::strip_invalid_chars($event['name']).'.ics';

					/*$dir=$GO_CONFIG->tmpdir.'attachments/';
					filesystem::mkdir_recursive($dir);
		
					$tmp_file = $dir.$name;
		
					$fp = fopen($tmp_file,"wb");
					fwrite ($fp,$ics_string);
					fclose($fp);
					
					$file =& new Swift_File($tmp_file);
					$attachment =& new Swift_Message_Attachment($file,utf8_basename($tmp_file), File::get_mime($tmp_file));*/

					$swift->message->attach(Swift_Attachment::newInstance($ics_string, $name,File::get_mime($name)));

					$swift->set_from($_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name']);
					
					if(!$swift->sendmail(true)) {
						throw new Exception('Could not send invitation');
					}
				}
			}

			if($calendar['group_id'] > 1)
			{			
				$group = $cal->get_group($calendar['group_id']);

				$num_admins = $cal->get_group_admins($calendar['group_id']);
				if($num_admins && ($insert || $modified || $accepted || $declined))
				{					
					$url = $GO_CONFIG->full_url.'dialog.php?module=calendar&function=showEvent&params='.json_encode(array('event_id' => $event_id));

					if(!$insert)
					{
						$body = sprintf($lang['calendar']['resource_modified_mail_body'],$_SESSION['GO_SESSION']['name'],$calendar['name']).'<br /><br />'
							. $cal->event_to_html($event, true)
							. '<br /><a href='.$url.'>'.$lang['calendar']['open_resource'].'</a>';
						$subject = sprintf($lang['calendar']['resource_modified_mail_subject'],$calendar['name'], $event['name'], date($date_format, $event['start_time']));
					}else
					{
						$body = sprintf($lang['calendar']['resource_mail_body'],$_SESSION['GO_SESSION']['name'],$calendar['name']).'<br /><br />'
							. $cal->event_to_html($event, true)
							. '<br /><a href='.$url.'>'.$lang['calendar']['open_resource'].'</a>';
						$subject = sprintf($lang['calendar']['resource_mail_subject'],$calendar['name'], $event['name'], date($date_format, $event['start_time']));						
					}

					while($cal->next_record())
					{
						$user_id = $cal->f('user_id');
						if($user_id != $GO_SECURITY->user_id)
						{
							$user = $GO_USERS->get_user($user_id);
							
							$send_mail['to'] = $user['email'];
							$send_mail['subject'] = $subject;
							$send_mail['body'] = $body;
							$send_mail['event'] = $old_event;
							$send_mail['group'] = $group;

							$send_mails[] = $send_mail;
						}	
					}					
				}
				
				if($old_event['user_id'] != $GO_SECURITY->user_id)
				{
					$send_mail = false;

					if($accepted)
					{
						$body = sprintf($lang['calendar']['your_resource_accepted_mail_body'],$_SESSION['GO_SESSION']['name'],$calendar['name']).'<br /><br />';
						$body .= $cal->event_to_html($event, true);

						$send_mail['subject'] = sprintf($lang['calendar']['your_resource_accepted_mail_subject'],$calendar['name'], date($date_format, $event['start_time']));
						$send_mail['body'] = $body;
					}else
					if($declined)
					{
						$body = sprintf($lang['calendar']['your_resource_declined_mail_body'],$_SESSION['GO_SESSION']['name'],$calendar['name']).'<br /><br />';
						$body .= $cal->event_to_html($event, true);

						$send_mail['subject'] = sprintf($lang['calendar']['your_resource_declined_mail_subject'],$calendar['name'], date($date_format, $event['start_time']));
						$send_mail['body'] = $body;
					}else
					if($modified)
					{
						$body = sprintf($lang['calendar']['your_resource_modified_mail_body'],$_SESSION['GO_SESSION']['name'],$calendar['name']).'<br /><br />';
						$body .= $cal->event_to_html($event, true);

						$send_mail['subject'] = sprintf($lang['calendar']['your_resource_modified_mail_subject'],$calendar['name'], date($date_format, $event['start_time']),$lang['calendar']['statuses'][$event['status']]);
						$send_mail['body'] = $body;
					}

					if($send_mail)
					{
						$user = $GO_USERS->get_user($old_event['user_id']);
						
						$send_mail['to'] = $user['email'];
						$send_mail['event'] = $old_event;
						$send_mail['group'] = $group;

						$send_mails[] = $send_mail;
					}
				}
			}
			else {
			//copy event properties
				$event_copy = $event;
				unset($event_copy['id'], $event_copy['reminder'], $event_copy['files_folder_id'], $event_copy['calendar_id']);

				$event_copy['busy'] = '0';
				$event_copy['user_id'] = (isset($event_copy['user_id']) && $event_copy['user_id'] > 0) ? $event_copy['user_id'] : $GO_SECURITY->user_id;

				$cal2 = new calendar();
				$cal3 = new calendar();

				$num_resources = $cal->get_authorized_calendars($GO_SECURITY->user_id, 0, 0, 1);
				if($num_resources > 0)
				{					
					while($resource_calendar = $cal->next_record())
					{
						$resource_id = $resource_calendar['id'];
						
						$existing_resource = $cal2->get_event_resource($event_id, $resource_id);					
						if(isset($resources) && in_array($resource_id, $resources))
						{
							
							$resource = $event_copy;
							$resource['participants_event_id'] = $event_id;
							$resource['calendar_id'] = $resource_id;							
							
							if($existing_resource)
							{
								$resource['id'] = $resource_id = $existing_resource['id'];
								$modified_resource = false;
								
								if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission']) 
								{
									require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
									$cf = new customfields();

									$values_old = array_values($cf->get_values($GO_SECURITY->user_id, 1, $resource_id));

									$custom_fields=isset($_POST['resource_options'][$resource_calendar['id']]) ? $_POST['resource_options'][$resource_calendar['id']] : array();
									$cf->update_fields($GO_SECURITY->user_id, $resource_id, 1, $custom_fields, false);
							
									$values = array_values($cf->get_values($GO_SECURITY->user_id, 1, $resource_id));
									for($i=0; $i<count($values_old); $i++) {
										if($values_old[$i] != $values[$i]) {
											$modified_resource = true;
										}
									}							
								}
							
								if($modified || $modified_resource)
								{
									$group = $cal2->get_group($resource_calendar['group_id']);
									$resource['status']='NEEDS-ACTION';
									$resource['background']='FF6666';

									$num_admins = $cal2->get_group_admins($resource_calendar['group_id']);
									if($num_admins)
									{																				
										$url = $GO_CONFIG->full_url.'dialog.php?module=calendar&function=showEvent&params='.json_encode(array('event_id' => $resource['id']));

										$body = sprintf($lang['calendar']['resource_modified_mail_body'],$_SESSION['GO_SESSION']['name'],$resource_calendar['name']).'<br /><br />'
											. $cal3->event_to_html($resource, true)
											. '<br /><a href='.$url.'>'.$lang['calendar']['open_resource'].'</a>';
										$subject = sprintf($lang['calendar']['resource_modified_mail_subject'],$resource_calendar['name'], $event_copy['name'], date($date_format, $event['start_time']));
										
										while($cal2->next_record())
										{
											$user_id = $cal2->f('user_id');
											if($user_id != $GO_SECURITY->user_id)
											{
												$user = $GO_USERS->get_user($user_id);

												$send_mail['to'] = $user['email'];
												$send_mail['subject'] = $subject;
												$send_mail['body'] = $body;
												$send_mail['event'] = $resource;
												$send_mail['group'] = $group;

												$send_mails[] = $send_mail;
											}
											
											if($user_id == $resource['user_id'])
											{
												$resource['status']='ACCEPTED';
												$resource['background']='CCFFCC';
											}																						
										}										
									}
									
									$cal3->update_event($resource, false, false, true, false);
								}
							}else
							{								
								$group = $cal2->get_group($resource_calendar['group_id']);
								
								if($cal2->group_admin_exists($resource_calendar['group_id'], $resource['user_id']))
								{
									$resource['status']='ACCEPTED';
								}else
								{
									$resource['status']='NEEDS-ACTION';
								}
								$resource['background']=$resource['status']=='ACCEPTED' ? 'CCFFCC' : 'FF6666';

								$resource_id = $resource['id'] = $cal3->add_event($resource);

								if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
								{
									require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
									$cf = new customfields();

									$custom_fields=isset($_POST['resource_options'][$resource_calendar['id']]) ? $_POST['resource_options'][$resource_calendar['id']] : array();

									$cf->update_fields($GO_SECURITY->user_id, $resource_id, 1, $custom_fields, true);
								}
															
								$num_admins = $cal2->get_group_admins($resource_calendar['group_id']);
								if($num_admins)
								{
									$url = $GO_CONFIG->full_url.'dialog.php?module=calendar&function=showEvent&params='.json_encode(array('event_id' => $resource_id));
									
									$body = sprintf($lang['calendar']['resource_mail_body'],$_SESSION['GO_SESSION']['name'],$resource_calendar['name']).'<br /><br />'
										. $cal3->event_to_html($resource, true)
										. '<br /><a href='.$url.'>'.$lang['calendar']['open_resource'].'</a>';
									$subject = sprintf($lang['calendar']['resource_mail_subject'],$resource_calendar['name'], $event_copy['name'], date($date_format, $event_copy['start_time']));

									while($cal2->next_record())
									{
										$user_id = $cal2->f('user_id');
										if($user_id != $GO_SECURITY->user_id)
										{
											$user = $GO_USERS->get_user($user_id);

											$send_mail['to'] = $user['email'];
											$send_mail['subject'] = $subject;
											$send_mail['body'] = $body;
											$send_mail['event'] = $resource;
											$send_mail['group'] = $group;

											$send_mails[] = $send_mail;
										}
									}
								}
							}							
						}elseif($existing_resource)
						{
							$cal3->delete_event($existing_resource['id']);
						}
					}
				}
			}

			if(isset($send_mails) && count($send_mails) > 0)
			{
				for($i=0; $i<count($send_mails); $i++)
				{
					require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
					$swift = new GoSwift($send_mails[$i]['to'], $send_mails[$i]['subject']);
					
					$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
					$body = $send_mails[$i]['body'];
					$values = '';
					$labels = '';

					if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission']) {
						require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
						$cf = new customfields();

						$categories = explode(',',$send_mails[$i]['group']['fields']);
						$fields = $cf->get_fields_with_values($send_mails[$i]['event']['user_id'], 1, $send_mails[$i]['event']['id']);

						$cf = array();
						for($j=0; $j<count($fields); $j++) {
							if(in_array('cf_category_'.$fields[$j]['category_id'], $categories) && $fields[$j]['datatype'] == 'checkbox') {
								$labels .= $fields[$j]['name'].': <br />';

								$value = (empty($fields[$j]['value'])) ? $lang['common']['no'] : $lang['common']['yes'];
								$values .= $value.'<br />';
							}
						}
					}

					$body = str_replace(array('{CUSTOM_FIELDS}', '{CUSTOM_VALUES}'), array($labels, $values), $body);
					$swift->set_body($body);

					$swift->sendmail();
				}
			}

			if(count($unauthorized_participants))
			{
				$response['feedback']=str_replace('{NAMES}', implode(', ',$unauthorized_participants), $lang['calendar']['unauthorized_participants_write']);
			}

			break;

		case 'save_calendar':

			$calendar['id']=$_POST['calendar_id'];
			$calendar['user_id'] = isset($_POST['user_id']) ? ($_POST['user_id']) : $GO_SECURITY->user_id;
			$calendar['group_id'] = isset($_POST['group_id']) ? ($_POST['group_id']) : 0;
			$calendar['show_bdays'] = isset($_POST['show_bdays']) ? 1 : 0;                        
			if($calendar['group_id'] == 0) $calendar['group_id'] = 1;
			$calendar['name']=$_POST['name'];


			if(empty($calendar['name'])) {
				throw new Exception($lang['common']['missingField']);
			}

			/*$existing_calendar = $cal->get_calendar_by_name($calendar['name']);
			if($existing_calendar && ($calendar['id']==0 || $existing_calendar['id']!=$calendar['id'])) {
			//throw new Exception($sc_calendar_exists);
			}*/

			if($calendar['id']>0) {
				$old_calendar = $cal->get_calendar($calendar['id']);
				if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_calendar['acl_id'])<GO_SECURITY::WRITE_PERMISSION) {
					throw new AccessDeniedException();
				}
				$cal->update_calendar($calendar, $old_calendar);
			}else {
				if(!$GO_MODULES->modules['calendar']['write_permission']) {
					throw new AccessDeniedException();
				}
				$response['acl_id'] = $calendar['acl_id'] = $GO_SECURITY->get_new_acl('calendar read: '.$calendar['name'], $calendar['user_id']);			
				$response['calendar_id']=$calendar['id']=$cal->add_calendar($calendar);
			}
			
			$tasklists = (isset($_REQUEST['tasklists'])) ? json_decode($_REQUEST['tasklists'], true) : array();
			if(!is_array($tasklists))
			{
				$tasklists = array();
			}
			
			foreach($tasklists as $tasklist)
			{
				if($tasklist['visible'] == 0)
				{
					$cal->delete_visible_tasklist($calendar['id'], $tasklist['id']);
				}else
				{
					$cal->add_visible_tasklist(array('calendar_id'=>$calendar['id'], 'tasklist_id'=>$tasklist['id']));
				}
			}

			$response['success']=true;
			break;


		case 'save_view':

			$view['id']=$_POST['view_id'];
			$view['user_id'] = isset($_POST['user_id']) ? ($_POST['user_id']) : $GO_SECURITY->user_id;
			$view['name']=$_POST['name'];

			$view_calendars = json_decode(($_POST['view_calendars']));

			//throw new Exception(var_export($view_calendars, true));


			if(empty($view['name'])) {
				throw new Exception($lang['common']['missingField']);
			}

			/*$existing_view = $cal->get_view_by_name($view['user_id'], $view['name']);
			if($existing_view && ($view['id']==0 || $existing_view['id']!=$view['id'])) {
				throw new Exception($sc_view_exists);
			}*/

			if($view['id']>0) {
				$old_view = $cal->get_view($view['id']);

				if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_view['acl_id'])<GO_SECURITY::WRITE_PERMISSION) {
					throw new AccessDeniedException();
				}
				$cal->update_view($view);

				//user id of the view changed. Change the owner of the ACL as well
				if($old_view['user_id'] != $view['user_id']) {
					$GO_SECURITY->chown_acl($old_view['acl_id'], $view['user_id']);
				}


				$cal2 = new calendar();

				$cal->get_view_calendars($view['id']);
				while($cal->next_record()) {
					$key = array_search($cal->f('id'), $view_calendars);
					if($key===false) {
						$cal2->remove_calendar_from_view($cal->f('id'), $view['id']);
					}else {
						unset($view_calendars[$key]);
					}
				}

				foreach($view_calendars as $calendar_id) {
					$cal->add_calendar_to_view($calendar_id, '', $view['id']);
				}

			}else {
				$response['acl_id'] = $view['acl_id'] = $GO_SECURITY->get_new_acl('view read: '.$view['name'], $view['user_id']);
				$response['view_id']=$cal->add_view($view);

				foreach($view_calendars as $calendar_id) {
					$cal->add_calendar_to_view($calendar_id, '', $response['view_id']);
				}
			}
			$response['success']=true;

			break;


		case 'save_group':

			$group_id = $group['id'] = isset($_POST['group_id']) ? $_POST['group_id'] : 0;

			if(!$GO_MODULES->modules['calendar']['write_permission'])
			{
				throw new AccessDeniedException();
			}

			if(isset($_POST['user_id']))
			{
				$group['user_id'] = $_POST['user_id'];
			}

			$fields = array();
			if(isset($_POST['fields']))
			{
				foreach($_POST['fields'] as $field=>$value)
				{
					$fields[] = $field;
				}
			}

			$group['fields'] = implode(',', $fields);

			$group['name'] = $_POST['name'];
			if($group['id'] > 0)
			{
				$cal->update_group($group);
				$response['success'] = true;
			}else
			{
				$group['user_id'] = $GO_SECURITY->user_id;				
				$response['group_id'] = $cal->add_group($group);

				$response['success'] = true;
			}
			break;


		case 'save_portlet':
			$calendars = json_decode($_POST['calendars'], true);
			$response['data'] = array();
			foreach($calendars as $calendar) {
				$calendar['user_id'] = $GO_SECURITY->user_id;
				if($calendar['visible'] == 0) {
					$cal->delete_visible_calendar($calendar['calendar_id'], $calendar['user_id']);
				}
				else {
					$calendar['calendar_id']=$cal->add_visible_calendar(array('calendar_id'=>$calendar['calendar_id'], 'user_id'=>$calendar['user_id']));
				}
				$response['data'][$calendar['calendar_id']]=$calendar;
			}
			$response['success']=true;
			break;

	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}

echo json_encode($response);