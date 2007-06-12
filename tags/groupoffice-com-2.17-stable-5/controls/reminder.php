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
require_once("../Group-Office.php");

$GO_SECURITY->authenticate();
load_basic_controls();
load_control('tooltip');

$projects_module = isset($GO_MODULES->modules['projects']) ? $GO_MODULES->modules['projects'] : false;
if($projects_module && $projects_module['read_permission'])
{
	require_once($projects_module['class_path'].'projects.class.inc');
	$projects = new projects();
}

$fs_module = isset($GO_MODULES->modules['filesystem']) ? $GO_MODULES->modules['filesystem'] : false;
if($fs_module && $fs_module['read_permission'])
{
	require_once($GO_CONFIG->class_path.'filesystem.class.inc');
	$fs = new filesystem();
}

$ab_module = isset($GO_MODULES->modules['addressbook']) ? $GO_MODULES->modules['addressbook'] : false;
if ($ab_module && $ab_module['read_permission'])
{
	require_once($ab_module['class_path'].'addressbook.class.inc');
	$ab = new addressbook();
}

$GO_HEADER['nomessages']=true;
$GO_HEADER['head'] = tooltip::get_header();

require_once($GO_THEME->theme_path."header.inc");

$task = isset($_POST['task']) ? $_POST['task'] : '';

$stay_open = false;

$form = new form('reminder_form');

$form->add_html_element(new input('hidden', 'task','',false));
$form->add_html_element(new input('hidden', 'event_id','',false));


if ($GO_MODULES->modules['calendar'] && $GO_MODULES->modules['calendar']['read_permission'])
{
	require_once($GO_LANGUAGE->get_language_file('calendar'));
	require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
	$cal = new calendar();
	$cal2 = new calendar();


	switch($task)
	{
		case 'dismiss_all':
			$cal->delete_reminders($GO_SECURITY->user_id);
			break;


		case 'snooze':
			if(isset($_POST['event_id']) && $_POST['event_id'] > 0)
			{
				$cal->update_reminder($GO_SECURITY->user_id, $_POST['event_id'], get_gmt_time()+$_POST['snooze'][$_POST['event_id']]);
			}
			break;

		case 'dismiss':
			if($event = $cal->get_event($_POST['event_id']))
			{
				$next_recurrence_time = $cal2->get_next_recurrence_time(0,0, $event);
				$update_reminder = $cal2->get_next_recurrence_time(0, $next_recurrence_time, $event)-$event['reminder'];

				if ($update_reminder > $next_recurrence_time)
				{
					$cal->update_reminder($GO_SECURITY->user_id, $event['id'], $update_reminder);
				}else
				{
					$cal->delete_reminder($GO_SECURITY->user_id, $event['id']);
				}
			}
			break;
	}
}

