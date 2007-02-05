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
require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');

load_basic_controls();

require_once($GO_LANGUAGE->get_language_file('calendar'));

require_once($GO_MODULES->path.'classes/calendar.class.inc');
$cal = new calendar();

$task = isset($_POST['task']) ? $_POST['task'] : '';
$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];


$event_id = isset($_REQUEST['event_id']) ? $_REQUEST['event_id'] : 0;
$calendar_id = isset($_REQUEST['calendar_id']) ? $_REQUEST['calendar_id'] : 0;

$event = $cal->get_event($_REQUEST['event_id']);
if (!$event)
{
	exit($strDataError);
}

switch($task)
{
	case 'delete':
	if ($event['write_permission'])
	{
		if(isset($_POST['exception_time']))
		{
			$exception['event_id'] = $event_id;
			$exception['time'] = $_POST['exception_time'];
			
			$update_event['id']=$event_id;
			$cal->update_event($update_event);
			
			$cal->add_exception($exception);
		}else
		{
			if($resource_group_id = $cal->get_resource_group_id_by_event_id($event['id']))
			{
				$cal->get_resource_group_admins($resource_group_id);
				while($cal->next_record())
				{
					if($cal->f('user_id') != $GO_SECURITY->user_id)
					{
            	$subject = sprintf($cal_resource_deleted_mail_subject, $event['name']);
							$body = $cal->event_to_html($event);
              $user = $GO_USERS->get_user($cal->f('user_id'));
              sendmail($user['email'], $_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name'], $subject, $body , '3', 'text/HTML');
					}
				}
				
			}	
			$cal->delete_event($event_id);
	
			$cal2 = new calendar();
			$cal2->get_event_resources($event_id);
			while($cal2->next_record())
			{
				if($resource_group_id = $cal->get_resource_group_id_by_event_id($cal2->f('id')))
				{
					$cal->get_resource_group_admins($resource_group_id);
					while($cal->next_record())
					{
						if($cal->f('user_id') != $GO_SECURITY->user_id)
        	  {	
							$subject = sprintf($cal_resource_deleted_mail_subject, $cal2->f('name'));
							$body = $cal->event_to_html($cal2->Record);
							$user = $GO_USERS->get_user($cal->f('user_id'));
							sendmail($user['email'], $_SESSION['GO_SESSION']['email'], $_SESSION['GO_SESSION']['name'], $subject, $body, '3', 'text/HTML');
						}
					}

				}
				$cal->delete_event($cal2->f('id'));

			}

		}
	}

	header('Location: '.$GO_MODULES->modules['calendar']['url']);
	exit();
	break;

	case 'unsubscribe':
	if($calendar = $cal->get_calendar($calendar_id))
	{
		if ($cal->event_is_subscribed($event_id, $calendar_id) && $GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_write']))
		{
			if ($cal->get_event_subscribtions($event_id) < 2)
			{
				$cal->delete_event($event_id);
			}else
			{
				$cal->unsubscribe_event($event_id, $calendar_id);
			}
		}
	}
	header('Location: '.$GO_MODULES->modules['calendar']['url']);
	exit();
	break;

}

require_once($GO_THEME->theme_path.'header.inc');
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
<input type="hidden" name="calendar_id" value="<?php echo $calendar_id; ?>" />
<input type="hidden" name="return_to" value="<?php echo htmlspecialchars($return_to); ?>" />
<input type="hidden" name="task" value="<?php echo $task; ?>" />

<?php
if(isset($_REQUEST['exception_time']))
{
	$input = new input('hidden', 'exception_time', $_REQUEST['exception_time']);
	echo $input->get_html();
}
?>

<table border="0" cellpadding="2">
<tr>
<td>
<table border="0" cellpadding="4">
<tr>
<td><img src="<?php echo $GO_THEME->images['questionmark']; ?>" border="0" /></td><td align="center"><h2><?php echo $sc_delete_event; ?></h2></td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<?php 
echo $strDeletePrefix." '".$event['name']."' ".$strDeleteSuffix; 
?></td>
</tr>
<tr>
<td>
<br />
<?php
if ($event['write_permission'])
{
	$button = new button($cmdOk,"javascript:document.forms[0].task.value='delete';document.forms[0].submit();");
	echo $button->get_html();
}else
{
	$button = new button($cmdOk,"javascript:document.forms[0].task.value='unsubscribe';document.forms[0].submit();");
	echo $button->get_html();
}
$button = new button($cmdCancel,"javascript:document.location='".htmlspecialchars($return_to)."';");
echo $button->get_html();
?>
</td>
</tr>
</table>
</form>
<?php

require_once($GO_THEME->theme_path.'footer.inc');
