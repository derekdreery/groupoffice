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

$GO_CONFIG->set_help_url($cal_help_url);

load_basic_controls();
load_control('datatable');
load_control('tooltip');
load_control('date_picker');

require_once($GO_MODULES->class_path.'calendar.class.inc');
$cal = new calendar();

$cal_settings = $cal->get_settings($GO_SECURITY->user_id);

if(isset($_POST['merged_view']) && $_POST['merged_view'] != $cal_settings['merged_view'])
{
	$cal_settings['merged_view'] = $_POST['merged_view'];	
	$update_settings=true;
}

//Remember if we came from a object to put the event to
$todo =  isset ($_REQUEST['todo']) ? $_REQUEST['todo'] : '0';

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

$print = (isset($_REQUEST['print']) && $_REQUEST['print']=='true');

$view_id = isset($_REQUEST['view_id']) ? $_REQUEST['view_id'] : $cal_settings['default_view_id'];
$calendar_id = isset($_REQUEST['calendar_id']) ? $_REQUEST['calendar_id']  : $cal_settings['default_cal_id'];


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
    $read_only=false;
  }
}
if(!isset($view) || !$view)
{
  //get the calendar properties and check permissions
  if ($calendar = $cal->get_calendar($calendar_id))
  {
		$calendar['read_permission'] = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_read']);
		$calendar['write_permission'] = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_write']);
		$read_only=!$calendar['write_permission'];
		if (!$calendar['read_permission'] && !$calendar['write_permission'] )
		{
			$cal->set_default_view($GO_SECURITY->user_id, 0, 0);
		 	header('Location: '.$_SERVER['PHP_SELF']);
		 	exit();
		}
		$title = $calendar['name'];
		$cal_start_hour = $calendar['start_hour'];
		$cal_end_hour = $calendar['end_hour'];
		$calendar_id = $calendar['id'];
  }
}

if($calendar_id > 0)
{
	$cal->set_default_view($GO_SECURITY->user_id, $calendar_id, 0);
	$calendar_view_id = 'calendar:'.$calendar_id;
	
	$calendar_start_hour = $calendar['start_hour'];
	$calendar_end_hour = $calendar['end_hour'];
	$time_interval=$calendar['time_interval'];
}else
{
	$cal->set_default_view($GO_SECURITY->user_id, 0, $view_id, $cal_settings['merged_view']);
	$calendar_view_id = 'view:'.$view_id;
	
	$calendar_start_hour = $view['start_hour'];
	$calendar_end_hour = $view['end_hour'];	
	$time_interval=$view['time_interval'];
}

$GO_HEADER['head'] = tooltip::get_header();

if(isset($_POST['calender_view']['offset']) && $_POST['calender_view']['offset'] != $cal_settings['show_days'])
{
	$cal_settings['show_days'] = $_POST['calender_view']['offset'];	
	$update_settings=true;
}

if(isset($_POST['view_type']) && $_POST['view_type'] != $cal_settings['view_type'])
{
	$cal_settings['view_type'] = $_POST['view_type'];
	$update_settings=true;
}

if($view_id > 0 && $cal_settings['merged_view']=='0')
{
	require_once($GO_MODULES->modules['calendar']['class_path'].'calendar_groupview.class.inc');
	$cal_view = new calendar_groupview('calender_view', 'calendar_form', $read_only);
}else
{
	if($cal_settings['view_type'] == 'list')
	{
		require_once($GO_MODULES->modules['calendar']['class_path'].'calendar_listview.class.inc');
		$cal_view = new calendar_listview('calender_view','calendar_form', $read_only);
	}elseif($cal_settings['show_days'] == '31')
	{
		require_once($GO_MODULES->modules['calendar']['class_path'].'calendar_monthview.class.inc');
		$cal_view = new calendar_monthview('calender_view','calendar_form', $read_only);
		if($print)
		{
			$cal_view->print=true;
		}
	}else
	{
		require_once($GO_MODULES->modules['calendar']['class_path'].'calendar_view.class.inc');
		$cal_view = new calendar_view('calender_view', $calendar_start_hour, $calendar_end_hour, $time_interval,'calendar_form', $read_only);		
	}
}

