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
require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('reminders');
require_once ($GO_CONFIG->class_path."base/reminder.class.inc.php");
$reminders = new reminder();

try{
	switch($_REQUEST['task'])
	{
		case 'save_reminder':		
			$reminder_id=$reminder['id']=isset($_POST['reminder_id']) ? $_POST['reminder_id'] : 0;

			if(!empty($_POST['link']))
			{
				$link = explode(':', $_POST['link']);
				$reminder['link_id']=$link[1];
				$reminder['link_type']=$link[0];
			}else
			{
				$reminder['link_id']=0;
				$reminder['link_type']=0;
			}

			//$reminder['user_id']=$_POST['user_id'];
			//$reminder['group_id']=$_POST['group_id'];

			$reminder['name']=$_POST['name'];
			$reminder['vtime']=$reminder['time']=strtotime(Date::to_input_format($_POST['date']).' '.$_POST['time']);
			$reminder['snooze_time']=$_POST['snooze_time'];
			$reminder['manual']=1;
			$reminder['text']=$_POST['text'];
			if($reminder['id']>0)
			{
				$reminders->update_reminder($reminder);
				$response['success']=true;
				$insert=false;
			}else
			{
				$reminder_id=$reminders->add_reminder($reminder);
				$response['reminder_id']=$reminder_id;
				$response['success']=true;
				$insert=true;
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
