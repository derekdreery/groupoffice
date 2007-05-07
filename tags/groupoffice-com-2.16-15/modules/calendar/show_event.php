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

require_once($GO_LANGUAGE->get_language_file('calendar'));

require_once($GO_MODULES->path.'classes/calendar.class.inc');
$cal = new calendar();


//get the local times
$local_time = get_time();
$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date("Y", $local_time);
$month = isset($_REQUEST['month']) ? $_REQUEST['month'] : date("m", $local_time);
$day = isset($_REQUEST['day']) ? $_REQUEST['day'] : date("j", $local_time);
$hour = isset($_REQUEST['hour']) ? $_REQUEST['hour'] : date("H", $local_time);
$min = isset($_REQUEST['min']) ? $_REQUEST['min'] : date("i", $local_time);

$task = isset($_POST['task']) ? $_POST['task'] : '';
$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

$calendar_id = isset($_REQUEST['calendar_id']) ? $_REQUEST['calendar_id'] : 0;
$event_id = isset($_REQUEST['event_id']) ? $_REQUEST['event_id'] : 0;

if (isset($_REQUEST['status']))
{
	$cal->set_event_status($event_id, $_REQUEST['status'], $_SESSION['GO_SESSION']['email']);
}


$event = $cal->get_event($event_id);

/*if ($calendar = $cal->get_calendar($calendar_id))
{
	$calendar['write_permission'] = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_write']);
}else
{
	exit($strDataError);
}*/

require_once($GO_THEME->theme_path.'header.inc');

