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

load_basic_controls();
load_control('color_selector');
load_control('datatable');

require_once($GO_MODULES->class_path.'calendar.class.inc');
$cal = new calendar();

$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : getdate();
$year = isset($_POST['year']) ? $_POST['year'] : $date["year"];
$month = isset($_POST['month']) ? $_POST['month'] : $date["mon"];
$day = isset($_POST['day']) ? $_POST['day'] : $date["mday"];

$task = isset($_POST['task']) ? $_POST['task'] : '';
$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

$view_id = isset($_REQUEST['view_id']) ? $_REQUEST['view_id'] : 0;

$hours = array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23");

if ($task == 'save')
{
	$name = smart_addslashes(trim($_POST['name']));
	if ($name != "")
	{
		$event_colors_override = isset($_POST['event_colors_override']) ? '1' : '0';
		if ($view_id > 0)
		{
			$existing_view = $cal->get_view_by_name($GO_SECURITY->user_id, $name);

			if ($existing_view && $existing_view['id'] != $view_id)
			{
				$feedback = $sc_view_exists;
				
			}elseif(!$cal->update_view($view_id, $name, $_POST['view_start_hour'], $_POST['view_end_hour'], $event_colors_override ,$_POST['time_interval']))
			{
				$feedback = $strSaveError;
			}
		}else
		{
			if ($cal->get_view_by_name($GO_SECURITY->user_id, $name))
			{
				$feedback = $sc_view_exists;
			}else
			{
				$acl_read = $GO_SECURITY->get_new_acl();
				$acl_write = $GO_SECURITY->get_new_acl();
				
				$GO_SECURITY->add_user_to_acl($GO_SECURITY->user_id, $acl_write);
	
				if (!$view_id = $cal->add_view($GO_SECURITY->user_id, $name,
					 $_POST['view_start_hour'], $_POST['view_end_hour'], 
					 $event_colors_override, $_POST['time_interval'], $acl_read, $acl_write))
				{
					$feedback = $strSaveError;
				}
			}
		}
	}else
	{
		$feedback = $error_missing_field;
	}

	if (!isset($feedback))
	{
		$calendars = isset($_POST['calendars']) ? $_POST['calendars'] : array();
		$cal->remove_calendars_from_view($view_id);

		foreach($calendars as $calendar_id)
		{
			$cal->add_calendar_to_view($calendar_id, $_POST['backgrounds'][$calendar_id], $view_id);
		}	
		if ($_POST['close'] == 'true')
		{
			header('Location: '.$return_to);
			exit();
		}
	}
}

if ($view_id > 0)
{
	$view = $cal->get_view($view_id);
	$title = $view['name'];
	
	if($view['user_id']!=$GO_SECURITY->user_id && !$GO_MODULES->write_permission)
	{
		header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
		exit();
	}
}else
{
	$view['start_hour'] = isset($_POST['view_start_hour']) ? $_POST['view_start_hour'] : '07';
	$view['end_hour'] = isset($_POST['view_end_hour']) ? $_POST['view_end_hour'] : '20';
	$view['name'] = isset($_POST['name']) ? smart_stripslashes($_POST['name']) : '';
	$view['event_colors_override'] = isset($_POST['name']) ? '1' : '0';
	$view['time_interval'] = isset($_REQUEST['time_interval']) ? smart_stripslashes($_REQUEST['time_interval']) : '1800';
	$title = $cal_new_view;
}

$tabstrip = new tabstrip('view', $title);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to(htmlspecialchars($return_to));
if($view_id > 0)
{
	$tabstrip->add_tab('properties', $strProperties);
	$tabstrip->add_tab('acl_read', $strReadRights);
	$tabstrip->add_tab('acl_write', $strWriteRights);
}
$GO_HEADER['head'] = color_selector::get_header();
require_once($GO_THEME->theme_path.'header.inc');

$form = new form('view_form');
$form->add_html_element(new input('hidden', 'view_id', $view_id));
$form->add_html_element(new input('hidden', 'task'));
$form->add_html_element(new input('hidden', 'close', 'false'));
$form->add_html_element(new input('hidden', 'return_to', $return_to));