if($event_count = $cal->get_events_to_remind($GO_SECURITY->user_id, true, false))
{
	$stay_open = true;

	$em_table = new table();
	$em_table->set_attribute('style', 'border:0px;margin-top:10px;width:100%');

	$img = new image('calendar');
	$img->set_attribute('style', 'border:0px;margin-right:10px;width:32px;height:32px');

	$em_cell = new table_cell();
	$em_cell->set_attribute('valign','top');
	$em_cell->add_html_element($img);

	$em_row = new table_row();
	$em_row->add_cell($em_cell);

	$link = new hyperlink("javascript:goto_url('".$GO_MODULES->modules['calendar']['url']."');",$lang_modules['calendar']);

	$h2 = new html_element('h2',$link->get_html());

	$em_cell = new table_cell($h2->get_html());
	$em_cell->set_attribute('style','width:100%');
	$em_row->add_cell($em_cell);
	$em_table->add_row($em_row);

	$em_row = new table_row();
	$em_row->add_cell(new table_cell('&nbsp;'));

	$table = new table();
	$table->set_attribute('style','width:100%;white-space:nowrap;');


	while($cal->next_record())
	{
		$now = get_gmt_time();

		$day = date('j', $now);
		$month = date('n', $now);
		$year = date('Y',$now);

		$day_start = mktime(0,0,0,$month, $day, $year);
		$day_end = mktime(0,0,0,$month, $day+1, $year);

		$start_time = $cal->f('occurence_time');
		$end_time = $start_time + $cal->f('end_time') - $cal->f('start_time');

		$timezone_offset = get_timezone_offset($start_time)*3600;
		$start_time += $timezone_offset;

		$timezone_offset = get_timezone_offset($end_time)*3600;
		$end_time += $timezone_offset;



		$title = '';
		$date_format = '';

		if (($start_time< $day_start) || $end_time > $day_end) {
			$date_format = $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'];
		} else {
			$date_format = $_SESSION['GO_SESSION']['time_format'];
		}
		$title = $sc_start_at.': '.date($date_format, $start_time).
		'<br />'.$sc_end_at.': '.date($date_format, $end_time);

		if (isset($GO_MODULES->modules['addressbook']) &&
		$GO_MODULES->modules['addressbook']['read_permission'] &&
		$cal->f('contact_id') > 0 && $contact = $ab->get_contact($cal->f('contact_id'))) {

			$middle_name = $contact['middle_name'] == '' ? '' : $contact['middle_name'].' ';
			$contact_name = $contact['first_name'].' '.$middle_name.$contact['last_name'];
			if ($title != '') {
				$title .= '<br />';
			}
			$title .= $sc_client.": ".htmlspecialchars($contact_name);

			if ($contact['company_name'] != '') {
				$title .= " (".htmlspecialchars($contact['company_name']).")";
			}
		}
		if ($cal->f('location') != '') {
			if ($title != '') {
				$title .= '<br />';
			}
			$title .= $sc_location.": ".htmlspecialchars($cal->f('location'));
		}

		$event_cal_count = $cal2->get_calendars_from_event($cal->f('id'));
		if ($title != '') {
			$title .= '<br />';
		}
		$title .= "$cal_event_calendars: ";
		$first = true;
		while ($cal2->next_record()) {
			if ($first) {
				$first = false;
			} else {
				$title .= ' ,';
			}
			$title .= htmlspecialchars($cal2->f('name'));
		}

		$div = new html_element('div', '&nbsp;');
		$div->set_attribute('class', 'summary_icon');
		$div->set_attribute('style', 'background-color: #'.$cal->f('background'));

		$link = new hyperlink('javascript:goto_url(\''.$GO_MODULES->modules['calendar']['url'].'event.php?event_id='.$cal->f('id').'\');',
		$div->get_html().date($date_format, $start_time).'&nbsp;'.
		htmlspecialchars($cal->f('name')));
		$link->set_tooltip(new tooltip($title, $cal->f('name')));

		if($cal->f('completion_time') > 0)
		{
			$cell->set_attribute('class', 'event_completed');
		}elseif($cal->f('todo') == '1' && $now>$cal->f('end_time'))
		{
			$div->set_attribute('class', 'event_late');
		}

		$row = new table_row();
		$cell = new table_cell($link->get_html());
		$cell->set_attribute('style','width:100%');
		$row->add_cell($cell);

		$select = new select('snooze['.$cal->f('id').']', '300');
		$select->add_value('300', '5 '.$sc_mins);
		$select->add_value('900', '15 '.$sc_mins);
		$select->add_value('1800', '30 '.$sc_mins);
		$select->add_value('3600', '1 '.$sc_hour);
		$select->add_value('7200', '2 '.$sc_hours);
		$select->add_value('86400', '1 '.$sc_day);
		$select->add_value('604800','1'.$sc_week);

		$row->add_cell(new table_cell($select->get_html()));

		$cell = new table_cell();

		$button = new button($strSnooze, "javascript:update_event_reminder(".$cal->f('id').",'snooze');");
		$button->set_attribute('style','margin-top:0px;');
		$cell->add_html_element($button);

		$button= new button($strDismiss, "javascript:update_event_reminder(".$cal->f('id').",'dismiss');");
		$button->set_attribute('style','margin-top:0px;');
		$cell->add_html_element($button);

		$row->add_cell($cell);
		$table->add_row($row);
	}

	$em_row->add_cell(new table_cell($table->get_html()));
	$em_table->add_row($em_row);
	$form->add_html_element($em_table);


}

