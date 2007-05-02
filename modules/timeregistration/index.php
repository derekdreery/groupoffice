<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.1 $ $Date: 2006/11/28 12:30:49 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 

require_once("../../Group-Office.php");


$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('timeregistration');
require_once($GO_LANGUAGE->get_language_file('timeregistration'));

load_basic_controls();

$page_title=$menu_projects;
require_once($GO_MODULES->class_path."timeregistration.class.inc");
$projects = new timeregistration();


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
	load_control('date_picker');
	$GO_HEADER['head'] = date_picker::get_header();
	$GO_HEADER['head'] .= '<link href="'.$GO_THEME->theme_url.'css/projects.css" type="text/css" rel="stylesheet" />';
}else {
	require($GO_MODULES->modules['timeregistration']['class_path'].'projects_list.class.inc');
	$projects_list = new timeregistration_list('timeregistration_list', 0, 0, 0);
	
	$GO_HEADER['head'] = $projects_list->get_header();
}

require_once($GO_THEME->theme_path."header.inc");

$form = new form('projects_form');


if($GO_MODULES->write_permission)
{
	$menu = new button_menu();
	$menu->add_button('projects', $pm_projects, 'index.php');
	$menu->add_button('pr_new_project', $pm_new_project, 'project.php');
	$menu->add_button('pr_load', $pm_load, 'index.php?post_action=load');
	
	
	/*if(isset($GO_MODULES->modules['calendar']) && $GO_MODULES->modules['calendar']['read_permission'])
	{
		$menu->add_button('projects', $pm_templates, 'templates.php');
	}*/
	
	if($post_action != 'load')
	{
		$menu->add_button('delete_big', $cmdDelete, $projects_list->get_delete_handler());
		if($GO_MODULES->write_permission)
		{
			//$menu->add_button('print', $cmdPrint, 'print_options.php');
		}
	}
	
	$form->add_html_element($menu);
}
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
