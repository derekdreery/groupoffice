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

function get_posted_event()
{
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
	if($_POST['repeat_type']>0)
	{
		$event['rrule']=Date::build_rrule($_POST['repeat_type'], $repeat_every,$event['repeat_end_time'], $days, $month_time);
	}else
	{
		$event['rrule']='';
	}
		
	return $event;
}

function round_quarters($time)
{
	$date = getdate($time);
	
	$mins = ceil($date['minutes']/15)*15;	
	$time = mktime($date['hours'], $mins, 0, $date['mon'], $date['mday'], $date['year']);
	
	return $time;
}


//we are unsuccessfull by default
$response =array('success'=>false);

try{

	switch($_REQUEST['task'])
	{
			
		case 'import':

			if (!file_exists($_FILES['ical_file']['tmp_name'][0]))
			{
				throw new Exception($lang['common']['noFileUploaded']);
			}else
			{
				if($count = $cal->import_ical_file($_FILES['ical_file']['tmp_name'][0], $_POST['calendar_id']))
				{
					$response['feedback'] = sprintf($lang['calendar']['import_success'], $count);
					$response['success']=true;					
				}else
				{
					throw new Exception($lang['common']['saveError']);
				}
				unlink($_FILES['ical_file']['tmp_name'][0]);
			}
			break;
		case 'delete_event':
			
			$event_id=$_POST['event_id'];
			
			$event = $cal->get_event($event_id);
			
			if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $event['acl_write']))
			{
				throw new AccessDeniedException();
			}
			
			if(isset($_POST['create_exception']) && $_POST['create_exception'] =='true')
			{
				$exceptionDate = strtotime(($_POST['exception_date']));

				//an instance of a recurring event was modified. We must create an exception for the
				//recurring event.
				$exception['event_id'] = $event_id;
				
				$event_start_time = $event['start_time'];			
				$exception['time'] = mktime(date('G', $event_start_time),date('i', $event_start_time), 0, date('n', $exceptionDate), date('j', $exceptionDate), date('Y', $exceptionDate));

				$cal->add_exception($exception);
			}else
			{
				$cal->delete_event($event_id);
			}
			
			$response['success']=true;
			
			
			break;
		
		case 'update_grid_event':
				
			if(isset($_POST['update_event_id']))
			{
				$update_event_id=$_POST['update_event_id'];
				$old_event = $cal->get_event($update_event_id);
				$calendar = $cal->get_calendar($old_event['calendar_id']);
				
				//an event is moved or resized
				if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id,$old_event['acl_write']))
				{
					throw new AccessDeniedException();
				}

				if(isset($_POST['createException']) && $_POST['createException'] =='true')
				{

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

					if(isset($_POST['offset']))
					{
						//move an event
						$offset = ($_POST['offset']);


						$update_event['start_time']=round_quarters($update_event['start_time']+$offset);
						$update_event['end_time']=$update_event['end_time']+$offset;

					}


					if(isset($_POST['offsetDays']))
					{
						//move an event
						$offsetDays = ($_POST['offsetDays']);
						$update_event['start_time'] = Date::date_add($update_event['start_time'], $offsetDays);
						$update_event['end_time'] = Date::date_add($update_event['end_time'], $offsetDays);
							
					}

					if(isset($_POST['duration']))
					{
						//change duration
						$duration = ($_POST['duration']);
						$update_event['end_time']=round_quarters($update_event['start_time']+$duration);
					}

					if(isset($_POST['update_calendar_id']))
					{
						$update_event['calendar_id']=$_POST['update_calendar_id'];
					}


					$response['new_event_id'] = $cal->copy_event($exception['event_id'], $update_event);
				}else
				{
					if(isset($_POST['offset']))
					{
						//move an event
						$offset = ($_POST['offset']);


						$update_event['start_time']=round_quarters($old_event['start_time']+$offset);
						$update_event['end_time']=$old_event['end_time']+$offset;
					}

					if(isset($_POST['offsetDays']))
					{
						//move an event
						$offsetDays = ($_POST['offsetDays']);
						$update_event['start_time'] = Date::date_add($old_event['start_time'], $offsetDays);
						$update_event['end_time'] = Date::date_add($old_event['end_time'], $offsetDays);
					}

					if(isset($_POST['duration']))
					{
						//change duration
						$duration = ($_POST['duration']);

						$update_event['start_time']=$old_event['start_time'];
						$update_event['end_time']=round_quarters($old_event['start_time']+$duration);
					}
					
					if(isset($_POST['update_calendar_id']))
					{
						$update_event['calendar_id']=$_POST['update_calendar_id'];
					}
					
					$update_event['id']=$update_event_id;
					$cal->update_event($update_event, $calendar, $old_event);

					//move the exceptions if a recurrent event is moved
					if(!empty($old_event['rrule']) && isset($offset))
					{
						$cal->move_exceptions(($_POST['update_event_id']), $offset);
					}
				}
				$response['success']=true;
			}
				
				
				
			break;




		case 'accept':

			$event_id = ($_REQUEST['event_id']);
			$calendar_id = ($_REQUEST['calendar_id']);
			

			$event = $cal->get_event($event_id);
			if($event['calendar_id']!=$calendar_id)
			{	
				if(!$cal->is_participant($event_id, $_SESSION['GO_SESSION']['email']))
				{
					throw new Exception($lang['calendar']['not_invited']);
				}
	
				$new_event['user_id']=$GO_SECURITY->user_id;
				$new_event['calendar_id']=$calendar_id;
				$new_event['participants_event_id']=$event_id;
	
				$cal->copy_event($event_id, $new_event);
				
				
				$cal->set_event_status($event_id, '1', $_SESSION['GO_SESSION']['email']);
				
				$owner = $GO_USERS->get_user($event['user_id']);
				
				require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
				$swift = new GoSwift($owner['email'], sprintf($lang['calendar']['accept_mail_subject'],$event['name']));
				
				$swift->set_from($GO_CONFIG->webmaster_email, $GO_CONFIG->title);
				
				$body = sprintf($lang['calendar']['accept_mail_body'],$_SESSION['GO_SESSION']['email']);		
				$body .= '<br /><br />'.$cal->event_to_html($event);
				
				$swift->set_body($body);
				$swift->sendmail();
			}
				

			$response['success']=true;


			break;

		case 'save_event':
			$event = get_posted_event();
			$event_id=$event['id'];
			//throw new Exception(nl2br(var_export($event, true)));

			/*
			 todo conflict checking
			 if($event['busy']=='0' || isset($_POST['ignore_conflicts']))
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
			 //var_dump($conflicts);
			 unset($conflicts[$event_id]);
			 }*/
			$conflicts=array();


			if(empty($event['calendar_id']))
			{
				throw new Exception($lang['calendar']['exceptionNoCalendarID']);
			}
				
			$calendar = $cal->get_calendar($event['calendar_id']);
				
			if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_write']))
			{
				throw new AccessDeniedException();
			}

			if(empty($event['name']) || empty($event['start_time']) || empty($event['end_time']))
			{
				throw new Exception($lang['common']['missingField']);
			}
			
			//throw new Exception(date('Ymd G:i', $cal->get_next_recurrence_time(0,$event['start_time'], $event)));
			if(!empty($event['rrule']) && Date::get_next_recurrence_time($event['start_time'],$event['start_time'], $event['rrule']) < $event['end_time'])
			{
				//Event will cumulate
				throw new Exception($lang['calendar']['cumulative']);
			}
			
			if(count($conflicts))
			{
				throw new Exception($cal_conflict);
			}
			

			if($event['id']>0)
			{
				$cal->update_event($event, $calendar);
				$response['files_folder_id']=$event['files_folder_id'];
				$response['success']=true;

			}else
			{
				
				$event_id= $cal->add_event($event, $calendar);
				if($event_id)
				{
					
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
					

					if(!empty($_POST['link']))
					{
						$link_props = explode(':', $_POST['link']);
						$GO_LINKS->add_link(
						($link_props[1]),
						($link_props[0]),
						$event_id,
						1);
					}

					if(isset($_REQUEST['exception_event_id']) && $_REQUEST['exception_event_id'] > 0)
					{
						$exception['event_id'] = ($_REQUEST['exception_event_id']);
						$exception['time'] = strtotime(($_POST['exceptionDate']));
						$cal->add_exception($exception);

						//for sync update the timestamp
						$update_event['id']=$_REQUEST['exception_event_id'];
						$cal->update_event($update_event);

						
					}
					
					$response['event_id']=$event_id;
					$response['success']=true;
				}					
			}
			
			if(!empty($_POST['tmp_files']) && $GO_MODULES->has_module('files'))
			{
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
				$fs = new files();
				$fs = new filesystem();
				
				//event = $cal->get_event($event_id);
				$path = $files->build_path($event['files_folder_id']);				
					
				$tmp_files = json_decode($_POST['tmp_files'], true);
				while($tmp_file = array_shift($tmp_files))
				{					
					$new_path = $GO_CONFIG->file_storage_path.$path.'/'.$tmp_file['name'];
					$fs->move($tmp_file['tmp_file'], $new_path);
					$files->import_file($new_path, $event['files_folder_id']);
				}
			}
			
			if(!empty($_POST['participants']))
			{
				$ids=array();
				$participants = json_decode($_POST['participants'], true);
				foreach($participants as $p)
				{										
					if(substr($p['id'], 0,4)=='new_')
					{
						$participant['event_id']=$event_id;
						$participant['name']=$p['name'];
						$participant['email']=$p['email'];
						
						$ids[]=$cal->add_participant($participant);					
					}else
					{
						$ids[]=$p['id'];
					}
				}
				$cal->delete_other_participants($event_id, $ids);
			}elseif(isset($response['event_id']))
			{
				$calendar_user = $GO_USERS->get_user($calendar['user_id']);
					
				if($calendar_user)
				{
					$participant['user_id']=$calendar_user['id'];
					$participant['event_id']=$event_id;
					$participant['name']=String::format_name($calendar_user);
					$participant['email']=$calendar_user['email'];
					$participant['status']=1;
					
					$cal->add_participant($participant);
				}
			}
			
			
			if(!empty($_POST['invitation']))
			{
				require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
				require_once $GO_CONFIG->class_path.'mail/swift/lib/classes/Swift/Plugins/DecoratorPlugin.php';
				require_once $GO_CONFIG->class_path.'mail/swift/lib/classes/Swift/Plugins/Decorator/Replacements.php';
				
				$RFC822 = new RFC822();
				
				$participants=array();
				$cal->get_participants($event_id);
				while($cal->next_record())
				{
					if($cal->f('status') !=1 && $cal->f('email')!=$_SESSION['GO_SESSION']['email'])
					{
						$participants[] = $RFC822->write_address($cal->f('name'), $cal->f('email'));
					}
				}
				if(count($participants))
				{
					
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
					$ics_string = $ical->export_event($event_id);
		
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
					
					
					
					if(!$swift->sendmail(true))
					{
						throw new Exception('Could not send invitation');
					}
				}
			}
			
			break;

		case 'save_calendar':

			$calendar['id']=$_POST['calendar_id'];
			$calendar['user_id'] = isset($_POST['user_id']) ? ($_POST['user_id']) : $GO_SECURITY->user_id;
			$calendar['name']=$_POST['name'];


			if(empty($calendar['name']))
			{
				throw new Exception($lang['common']['missingField']);
			}

			$existing_calendar = $cal->get_calendar_by_name($calendar['name']);
			if($existing_calendar && ($calendar['id']==0 || $existing_calendar['id']!=$calendar['id']))
			{
				throw new Exception($sc_calendar_exists);
			}

			if($calendar['id']>0)
			{
				$old_calendar = $cal->get_calendar($calendar['id']);
				if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_calendar['acl_write']))
				{
					throw new AccessDeniedException();
				}
				$cal->update_calendar($calendar, $existing_calendar);
			}else
			{
				if(!$GO_MODULES->modules['calendar']['write_permission'])
				{
					//throw new AccessDeniedException();
				}
				$response['acl_read'] = $calendar['acl_read'] = $GO_SECURITY->get_new_acl('calendar read: '.$calendar['name'], $calendar['user_id']);
				$response['acl_write'] = $calendar['acl_write'] = $GO_SECURITY->get_new_acl('calendar write: '.$calendar['name'], $calendar['user_id']);
					
				$response['acl_read'] =
				$response['calendar_id']=$cal->add_calendar($calendar);
			}
			$response['success']=true;

			break;




		case 'save_view':

			$view['id']=$_POST['view_id'];
			$view['user_id'] = isset($_POST['user_id']) ? ($_POST['user_id']) : $GO_SECURITY->user_id;
			$view['name']=$_POST['name'];

			$view_calendars = json_decode(($_POST['view_calendars']));

			//throw new Exception(var_export($view_calendars, true));


			if(empty($view['name']))
			{
				throw new Exception($lang['common']['missingField']);
			}

			$existing_view = $cal->get_view_by_name($view['user_id'], $view['name']);
			if($existing_view && ($view['id']==0 || $existing_view['id']!=$view['id']))
			{
				throw new Exception($sc_view_exists);
			}

			if($view['id']>0)
			{
				$old_view = $cal->get_view($view['id']);
				if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_view['acl_write']))
				{
					throw new AccessDeniedException();
				}
				$cal->update_view($view);

				//user id of the view changed. Change the owner of the ACL as well
				if($old_view['user_id'] != $view['user_id'])
				{
					$GO_SECURITY->chown_acl($old_view['acl_read'], $view['user_id']);
					$GO_SECURITY->chown_acl($old_view['acl_write'], $view['user_id']);
				}
					
					
				$cal2 = new calendar();
					
				$cal->get_view_calendars($view['id']);
				while($cal->next_record())
				{
					$key = array_search($cal->f('id'), $view_calendars);
					if($key===false)
					{
						$cal2->remove_calendar_from_view($cal->f('id'), $view['id']);
					}else
					{
						unset($view_calendars[$key]);
					}
				}
					
				foreach($view_calendars as $calendar_id)
				{
					$cal->add_calendar_to_view($calendar_id, '', $view['id']);
				}
					
			}else
			{
				//if(!$GO_MODULES->modules['calendar']['write_permission'])
				//{
				//	throw new AccessDeniedException();
				//}
				$response['acl_read'] = $view['acl_read'] = $GO_SECURITY->get_new_acl('view read: '.$view['name'], $view['user_id']);
				$response['acl_write'] = $view['acl_write'] = $GO_SECURITY->get_new_acl('view write: '.$view['name'], $view['user_id']);

				$response['acl_read'] =
				$response['view_id']=$cal->add_view($view);

				foreach($view_calendars as $calendar_id)
				{
					$cal->add_calendar_to_view($calendar_id, '', $response['view_id']);
				}

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