$todo_count = $cal->get_events_to_remind($GO_SECURITY->user_id, false, true);
if($todo_count)
{
	$stay_open = true;

	$em_table = new table();
	$em_table->set_attribute('style', 'border:0px;margin-top:10px;width:100%');

	$img = new image('todos');
	$img->set_attribute('style', 'border:0px;margin-right:10px;width:32px;height:32px');

	$em_cell = new table_cell();
	$em_cell->set_attribute('valign','top');
	$em_cell->add_html_element($img);

	$em_row = new table_row();
	$em_row->add_cell($em_cell);

	$link = new hyperlink("javascript:goto_url('".$GO_MODULES->modules['todos']['url']."');",$lang_modules['todos']);

	$h2 = new html_element('h2',$link->get_html());

	$em_cell = new table_cell($h2->get_html());
	$em_cell->set_attribute('style','width:100%');
	$em_row->add_cell($em_cell);
	$em_table->add_row($em_row);

	$em_row = new table_row();
	$em_row->add_cell(new table_cell('&nbsp;'));

	$table = new table();
	$table->set_attribute('style','width:100%;white-space:nowrap;');


	while($cal->next_record())
	{
		$now = get_gmt_time();

		$day = date('j', $now);
		$month = date('n', $now);
		$year = date('Y',$now);

		$day_start = mktime(0,0,0,$month, $day, $year);
		$day_end = mktime(0,0,0,$month, $day+1, $year);

		$start_time = $cal->f('occurence_time');
		$end_time = $start_time + $cal->f('end_time') - $cal->f('start_time');

		$timezone_offset = get_timezone_offset($start_time)*3600;
		$start_time += $timezone_offset;

		$timezone_offset = get_timezone_offset($end_time)*3600;
		$end_time += $timezone_offset;


		$title = '';
		$date_format = '';

		if (($start_time< $day_start) || $end_time > $day_end) {
			$date_format = $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'];
		} else {
			$date_format = $_SESSION['GO_SESSION']['time_format'];
		}
		$title = $sc_start_at.': '.date($date_format, $start_time).
		'<br />'.$sc_end_at.': '.date($date_format, $end_time);

		if (isset($GO_MODULES->modules['addressbook']) &&
		$GO_MODULES->modules['addressbook']['read_permission'] &&
		$cal->f('contact_id') > 0 && $contact = $ab->get_contact($cal->f('contact_id'))) {

			$middle_name = $contact['middle_name'] == '' ? '' : $contact['middle_name'].' ';
			$contact_name = $contact['first_name'].' '.$middle_name.$contact['last_name'];
			if ($title != '') {
				$title .= '<br />';
			}
			$title .= $sc_client.": ".htmlspecialchars($contact_name);

			if ($contact['company_name'] != '') {
				$title .= " (".htmlspecialchars($contact['company_name']).")";
			}
		}
		if ($cal->f('location') != '') {
			if ($title != '') {
				$title .= '<br />';
			}
			$title .= $sc_location.": ".htmlspecialchars($cal->f('location'));
		}

		$event_cal_count = $cal2->get_calendars_from_event($cal->f('id'));
		if ($title != '') {
			$title .= '<br />';
		}
		$title .= "$cal_event_calendars: ";
		$first = true;
		while ($cal2->next_record()) {
			if ($first) {
				$first = false;
			} else {
				$title .= ' ,';
			}
			$title .= htmlspecialchars($cal2->f('name'));
		}

		$div = new html_element('div', '&nbsp;');
		$div->set_attribute('class', 'summary_icon');
		$div->set_attribute('style', 'background-color: #'.$cal->f('background'));

		$link = new hyperlink('javascript:goto_url(\''.$GO_MODULES->modules['calendar']['url'].'event.php?event_id='.$cal->f('id').'\');',
		$div->get_html().date($date_format, $start_time).'&nbsp;'.
		htmlspecialchars($cal->f('name')));
		$link->set_tooltip(new tooltip($title, $cal->f('name')));

		if($cal->f('completion_time') > 0)
		{
			$div->set_attribute('class', 'event_completed');
		}elseif($cal->f('todo') == '1' && $now>$cal->f('end_time'))
		{
			$div->set_attribute('class', 'event_late');
		}

		$row = new table_row();
		$cell = new table_cell($link->get_html());
		$cell->set_attribute('style','width:100%');
		$row->add_cell($cell);

		$select = new select('snooze['.$cal->f('id').']', '300');
		$select->add_value('300', '5 '.$sc_mins);
		$select->add_value('900', '15 '.$sc_mins);
		$select->add_value('1800', '30 '.$sc_mins);
		$select->add_value('3600', '1 '.$sc_hour);
		$select->add_value('7200', '2 '.$sc_hours);
		$select->add_value('86400', '1 '.$sc_day);
		$select->add_value('604800','1'.$sc_week);

		$row->add_cell(new table_cell($select->get_html()));

		$cell = new table_cell();

		$button = new button($strSnooze, "javascript:update_event_reminder(".$cal->f('id').",'snooze');");
		$button->set_attribute('style','margin-top:0px;');
		$cell->add_html_element($button);

		$button= new button($strDismiss, "javascript:update_event_reminder(".$cal->f('id').",'dismiss');");
		$button->set_attribute('style','margin-top:0px;');
		$cell->add_html_element($button);

		$row->add_cell($cell);
		$table->add_row($row);

		$cal2->reminder_mail_sent($GO_USERS->f('id'), $cal->f('id'));


	}

	$em_row->add_cell(new table_cell($table->get_html()));
	$em_table->add_row($em_row);
	$form->add_html_element($em_table);


}

