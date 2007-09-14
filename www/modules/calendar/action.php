<?php
/**
 * @copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.13 $ $Date: 2006/10/20 12:36:43 $3
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */


 require_once("../../Group-Office.php");
 $GO_SECURITY->authenticate();
 $GO_MODULES->authenticate('calendar');

 require_once ($GO_MODULES->class_path."calendar.class.inc");
 require_once ($GO_LANGUAGE->get_language_file('calendar'));
 $cal = new calendar();

 //ini_set('display_errors','off');

 function get_posted_event()
 {
 	

	$event['id']=smart_addslashes($_POST['event_id']);
 	
 	$event['name'] = smart_addslashes(trim($_POST['subject']));
 	$event['description'] = smart_addslashes(trim($_POST['description']));
 	$event['location'] = smart_addslashes(trim($_POST['location']));
 	$event['status_id'] = smart_addslashes($_POST['status_id']);
 	$event['busy']=isset($_POST['busy']) ? '1' : '0';
 	$event['timezone'] = $_SESSION['GO_SESSION']['timezone'];
 	$event['DST'] = $_SESSION['GO_SESSION']['DST'];
 	$event['reminder'] = isset($_POST['reminder_multiplier']) ? $_POST['reminder_multiplier'] * $_POST['reminder_value'] : 0;
 	//$event['background'] = $_POST['background'];

 	$timezone_offset = get_timezone_offset(date_to_unixtime($_POST['start_date']));

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

 	$event['repeat_forever'] = isset ($_POST['repeat_forever']) ? '1' : '0';
 	$event['repeat_every'] = isset ($_POST['repeat_every']) ? $_POST['repeat_every'] : '0';
 	$event['month_time'] = isset ($_POST['month_time']) ? $_POST['month_time'] : '0';

 	$event['repeat_type'] = $_POST['repeat_type'];
 	if ($event['repeat_type'] != REPEAT_NONE) {
 		$event['repeat_end_time'] = isset ($_POST['repeat_forever']) ? '0' : local_to_gmt_time(date_to_unixtime($_POST['repeat_end_date'].' '.$end_hour.':'.$end_min));
 	} else {
 		$event['repeat_end_time'] = 0;
 	}

 	$shift_day=0;
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
 	return $event;
 }


 //we are unsuccessfull by default
 $result =array('success'=>false);

 switch($_REQUEST['task'])
 {
 	case 'update_event':

 		$event['id']=smart_addslashes($_POST['event_id']);

 		if(isset($_POST['startDate']))
 		{
 			$old_event = $cal->get_event($event['id']);
 				
 			$event['start_time']=local_to_gmt_time(strtotime(smart_stripslashes($_POST['startDate'])));
 			$event['end_time']=$event['start_time']+$old_event['end_time']-$old_event['start_time'];
 		}

 		if(isset($_POST['endDate']))
 		{
 			//echo get_timestamp(local_to_gmt_time(strtotime(smart_stripslashes($_POST['endDate']))));
 			$event['end_time']=local_to_gmt_time(strtotime(smart_stripslashes($_POST['endDate'])));
 		}

 		$result['success']=$cal->update_event($event);
 		break;

 	case 'save_event':


 		$calendar_id=smart_addslashes($_POST['calendar_id']);
 		
 		$event = get_posted_event();
		
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


 		if(empty($event['name']) || empty($event['start_time']) || empty($event['end_time']))
 		{
 			$result['errors']=$error_missing_field;
 		}elseif($event['repeat_type'] != REPEAT_NONE && $cal->get_next_recurrence_time(0,$event['start_time'], $event) < $event['end_time'])
 		{
 			//Event will cumulate
 			$result['errors']=$cal_cumulative;
 		}elseif(count($conflicts))
 		{
 			$result['errors'] = $cal_conflict;
 		}else
 		{
 			if($event['id']>0)
 			{
 				$cal->update_event($event);
 				$result['success']=true;
 			}else
 			{
	 			$event['link_id'] = $GO_LINKS->get_link_id();
	
	
	
	 			$event_id= $cal->add_event($event);
	 			if($event_id)
	 			{
	 				$cal->subscribe_event($event_id, $calendar_id);
	
	
	 				if(isset($_POST['link_id']) && $_POST['link_id']>0)
	 				{
	 					$GO_LINKS->add_link($_POST['link_id'],$_POST['link_type'], $event['link_id'], 1);
	 				}
	
	
	 				//TODO create exception
	 				/*
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
	 				*/
	
	
	
	 				$result['event_id']=$event_id;
	 				$result['success']=true;
	 			}
	 		}
 		}

 		break;
 }

 echo json_encode($result);