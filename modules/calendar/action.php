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
$GLOBALS['GO_SECURITY']->json_authenticate('calendar');

require_once ($GLOBALS['GO_MODULES']->modules['calendar']['class_path']."calendar.class.inc.php");
require_once ($GLOBALS['GO_MODULES']->modules['calendar']['class_path']."go_ical.class.inc");
require_once ($GLOBALS['GO_LANGUAGE']->get_language_file('calendar'));
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

	$event['busy']=isset($_POST['busy']) ? '1' : '0';
	$event['reminder'] = isset($_POST['reminder_multiplier']) ? $_POST['reminder_multiplier'] * $_POST['reminder_value'] : 0;
	$event['background'] = !empty($_POST['background']) ? $_POST['background'] : 'EBF1E2';
	$event['category_id'] = isset($_POST['category_id']) ? $_POST['category_id'] : 0;

	$timezone_offset = Date::get_timezone_offset(Date::to_unixtime($_POST['start_date']));

	if (isset ($_POST['all_day_event'])) {
		$event['all_day_event'] = '1';
		$start_time = "00:00";
		$end_time = '23:59';
	} else {
		$event['all_day_event'] = '0';
		$start_time = $_POST['start_time'];
		$end_time = $_POST['end_time'];
	}

	$start_date = new DateTime(Date::to_input_format($_POST['start_date'].' '.$start_time));
	$start_date->setTimezone($gmt_tz);
	$event['start_time'] = $start_date->format('U');

	$end_date = new DateTime(Date::to_input_format($_POST['end_date'].' '.$end_time));
	$start_date->setTimezone($gmt_tz);
	$event['end_time'] = $end_date->format('U');

	$repeat_every = isset ($_POST['repeat_every']) ? $_POST['repeat_every'] : '1';
	$event['repeat_end_time'] = (isset ($_POST['repeat_forever']) || !isset($_POST['repeat_end_date'])) ? '0' : Date::to_unixtime($_POST['repeat_end_date'].' '.$end_time);


	$month_time = isset ($_POST['month_time']) ? $_POST['month_time'] : '0';


	$days['mon'] = isset ($_POST['repeat_days_1']) ? '1' : '0';
	$days['tue'] = isset ($_POST['repeat_days_2']) ? '1' : '0';
	$days['wed'] = isset ($_POST['repeat_days_3']) ? '1' : '0';
	$days['thu'] = isset ($_POST['repeat_days_4']) ? '1' : '0';
	$days['fri'] = isset ($_POST['repeat_days_5']) ? '1' : '0';
	$days['sat'] = isset ($_POST['repeat_days_6']) ? '1' : '0';
	$days['sun'] = isset ($_POST['repeat_days_0']) ? '1' : '0';

	$days = Date::shift_days_to_gmt($days, date('G', $event['start_time']), Date::get_timezone_offset($event['start_time']));

	//go_debug(var_export($days, true));
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
		
		case 'save_calendar_colors':
			
			$data = json_decode($_POST['griddata']);
			
			foreach($data as $d) {
				$cal->setCalendarColor($d->id, $GO_SECURITY->user_id, $d->color);
			}
			
//			var_dump($data);
			
			$response['success'] = true;