if(isset($update_settings))
{
	$cal->update_settings($cal_settings);
}

$link_back = isset($_REQUEST['link_back']) ? $_REQUEST['link_back'] : $_SERVER['PHP_SELF'].'?year='.$cal_view->year.'&month='.$cal_view->month.'&day='.$cal_view->day;
$cal_view->set_return_to($link_back);

$GO_HEADER['head'] .= date_picker::get_header();
$GO_HEADER['head'] .= $cal_view->get_header();

$GO_HEADER['head'] .= "
<script type=\"text/javascript\">
    function date_picker(calendar) {
			var y = calendar.date.getFullYear();
			var m = calendar.date.getMonth()+1;     // integer, 0..11
			var d = calendar.date.getDate();      // integer, 1..31					
			".$cal_view->get_date_handler('d','m','y')."			
   }

  function change_calendar()
  {
    document.forms[0].method='get';
    document.forms[0].submit();
  }
  
  function print_calendar()
  {
  	openPopup('calendar_print', 'about:blank');
  	document.forms[0].target='calendar_print' ;
  	document.forms[0].print.value='true';
  	document.forms[0].submit();
  	document.forms[0].print.value='false';
  	document.forms[0].target='_self' ;
  	
  }
</script>";

if($cal_settings['refresh_rate'] > 0 && !$print)
{
	$GO_HEADER['auto_refresh']['interval'] = $cal_settings['refresh_rate'];
	$GO_HEADER['auto_refresh']['action'] = 'javascript:document.calendar_form.submit();';
}

