<?
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
//$GO_MODULES->authenticate('calendar');
require_once($GO_LANGUAGE->get_language_file('calendar'));

$cal_settings['merged_view'] = '1';
$cal_settings['default_cal_id'] = '5';
$cal_settings['default_view_id'] = '0';
$cal_settings['view_type'] = 'grid';
$cal_settings['weekview'] = '';
$cal_settings['refresh_rate'] = '0';
$cal_settings['show_days'] = 6;
$_SESSION['GO_SESSION']['first_weekday'] = 1;
$todo = '0';
$task = '';
$link_back = '/';
$read_only = true;
$clickable = false;
$offset = 6;

$calendar_id = isset($_REQUEST['id']) ? $_REQUEST['id']  : $cal_settings['default_cal_id'];

load_basic_controls();
load_control('datatable');
load_control('tooltip');

require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
$cal = new calendar();

if(isset($_POST['merged_view']) && $_POST['merged_view'] != $cal_settings['merged_view'])
{
	$cal_settings['merged_view'] = $_POST['merged_view'];	
	$update_settings=true;
}

$view_id = isset($_REQUEST['view_id']) ? $_REQUEST['view_id'] : $cal_settings['default_view_id'];

//if a view is given then display view. Otherwise open a calendar
if($view_id > 0)
{
  $view = $cal->get_view($view_id);
  if ($view)
  {	
    $title = $view['name'];
    $calendar_id = 0;
    $cal_start_hour = $view['start_hour'];
    $cal_end_hour = $view['end_hour'];  
  }
}
if(!isset($view) || !$view)
{
  //get the calendar properties and check permissions
  if ($calendar = $cal->get_calendar($calendar_id))
  {
		$title = $calendar['name'];
		$cal_start_hour = $calendar['start_hour'];
		$cal_end_hour = $calendar['end_hour'];
  }
}

if($calendar_id > 0)
{
	$calendar_start_hour = $calendar['start_hour'];
	$calendar_end_hour = $calendar['end_hour'];
	$time_interval=$calendar['time_interval'];
}else
{
	$calendar_start_hour = $view['start_hour'];
	$calendar_end_hour = $view['end_hour'];	
	$time_interval=$view['time_interval'];
}

$GO_HEADER['head'] = tooltip::get_header();
$GO_HEADER['head'] .= $GO_THEME->get_stylesheet('calendar');


if(isset($_POST['view_type']) && $_POST['view_type'] != $cal_settings['view_type'])
{
	$cal_settings['view_type'] = $_POST['view_type'];
	$update_settings=true;
}

if($view_id > 0 && $cal_settings['merged_view']=='0')
{
	require_once($GO_MODULES->modules['calendar']['class_path'].'calendar_groupview.class.inc');
	$cal_view = new calendar_groupview('calendar_view', 'calendar_form', $read_only, $cal_settings['show_days']);
}else
{
	if($cal_settings['view_type'] == 'list')
	{
		require_once($GO_MODULES->modules['calendar']['class_path'].'calendar_listview.class.inc');
		$cal_view = new calendar_listview('calendar_view','calendar_form', $read_only);
	}elseif($cal_settings['show_days'] == '31')
	{
		require_once($GO_MODULES->modules['calendar']['class_path'].'calendar_monthview.class.inc');
		$cal_view = new calendar_monthview('calendar_view','calendar_form', $read_only);
		if($print)
		{
			$cal_view->print=true;
		}
	}else
	{
		require_once($GO_MODULES->modules['calendar']['class_path'].'calendar_view.class.inc');
		$cal_view = new calendar_view('calendar_view', $calendar_start_hour, $calendar_end_hour, $time_interval,'calendar_form', $read_only, $clickable, $offset);		
	}
}

if(isset($update_settings))
{
	$cal->update_settings($cal_settings);
}



$link_back = isset($_REQUEST['link_back']) ? $_REQUEST['link_back'] : $_SERVER['PHP_SELF'].'?year='.$cal_view->year.'&month='.$cal_view->month.'&day='.$cal_view->day;
$cal_view->set_return_to($link_back);

$GO_HEADER['head'] .= $cal_view->get_header();

if($cal_settings['refresh_rate'] > 0 && !$print)
{
	$GO_HEADER['auto_refresh']['interval'] = $cal_settings['refresh_rate'];
	$GO_HEADER['auto_refresh']['action'] = 'javascript:document.calendar_form.submit();';
}

