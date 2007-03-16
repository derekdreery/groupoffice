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
$GO_MODULES->authenticate('addressbook');
require_once($GO_LANGUAGE->get_language_file('addressbook'));

load_basic_controls();
load_control('datatable');


$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

//load contact management class
require_once($GO_MODULES->class_path."addressbook.class.inc");

$ab = new addressbook();

$datatable = new datatable('contacts');

switch($datatable->task)
{
	case 'delete':
		foreach($datatable->selected as $delete_addressbook_id) {
		    $delete_ab = $ab->get_addressbook($delete_addressbook_id);
		    if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $delete_ab['acl_write']))
		    {
	        if ($ab->delete_addressbook($delete_addressbook_id))
	        {
            $GO_SECURITY->delete_acl($delete_ab['acl_write']);
            $GO_SECURITY->delete_acl($delete_ab['acl_read']);
	        }
		    }else
		    {
		    	$feedback = $strAccessDenied;
		    	break;
		    }
		}
	break;
}

$GO_HEADER['head'] = $datatable->get_header();

require_once($GO_THEME->theme_path."header.inc");

$form = new form('contacts');

$form->add_html_element(new input('hidden', 'delete_addressbook_id'));
$form->add_html_element(new input('hidden', 'task'));
$form->add_html_element(new input('hidden', 'close', 'false'));
$form->add_html_element(new input('hidden', 'return_to', $return_to));
$form->add_html_element(new input('hidden', 'link_back', $link_back));

$tabstrip = new tabstrip('ab_admin_tabstrip', $strAdministrate);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to(htmlspecialchars($return_to));

$tabstrip->add_tab('addressbooks', $ab_addressbooks);
$tabstrip->add_tab('templates', $ab_templates);
//$tabstrip->add_tab('mailings', $ab_mailings);

switch($tabstrip->get_active_tab_id())
{
	case 'templates':
		
  
		$tp_plugin = $GO_MODULES->get_plugin('templates');
		if (!$tp_plugin)
		{
			$tabstrip->add_html_element(new html_element('p', $strProOnly));
		}else
		{
			require($tp_plugin['path'].'templates.inc');
		}
	break;

		
	default:

	$menu = new button_menu();

	if ($GO_MODULES->write_permission) {    
	  $menu->add_button('add', $cmdAdd, 'addressbook.php?return_to='.$link_back);
	  $menu->add_button('delete_big', $cmdDelete, $datatable->get_delete_handler());
	}

	$menu->add_button('close', $cmdClose, htmlspecialchars($return_to));

	if (isset($feedback))
	{
		$p = new html_element('p', $feedback);
		$p->set_attribute('class', 'Error');
		$tabstrip->add_html_element($p);
	}

	$datatable->add_column(new table_heading($ab_addressbook));
	$datatable->add_column(new table_heading($strOwner));

	$ab_count = $ab->get_user_addressbooks($GO_SECURITY->user_id, $datatable->start, $datatable->offset);
	$datatable->set_pagination($ab_count);

	if ($ab_count > 0) {
		while ($ab->next_record()) {

			$row = new table_row($ab->f('id'));
			$row->set_attribute('ondblclick', "javascript:document.location='addressbook.php?addressbook_id=".$ab->f('id')."&return_to=".urlencode($link_back)."'");
			$row->add_cell(new table_cell($ab->f('name')));
			$row->add_cell(new table_cell(show_profile($ab->f('user_id'))));

			$datatable->add_row($row);
		}
	} else {
		$row = new table_row();
		$cell = new table_cell($ab_no_addressbook);
		$cell->set_attribute('colspan','2');
		$row->add_cell($cell);
		$datatable->add_row($row);		
	}

	$form->add_html_element($menu);
	$tabstrip->add_html_element($datatable);
	break;
}
$form->add_html_element($tabstrip);
echo $form->get_html();

require_once($GO_THEME->theme_path."footer.inc");
