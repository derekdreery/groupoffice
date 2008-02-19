<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.49 $ $Date: 2006/11/28 10:42:44 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 

require_once("../../Group-Office.php");


$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('projects');
require_once($GO_LANGUAGE->get_language_file('projects'));

$GO_CONFIG->set_help_url($pm_help_url);

load_basic_controls();

$page_title=$menu_projects;
require_once($GO_MODULES->class_path."projects.class.inc");
$projects = new projects();

$post_action = isset($_REQUEST['post_action']) ? $_REQUEST['post_action'] : '';
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$link_back = $_SERVER['PHP_SELF'].'?post_action='.$post_action.'&task='.$task;
$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

$time = get_time();
$day = date("j", $time);
$year = date("Y", $time);
$month = date("m", $time);

$date = date($_SESSION['GO_SESSION']['date_format'], $time);


if ($post_action == 'load')
{
	load_control('date_picker');
	$GO_HEADER['head'] = date_picker::get_header();
}else {
	require($GO_MODULES->modules['projects']['class_path'].'projects_list.class.inc');
	$projects_list = new projects_list('projects_list', 0, 0, 0);
	
	$GO_HEADER['head'] = $projects_list->get_header();
}

require_once($GO_THEME->theme_path."header.inc");

$form = new form('projects_form');

$menu = new button_menu();
if($GO_MODULES->write_permission)
{
	
	$menu->add_button('projects', $pm_projects, 'index.php');
	$menu->add_button('pr_new_project', $pm_new_project, 'project.php');
	$menu->add_button('pr_load', $pm_load, 'index.php?post_action=load');
	$menu->add_button('pr_fees', $pm_fees, 'fees.php');
	
	
	if(isset($GO_MODULES->modules['calendar']) && $GO_MODULES->modules['calendar']['read_permission'])
	{
		$menu->add_button('projects', $pm_templates, 'templates.php');
		$menu->add_button('calendar', 'Agenda dos Projectos', 'projects_calendar.php'); /* Suggestion: 'Project agenda' -> phrase to add in projects modules language */
	}
	
	if($post_action != 'load')
	{
		$menu->add_button('delete_big', $cmdDelete, $projects_list->get_delete_handler());
		if($GO_MODULES->write_permission)
		{
			$menu->add_button('print', $cmdPrint, 'print_options.php');
		}
	}
	
	
}else {
	$menu->add_button('projects', $pm_projects, 'index.php');
	$menu->add_button('pr_load', $pm_load, 'index.php?post_action=load');
}
$form->add_html_element($menu);
$form->add_html_element(new input('hidden', 'task'));
$form->add_html_element(new input('hidden', 'post_action',$post_action));
$form->add_html_element(new input('hidden', 'return_to', $return_to));

switch($post_action)
{
	case 'load':
		$container = &$form;
		require_once('load.inc');
	break;

	default:
		
		$form->add_html_element($projects_list);
	break;
}

echo $form->get_html();
require_once($GO_THEME->theme_path."footer.inc");