require_once($GO_THEME->theme_path."header.inc");

$form = new form('calendar_form');
$form->add_html_element(new input('hidden','task'));

if(isset($_REQUEST['link_back']))
{
	$form->add_html_element(new input('hidden','link_back', $link_back));
}
$form->add_html_element(new input('hidden','calendar_id', $calendar_id, false));
$form->add_html_element(new input('hidden','view_id', $view_id, false));
$form->add_html_element(new input('hidden','todo', $todo, false));
$form->add_html_element(new input('hidden','print', 'false', false));
$form->add_html_element(new input('hidden','view_type', $cal_settings['view_type'], false));

$table = new table();
$table->set_attribute('cellpadding','0');
$table->set_attribute('cellspacing','0');
$table->set_attribute('style','width:100%');

$row = new table_row();

$link = new hyperlink("javascript:popup('select_calendar.php', '400','400');",$cal_open_calendar);
$link->set_attribute('class','normal');

$link_text = isset($calendar) ? $calendar['name'] : $view['name'];
$cell = new table_cell('<b>'.$link_text.'</b> ('.$link->get_html().')');

$row->add_cell($cell);
$row = new table_row();

$cell = new table_cell();
$cell->set_attribute('style','text-align:right');

if(isset($view) && $view)
{
  //$calendars = $cal->get_view_calendars($view_id);
  //if(count($calendars) > 1)
 // {
    $select = new select("merged_view", $cal_settings['merged_view']);
    $select->set_attribute('onchange', 'javascript:document.forms[0].submit();');
    $select->add_value('0', $cal_view_emerged);
    $select->add_value('1', $cal_view_merged);   
    $cell->add_html_element($select);
  //}    
}

	$select = new select("offset", $cal_view->offset);
	$select->set_attribute('onchange', 'javascript:'.$cal_view->get_change_view_handler($cal_view->clicked_day, $cal_view->clicked_month, $cal_view->clicked_year, 'this.value'));

	$interval = 7;

$span = new html_element('span');

$fwd_img = new image('forward_small');
$fwd_img->set_attribute('style','width:16px;height:16px;border:0px');
$fwd_img->set_attribute('align','absmiddle');

$back_img = new image('back_small');
$back_img->set_attribute('style','width:16px;height:16px;border:0px');
$back_img->set_attribute('align','absmiddle');

if($cal_settings['show_days'] == '31')
{
	$span->add_html_element(new hyperlink('javascript:'.$cal_view->get_date_handler(1, $cal_view->clicked_month-1, $cal_view->clicked_year), $back_img->get_html()));
	$span->innerHTML .= '&nbsp;&nbsp;'.$months[$cal_view->clicked_month-1].', '.$cal_view->clicked_year.'&nbsp;&nbsp;';
	$span->add_html_element(new hyperlink('javascript:'.$cal_view->get_date_handler(1, $cal_view->clicked_month+1, $cal_view->clicked_year), $fwd_img->get_html()));
}elseif($cal_settings['show_days'] == '7' || $cal_settings['show_days'] == '5')
{
	$span->add_html_element(new hyperlink('javascript:'.$cal_view->get_date_handler($cal_view->day-$interval, $cal_view->month, $cal_view->year), $back_img->get_html()));
	
	$span->innerHTML .= '&nbsp;&nbsp;'.$sc_week.' '.date('W', $cal_view->start_time).'&nbsp;&nbsp;';
	
	
	$span->add_html_element(new hyperlink('javascript:'.$cal_view->get_date_handler($cal_view->day+$interval, $cal_view->month, $cal_view->year), $fwd_img->get_html()));
}else
{
	$span->add_html_element(new hyperlink('javascript:'.$cal_view->get_date_handler($cal_view->day-$interval, $cal_view->month, $cal_view->year), $back_img->get_html()));
	
	$span->innerHTML .= '&nbsp;&nbsp;'.date($_SESSION['GO_SESSION']['date_format'], $cal_view->start_time).'&nbsp;';
	
	if($cal_settings['show_days'] > 1)
	{
		$span->innerHTML .= '-&nbsp;'.date($_SESSION['GO_SESSION']['date_format'], $cal_view->end_time-86400).'&nbsp;&nbsp;';	
	}
	$span->add_html_element(new hyperlink('javascript:'.$cal_view->get_date_handler($cal_view->day+$interval, $cal_view->month, $cal_view->year), $fwd_img->get_html()));
}
$span->set_attribute('style','font-size:14px;margin-left:10px;');
	
