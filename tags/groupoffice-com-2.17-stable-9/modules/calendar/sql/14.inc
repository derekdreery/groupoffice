<?php
$go_mod = new GO_MODULES();
if($go_mod->get_module('todos'))
{
	$sql = "SELECT * FROM td_todos";

	require_once($GO_CONFIG->root_path.'modules/calendar/classes/calendar.class.inc');
	$cal = new calendar();	
	$db = new db();

	$db->query($sql);
	while($db->next_record(MYSQL_ASSOC))
	{
	  $cal->get_user_calendars($db->f('res_user_id'));
	  if ($cal->next_record())
	  {
	    //yes he does so set it default
	    $calendar_id = $cal->f('id');
	  }else
		{
			$user = $GO_USERS->get_user($db->f('res_user_id'));
			
			$middle_name = $user['middle_name'] == '' ? ' ' : ' '.$user['middle_name'].' ';
			$name = $user['first_name'].$middle_name.$user['last_name'];
			
	 		$calendar_name =$name;
	    $new_calendar_name = $calendar_name;
	    $x = 1;
	    while($cal->get_calendar_by_name($new_calendar_name))
	    {
				$new_calendar_name = $calendar_name.' ('.$x.')';
	      $x++;
	    }
	    if (!$calendar_id = $cal->add_calendar($db->f('res_user_id'), addslashes($new_calendar_name), 7, 20))
	    {				
				echo 'Failed creating '.$new_calendar_name.'<br />';
				continue;
	    }
		}
		
		$event = $db->Record;
		$event['end_time'] = $event['due_time'];
		$event['user_id'] = $event['res_user_id'];
		$event['todo'] = '1';

		unset($event['id'], $event['due_time'],$event['remind_style'], $event['remind_time'],$event['status'], $event['file_path'], $event['priority'],$event['res_user_id']);
		
		$event['acl_read'] = $GO_SECURITY->get_new_acl('Event read');
		$event['acl_write'] = $GO_SECURITY->get_new_acl('Event read');	
		$GO_SECURITY->add_user_to_acl($db->f('res_user_id'), $event['acl_write']);
		if($db->f('res_user_id') != $db->f('user_id'))
		{
			$GO_SECURITY->add_user_to_acl($db->f('user_id'), $event['acl_write']);
		}
		$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_everyone, $event['acl_read']);	
		
		$event = array_map('addslashes',$event);

		if($event_id = $cal->add_event($event))
		{
			$cal->subscribe_event($event_id, $calendar_id);
		}
		echo 'Adding '.$event['name'].' to '.$calendar_id.'<br />';
	}
}
