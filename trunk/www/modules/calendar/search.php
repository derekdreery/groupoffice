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

load_basic_controls();
load_control('date_picker');
load_control('datatable');

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');
require_once($GO_LANGUAGE->get_language_file('calendar'));

require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
$cal = new calendar();

$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

$cal_settings = $cal->get_settings($GO_SECURITY->user_id);

$view_id = isset($_REQUEST['view_id']) ? $_REQUEST['view_id'] : $cal_settings['default_view_id'];
$calendar_id = isset($_REQUEST['calendar_id']) ? $_REQUEST['calendar_id']  : $cal_settings['default_cal_id'];

$task = isset($_POST['task']) ? $_POST['task'] : '';

$GO_HEADER['head'] = datatable::get_header();
$GO_HEADER['head'] .= date_picker::get_header();
$GO_HEADER['body_arguments'] = 'onload="javascript:document.search_form.query.focus();" onkeypress="javascript:executeOnEnter(event,\'search()\');"';
require_once($GO_THEME->theme_path."header.inc");

$form = new form('search_form');
$form->add_html_element(new input('hidden', 'task', $task));
$form->add_html_element(new input('hidden', 'return_to', $return_to));
$form->add_html_element(new input('hidden', 'link_back', $link_back));
$form->add_html_element(new input('hidden','calendar_id', $calendar_id, false));
$form->add_html_element(new input('hidden','view_id', $view_id, false));

$menu = new button_menu();
$menu->add_button('close',$cmdClose,htmlspecialchars($return_to));


$tabstrip = new tabstrip('search_strip', $cmdSearch);
$tabstrip->set_return_to(htmlspecialchars($return_to));
$tabstrip->set_attribute('style','width:100%');

$table = new table();

$row = new table_row();