$cell->add_html_element($span);


if($view_id > 0 && $cal_settings['merged_view'] == '1' && $view['event_colors_override'] == '0')
{
	$h3 = new html_element('h3',htmlspecialchars($sc_calendars));
	$h3->set_attribute('style', 'margin-top:5px');
	$cell->add_html_element($h3);
	$div = new html_element('div', '&nbsp;');
	$div->set_attribute('class', 'summary_icon');
	$div->set_attribute('style', 'background-color: #FFFFCC');		
	$legendItem = new html_element('div', $div->get_html().' '.htmlspecialchars($cal_multiple_calendars));
	$legendItem->set_attribute('style','margin-bottom:3px;');
	$cell->add_html_element($legendItem);
	
	$calendars = $cal->get_view_calendars($view_id);
	foreach($calendars as $calendar)
	{
		$div = new html_element('div', '&nbsp;');
		$div->set_attribute('class', 'summary_icon');
		$div->set_attribute('style', 'background-color: #'.$calendar['background']);		
		$legendItem = new html_element('div', $div->get_html().' '.htmlspecialchars($calendar['name']));
		$legendItem->set_attribute('style','margin-bottom:3px;');
		$cell->add_html_element($legendItem);
	}
}elseif($cal_settings['view_type'] == 'grid' && $calendar_id>0 && $cal->get_calendar_backgrounds($calendar_id))
{
	$h3 = new html_element('h3',htmlspecialchars($cal_background_colors));
	$h3->set_attribute('style', 'margin-top:5px');
	$cell->add_html_element($h3);
	
	while($cal->next_record())
	{
		$cal_view->set_background_color($cal->f('color'), $cal->f('weekday'), $cal->f('start_time'), $cal->f('end_time'));
		
		$div = new html_element('div', '&nbsp;');
		$div->set_attribute('class', 'summary_icon');
		$div->set_attribute('style', 'background-color: #'.$cal->f('color'));		
		$legendItem =new html_element('div', $div->get_html().' '.htmlspecialchars($cal->f('name')));
		$legendItem->set_attribute('style','margin-bottom:3px;');
		$cell->add_html_element($legendItem);			
	}
}
$row->add_cell($cell);


if($view_id > 0 && $cal_settings['merged_view']=='0')
{
	$calendars = $cal->get_view_calendars($view_id);
	foreach($calendars as $calendar)
	{
		$cal_view->add_calendar($calendar);
		
		/*$cal->get_calendar_backgrounds($calendar['id']);
		while($cal->next_record())
		{
			$cal_view->set_background_color($calendar['id'], $cal->f('color'), $cal->f('weekday'), $cal->f('start_time'), $cal->f('end_time'));
		}*/
			
		$events = $cal->get_events_in_array($calendar['id'], 0, 0, 
			local_to_gmt_time($cal_view->start_time), local_to_gmt_time($cal_view->end_time),true,false,true,false,true);
		foreach($events as $event)
		{
			$cal_view->add_event($calendar['id'], $event);
		}
	}
}else
{
	$events = $cal->get_events_in_array($calendar_id, $view_id, 0, 
			local_to_gmt_time($cal_view->start_time), local_to_gmt_time($cal_view->end_time),true,false,true,false,true);
	
	foreach($events as $event)
	{
		if($view_id > 0 && $view['event_colors_override'] == '0')
		{
			$event['background'] = (isset($event['calendars']) && count($event['calendars']) > 1) ? 'FFFFCC' : $cal->get_view_color($view_id, $event['id']);
			//$event['background'] = $cal->get_view_color($view_id, $event['id']);
		}		
		$cal_view->add_event($event);
	}
	
}

$table->add_row($row);
$row = new table_row();

$cell = new table_cell($cal_view->get_html());
$cell->set_attribute('style', 'vertical-align:top;width:100%');
$row->add_cell($cell);
$table->add_row($row);

$form->add_html_element($table);
echo $form->get_html();

require_once($GO_THEME->theme_path."footer.inc");