//			while($record =$cal->next_record()) {
//				$color = $cal->getCalendarColor($record->id,$GO_SECURITY->user_id);
//				
//				$response['results'][] = $record;
//			}
//			
			break;

		case 'import':

			//attempt to use greedy settings
			ini_set('max_execution_time', 180);
			ini_set('memory_limit','100M');

			if (!file_exists($_FILES['ical_file']['tmp_name'][0])) {
				throw new Exception($lang['common']['noFileUploaded']);
			}else {
				File::mkdir($GLOBALS['GO_CONFIG']->tmpdir);
				$tmpfile = $GLOBALS['GO_CONFIG']->tmpdir.uniqid(time());
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

			if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $event['acl_id'])<GO_SECURITY::DELETE_PERMISSION) {
				throw new AccessDeniedException();
			}

			if(isset($_POST['create_exception']) && $_POST['create_exception'] =='true') {
				$exceptionDate = strtotime($_POST['exception_date']);

				//an instance of a recurring event was modified. We must create an exception for the
				//recurring event.
				//$exception['event_id'] = $event_id;

				$event_start_time = $event['start_time'];
				$exception['time'] = mktime(date('G', $event_start_time),date('i', $event_start_time), 0, date('n', $exceptionDate), date('j', $exceptionDate), date('Y', $exceptionDate));

				//$exception_event = $cal->get_event($event_id);
				$cal->add_exception_for_all_participants($event['resource_event_id'], $exception);
				
				$calendar = $cal->get_calendar($event['calendar_id']);
				
				//$cal->send_invitation($event, $calendar, false);
			}else
			{
				if(!empty($_REQUEST['send_cancellation']))
				{
					require_once($GLOBALS['GO_CONFIG']->class_path.'mail/GoSwift.class.inc.php');
					$RFC822 = new RFC822();

					$participants=array();
					$cal->get_participants($event_id);
					while($cal->next_record())
					{
						if($cal->f('user_id') != $GLOBALS['GO_SECURITY']->user_id)
						{
							$participants[] = $RFC822->write_address($cal->f('name'), $cal->f('email'));
						}
					}

					if(count($participants))
					{
						$cal->update_event_sequence($event['id'], ++$event['sequence']);

						$swift = new GoSwift(
								implode(',', $participants),
								$lang['calendar']['cancellation'].': '.$event['name']);

						//create ics attachment
						require_once ($GLOBALS['GO_MODULES']->modules['calendar']['class_path'].'go_ical.class.inc');
						$ical = new go_ical('2.0', false, 'CANCEL');
						$ical->line_break="\r\n";
						$ical->dont_use_quoted_printable = true;

						$ics_string = $ical->export_event($event_id);

						$swift->set_body($cal->event_to_html($event, false, true));

						$swift->message->attach(new Swift_MimePart($ics_string, 'text/calendar; name="calendar.ics"; charset="utf-8"; METHOD="REQUEST"'));
						//$name = File::strip_invalid_chars($event['name']).'.ics';
						//$swift->message->attach(Swift_Attachment::newInstance($ics_string, $name, File::get_mime($name)));

						$swift->set_from($_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name']);

						if(!$swift->sendmail(true)) {
							throw new Exception('Could not send invitation');
						}
					}
				}
				$cal->delete_event($event_id);
			}
			

			$response['success']=true;
			break;

		case 'accept':

			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$event_id = ($_REQUEST['event_id']);
			$calendar_id = isset($_REQUEST['calendar_id']) ? $_REQUEST['calendar_id'] : 0;

			$event = $cal->get_event($event_id);

			$event_exists= $cal->get_event_by_uuid($event['uuid'], 0, $calendar_id);


			if(!$cal->is_participant($event_id, $_SESSION['GO_SESSION']['email'])) {
				throw new Exception($lang['calendar']['not_invited']);
			}			

			if(!$event_exists && !empty($calendar_id) && $event['calendar_id']!=$calendar_id) {
				$new_event['user_id']=$GLOBALS['GO_SECURITY']->user_id;
				$new_event['calendar_id']=$calendar_id;
				$new_event['uuid']=$event['uuid'];
			
				$target_event_id=$cal->copy_event($event_id, $new_event);
				
			}else
			{
				$up_event=$event;
				$up_event['id']=$event_exists['id'];
				$up_event['user_id']=$GLOBALS['GO_SECURITY']->user_id;
				$up_event['calendar_id']=$calendar_id;
				unset($up_event['resource_event_id'], $up_event['acl_id']);

				$cal->update_event($up_event);
				$cal->remove_participants($event_exists['id']);
				$target_event_id=$event_exists['id'];
				
			}

			$cal->set_event_status($event_id, '1', $_SESSION['GO_SESSION']['email']);

			$cal->copy_participants($event_id, $target_event_id);

			$owner = $GO_USERS->get_user($event['user_id']);

			require_once($GLOBALS['GO_CONFIG']->class_path.'mail/GoSwift.class.inc.php');
			$swift = new GoSwift($owner['email'], sprintf($lang['calendar']['accept_mail_subject'],$event['name']));

			$swift->set_from($GLOBALS['GO_CONFIG']->webmaster_email, $GLOBALS['GO_CONFIG']->title);

			$body = sprintf($lang['calendar']['accept_mail_body'],$_SESSION['GO_SESSION']['email']);
			$body .= '<br /><br />'.$cal->event_to_html($event);

			$swift->set_body($body);
			$swift->sendmail();

			$response['success']=true;
			break;

		case 'update_grid_event':

			

			if(isset($_POST['update_event_id'])) {
				$update_event_id=$_POST['update_event_id'];
				$old_event = $cal->get_event($update_event_id);
				$calendar = $cal->get_calendar($old_event['calendar_id']);

				//an event is moved or resized
				if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $old_event['acl_id'])<GO_SECURITY::WRITE_PERMISSION) {
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

					//$cal->add_exception($exception);
					$cal->add_exception_for_all_participants($old_event['resource_event_id'], $exception);

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

					if(isset($_POST['end_time'])) {
					//change duration
						//$duration = ($_POST['duration']);
						$update_event['end_time']=$_POST['end_time'];//round_quarters($update_event['start_time']+$duration);
					}

					if(isset($_POST['update_calendar_id'])) {
						$update_event['calendar_id']=$_POST['update_calendar_id'];
					}

					$update_event['exception_for_event_id']=$exception['event_id'];
					$update_event['uuid']=$old_event['uuid'];
					
					$response['new_event_id'] = $cal->copy_event($exception['event_id'], $update_event);
					$cal->copy_participants($exception['event_id'], $response['new_event_id']);
					
					$invitation_event_id= $response['new_event_id'];

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

					if(isset($_POST['end_time'])) {
					//change duration
						//$duration = ($_POST['duration']);

						//$update_event['start_time']=$old_event['start_time'];
						//$update_event['end_time']=$_POST['end_time'];//round_quarters($old_event['start_time']+$duration);

						$old_end_date = getdate($old_event['end_time']);
						$new_end_time = getdate($_POST['end_time']);

						$update_event['end_time']=mktime($new_end_time['hours'],$new_end_time['minutes'], 0,$old_end_date['mon'],$old_end_date['mday'],$old_end_date['year']);
					}

					if(isset($_POST['update_calendar_id'])) {
						$update_event['calendar_id']=$_POST['update_calendar_id'];

					}

					if(isset($update_event))
					{
						require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
						$GO_USERS = new GO_USERS();

						$update_event['id']=$update_event_id;
						$cal->update_event($update_event, $calendar, $old_event);
						
						if($old_event['exception_for_event_id']>0){
							//for sync and caldav update the timestamp
							$update_recurring_event=array();
							$update_recurring_event['id']=$old_event['exception_for_event_id'];
							$update_recurring_event['mtime']=time();
							$cal->update_row('cal_events', 'id', $update_recurring_event);
						}

						if($calendar['group_id'] > 1)
						{
							//a resource admin is updating a resource here
							$group = $cal->get_group($calendar['group_id']);
							$cal->get_group_admins($calendar['group_id']);
							while($cal->next_record())
							{
								if($cal->f('user_id') != $GLOBALS['GO_SECURITY']->user_id)
								{
									$user = $GO_USERS->get_user($cal->f('user_id'));
									$cal->send_resource_notification('modified_for_admin', $event, $calendar, $_SESSION['GO_SESSION']['name'], $user['email'], $group);
								}
							}
						}else
						{
							$cal2 = new calendar();
							$cal3 = new calendar();
							$cal->get_event_resources($update_event_id);
							while($resource = $cal->next_record()){
								$resource_calendar=$cal2->get_calendar($resource['calendar_id']);
								$group = $cal2->get_group($resource_calendar['group_id']);

								$update_resource = false;
								$num_admins = $cal2->get_group_admins($resource_calendar['group_id']);
								while($cal2->next_record())
								{
									if($cal2->f('user_id') != $GLOBALS['GO_SECURITY']->user_id)
									{
										$update_resource = true;

										$user = $GO_USERS->get_user($cal2->f('user_id'));
										$cal->send_resource_notification('modified_for_admin', $resource, $resource_calendar, $_SESSION['GO_SESSION']['name'], $user['email'], $group);
									}
								}
								if($update_resource)
								{
									$resource['status']='NEEDS-ACTION';
									$resource['background']='FF6666';
									$cal3->update_row('cal_events', 'id', $resource);
								}
							}
							
							
						}
						
						$invitation_event_id= $old_event['id'];
					}
					
					
/*
					//move the exceptions if a recurrent event is moved
					if(!empty($old_event['rrule']) && isset($offset))
					{
						$cal->move_exceptions(($_POST['update_event_id']), $offset);
					}*/
				}
				
				
				if(isset($_POST['send_invitation']) && $_POST['send_invitation']=='true') {
					
					$invitation_event = $cal->get_event($invitation_event_id);					
					$cal->send_invitation($invitation_event,$calendar, false);
				}

				$view_id = (isset($_REQUEST['view_id']) && $_REQUEST['view_id']) ? $_REQUEST['view_id'] : 0;
				if($view_id && $update_event['calendar_id'])
				{
					if($cal->get_view_calendars($view_id))
					{
						while($cal->next_record())
						{
							$calendars[] = $cal->f('id');
						}

						$response['is_visible'] = in_array($update_event['calendar_id'], $calendars);
					}
				}

				$response['success']=true;
			}
		break;

		case 'save_event':
			require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$event = get_posted_event();
			$event_id=$event['id'];
			$group_id = isset($_POST['group_id']) ? $_POST['group_id'] : 0;
			$calendar_id = $event['calendar_id'];
			$check_conflicts = isset($_POST['check_conflicts']) && !empty($_POST['busy']) ? $_POST['check_conflicts'] : 0;


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

			if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $calendar['acl_id'])<GO_SECURITY::WRITE_PERMISSION) {
				throw new AccessDeniedException();
			}

			if(empty($event['name']) || empty($event['start_time']) || empty($event['end_time'])) {
				throw new Exception($lang['common']['missingField']);
			}

			//throw new Exception(date('Ymd G:i', $cal->get_next_recurrence_time(0,$event['start_time'], $event)));
			if(!empty($event['rrule']) && Date::get_next_recurrence_time($event['start_time'],$event['start_time'], $event['end_time']-$event['start_time'],$event['rrule']) < $event['end_time']) {
			//Event will cumulate
				throw new Exception($lang['calendar']['cumulative']);
			}

			/* Check for conflicts regarding resources */
			if (isset($resources)) {
				$cal = new calendar();
				$concurrent_resources = $cal->get_events_in_array($resources,0, $event['start_time'], $event['end_time'], true);


				foreach ($concurrent_resources as $key=>$value) {
					if ($value['resource_event_id'] != $event['id']) {
						$cal2 = new calendar();
						$resource = $cal2->get_calendar($value['calendar_id']);
						$response['success'] = false;
						$response['resources'][] = $resource['name'];
						$response['feedback'] = 'Resource conflict';
					}
				}

				if (isset($response['feedback']) && $response['feedback']=='Resource conflict') {
					break;
				}
			}

			/* Check for conflicts with other events in the calendar */
			if ($check_conflicts) {
				$conflict_events = $cal->get_events_in_array(array($event['calendar_id']), 0, $event['start_time'], $event['end_time'], true);

				while($conflict_event = array_shift($conflict_events)) {

					if($conflict_event['id']!=$event_id && (empty($_POST['exception_event_id']) || $_POST['exception_event_id']!=$conflict_event['id'])){

						go_debug('Event starts:'.Date::get_timestamp($event['start_time']).
										' ends: '.Date::get_timestamp($event['end_time']));

						go_debug('Conflict with:'.$conflict_event['name'].' starts: '.Date::get_timestamp($conflict_event['start_time']).
										' ends: '.Date::get_timestamp($conflict_event['end_time']));
						throw new Exception('Ask permission');
					}
				}
			}

			$insert = false;
			$modified = false;
			$accepted = false;
			$declined = false;

			if($event['id'] > 0)
			{
				$old_event = $cal->get_event($event_id);
				$update_related = (isset($_POST['resources'])) ? false : true;

				if(($old_event['status'] != 'ACCEPTED') && ($event['status'] == 'ACCEPTED')){
					$accepted = true;

					if($calendar['group_id'] > 1)//resource                                                
                                                $event['busy']='1';						
				}

				if(($old_event['status'] != 'DECLINED') && ($event['status'] == 'DECLINED')){
					$declined = true;
					if($calendar['group_id'] > 1)//resource                                               
						$event['busy']='0';
				}

				if($old_event['start_time'] != $event['start_time'] || $old_event['end_time'] != $event['end_time'])
					$modified = true;

				$event['sequence'] = $old_event['sequence'] + 1;

				$cal->update_event($event, $calendar, $old_event, $update_related, false);

				if(isset($event['files_folder_id']))
					$response['files_folder_id']=$event['files_folder_id'];
				$response['success']=true;
			}else
			{            
				
				if(isset($_REQUEST['exception_event_id']) && $_REQUEST['exception_event_id'] > 0) {
						//$exception['event_id'] = ($_REQUEST['exception_event_id']);
						$exception['time'] = strtotime($_POST['exceptionDate']);
						//$cal->add_exception($exception);

						$exception_event = $cal->get_event($_REQUEST['exception_event_id']);
						
						//UUID of recurrence exceptions stays the same. Events are unique based on UUID and RECURRENCE-ID in icalendar specs.
						$event['exception_for_event_id']=$_REQUEST['exception_event_id'];
						$event['uuid']=$exception_event['uuid'];

						$cal->add_exception_for_all_participants($exception_event['resource_event_id'], $exception);

						//for sync update the timestamp
						$update_recurring_event=array();
						$update_recurring_event['id']=$_REQUEST['exception_event_id'];
						$update_recurring_event['mtime']=time();
						$cal->update_row('cal_events', 'id', $update_recurring_event);
					}
				
				$event_id= $cal->add_event($event, $calendar);
				$old_event = $cal->get_event($event_id);
				$insert = true;
				if($event_id) {

					if(isset($event['files_folder_id']))
						$response['files_folder_id']=$event['files_folder_id'];
					if(!empty($_POST['link'])) {

						require_once($GLOBALS['GO_CONFIG']->class_path.'base/links.class.inc.php');
						$GO_LINKS = new GO_LINKS();

						$link_props = explode(':', $_POST['link']);
						$GO_LINKS->add_link(
								($link_props[1]),
								($link_props[0]),
								$event_id,
								1);
					}

					

					$response['event_id']=$event_id;
					$response['success']=true;
				}
			}

			if(isset($GLOBALS['GO_MODULES']->modules['customfields']) && $GLOBALS['GO_MODULES']->modules['customfields']['read_permission']) {
				require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();

				if(!$insert && $calendar['group_id'] > 1) {
					$values_old = array_values($cf->get_values($GLOBALS['GO_SECURITY']->user_id, 1, $event_id));
				}
			
				$cf->update_fields($GLOBALS['GO_SECURITY']->user_id, $event_id, 1, $_POST, $insert);

				if(!$insert && $calendar['group_id'] > 1) {
					$values = array_values($cf->get_values($GLOBALS['GO_SECURITY']->user_id, 1, $event_id));
					for($i=0; $i<count($values_old); $i++) {
						if($values_old[$i] != $values[$i]) {
							$modified = true;
						}
					}
				}
			}

			if(!empty($_POST['tmp_files']) && $GLOBALS['GO_MODULES']->has_module('files')) {
				require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
				$files = new files();
				$fs = new filesystem();

				//event = $cal->get_event($event_id);
				$path = $files->build_path($event['files_folder_id']);

				$tmp_files = json_decode($_POST['tmp_files'], true);
				while($tmp_file = array_shift($tmp_files)) {
					if(!empty($tmp_file['tmp_file'])){
						$new_path = $GLOBALS['GO_CONFIG']->file_storage_path.$path.'/'.$tmp_file['name'];
						$fs->move($tmp_file['tmp_file'], $new_path);
						$files->import_file($new_path, $event['files_folder_id']);
					}
				}
			}

			/*
			 * When the user adds events to participants calendar's directly it might be
			 * that the user is unauthorized. We report those at the end of this switch case.
			 */
			$unauthorized_participants=array();

			if(!empty($_POST['participants'])) {

				$ids=array();
        $newAddedIds=array();
        
				$participants = json_decode($_POST['participants'], true);
				foreach($participants as $p) {
					$participant['event_id']=$event_id;
					$participant['name']=$p['name'];
					$participant['email']=$p['email'];
					$participant['user_id']=(isset($p['user_id'])) ? $p['user_id'] : 0;
					$participant['status']=$p['status'] ;
					$participant['is_organizer']=(isset($p['is_organizer'])) ? $p['is_organizer'] : 0;
					$participant['role']='REQ-PARTICIPANT';

					if(substr($p['id'], 0,4)=='new_') {
						if(isset($_POST['import']) && $participant['user_id'] > 0) {
							$calendar = $cal->get_default_import_calendar($participant['user_id']);

							if($calendar_id != $calendar['id']) {

								if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $calendar['acl_id'])>=GO_SECURITY::WRITE_PERMISSION) {
									$response['cal'] = $calendar;

									$event['calendar_id'] = $calendar['id'];
									/*if(!isset($event['resource_event_id'])) {
										$event['resource_event_id'] = $event_id;
									}*/
									unset($event['files_folder_id']);
									$cal->add_event($event, $calendar);
									$participant['status']=1;
								}else
								{
									$unauthorized_participants[] = $participant['name'];
								}
							}
						}
            
            $participant_id = $cal->add_participant($participant);
            
						$ids[]= $participant_id;
            $newAddedIds[]= $participant_id;
            
					}else {
						$ids[]=$p['id'];

						if(isset($_POST['import']) && $participant['user_id'] > 0) {
							$calendar = $cal->get_default_import_calendar($participant['user_id']);

							if($calendar_id != $calendar['id']) {
								if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $calendar['acl_id'])>=GO_SECURITY::WRITE_PERMISSION) {
									if(!$cal->has_participants_event($event_id, $calendar['id'])){
										$event['calendar_id'] = $calendar['id'];
										/*if(!isset($event['resource_event_id'])) {
											$event['resource_event_id'] = $event_id;
										}*/
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
					$participant['is_organizer']=1;
					$participant['role']='REQ-PARTICIPANT';

					$cal->add_participant($participant);
				}
			}

			if(!empty($_POST['send_invitation'])) {
        // Check if there are newly added participants
        if(!empty($newAddedIds))
          $cal->send_invitation($event, $calendar, true, $newAddedIds);
        else
          $cal->send_invitation($event, $calendar, true);
			}

			if($calendar['group_id'] > 1)
			{
				//a resource admin is updating a resource here

				$group = $cal->get_group($calendar['group_id']);

				$num_admins = $cal->get_group_admins($calendar['group_id']);
				if($num_admins && ($insert || $modified || $accepted || $declined))
				{
					if(!$insert)
					{
						$message_type='modified_for_admin';
					}else
					{
						$message_type='new';
					}

					while($cal->next_record())
					{
						if($cal->f('user_id') != $GLOBALS['GO_SECURITY']->user_id)
						{
							$user = $GO_USERS->get_user($cal->f('user_id'));
							$cal->send_resource_notification($message_type, $event, $calendar, $_SESSION['GO_SESSION']['name'], $user['email'], $group);
						}
					}
				}

				if($old_event['user_id'] != $GLOBALS['GO_SECURITY']->user_id)
				{
					$message_type=false;
					if($accepted)
					{
						$message_type='accepted';
					}elseif($declined)
					{
						$message_type='declined';

					}elseif($modified)
					{
						$message_type='modified_for_user';
					}

					if($message_type){
						$user = $GO_USERS->get_user($old_event['user_id']);
						$cal->send_resource_notification($message_type, $event, $calendar, $_SESSION['GO_SESSION']['name'], $user['email'], $group);
					}
				}
			}
			else {
			//copy event properties
				$event_copy = $event;
				unset($event_copy['id'], $event_copy['reminder'], $event_copy['files_folder_id'], $event_copy['calendar_id'], $event_copy['uuid']);

				$event_copy['busy'] = '0';
				$event_copy['user_id'] = (isset($event_copy['user_id']) && $event_copy['user_id'] > 0) ? $event_copy['user_id'] : $GLOBALS['GO_SECURITY']->user_id;

				$cal2 = new calendar();
				$cal3 = new calendar();

				$num_resources = $cal->get_authorized_calendars($GLOBALS['GO_SECURITY']->user_id, 0, 0, 1);
				if($num_resources > 0)
				{
					while($resource_calendar = $cal->next_record())
					{
						$resource_id = $resource_calendar['id'];

						$existing_resource = $cal2->get_event_resource($event_id, $resource_id);
						if(isset($resources) && in_array($resource_id, $resources))
						{
							$resource = $event_copy;
							$resource['resource_event_id'] = $event_id;
							$resource['calendar_id'] = $resource_id;

							if($existing_resource)
							{
								$resource['id'] = $resource_id = $existing_resource['id'];
								$modified_resource = false;

								if(isset($GLOBALS['GO_MODULES']->modules['customfields']) && $GLOBALS['GO_MODULES']->modules['customfields']['read_permission'])
								{
									require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
									$cf = new customfields();

									$values_old = array_values($cf->get_values($GLOBALS['GO_SECURITY']->user_id, 1, $resource_id));

									$custom_fields=isset($_POST['resource_options'][$resource_calendar['id']]) ? $_POST['resource_options'][$resource_calendar['id']] : array();
									$cf->update_fields($GLOBALS['GO_SECURITY']->user_id, $resource_id, 1, $custom_fields, false);

									$values = array_values($cf->get_values($GLOBALS['GO_SECURITY']->user_id, 1, $resource_id));
									for($i=0; $i<count($values_old); $i++) {
										if($values_old[$i] != $values[$i]) {
											$modified_resource = true;
										}
									}
								}

								$group = $cal2->get_group($resource_calendar['group_id']);

								if($modified || $modified_resource)
								{
									$resource['status']='NEEDS-ACTION';
									$resource['background']='FF6666';

									$num_admins = $cal2->get_group_admins($resource_calendar['group_id']);
									if($num_admins)
									{
										while($cal2->next_record())
										{
											if($cal2->f('user_id') != $GLOBALS['GO_SECURITY']->user_id)
											{
												$user = $GO_USERS->get_user($cal2->f('user_id'));
												$cal->send_resource_notification('modified_for_admin', $resource, $resource_calendar, $_SESSION['GO_SESSION']['name'], $user['email'], $group);
											}

											if($cal2->f('user_id') == $resource['user_id'])
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
                                                               
                                                                $resource['busy'] = ($group['show_not_as_busy']) ? '0' : '1';

								$resource_id = $resource['id'] = $cal3->add_event($resource);

								if(isset($GLOBALS['GO_MODULES']->modules['customfields']) && $GLOBALS['GO_MODULES']->modules['customfields']['read_permission'])
								{
									require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
									$cf = new customfields();

									$custom_fields=isset($_POST['resource_options'][$resource_calendar['id']]) ? $_POST['resource_options'][$resource_calendar['id']] : array();

									$cf->update_fields($GLOBALS['GO_SECURITY']->user_id, $resource_id, 1, $custom_fields, true);
								}

								$num_admins = $cal2->get_group_admins($resource_calendar['group_id']);
								if($num_admins)
								{
									while($cal2->next_record())
									{
										if($cal2->f('user_id') != $GLOBALS['GO_SECURITY']->user_id)
										{
											$user = $GO_USERS->get_user($cal2->f('user_id'));
											$cal->send_resource_notification('new', $resource, $resource_calendar, $_SESSION['GO_SESSION']['name'], $user['email'], $group);
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

			$params = array(&$response, $event);
			$GLOBALS['GO_EVENTS']->fire_event('save_event', $params);

			//When using the option to directly put the event into the user
			//participant's calendar it might happen that the user is not authorized to do this.
			if(count($unauthorized_participants))
			{
				$response['feedback']=str_replace('{NAMES}', implode(', ',$unauthorized_participants), $lang['calendar']['unauthorized_participants_write']);
			}

			break;

		case 'save_calendar':

			$calendar['id']=$_POST['calendar_id'];
			$calendar['user_id'] = isset($_POST['user_id']) ? ($_POST['user_id']) : $GLOBALS['GO_SECURITY']->user_id;
			$calendar['group_id'] = isset($_POST['group_id']) ? ($_POST['group_id']) : 0;
			$calendar['show_bdays'] = isset($_POST['show_bdays']) ? 1 : 0;
			if($calendar['group_id'] == 0) $calendar['group_id'] = 1;
			$calendar['name']=$_POST['name'];

			$calendar['comment']=$_POST['comment'];
			if(isset($_POST['tasklist_id']))
				$calendar['tasklist_id']=$_POST['tasklist_id'];

			$calendar['public']=isset($_POST['public']) ? '1' : '0';


			if(empty($calendar['name'])) {
				throw new Exception($lang['common']['missingField']);
			}

			/*$existing_calendar = $cal->get_calendar_by_name($calendar['name']);
			if($existing_calendar && ($calendar['id']==0 || $existing_calendar['id']!=$calendar['id'])) {
			//throw new Exception($sc_calendar_exists);
			}*/

			if($calendar['id']>0) {
				$old_calendar = $cal->get_calendar($calendar['id']);
				$insert = false;
				if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $old_calendar['acl_id'])<GO_SECURITY::WRITE_PERMISSION) {
					throw new AccessDeniedException();
				}
				if(!$GLOBALS['GO_SECURITY']->has_admin_permission($GLOBALS['GO_SECURITY']->user_id))
				{
					unset($calendar['user_id']);
				}
				$cal->update_calendar($calendar, $old_calendar);
			}else {
				if(!$GLOBALS['GO_MODULES']->modules['calendar']['write_permission']) {
					throw new AccessDeniedException();
				}
				$response['acl_id'] = $calendar['acl_id'] = $GLOBALS['GO_SECURITY']->get_new_acl('calendar read: '.$calendar['name'], $calendar['user_id']);
				$response['calendar_id']=$calendar['id']=$cal->add_calendar($calendar);
				$insert = true;

				/*
				 * Automatically add resource admins to manage permission. Resources have a group id higher then 1
				 */

				if(!empty($calendar['group_id'])){
					$cal->get_group_admins($calendar['group_id']);
					while($group_admin = $cal->next_record())
						$GLOBALS['GO_SECURITY']->add_user_to_acl($group_admin['user_id'], $calendar['acl_id'], GO_SECURITY::MANAGE_PERMISSION);
				}
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


			if(isset($GLOBALS['GO_MODULES']->modules['customfields']) && $GLOBALS['GO_MODULES']->modules['customfields']['read_permission'])
			{
				require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$cf->update_fields($GLOBALS['GO_SECURITY']->user_id, $calendar['id'], 21, $_POST, $insert);
			}

			$response['success']=true;
			break;


		case 'save_view':

			$view['id']=$_POST['view_id'];
			$view['user_id'] = isset($_POST['user_id']) ? ($_POST['user_id']) : $GLOBALS['GO_SECURITY']->user_id;
			$view['name']=$_POST['name'];
			$view['merge'] = isset($_POST['merge']) && $_POST['merge']=='on' ? '1' : '0';
			$view['owncolor'] = isset($_POST['owncolor']) && $_POST['owncolor']=='on' ? '1' : '0';

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

				if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $old_view['acl_id'])<GO_SECURITY::WRITE_PERMISSION) {
					throw new AccessDeniedException();
				}
				$cal->update_view($view);

				//user id of the view changed. Change the owner of the ACL as well
				if($old_view['user_id'] != $view['user_id']) {
					$GLOBALS['GO_SECURITY']->chown_acl($old_view['acl_id'], $view['user_id']);
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
				$response['acl_id'] = $view['acl_id'] = $GLOBALS['GO_SECURITY']->get_new_acl('view read: '.$view['name'], $view['user_id']);
				$response['view_id']=$cal->add_view($view);

				foreach($view_calendars as $calendar_id) {
					$cal->add_calendar_to_view($calendar_id, '', $response['view_id']);
				}
			}
			$response['success']=true;

			break;


		case 'save_group':

			$group_id = $group['id'] = isset($_POST['group_id']) ? $_POST['group_id'] : 0;
                        $group['show_not_as_busy'] = isset($_REQUEST['show_not_as_busy']) ? 1 : 0;

			if(!$GLOBALS['GO_MODULES']->modules['calendar']['write_permission'])
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
			}else
			{
				$group['user_id'] = $GLOBALS['GO_SECURITY']->user_id;
				$response['group_id'] = $cal->add_group($group);
			}

			if($group['id'] == 1)
			{
				$group = $cal->get_group(1);
				$response['fields'] = $group['fields'];
			}

			$response['success'] = true;
			break;


		case 'save_portlet':
			$calendars = json_decode($_POST['calendars'], true);
			$response['data'] = array();
			foreach($calendars as $calendar) {
				$calendar['user_id'] = $GLOBALS['GO_SECURITY']->user_id;
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

		case 'change_merge':
			$view = array();
			$view['id'] = $_POST['view_id'];
			$view['merge'] = $_POST['merge'];
			$cal->update_view($view);
			$response['success']=true;
			break;


		case 'save_permissions':

		    $acl_level = isset($_REQUEST['acl_id']) ? $_REQUEST['acl_id'] : 0;
		    $group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] : 0;
		    $calendars = isset($_REQUEST['calendars']) ? json_decode($_REQUEST['calendars'], true) : array();
		    $resources = isset($_REQUEST['resources']) ? $_REQUEST['resources'] : 0;

		    if($acl_level && $group_id)
		    {
			$writable_calendars = array();
			$cal->get_writable_calendars($GLOBALS['GO_SECURITY']->user_id, 0, 0, $resources);
			while($cal->next_record())
			{
			    $calendar = $cal->record;
			    $writable_calendars[] = $calendar['id'];

			    if(!in_array($calendar['id'], $calendars))
			    {
				$current_acl_level = $GLOBALS['GO_SECURITY']->group_in_acl($group_id, $calendar['acl_id']);
				if(!$current_acl_level || ($current_acl_level ==  $acl_level))
				{
				    $GLOBALS['GO_SECURITY']->delete_group_from_acl($group_id, $calendar['acl_id']);
				}
			    }
			}

			foreach($calendars as $calendar_id)
			{
			    if(in_array($calendar_id, $writable_calendars))
			    {
				$calendar = $cal->get_calendar($calendar_id);

				$current_acl_level = $GLOBALS['GO_SECURITY']->group_in_acl($group_id, $calendar['acl_id']);
				if(!$current_acl_level)
				{
				    $GLOBALS['GO_SECURITY']->add_group_to_acl($group_id, $calendar['acl_id'], $acl_level);

				}else
				if($current_acl_level < $acl_level)
				{
				    if($GLOBALS['GO_SECURITY']->delete_group_from_acl($group_id, $calendar['acl_id']))
				    {
					$GLOBALS['GO_SECURITY']->add_group_to_acl($group_id, $calendar['acl_id'], $acl_level);
				    }
				}
			    }
			}

			$response['success'] = true;
		    }else
		    {
			$response['success'] = false;
		    }

		    break;


		    case 'save_category':

			$category['id'] = (isset($_REQUEST['id']) && $_REQUEST['id']) ? $_REQUEST['id'] : 0;
                        $category['name'] = (isset($_REQUEST['name']) && $_REQUEST['name']) ? $_REQUEST['name'] : '';
			$category['color'] = (isset($_REQUEST['color']) && $_REQUEST['color']) ? $_REQUEST['color'] : '';
                        $category['user_id'] = (isset($_REQUEST['global'])) ? 0 : $GLOBALS['GO_SECURITY']->user_id;

			if(empty($category['name']))
			{
				throw new Exception($lang['common']['missingField']);
			}

			if($category['id']>0)
			{
				$cal->update_category($category);
			}else
			{
				$response['id'] = $cal->add_category($category);
			}

			$response['success'] = true;
			break;


		case 'copy_event':

			$event_id = (isset($_REQUEST['event_id']) && $_REQUEST['event_id']) ? $_REQUEST['event_id'] : 0;
			$view_id = (isset($_REQUEST['view_id']) && $_REQUEST['view_id']) ? $_REQUEST['view_id'] : 0;
			$calendar_id = (isset($_REQUEST['calendar_id']) && $_REQUEST['calendar_id']) ? $_REQUEST['calendar_id'] : 0;
			$offset = (isset($_REQUEST['offset']) && $_REQUEST['offset']) ? $_REQUEST['offset'] : 0;

			if($view_id)
			{
				if($cal->get_view_calendars($view_id))
				{
					while($cal->next_record())
					{
						$calendars[] = $cal->f('id');
					}

					$response['is_visible'] = in_array($calendar_id, $calendars);
				}
			}

			$response['success'] = false;
			if($event_id)
			{
				$event = $cal->get_event($event_id);
				if($event)
				{
					$new_event['calendar_id'] = ($calendar_id) ? $calendar_id : $event['calendar_id'];

					$calendar = $cal->get_calendar($new_event['calendar_id']);
					if($GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $calendar['acl_id'])<GO_SECURITY::WRITE_PERMISSION){
						throw new AccessDeniedException();
					}

					$new_event['user_id'] = $GLOBALS['GO_SECURITY']->user_id;

					$new_event['start_time'] = Date::date_add($event['start_time'], $offset);
					$new_event['end_time'] = Date::date_add($event['end_time'], $offset);

					$response['event_id'] = $cal->copy_event($event_id, $new_event);

					$response['success'] = true;
				}
			}

			break;


		/*case 'icalendar_process_response':

			$event_id = (isset($_REQUEST['event_id']) && $_REQUEST['event_id']) ? $_REQUEST['event_id'] : '';
			$email_sender = (isset($_REQUEST['email_sender']) && $_REQUEST['email_sender']) ? $_REQUEST['email_sender'] : '';
			$status_id = (isset($_REQUEST['status_id']) && $_REQUEST['status_id']) ? $_REQUEST['status_id'] : '';
			$last_modified = (isset($_REQUEST['last_modified']) && $_REQUEST['last_modified']) ? $_REQUEST['last_modified'] : '';

			if(!$email_sender || !$status_id || !$last_modified)
			{
				throw new Exception($lang['common']['missingField']);
			}

			$cal->set_event_status($event_id, $status_id, $email_sender, $last_modified);

			$response['success'] = true;

			break;*/

	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}

echo json_encode($response);