$row->add_cell(new table_cell($sc_calendar.':'));
$link = new hyperlink("javascript:popup('select_calendar.php', '300','400');",$cal_open_calendar);
$link->set_attribute('class','normal');
if($calendar_id > 0)
{
	$calendar=$cal->get_calendar($calendar_id);
}else
{
	$view = $cal->get_view($view_id);
}
$link_text = isset($calendar) ? $calendar['name'] : $view['name'];
$cell = new table_cell('<b>'.$link_text.'</b> ('.$link->get_html().')');
$row->add_cell($cell);
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($strKeyword.':*'));
$input = new input('text', 'query');
$row->add_cell(new table_cell($input->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($sc_start_time.':'));
$start_date = isset ($_POST['start_date']) ? $_POST['start_date'] : '';
$datepicker = new date_picker('start_date', $_SESSION['GO_SESSION']['date_format'], $start_date);
$row->add_cell(new table_cell($datepicker->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell($sc_end_time.':'));
$end_date = isset ($_POST['end_date']) ? $_POST['end_date'] : '';
$datepicker = new date_picker('end_date', $_SESSION['GO_SESSION']['date_format'], $end_date);
$row->add_cell(new table_cell($datepicker->get_html()));
$table->add_row($row);

$tabstrip->add_html_element($table);
$tabstrip->add_html_element(new button($cmdSearch, 'javascript:search();'));

if($task == 'search')
{
	$now = get_gmt_time();
	
	$datatable = new datatable('search_table');
	$datatable->set_attribute('style','width:100%');

	if($datatable->task == 'delete')
	{
		foreach($datatable->selected as $event_id)
		{
			$event = $cal->get_event($event_id);

			if ($cal->has_write_permission($GO_SECURITY->user_id, $event))
			{
				$cal->delete_event($event_id);
			}else
			{
				$feedback = $GLOBALS['strAccessDenied'];
				break;
			}
		}
	}

	$datatable->add_column(new table_heading($GLOBALS['strName'], 'name'));
	$datatable->add_column(new table_heading($sc_start_at, 'start_time'));
	$datatable->add_column(new table_heading($sc_end_at,'end_time'));
	$datatable->add_column(new table_heading($sc_calendars));
	$datatable->add_column(new table_heading($sc_recur_section, 'repeat_type'));
	$th = new table_heading('&nbsp;');
	
	$start_date = empty($_POST['start_date']) ? 0 : date_to_unixtime($_POST['start_date']);
	$end_date = empty($_POST['end_date']) ? 0 : date_to_unixtime($_POST['end_date']);
	
	$count = $cal->search_events(
		$GO_SECURITY->user_id, 
		$calendar_id,
		$view_id,
		'%'.smart_addslashes($_POST['query']).'%', 
		$start_date, 
		$end_date, 
		$datatable->sort_index, 
		$datatable->sql_sort_order,
		$datatable->start, 
		$datatable->offset);

	$datatable->set_pagination($count);
	
	if($count == 0)
	{
		$row = new table_row();
		$cell = new table_cell($sc_no_events);
		$cell->set_attribute('colspan','99');
		$row->add_cell($cell);
		$datatable->add_row($row);
	}else
	{
		$menu->add_button('delete_big', $strDeleteItem, $datatable->get_delete_handler());
		$cal2 = new calendar();
		while ($cal->next_record())
		{
			$row = new table_row($cal->f('id'));

			$private=false;
			if($cal2->has_write_permission($GO_SECURITY->user_id, $cal->Record))
			{
				$row->set_attribute('ondblclick', 
					"javascript:window.location.href='".
					$GO_MODULES->modules['calendar']['url'].
					'event.php?event_id='.$cal->f('id').
					'&return_to='.urlencode($link_back)."';");	
			}elseif($cal2->has_read_permission($GO_SECURITY->user_id, $cal->Record, false))
			{
				$row->set_attribute('ondblclick', 
					"javascript:window.location.href='".
					$GO_MODULES->modules['calendar']['url'].
					'show_event.php?event_id='.$cal->f('id').
					'&return_to='.urlencode($link_back)."';");	
			}else
			{
				$private = true;
			}	
			
			
			$div = new html_element('div','&nbsp;');
			$div->set_attribute('class','summary_icon');
			$div->set_attribute('style','background-color: #'.$cal->f('background'));
			
			if($private)
			{
				$name_cell =new table_cell(htmlspecialchars($sc_private_event));
			}else
			{			
				$name_cell =new table_cell($div->get_html().htmlspecialchars($cal->f('name')));
				if($cal->f('todo')=='1')
				{
					if($cal->f('completion_time') > 0)
					{
						$input->set_attribute('checked','checked');
						$name_cell->set_attribute('class', 'event_completed');
					}elseif($cal->f('todo') == '1' && $now>$cal->f('end_time'))
					{
						$name_cell->set_attribute('class', 'event_late');
					}
				}
			}			
		
			$row->add_cell($name_cell);
			
			if($cal->f('all_day_event') != '1')
			{
				$start_time = get_timestamp($cal->f('start_time'));
				$end_time = get_timestamp($cal->f('end_time'));
			}else
			{
				$start_time = date($_SESSION['GO_SESSION']['date_format'], $cal->f('start_time'));
				$end_time = date($_SESSION['GO_SESSION']['date_format'], $cal->f('end_time'));
			}
			$row->add_cell(new table_cell($start_time));
			$row->add_cell(new table_cell($end_time));
			
			$cal2 = new calendar();
	
			$calendars = '';
			$event_cal_count = $cal2->get_calendars_from_event($cal->f('id'));
			$first = true;
			while ($cal2->next_record()) {
				if ($first) {
					$first = false;
				} else {
					$calendars .= ', ';
				}
				$calendars .= htmlspecialchars($cal2->f('name'));
			}	
			$row->add_cell(new table_cell($calendars));
			
			$cell = new table_cell($sc_types[$cal->f('repeat_type')]);
			
			if($cal->f('repeat_type') > 0)
			{
				if($cal->f('repeat_forever') == '1')
				{
					$cell->innerHTML .= ' '.$cal_forever;
				}else
				{
					$cell->innerHTML .= ' '.$cal_until.' '.date($_SESSION['GO_SESSION']['date_format'], gmt_to_local_time($cal->f('repeat_end_time')));
				}
			}
			$row->add_cell($cell);								
			$datatable->add_row($row);
		}			
	}
	$tabstrip->add_html_element($datatable);
	
}

$form->add_html_element($menu);

if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $form->add_html_element($p);
}
$form->add_html_element($tabstrip);
echo $form->get_html();
?>
<script type="text/javascript">
function search()
{
	document.search_form.task.value='search';
	document.search_form.submit();
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
