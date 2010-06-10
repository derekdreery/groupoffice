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
			if($reminder['user_id']>0){
				$user = $GO_USERS->get_user($reminder['user_id']);
				$reminder['user_name']=String::format_name($user);
			}else
			{
				$reminder['user_name']='';
			}
			
			if($reminder['group_id']>0){
				$group = $GO_GROUPS->get_group($reminder['group_id']);
				$reminder['group_name']=$group['name'];
			}else
			{
				$reminder['group_name']='';
			}

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
			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'id';
			$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC';
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';
			$query = isset($_REQUEST['query']) ? '%'.$_REQUEST['query'].'%' : '';

			$response['total'] = $reminders->get_manual_reminders($query, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($reminder = $reminders->next_record())
			{
				$user = $GO_USERS->get_user($reminder['user_id']);
				$reminder['user_name']=String::format_name($user);
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
