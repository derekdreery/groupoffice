<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.5 $ $Date: 2006/11/22 09:35:41 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 

require_once("../../Group-Office.php");
load_basic_controls();
load_control('datatable');

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('projects');
require_once($GO_LANGUAGE->get_language_file('projects'));

$page_title=$menu_projects;
require_once($GO_MODULES->class_path."projects.class.inc");
$projects = new projects();

//check for the addressbook module
$ab_module = isset($GO_MODULES->modules['addressbook']) ? $GO_MODULES->modules['addressbook'] : false;
if ($ab_module && $ab_module['read_permission'])
{
  require_once($ab_module['class_path'].'addressbook.class.inc');
  $ab = new addressbook();
}else
{
	$ab_module = false;
}


$post_action = isset($_REQUEST['post_action']) ? $_REQUEST['post_action'] : '';
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

$time = get_time();
$day = date("j", $time);
$year = date("Y", $time);
$month = date("m", $time);

$date = date($_SESSION['GO_SESSION']['date_format'], $time);

if ($post_action == 'load')
{
	$GO_HEADER['head'] = date_picker::get_header();
}

$datatable = new datatable('pm_templates');
$GO_HEADER['head'] = $datatable->get_header();
require_once($GO_THEME->theme_path."header.inc");

$form = new form('projects_form');

$menu = new button_menu();

$menu->add_button('pr_new_project', $pm_new_template, 'template.php');





$menu->add_button('delete_big', $cmdDelete, $datatable->get_delete_handler());
$menu->add_button('close', $cmdClose, 'index.php');

if($datatable->task == 'delete')
{
	foreach($datatable->selected as $template_id)
	{
		$projects->delete_template($template_id);
	}
}



$datatable->add_column(new table_heading($strName));
$datatable->add_column(new table_heading($strOwner));

$count = $projects->get_authorized_templates($GO_SECURITY->user_id);

if($count)
{
	while($projects->next_record())
	{
		$row = new table_row($projects->f('id'));
		$row->set_attribute('ondblclick', "javascript:document.location='template.php?template_id=".$projects->f('id')."'");
		$row->add_cell(new table_cell($projects->f('name')));
		$row->add_cell(new table_cell(show_profile($projects->f('user_id'))));	
		
		$datatable->add_row($row);		
	}
}else
{
	$row = new table_row();
	$cell = new table_cell($pm_no_templates);
	$cell->set_attribute('colspan','2');
	$row->add_cell($cell);
	$datatable->add_row($row);		
}



$form->add_html_element($menu);
$form->add_html_element(new input('hidden', 'return_to', $return_to));


$form->add_html_element($datatable);


echo $form->get_html();
require_once($GO_THEME->theme_path."footer.inc");
