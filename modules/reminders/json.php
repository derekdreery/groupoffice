<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
require_once('../../Group-Office.php');
$GO_SECURITY->json_authenticate('reminders');

require_once ($GO_CONFIG->class_path."base/reminder.class.inc.php");
$reminders = new reminder();

$task=isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
try{
	switch($task)
	{
		case 'reminder':
			$reminder = $reminders->get_reminder($_REQUEST['reminder_id']);

			if($reminder['link_id']>0){
				$reminder['link']=$reminder['link_type'].':'.$reminder['link_id'];
				require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
				$search=new search();
				
				$link = $search->get_search_result($reminder['link_id'], $reminder['link_type']);
				$reminder['link_name']=$link['name'];
			}else
			{
				$reminder['link_name']='';
			}

			$reminder['date']=date($_SESSION['GO_SESSION']['date_format'], $reminder['time']);
			$reminder['time']=date($_SESSION['GO_SESSION']['time_format'], $reminder['time']);

			$response['data']=$reminder;
			$response['success']=true;
			break;

		case 'reminder_users':
			
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_users = json_decode($_POST['delete_keys']);
					foreach($delete_users as $user_id)
					{
						$reminders->remove_user_from_reminder($user_id, $_POST['reminder_id'], false);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			if(isset($_POST['add_users']))
			{
				$reminder = $reminders->get_reminder($_POST['reminder_id']);

				$add_users = json_decode($_POST['add_users'], true);
				foreach($add_users as $user_id){
					$reminders->add_user_to_reminder($user_id, $_POST['reminder_id'], $reminder['time']);
				}
			}

			if(isset($_POST['add_groups']))
			{
				require_once($GO_CONFIG->class_path.'base/groups.class.inc.php');
				$GO_GROUPS = new GO_GROUPS();
				$reminder = $reminders->get_reminder($_POST['reminder_id']);

				$add_groups = json_decode($_POST['add_groups'], true);
				foreach($add_groups as $group_id){

					$GO_GROUPS->get_users_in_group($group_id);
					while($record = $GO_GROUPS->next_record()){
						$reminders->add_user_to_reminder($record['id'], $_POST['reminder_id'], $reminder['time']);
					}
				}
			}

			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';

			$response['total'] = $reminders->get_reminder_users($_POST['reminder_id'], true, $start, $limit);
			$response['results']=array();

			while($record = $reminders->next_record()){

				$record['name']=String::format_name($record);
				$response['results'][]=$record;
			}
			break;
		
		case 'reminders':
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_reminders = json_decode($_POST['delete_keys']);
					foreach($delete_reminders as $reminder_id)
					{
						$reminders->delete_reminder($reminder_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'time';
			$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC';
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';
			$query = !empty($_REQUEST['query']) ? '%'.$_REQUEST['query'].'%' : '';

			$response['total'] = $reminders->get_manual_reminders($query, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($reminder = $reminders->next_record())
			{
				$reminder['time']=Date::get_timestamp($reminder['time']);
				$response['results'][] = $reminder;
			}
			break;
/* {TASKSWITCH} */
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