echo '<form name="event" method="post" action="'.$_SERVER['PHP_SELF'].'">';
echo '<input type="hidden" name="calendar_id" value="'.$calendar_id.'" />';
echo '<input type="hidden" name="event_id" value="'.$event_id.'" />';
echo '<input type="hidden" name="task" value="" />';
echo '<input type="hidden" name="return_to" value="'.htmlspecialchars($return_to).'" />';
?>
<table border="0" cellpadding="10">
<tr>
	<td valign="top">
	<table border="0">
	<tr>
		<td><?php echo $strName; ?>:</td>
		<td><?php echo $event['name']; ?></td>
	</tr>
	<tr>
		<td><?php echo $strOwner; ?>:</td>
		<td><?php echo show_profile($event['user_id'], '', 'normal'); ?></td>
	</tr>

	<?php
	if ($event['contact_id'] > 0)
	{
		echo '<tr><td>'.$sc_client.':</td>';
		echo '<td>'.show_contact($event['contact_id'], '', rawurlencode($_SERVER['REQUEST_URI'])).'</td></tr>';
	}
	if ($event['description'] != '')
	{
		echo '<tr><td valign="top">'.$strDescription.':</td>';
		echo '<td>'.text_to_html($event['description']).'</td></tr>';
	}

	if ($event['location'] != '')
	{
		echo '<tr><td>'.$sc_location.':</td>';
		echo '<td>'.$event['location'].'</td></tr>';
	}

	echo '<tr><td>'.$sc_type.':</td>';
	echo '<td>'.$sc_types[$event['repeat_type']].'</td></tr>';

	

	//don't calculate timezone offset for all day events
	$timezone_offset = ($event['all_day_event'] == '0') ? (get_timezone_offset($event['start_time'])*3600) : 0;

	if ($event['all_day_event'] == '1')
	{
		$event_time_format = $_SESSION['GO_SESSION']['date_format'];
	}else
	{
		$event_time_format = $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'];
	}

	switch($event['repeat_type'])
	{
		case REPEAT_NONE:
			echo '<tr><td>'.$sc_start_at.':</td><td>'.date($event_time_format, $event['start_time']+$timezone_offset).'</td></tr>';
			echo '<tr><td>'.$sc_end_at.':</td><td>'.date($event_time_format, $event['end_time']+$timezone_offset).'</td></tr>';
		break;

		case REPEAT_WEEKLY:
			if ($event['all_day_event'] == '0')
			{
				echo '<tr><td>'.$sc_start_at.':</td><td>'.date($_SESSION['GO_SESSION']['time_format'], $event['start_time']+$timezone_offset).'</td></tr>';
				echo '<tr><td>'.$sc_end_at.':</td><td>'.date($_SESSION['GO_SESSION']['time_format'], $event['end_time']+$timezone_offset).'</td></tr>';
			}

			echo '<tr><td>'.$sc_at_days.':</td><td>';

			$local_start_hour = date('H',$event['start_time']-$timezone_offset) + ($timezone_offset/3600);
			if ($local_start_hour > 23)
			{
				$local_start_hour = $local_start_hour - 24;
				$shift_day = 1;
			}elseif($local_start_hour < 0)
			{
				$local_start_hour = 24 + $local_start_hour;
				$shift_day = -1;
			}else
			{
				$shift_day = 0;
			}

			if ($event['sun'] == 1)
			{
				$event['days'][] = $full_days[0+$shift_day];
			}
			if ($event['mon'] == 1)
			{
				$event['days'][] = $full_days[1+$shift_day];
			}

			if ($event['tue'] == 1)
			{
				$event['days'][] = $full_days[2+$shift_day];
			}

			if ($event['wed'] == 1)
			{
				$event['days'][] = $full_days[3+$shift_day];
			}

			if ($event['thu'] == 1)
			{
				$event['days'][] = $full_days[4+$shift_day];
			}

			if ($event['fri'] == 1)
			{
				$event['days'][] = $full_days[5+$shift_day];
			}

			if ($event['sat'] == 1)
			{
				$event['days'][] = $full_days[6]+$shift_day;
			}
			echo implode(', ', $event['days']);
			echo '</td></tr>';
			echo '<tr><td>'.$sc_cycle_end.':</td><td>';
			if ($event['repeat_forever'] == 1)
			{
				echo $sc_noend;
			}else
			{
				echo date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], $event['repeat_end_time']);
			}
			echo '</td></tr>';

		break;

		case REPEAT_DAILY:
			if ($event['all_day_event'] == '0')
			{
				echo '<tr><td>'.$sc_start_at.':</td><td>'.date($_SESSION['GO_SESSION']['time_format'], $event['start_time']+$timezone_offset).'</td></tr>';
				echo '<tr><td>'.$sc_end_at.':</td><td>'.date($_SESSION['GO_SESSION']['time_format'], $event['end_time']+$timezone_offset).'</td></tr>';
			}
			echo '<tr><td>'.$sc_cycle_end.':</td><td>';
			if ($event['repeat_forever'] == 1)
			{
				echo $sc_noend;
			}else
			{
				echo date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], $event['repeat_end_time']);
			}
			echo '</td></tr>';
		break;

		case REPEAT_MONTH_DATE:
			$event['start_day'] = date('j', $event['start_time']);
			$event['start_hour'] = date('G', $event['start_time']);
			$event['start_min'] = date('i', $event['start_time']);

			$start_time = mktime($event['start_hour'], $event['start_min'], 0, $month, $event['start_day'], $year);
			$end_time = $event['start_time'] + $event['end_time'] - $event['start_time'];

			echo '<tr><td>'.$sc_start_at.':</td><td>'.date($event_time_format, $start_time+$timezone_offset).'</td></tr>';
			echo '<tr><td>'.$sc_end_at.':</td><td>'.date($event_time_format, $end_time+$timezone_offset).'</td></tr>';
			echo '<tr><td>'.$sc_cycle_end.':</td><td>';
			if ($event['repeat_forever'] == 1)
			{
				echo $sc_noend;
			}else
			{
				echo date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], $event['repeat_end_time']);
			}
			echo '</td></tr>';
		break;

		case REPEAT_MONTH_DAY:
			if ($event['all_day_event'] == '0')
			{
				echo '<tr><td>'.$sc_start_at.':</td><td>'.date($_SESSION['GO_SESSION']['time_format'], $event['start_time']+$timezone_offset).'</td></tr>';
				echo '<tr><td>'.$sc_end_at.':</td><td>'.date($_SESSION['GO_SESSION']['time_format'], $event['end_time']+$timezone_offset).'</td></tr>';
			}

			echo '<tr><td>'.$sc_at_days.':</td><td>';

			$local_start_hour = date('H',$event['start_time']-$timezone_offset) + ($timezone_offset/3600);
			if ($local_start_hour > 23)
			{
				$local_start_hour = $local_start_hour - 24;
				$shift_day = 1;
			}elseif($local_start_hour < 0)
			{
				$local_start_hour = 24 + $local_start_hour;
				$shift_day = -1;
			}else
			{
				$shift_day = 0;
			}

			if ($event['sun'] == 1)
			{
				$event['days'][] = $full_days[0+$shift_day];
			}
			if ($event['mon'] == 1)
			{
				$event['days'][] = $full_days[1+$shift_day];
			}

			if ($event['tue'] == 1)
			{
				$event['days'][] = $full_days[2+$shift_day];
			}

			if ($event['wed'] == 1)
			{
				$event['days'][] = $full_days[3+$shift_day];
			}

			if ($event['thu'] == 1)
			{
				$event['days'][] = $full_days[4+$shift_day];
			}

			if ($event['fri'] == 1)
			{
				$event['days'][] = $full_days[5+$shift_day];
			}

			if ($event['sat'] == 1)
			{
				$event['days'][] = $full_days[6]+$shift_day;
			}
			echo $month_times[$event['month_time']-1].' ';
			echo implode(', ', $event['days']);
			echo '</td></tr>';
			echo '<tr><td>'.$sc_cycle_end.':</td><td>';
			if ($event['repeat_forever'] == 1)
			{
				echo $sc_noend;
			}else
			{
				echo date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], $event['repeat_end_time']);
			}
			echo '</td></tr>';

		break;

		case REPEAT_YEARLY;

			$event['start_month'] = date('m', $event['start_time']);
			$event['start_day'] = date('j', $event['start_time']);
			$event['start_hour'] = date('G', $event['start_time']);
			$event['start_min'] = date('i', $event['start_time']);


			$start_time = mktime($event['start_hour'], $event['start_min'], 0, $event['start_month'], $event['start_day'], $year);
			$end_time = $event['start_time'] + $event['end_time'] - $event['start_time'];

			echo '<tr><td>'.$sc_start_at.':</td><td>'.date($event_time_format, $start_time+$timezone_offset).'</td></tr>';
			echo '<tr><td>'.$sc_end_at.':</td><td>'.date($event_time_format, $end_time+$timezone_offset).'</td></tr>';
			echo '<tr><td>'.$sc_cycle_end.':</td><td>';
			if ($event['repeat_forever'] == 1)
			{
				echo $sc_noend;
			}else
			{
				echo date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], $event['repeat_end_time']);
			}
			echo '</td></tr>';
		break;
	}

	echo '<tr><td colspan="2"><br />';
	$button = new button($cmdClose, "javascript:document.location= '".htmlspecialchars($return_to)."'");
	echo $button->get_html();
	echo '</td></tr>';
	?>
	</table>
	</td>
	<td valign="top">
	<?php
	if ($cal->get_participants($event_id))
	{
		echo '<input type="hidden" name="status" />';
		echo '<table border="0">';
		echo '<tr><td><h3>'.$strName.'</td>';
		echo '<td><h3>'.$strEmail.'</td>';
		echo '<td><h3>'.$sc_status.'</td></tr>';

		while ($cal->next_record())
		{
			echo '<tr><td nowrap>'.show_profile_by_email(smart_addslashes($cal->f('email')), $cal->f('name')).'&nbsp;</td>';
			echo '<td nowrap>'.mail_to(empty_to_stripe(addslashes($cal->f('email'))), htmlspecialchars($cal->f('email'))).'&nbsp;</td><td>';
			switch($cal->f('status'))
			{
				case '0':
					echo $sc_not_responded;
				break;

				case '1':
					echo $sc_accepted;
				break;

				case '2':
					echo $sc_declined;
				break;

			}
			echo '</td></tr>';
		}
		echo '</table>';
		$status = $cal->get_event_status($event_id, $_SESSION['GO_SESSION']['email']);
		if($status !== false)
		{
			echo '<br />';
			switch ($status)
			{
				case '0';
					$button = new button($sc_accept, "javascript:document.location='".$_SERVER['REQUEST_URI']."&status=1'");
					echo '&nbsp;&nbsp;';
					$button = new button($sc_decline, "javascript:document.location='".$_SERVER['REQUEST_URI']."&status=2'");
				break;

				case '1';
					$button = new button($sc_decline, "javascript:document.location='".$_SERVER['REQUEST_URI']."&status=2'");
				break;

				case '2';
					$button = new button($sc_accept, "javascript:document.location='".$_SERVER['REQUEST_URI']."&status=1'");
				break;
			}
		}
	}
	?>
	</td>
</tr>
</table>
<?php
require_once($GO_THEME->theme_path.'footer.inc');