switch($tabstrip->get_active_tab_id())
{
	case 'acl_read':
		$tabstrip->innerHTML .= get_acl($view['acl_read']);
		$tabstrip->add_html_element(new button($cmdClose,"javascript:document.location='".htmlspecialchars($return_to)."'"));
	break;
	
	case 'acl_write':
		$tabstrip->innerHTML .= get_acl($view['acl_write']);
		$tabstrip->add_html_element(new button($cmdClose,"javascript:document.location='".htmlspecialchars($return_to)."'"));
	break;
	
	default:
		
	if(isset($feedback))
	{
		$p = new html_element('p',$feedback);
		$p->set_attribute('class','Error');
		$tabstrip->add_html_element($p);
	}
	
	$table = new table();
	$row = new table_row();
	$row->add_cell(new table_cell($strName.'*: '));
	$input = new input('text', 'name',$view['name']);
	$input->set_attribute('maxlength','100');
	$input->set_attribute('style', 'width:300px');
	$row->add_cell(new table_cell($input->get_html()));
	$table->add_row($row);
	
	$row = new table_row();
	$row->add_cell(new table_cell($sc_show_hours.': '));
	
	$select1 = new select('view_start_hour', $view['start_hour']);
	$select1->add_arrays($hours, $hours);	
	$select2 = new select('view_end_hour', $view['end_hour']);
	$select2->add_arrays($hours, $hours);	
	
	$row->add_cell(new table_cell($select1->get_html().$sc_to.$select2->get_html()));
	$table->add_row($row);
	
	$row = new table_row();
	$row->add_cell(new table_cell($cal_scale.': '));
	
	$select = new select('time_interval', $view['time_interval']);
	$select->add_value('900', '15 '.$sc_mins);
	$select->add_value('1800', '30 '.$sc_mins);
	$select->add_value('3600', '1 '.$sc_hour);	
	
	$row->add_cell(new table_cell($select->get_html()));
	$table->add_row($row);	

	$row = new table_row();
	$checkbox = new checkbox('event_colors_override', 'event_colors_override', '1', $cal_event_colors_override, ($view['event_colors_override']=='1'));
	$cell = new table_cell($checkbox->get_html());
	$cell->set_attribute('colspan','2');
	$row->add_cell($cell);
	$table->add_row($row);
	
	$tabstrip->add_html_element($table);
	
	
	$table = new datatable('views');
	$table->add_column(new table_heading($strName));
	$table->add_column(new table_heading($sc_background));
	$table->add_column(new table_heading($strOwner));

	$colors[] = 'CCFFCC';	
  $colors[] = 'FF6666';
  $colors[] = 'CCCCCC';
  $colors[] = '99CCFF';
  $colors[] = 'FF99FF';
  $colors[] = 'FFCC66';
  $colors[] = 'CCCC66';
  $colors[] = 'F1F1F1';
  $colors[] = 'FFCCFF';
  
  $count =0;
  
  $cal2 = new calendar();
  
  $groups[] = array('id'=>0, 'name' =>$sc_calendars);
  
	$cal->get_resource_groups();
	while($cal->next_record())
	{
		$groups[] = $cal->Record;
	}  
	
	foreach($groups as $group)
	{	
		if($cal->get_authorized_calendars($GO_SECURITY->user_id, $group['id']))
		{		
			$row = new table_row();
			$cell = new table_cell($group['name']);
			$cell->set_attribute('colspan','99');
			$cell->set_attribute('style','vertical-align:middle');
			$row->set_attribute('class','groupRow');
			$row->add_cell($cell);
			$table->add_row($row);
		
			while($cal->next_record())
			{
				if ($view_id > 0 && $task != 'save')
				{
					if($vc = $cal2->get_view_calendar($cal->f('id'), $view_id))
					{
						$check = true;
						$background = $vc['background'];
					}else
					{
						$check = false;
						$background = $colors[$count];
						$count++;
						if($count== count($colors))
						{
							$count = 0;
						}
					}
				}else
				{
					$check = isset($_POST['calendars']) ? in_array($cal->f('id'), $_POST['calendars']) : false;
					$background = $colors[$count];
					$background = isset($_POST['backgrounds'][$cal->f('id')]) ? $_POST['backgrounds'][$cal->f('id')] : $colors[$count];
					$count++;
					if($count == count($colors))
					{
						$count = 0;
					}
				}
				
				$row = new table_row();
				$checkbox = new checkbox($cal->f('id'),'calendars[]', $cal->f('id'), $cal->f('name'), $check);
				$row->add_cell(new table_cell($checkbox->get_html()));
				$color_selector = new color_selector('background_'.$cal->f('id'),'backgrounds['.$cal->f('id').']', $background, 'view_form');

		    
		    $row->add_cell(new table_cell($color_selector->get_html()));
		    $row->add_cell(new table_cell(show_profile($cal->f('user_id'))));
				$table->add_row($row);
			}
		}
	}

	
	$tabstrip->add_html_element($table);

	$tabstrip->add_html_element(new button($cmdOk,"javascript:document.forms[0].close.value='true';document.forms[0].task.value='save';document.forms[0].submit()"));
	$tabstrip->add_html_element(new button($cmdApply,"javascript:document.forms[0].task.value='save';document.forms[0].submit()"));
	$tabstrip->add_html_element(new button($cmdClose,"javascript:document.location='".htmlspecialchars($return_to)."'"));		
	break;
}
$form->add_html_element($tabstrip);
echo $form->get_html();
require_once($GO_THEME->theme_path.'footer.inc');