if($print)
{
	$GO_HEADER['head'] .= "<script type=\"text/javascript\">window.print();</script>" ;
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



if(!$print)
{
	$menu = new button_menu();
	$menu->add_button('cal_compose', $sc_new_app, 'javascript:'.$cal_view->get_new_event_handler($cal_view->current_year,$cal_view->current_month, $cal_view->current_day, $cal_view->hour, $cal_view->min));
	
		$menu->add_button('cal_day', $sc_day_view, 'javascript:'.$cal_view->get_change_view_handler($cal_view->clicked_day,$cal_view->clicked_month, $cal_view->clicked_year,1));
		$menu->add_button('cal_week', $sc_week_view, 'javascript:'.
			$cal_view->get_change_view_handler($cal_view->clicked_day,$cal_view->clicked_month, $cal_view->clicked_year,$cal_settings['weekview']));
	
	if($view_id == 0 || $cal_settings['merged_view'] == 1)
	{
		$menu->add_button('cal_month', $sc_month_view, 'javascript:'.$cal_view->get_change_view_handler($cal_view->clicked_day,$cal_view->clicked_month, $cal_view->clicked_year,31));
		
		if($cal_settings['view_type'] == 'grid')
		{
			$menu->add_button('cal_list', $sc_list_view, 'javascript:change_view_type(\'list\', \'calendar_form\');');
		}else
		{
			$menu->add_button('cal_list', $cal_grid_view, 'javascript:change_view_type(\'grid\', \'calendar_form\');');
		}
	}
	$menu->add_button('cal_refresh', $sc_refresh,  'javascript:'.$cal_view->get_date_handler($cal_view->clicked_day,$cal_view->clicked_month, $cal_view->clicked_year));
	$menu->add_button('search', $cmdSearch, 'search.php?return_to='.urlencode($link_back));
	$menu->add_button('cal_calendar', $cal_admin, 'admin.php?return_to='.urlencode($link_back));	
	$menu->add_button('cal_view', $strAvailability, 'event.php?view_id='.
		$view_id.'&calendar_id='.$calendar_id.'&task=availability&day='.
		$cal_view->clicked_day.'&month='.$cal_view->clicked_month.
		'&year='.$cal_view->clicked_year.'&return_to='.urlencode($link_back));	
	$menu->add_button('cal_print', $cmdPrint,  'javascript:print_calendar();');
	

	$form->add_html_element($menu); 



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
	
	
	
	$cell = new table_cell($sc_view.': ');
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

	$select->add_value('1', '1 '.$sc_day);
	$select->add_value('2', '2 '.$sc_days);
	$select->add_value('5', '5 '.$sc_days);
	$select->add_value('7', '1 '.$sc_week);
	if($view_id == 0 || $cal_settings['merged_view'] == 1)
	{
		$select->add_value('14', '2 '.$sc_weeks);
		$select->add_value('31', '1 '.$sc_month);			
	}
	$cell->add_html_element($select);
	if ($cal_view->offset == 5)
	{
		$interval= 7;
	}else
	{
		$interval = $cal_view->offset;
	}

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


	$row->add_cell($cell);
	$table->add_row($row);
	
	$form->add_html_element($table);
	
	$table = new table();
	
	$table->set_attribute('style','width:100%');
	$row = new table_row();
	

	$row = new table_row();

	$cell = new table_cell();
	$cell->set_attribute('valign', 'top');

	$div = new html_element('div', ' ');
	$div->set_attribute('id','date_picker1_container');
	$div->set_attribute('style', 'width:225px;');

	$cell->add_html_element($div);
	$cell->add_html_element(new date_picker('date_picker1', '',$cal_view->clicked_month.'/1/'.$cal_view->clicked_year, 'date_picker1_container', 'date_picker'));

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
	}else
	{
		$div = new html_element('div', ' ');
		$div->set_attribute('id','date_picker2_container');
		$div->set_attribute('style', 'width:225px;margin-top: 10px;');

		$cell->add_html_element($div);
		$cell->add_html_element(new date_picker('date_picker2', '',($cal_view->clicked_month+1).'/1/'.$cal_view->clicked_year, 'date_picker2_container', 'date_picker'));
	}
	$row->add_cell($cell);
}else
{
	
	
	
	if($view_id > 0)
	{
		$calendars = $cal->get_view_calendars($view_id);
		$view = $cal->get_view($view_id);
		$h1 = new html_element('h1', $view['name']);
		
	}else
	{
		$calendar = $cal->get_calendar($calendar_id);
		$h1 = new html_element('h1', $calendar['name']);
	}
	
	if($cal_settings['show_days'] == '31')
	{
		
		$h1 ->innerHTML .= ' - '.$months[$cal_view->clicked_month-1].', '.$cal_view->clicked_year;
		
	}
	
	$form->add_html_element($h1);
	
	$table = new table();
	$table->set_attribute('style','width:100%');
	$row = new table_row();
}


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
			local_to_gmt_time($cal_view->start_time), local_to_gmt_time($cal_view->end_time),$cal_settings['show_todos'],false,true,false,true);
		foreach($events as $event)
		{
			$cal_view->add_event($calendar['id'], $event);
		}
	}
}else
{
	
	require_once($GO_MODULES->class_path.'cal_holidays.class.inc');
	$holidays=new holidays();
	// if there are no holidays in database they are automatically generated
	if ((array_key_exists($_SESSION['GO_SESSION']['language']['code'], $holidays->in_holidays["fix"]) || 
		array_key_exists($_SESSION['GO_SESSION']['language']['code'], $holidays->in_holidays["var"]) ||
		array_key_exists($_SESSION['GO_SESSION']['language']['code'], $holidays->in_holidays["spc"]))) {
		if(!$holidays->get_holidays($GO_SECURITY->user_id, $cal_view->year)){
			$holidays->add_holidays($GO_SECURITY->user_id, $cal_view->year, $_SESSION['GO_SESSION']['language']['code']);
		}
	}
	
	
	
	

	$events = $cal->get_events_in_array($calendar_id, $view_id, 0, 
			local_to_gmt_time($cal_view->start_time), local_to_gmt_time($cal_view->end_time), $cal_settings['show_todos'],false,true,false,true);
	
	
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
$cal_view->load_holidays($GO_SECURITY->user_id);


$cell = new table_cell($cal_view->get_html());
$cell->set_attribute('style', 'vertical-align:top;width:100%');
$row->add_cell($cell);
$table->add_row($row);

$form->add_html_element($table);
echo $form->get_html();

require_once($GO_THEME->theme_path."footer.inc");
