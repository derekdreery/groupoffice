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

require('../../Group-Office.php');

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');



require_once ($GO_MODULES->class_path."calendar.class.inc");
$cal = new calendar();


$task=isset($_REQUEST['task']) ? smart_addslashes($_REQUEST['task']) : 0;

switch($task)
{
	case 'event':
		
		$event = $cal->get_event(smart_addslashes($_REQUEST['event_id']));
		
		//$result['succes']=true;
		//$result['data']=$event;
		
		$event['subject']=$event['name'];
		
		$start_time = gmt_to_local_time($event['start_time']);
		$end_time = gmt_to_local_time($event['end_time']);
		
		$event['start_date']=date($_SESSION['GO_SESSION']['date_format'], $start_time);
		$event['start_hour'] = date('G', $start_time);
		$event['start_min'] = date('i', $start_time);
		
		$event['end_date']=date($_SESSION['GO_SESSION']['date_format'], $end_time);
		$event['end_hour'] = date('G', $end_time);
		$event['end_min'] = date('i', $end_time);
		
		$event['repeat_days_0']=($event['sun']=='1');
		$event['repeat_days_1']=($event['mon']=='1');
		$event['repeat_days_2']=($event['tue']=='1');
		$event['repeat_days_3']=($event['wed']=='1');
		$event['repeat_days_4']=($event['thu']=='1');
		$event['repeat_days_5']=($event['fri']=='1');
		$event['repeat_days_6']=($event['sat']=='1');
		
		$event['repeat_end_date']=date($_SESSION['GO_SESSION']['date_format'], gmt_to_local_time($event['repeat_end_time']));
		
		
		$multipliers[] = 604800;
		$multipliers[] = 86400;
		$multipliers[] = 3600;
		$multipliers[] = 60;

		$event['reminder_multiplier'] = 60;
		$event['reminder_value'] = 0;

		if($event['reminder'] != 0)
		{
			for ($i = 0; $i < count($multipliers); $i ++) {
				$devided = $event['reminder'] / $multipliers[$i];
				$match = (int) $devided;
				if ($match == $devided) {
					$event['reminder_multiplier'] = $multipliers[$i];
					$event['reminder_value'] = $devided;
					break;
				}
			}
		}
		
		
		
		
	
		//echo json_encode($result);
		echo '({"event":['.json_encode($event).']})';
		
		break;
	case 'events':
		
		$calendar_id=isset($_REQUEST['calendar_id']) ? smart_addslashes($_REQUEST['calendar_id']) : 0;
		$calendars=isset($_REQUEST['calendars']) ? json_decode(smart_stripslashes($_REQUEST['calendars'])) : array($calendar_id);
		//$view_id=isset($_REQUEST['view_id']) ? smart_addslashes($_REQUEST['view_id']) : 0;
		$start_time=isset($_REQUEST['start_time']) ? strtotime($_REQUEST['start_time']) : 0;
		$end_time=isset($_REQUEST['end_time']) ? strtotime($_REQUEST['end_time']) : 0;
		
		$events = $cal->get_events_in_array($calendars, 0, 
					local_to_gmt_time($start_time), local_to_gmt_time($end_time), false,false,true,false,true);
		$results=array();
		$count=0;
		foreach($events as $event)
		{
			$results[] = array(
				'id'=>$count,
				'event_id'=> $event['id'],
				'name'=> $event['name'],
				'start_time'=> date('Y-m-d H:i', gmt_to_local_time($event['start_time'])),
				'end_time'=> date('Y-m-d H:i', gmt_to_local_time($event['end_time'])),
				'tooltip'=>'Dit is een beschrijving'
			);
			$count++;
		}
		
		echo '({"total":"'.$count.'","results":'.json_encode($results).'})'; 

		break;
		
	case 'calendar_groups':
		
		$results = array();
		
		
		
		if($cal->get_authorized_calendars($GO_SECURITY->user_id, 0))
		{
			$calendars=array();
			while($cal->next_record())
			{
				$calendars[]=$cal->Record;
			}
			
			$results[]=array('group_id'=>0, 'name'=>'Calendars', 'calendars'=>$calendars);
		}
		
		$cal2 = new calendar();
		
		$cal2->get_resource_groups();
		while($cal2->next_record())
		{

			if($cal->get_authorized_calendars($GO_SECURITY->user_id, $cal2->f('id')))
			{		
				$calendars=array();	
				while($cal->next_record(MYSQL_ASSOC))
				{
					$calendars[]=$cal->Record;
				}
				$results[]=array('group_id'=>$cal2->f('id'), 'name'=>$cal2->f('name'), 'calendars'=>$calendars);									
			}
			$resourceNode->addNode($catNode);
		}
		echo json_encode($results);
		
		
		break;
}

			
