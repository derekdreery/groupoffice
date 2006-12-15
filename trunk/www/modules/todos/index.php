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
$GO_MODULES->authenticate('todos');
require_once($GO_LANGUAGE->get_language_file('calendar'));

load_basic_controls();
load_control('datatable');

$post_action = isset($_REQUEST['post_action']) ? $_REQUEST['post_action'] : '';
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? htmlspecialchars($_REQUEST['link_back']) : $_SERVER['REQUEST_URI'];

//load contact management class
require_once($GO_MODULES->modules['calendar']['class_path']."calendar.class.inc");
$cal = new calendar();


$cal_settings = $cal->get_settings($GO_SECURITY->user_id);

if($_SERVER['REQUEST_METHOD']=='POST')
{
	if(isset($_POST['show_completed']) && $cal_settings['show_completed']=='0')
	{
		$cal_settings['show_completed']='1';
	}elseif(!isset($_POST['show_completed']) && $cal_settings['show_completed']=='1')
	{
		$cal_settings['show_completed']='0';
	}
	$cal->update_settings($cal_settings);
}

$view_id = isset($_REQUEST['view_id']) ? $_REQUEST['view_id'] : $cal_settings['default_view_id'];
$calendar_id = isset($_REQUEST['calendar_id']) ? $_REQUEST['calendar_id']  : $cal_settings['default_cal_id'];

if($view_id == 0)
{
	$calendar = $cal->get_calendar($calendar_id);
	$calendar_id=$calendar['id'];
	
	$write_permission = $read_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_write']);
	if(!$read_permission)
	{
		$read_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_read']);
	}
	
	if(!$read_permission)
	{
		header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
		exit();
	}
	
	$cal->set_default_view($GO_SECURITY->user_id, $calendar_id, 0);
	$calendar_view_id = 'calendar:'.$calendar_id;
}else
{
	$view = $cal->get_view($view_id);
	 
	$cal->set_default_view($GO_SECURITY->user_id, 0, $view_id, '1');
	$write_permission = $read_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $view['acl_write']);
	if(!$read_permission)
	{
		$read_permission = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $view['acl_read']);
	}
	
	if(!$read_permission)
	{
		header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
		exit();
	}
}
require_once($GO_MODULES->modules['calendar']['class_path'].'events_list.class.inc');
$el = new events_list('todos_list', true, false, $calendar_id,$view_id,0, false,true, '0', $GO_MODULES->modules['todos']['url'], ($cal_settings['show_completed']=='1'));


$GO_HEADER['head'] = datatable::get_header();
require_once($GO_THEME->theme_path."header.inc");

$form = new form('todos_form');
$form->add_html_element(new input('hidden','calendar_id', $calendar_id, false));
$form->add_html_element(new input('hidden','view_id', $view_id, false));

if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $form->add_html_element($p);
}



$menu = new button_menu();
if($write_permission)
{
	$menu->add_button('todos_new', $cal_add_todo, $GO_MODULES->modules['calendar']['url'].'event.php?todo=1&return_to='.urlencode($GO_MODULES->modules['todos']['url']));
}else
{
	$menu->add_button('todos_new', $cal_add_todo, "javascript:alert('".htmlspecialchars(addslashes($strAccessDenied))."');");
}
$menu->add_button('delete_big', $cmdDelete, $el->get_delete_handler());

$form->add_html_element($menu);


$table = new table();
$table->set_attribute('cellpadding','0');
$table->set_attribute('cellspacing','0');
$table->set_attribute('style','width:100%');
$row = new table_row();

$link = new hyperlink("javascript:popup('".$GO_MODULES->modules['calendar']['url']."select_calendar.php', '300','400');",$cal_open_calendar);
$link->set_attribute('class','normal');

$link_text = isset($calendar) ? $calendar['name'] : $view['name'];
$cell = new table_cell('<b>'.$link_text.'</b> ('.$link->get_html().')');
$row->add_cell($cell);

$checkbox = new checkbox('show_completed','show_completed','1',$cal_show_completed, ($cal_settings['show_completed']=='1'));
$checkbox->set_attribute('onclick', 'javascript:document.forms[0].submit();');
$cell = new table_cell($checkbox->get_html());
$cell->set_attribute('style','text-align:right');
$row->add_cell($cell);
$table->add_row($row);
$form->add_html_element($table);
$form->add_html_element($el);
echo $form->get_html();
require_once($GO_THEME->theme_path."footer.inc");
?>