if($event_count+$todo_count>1)
{
	$button = new button($cal_dismiss_all, "javascript:document.reminder_form.task.value='dismiss_all';document.reminder_form.submit();");
	$form->add_html_element($button);
}

if($_SERVER['REQUEST_METHOD'] != 'POST' && (!isset($_SESSION['reminder_beep']) || $_SESSION['reminder_beep']))
{
	echo '<object width="1" height="1">'.
	'<param name="movie" value="'.$GO_THEME->sounds['reminder'].'">'.
	'<param name="loop" value="false">'.
	'<embed src="'.$GO_THEME->sounds['reminder'].'" loop="false" width="1" height="1">'.
	'</embed>'.
	'</object>';
}

?>
<script type="text/javascript">

<?php
if(!$stay_open)
{
	$_SESSION['reminder_beep']=true;
	echo 'window.close();';
}else {
	$_SESSION['reminder_beep']=false;
}

?>
function update_event_reminder(event_id, task)
{
	document.reminder_form.task.value=task;
	document.reminder_form.event_id.value=event_id;
	document.reminder_form.submit();
}

function update_todo_reminder(todo_id, task)
{
	document.reminder_form.task.value=task;
	document.reminder_form.todo_id.value=todo_id;
	document.reminder_form.submit();
}

function goto_url(url)
{
	if (opener.parent.main)
	{
		if(url.indexOf("?") > 0)
		{
			url = url+'&return_to='+escape(opener.parent.main.location)
		}else
		{
			url = url+'?return_to='+escape(opener.parent.main.location)
		}
		opener.parent.main.location=url;
		opener.parent.main.focus();
	}else
	{
		window.open('<?php echo $GO_CONFIG->full_url.'index.php?return_to='; ?>'+escape(url), 'groupoffice','scrollbars=yes,resizable=yes,status=yes');

	}
	document.location=document.location;
}
</script>
<?php


echo $form->get_html();

require_once($GO_THEME->theme_path."footer.inc");
