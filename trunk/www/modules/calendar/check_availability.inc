<?php
/*
Copyright SoftSys 2004
Author: Georg Lorenz <georg@lonux.de>
Version: 1.0 Release date: 29 October 2004

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

if(!isset($_POST['task']))
{
	$_POST['task'] = 'availability';
}

if(!isset($_POST['calendars']))
{
	if(isset($_REQUEST['view_id']) && $_REQUEST['view_id'] > 0)
	{
		$view_calendars = $cal->get_view_calendars($_REQUEST['view_id']);
		foreach($view_calendars as $view_calendar)
		{
			$_POST['calendars'][]=$view_calendar['id'];
		}
	}else
	{
		$_POST['calendars'][]=$calendar_id;
	}
}
$_POST['day'] = isset($_POST['day']) ? $_POST['day'] : $day;
$_POST['month'] = isset($_POST['month']) ? $_POST['month'] : $month;
$_POST['year'] = isset($_POST['year']) ? $_POST['year'] : $year;

$_POST['end_hour'] = isset($_POST['end_hour']) ? $_POST['end_hour'] : $hour;
$_POST['end_min'] = isset($_POST['end_min']) ? $_POST['end_min'] : $min;
$_POST['start_hour'] = isset($_POST['start_hour']) ? $_POST['start_hour'] : $hour;
$_POST['start_min'] = isset($_POST['start_min']) ? $_POST['start_min'] : $min;



function set_availability($availability, $from_hour, $to_hour, $event='', $current_date_time=0)
{
	if($event['all_day_event'] == '1')
	{
		for($hour=$from_hour;$hour<=$to_hour;$hour++)
		{
			for($min=0;$min<=45;$min+=15)
			{
				$availability[$hour][$min] = false;
			}
		}
	}else
	{
		$date = getdate($current_date_time);

		$timezone_offset = get_timezone_offset($event['start_time'])*3600;
		$event['start_time'] += $timezone_offset;
		$timezone_offset = get_timezone_offset($event['end_time'])*3600;
		$event['end_time'] += $timezone_offset;

		$event_start = getdate($event['start_time']);
		$event_end = getdate($event['end_time']);
		if($event_start['mday'] == $date['mday'] && $event_start['mon'] == $date['mon'] && $event_start['year'] == $date['year'])
		{
			if($event_start['mday'] == $event_end['mday'] && $event_start['mon'] == $event_end['mon'] && $event_start['year'] == $event_end['year'])
			{
				if($event_start['hours'] == $event_end['hours'])
				{
					for($min=0;$min<$event_end['minutes'];$min+=15)
					{
						if($event_start['minutes'] <= $min && $event_end['minutes'] >= $min)
						{
							$availability[$event_start['hours']][$min] = false;
						}
					}
				}else
				{
					$go_on = true;
					for($index=$event_start['hours'];$index<$event_end['hours'];$index++)
					{
						if($index > $to_hour)
						{
							$go_on = false;
							break;
						}
						for($min=0;$min<=45;$min+=15)
						{
							if($event_start['hours'] == $index && $event_start['minutes'] <= $min)
							{
								$availability[$index][$min] = false;
							}elseif($event_start['hours'] < $index)
							{
								$availability[$index][$min] = false;
							}
						}
					}
					if($go_on)
					{
						for($min=0;$min<$event_end['minutes'];$min+=15)
						{
							if($event_end['minutes'] >= $min)
							{
								$availability[$event_end['hours']][$min] = false;
							}
						}
					}
				}
			}else
			{
				for($hour=$event_start['hours'];$hour<=$to_hour;$hour++)
				{
					for($min=0;$min<=45;$min+=15)
					{
						if($event_start['hours'] == $hour && $event_start['minutes'] <= $min)
						{
							$availability[$hour][$min] = false;
						}elseif($event_start['hours'] < $hour)
						{
							$availability[$hour][$min] = false;
						}
					}
				}
			}
		}elseif($event_end['mday'] == $date['mday'] && $event_end['mon'] == $date['mon'] &&	$event_end['year'] == $date['year'])
		{
			for($hour=$from_hour;$hour<=$event_end['hours'];$hour++)
			{
				for($min=0;$min<=45;$min+=15)
				{
					if($event_end['hours'] == $hour && $event_end['minutes'] > $min)
					{
						$availability[$hour][$min] = false;
					}elseif($event_end['hours'] > $hour)
					{
						$availability[$hour][$min] = false;
					}
				}
			}
		}else
		{
			for($hour=$from_hour;$hour<$to_hour;$hour++)
			{
				for($min=0;$min<=45;$min+=15)
				{
					$availability[$hour][$min] = false;
				}
			}
		}
	}
	return $availability;
}

//$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$participants = smart_stripslashes($event['to']);
//$calendar_id = isset($event['calendars']) ? $event['calendars'][0] : $calendar_id;


$choice['type'] = 'participants';
$choice['value'] = $sc_participants;
if(isset($_REQUEST['calendar_participants']))
{
	$choice = explode(':', $_REQUEST['calendar_participants']);
	$choice['type'] = $choice[0];
	$choice['value'] = $choice[1];
	if($choice['type'] == 'calendar')
	{
		$calendar_id = $choice['value'];
		$view_id = 0;
	}elseif($choice['type'] == 'view')
	{
		$view_id = $choice['value'];
		$calendar_id = 0;
	}
}

//$period_offset = date_to_unixtime($start_date);
$from_hour = 7;
$to_hour = 20;

$availability_cal = array();
$availability_member = array();
$availability_merged = array();

$member_rows = '';
$cal_row = '';
if(isset($_POST['day']))
{
	$new_time = mktime(0, 0, 0,$_POST['month'], $_POST['day'], $_POST['year']);
}else
{
	$new_time = date_to_unixtime($event['start_date']);
}

$start_date = date($_SESSION['GO_SESSION']['date_format'], $new_time);
$end_date = $start_date;

$date_index = date('Ymd', $new_time);
$date = getdate($new_time);

$day = $date['mday'];
$month = $date['mon'];
$year = $date['year'];

$interval_start_time = local_to_gmt_time(mktime(0,0,0,$date['mon'], $date['mday'], $date['year']));
$interval_end_time = local_to_gmt_time(mktime(0,0,0,$date['mon'], $date['mday']+1, $date['year']));

$availability_table = new table();
$availability_table->set_attribute('class','availability');
$availability_table->set_attribute('style','width:100%');

$row = new table_row();

$th = new table_heading('<h2>'.$sc_participants.'</h2>');
$th->set_attribute('class','caption');
$th->set_attribute('colspan','2');
$row->add_cell($th);
$availability_table->add_row($row);

if($current_cal = $cal->get_calendar($calendar_id))
{
	$from_hour = $current_cal['start_hour'];
	$to_hour = $current_cal['end_hour'];
}

for($hour=$from_hour;$hour<=$to_hour;$hour++)
{
	for($min=0;$min<=45;$min+=15)
	{
		$availability_merged[$hour][$min] = true;
	}
}



$user_profile = $GO_USERS->get_user($GO_SECURITY->user_id);
$tmp_participants = empty($participants) ? $user_profile['email'] : $user_profile['email'].','.$participants;

$participants_array = $RFC822->explode_address_list($tmp_participants);

for($i=0;$i<sizeof($participants_array);$i++)
{
	if(!empty($participants_array[$i]))
	{
		$id = 0;

		if($member_profile = $GO_USERS->get_user_by_email(smart_addslashes($participants_array[$i])))
		{
			$id = $member_profile["id"];

			$member_events = false;
			if($id)
			{
				$member_events = $cal->get_events_in_array(0, 0, $id,
				$interval_start_time, $interval_end_time,false,false,true,false,false,true);
			}

			$row = new table_row();

			$img = new image('user_small');
			$img->set_attribute('style','border:0px;height:16px;width:16px;margin-right:5px;');
			$img->set_attribute('align','absmiddle');


			if($i == 0)
			{
				$th = new table_heading($img->get_html().$cal_own_availability);
			}else
			{
				$th = new table_heading($img->get_html().get_name_from_string(stripslashes($participants_array[$i])));
			}
			$th->set_attribute('class','caption');
			$th->set_attribute('style','white-space:nowrap');
			$row->add_cell($th);

			$cell = new table_cell();
			$cell->set_attribute('style','width:100%');


			$subtable = new table();
			$subtable->set_attribute('style','width:100%');

			$subrow = new table_row();

			for($hour=$from_hour;$hour<=$to_hour;$hour++)
			{
				for($min=0;$min<=45;$min+=15)
				{
					$availability_member[$hour][$min] = true;
				}
				$tooltip = $hour.':00';

				$link = new hyperlink('javascript:goto_event(\''.$hour.'\',\''.($hour+1).'\',\'00\');', $hour);
				$link->set_attribute('class','hour');

				$subcell = new table_cell($link->get_html());
				$subcell->set_attribute('class','hour');
				$subcell->set_attribute('colspan','4');
				$subcell->set_tooltip(new tooltip($tooltip, $cal_own_availability, 'WRAP'));

				$subrow->add_cell($subcell);
			}
			$subtable->add_row($subrow);

			while ($member_event = array_shift($member_events))
			{
				$availability_member = set_availability($availability_member, $from_hour, $to_hour,$member_event, $new_time);
			}

			$subrow = new table_row();

			for($hour=$from_hour;$hour<=$to_hour;$hour++)
			{
				for($min=0;$min<=45;$min+=15)
				{
					$minute = strlen($min) == 1 ? '0'.$min : $min;
					$tooltip = $hour.':'.$minute;

					$subcell = new table_cell('&nbsp;&nbsp;&nbsp;');
					$subcell->set_tooltip(new tooltip($tooltip, $cal_own_availability, 'WRAP'));
					if($availability_member[$hour][$min])
					{
						$subcell->set_attribute('class','available');

						$link = new hyperlink('javascript:goto_event(\''.$hour.'\',\''.($hour+1).'\',\''.$min.'\');', '&nbsp;&nbsp;&nbsp;');
						$link->set_attribute('class','hour');
						$subcell->innerHTML = $link->get_html();
					}else
					{
						$subcell->set_attribute('class','unavailable');
					}
					$availability_merged[$hour][$min] = !$availability_member[$hour][$min] ? $availability_member[$hour][$min]: $availability_merged[$hour][$min];
					$subrow->add_cell($subcell);
				}
			}
			$subtable->add_row($subrow);


			$cell->add_html_element($subtable);
			$row->add_cell($cell);
			$availability_table->add_row($row);
		}
	}
}






$calendars = isset($_POST['calendars']) ? $_POST['calendars'] : array();
if(count($calendars))
{
	$row = new table_row();
	$th = new table_heading('<h2>'.$sc_calendars.'</h2>');
	$th->set_attribute('class','caption');
	$th->set_attribute('colspan','2');
	$row->add_cell($th);
	$availability_table->add_row($row);
	add_calendars($calendars);
}

$resources = isset($_POST['resources']) ? $_POST['resources'] : array();
if(count($resources))
{
	$row = new table_row();
	$th = new table_heading('<h2>'.$cal_resources.'</h2>');
	$th->set_attribute('class','caption');
	$th->set_attribute('colspan','2');
	$row->add_cell($th);
	$availability_table->add_row($row);
	add_calendars($resources);
}

function add_calendars($calendars)
{
	global $availability_table, $cal, $to_hour, $from_hour, $interval_start_time, $interval_end_time, $current_cal, $availability_merged, $new_time, $day, $month,$year;

	foreach($calendars as $cal_id)
	{
		$calendar= $cal->get_calendar($cal_id);

		for($hour=$from_hour;$hour<=$to_hour;$hour++)
		{
			for($min=0;$min<=45;$min+=15)
			{
				$availability_cal[$hour][$min] = true;
			}
		}

		$cal_events = $cal->get_events_in_array($calendar['id'], 0, 0,
		$interval_start_time, $interval_end_time,false,false,true,false,false,true);

		$row = new table_row();

		$img = new image('calendar_small');
		$img->set_attribute('style','border:0px;height:16px;width:16px;margin-right:5px;');
		$img->set_attribute('align','absmiddle');

		$th = new table_heading($img->get_html().$calendar['name']);
		$th->set_attribute('style','white-space:nowrap');
		$th->set_attribute('class','caption');
		$row->add_cell($th);

		$cell = new table_cell();
		$cell->set_attribute('style','width:100%');


		$subtable = new table();
		$subtable->set_attribute('style','width:100%');

		$subrow = new table_row();



		for($hour=$from_hour;$hour<=$to_hour;$hour++)
		{
			$tooltip = $hour.':00';

			$link = new hyperlink('javascript:goto_event_single_calendar('.$calendar['id'].', \''.$hour.'\',\''.($hour+1).'\',\'00\');', $hour);
			$link->set_attribute('class','hour');

			$subcell = new table_cell($link->get_html());
			$subcell->set_attribute('class','hour');
			$subcell->set_attribute('colspan','4');
			$subcell->set_tooltip(new tooltip($tooltip, $calendar['name'], 'WRAP'));

			$subrow->add_cell($subcell);
		}
		$subtable->add_row($subrow);

		while ($cal_event = array_shift($cal_events))
		{
			$availability_cal = set_availability($availability_cal, $from_hour, $to_hour, $cal_event, $new_time);
		}

		$subrow = new table_row();

		$background_colors=array();
		$cal->get_calendar_backgrounds($calendar['id']);
		while($cal->next_record())
		{
			for($i=$cal->f('start_time');$i<$cal->f('end_time');$i+=900)
			{
				//echo $cal->f('weekday').gmdate('Gi', $i).'<br />';
				$background_colors[$cal->f('weekday').gmdate('Gi', $i)]=$cal->f('color');
			}
		}

		//foreach($availability_cal as $hour => $value)
		for($hour=$from_hour;$hour<=$to_hour;$hour++)
		{
			for($min=0;$min<=45;$min+=15)
			{
				$minute = strlen($min) == 1 ? '0'.$min : $min;
				$tooltip = $hour.':'.$minute;

				$subcell = new table_cell('&nbsp;&nbsp;&nbsp;');
				$subcell->set_tooltip(new tooltip($tooltip, $calendar['name'], 'WRAP'));
				//echo $year.$month.$day.' '.$hour.':'.$min.'<br>';

				$time = mktime($hour, $min, 0, $month, $day, $year);
				//echo date('Ymd G:i', $time).'<br>';
				if(isset($background_colors[date('wGi', $time)]))
				{
					$subcell->set_attribute('style','background-color:#'.$background_colors[date('wGi', $time)]);
				}

				if($availability_cal[$hour][$min])
				{
					$subcell->set_attribute('class','available');

					$link = new hyperlink('javascript:goto_event_single_calendar('.$calendar['id'].',\''.$hour.'\',\''.($hour+1).'\',\''.$min.'\');', '&nbsp;&nbsp;&nbsp;');
					$link->set_attribute('class','hour');
					$subcell->innerHTML = $link->get_html();
				}else
				{
					$subcell->set_attribute('class','unavailable');
				}
				$availability_merged[$hour][$min] = !$availability_cal[$hour][$min] ? $availability_cal[$hour][$min] : $availability_merged[$hour][$min];
				$subrow->add_cell($subcell);
			}
		}
		$subtable->add_row($subrow);


		$cell->add_html_element($subtable);
		$row->add_cell($cell);
		$availability_table->add_row($row);
	}
}

if((count($participants_array) + count($calendars)+count($resources))>1)
{
	$row = new table_row();
	$th = new table_heading('<h2>'.$cal_merged_overview.'</h2>');
	$th->set_attribute('class','caption');
	$th->set_attribute('colspan','2');
	$row->add_cell($th);
	$availability_table->add_row($row);

	$row = new table_row();

	$img = new image('users_small');
	$img->set_attribute('style','border:0px;height:16px;width:16px;margin-right:5px;');
	$img->set_attribute('align','absmiddle');

	$th = new table_heading($img->get_html().$cal_merged_overview);
	$th->set_attribute('style','white-space:nowrap');
	$th->set_attribute('class','caption');
	$row->add_cell($th);

	$cell = new table_cell();
	$cell->set_attribute('style','width:100%');


	$subtable = new table();
	$subtable->set_attribute('style','width:100%');

	$subrow = new table_row();

	for($hour=$from_hour;$hour<=$to_hour;$hour++)
	{
		$tooltip = $hour.':00';

		$link = new hyperlink('javascript:goto_event(\''.$hour.'\',\''.($hour+1).'\',\'00\');', $hour);
		$link->set_attribute('class','hour');

		$subcell = new table_cell($link->get_html());
		$subcell->set_attribute('class','hour');
		$subcell->set_attribute('colspan','4');
		$subcell->set_tooltip(new tooltip($tooltip, $cal_merged_overview, 'WRAP'));

		$subrow->add_cell($subcell);
	}
	$subtable->add_row($subrow);

	$subrow = new table_row();

	foreach($availability_merged as $hour => $value)
	{
		$tooltip = $hour.':00';
		for($min=0;$min<=45;$min+=15)
		{
			$minute = strlen($min) == 1 ? '0'.$min : $min;
			$tooltip = $hour.':'.$minute;

			$subcell = new table_cell('&nbsp;&nbsp;&nbsp;');
			$subcell->set_tooltip(new tooltip($tooltip,$cal_merged_overview, 'WRAP'));
			if($value[$min])
			{
				$subcell->set_attribute('class','available');

				$link = new hyperlink('javascript:goto_event(\''.$hour.'\',\''.($hour+1).'\',\''.$min.'\');', '&nbsp;&nbsp;&nbsp;');
				$link->set_attribute('class','hour');
				$subcell->innerHTML = $link->get_html();
			}else
			{
				$subcell->set_attribute('class','unavailable');
			}
			$subrow->add_cell($subcell);
		}
	}
	$subtable->add_row($subrow);

	$cell->add_html_element($subtable);
	$row->add_cell($cell);
	$availability_table->add_row($row);
}

$form->add_html_element(new input('hidden','single_calendar_id','0', false));

if(!isset($_POST['day']))
{
	$form->add_html_element(new input('hidden','day',$day, false));
	$form->add_html_element(new input('hidden','month',$month, false));
	$form->add_html_element(new input('hidden','year',$year, false));
}
unset($_POST['single_calendar_id'],$_POST['event_table'], $_POST['end_date'], $_POST['start_date'], $_POST['ignore_conflicts']);
$form->innerHTML .= array_to_form($_POST);


$table = new table();

$img_back = new image('back_small');
$img_back->set_attribute('style','border:0px;height:16px;width:16px;margin-right:5px;');
$img_back->set_attribute('align','absmiddle');

$img_forw = new image('forward_small');
$img_forw->set_attribute('style','border:0px;height:16px;width:16px;margin-left:5px;');
$img_forw->set_attribute('align','absmiddle');

$prev_day = date($_SESSION['GO_SESSION']['date_format'], mktime(0, 0, 0, $date["mon"], $date["mday"]-1, $date["year"]));
$next_day = date($_SESSION['GO_SESSION']['date_format'], mktime(0, 0, 0, $date["mon"], $date["mday"]+1, $date["year"]));

$link_back = new hyperlink('javascript:goto_date('.($date['mday']-1).', '.$date['mon'].', '.$date['year'].');',$img_back->get_html());
$link_forw = new hyperlink('javascript:goto_date('.($date['mday']+1).', '.$date['mon'].', '.$date['year'].');',$img_forw->get_html());

$row = new table_row();
$row->add_cell(new table_cell($strDate.':'));
$row->add_cell(new table_cell($link_back->get_html().$link_forw->get_html()));
$datepicker= new date_picker('start_date', $_SESSION['GO_SESSION']['date_format'], $start_date, '', '', 'onchange="javascript:date_picker(calendar);"');
$row->add_cell(new table_cell($datepicker->get_html()));
$form->add_html_element(new input('hidden','end_date',$end_date, false));
$table->add_row($row);
$form->add_html_element($table);
$form->add_html_element($availability_table);

if($cal->get_backgrounds())
{
	$h3 = new html_element('h3',htmlspecialchars($cal_background_colors));
	$h3->set_attribute('style', 'margin-top:10px');
	$form->add_html_element($h3);

	while($cal->next_record())
	{

		$div = new html_element('div', '&nbsp;');
		$div->set_attribute('class', 'summary_icon');
		$div->set_attribute('style', 'background-color: #'.$cal->f('color'));
		$legendItem =new html_element('div', $div->get_html().' '.htmlspecialchars($cal->f('name')));
		$legendItem->set_attribute('style','margin-bottom:3px;');
		$form->add_html_element($legendItem);
	}
}


$form->add_html_element(new button($cmdBack,"javascript:document.event_form.task.value='change_event';document.event_form.submit();"));

?>
<script type="text/javascript" language="javascript">
function date_picker(calendar) {
	// Beware that this function is called even if the end-user only
	// changed the month/year.  In order to determine if a date was
	// clicked you can use the dateClicked property of the calendar:
	if (calendar.dateClicked) {
		// OK, a date was clicked, redirect to /yyyy/mm/dd/index.php
		var y = calendar.date.getFullYear();
		var m = calendar.date.getMonth()+1;     // integer, 0..11
		var d = calendar.date.getDate();      // integer, 1..31

		goto_date(d,m,y);
	}
};

function goto_date(day, month, year)
{
	document.event_form.task.value='availability';
	document.event_form.day.value = day;
	document.event_form.month.value = month;
	document.event_form.year.value = year;
	document.event_form.submit();
}

function goto_event_single_calendar(calendar_id, start_hour,end_hour, min)
{
	document.event_form.single_calendar_id.value=calendar_id;
	document.event_form.task.value='change_event';
	document.event_form.start_hour.value=start_hour;
	document.event_form.end_hour.value=end_hour;
	document.event_form.start_min.value=min;
	document.event_form.end_min.value=min;
	document.event_form.submit();
}

function goto_event(start_hour,end_hour, min)
{
	document.event_form.task.value='change_event';
	document.event_form.start_hour.value=start_hour;
	document.event_form.end_hour.value=end_hour;
	document.event_form.start_min.value=min;
	document.event_form.end_min.value=min;
	document.event_form.submit();
}
</script>
