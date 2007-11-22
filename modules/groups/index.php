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
$GO_MODULES->authenticate('groups');
require_once($GO_LANGUAGE->get_language_file('groups'));

load_basic_controls();
load_control('datatable');

$GO_HEADER['head'] = datatable::get_header();
$page_title = $groups_title;
require_once($GO_THEME->theme_path."header.inc");

$form = new form('group_form');
if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $form->add_html_element($p);
}

$datatable = new datatable('groups_table');
$datatable->set_attribute('style','width:100%');

if($datatable->task == 'delete')
{
	foreach($datatable->selected as $group_id)
	{
	  if ($group_id != $GO_CONFIG->group_everyone 
	      && $group_id!= $GO_CONFIG->group_root)
	  {
	    $GO_GROUPS->delete_group($group_id);
	  }
	}
}

$menu = new button_menu();
$menu->add_button('groups',$cmdAdd,'group.php');
$menu->add_button('delete_big',$cmdDelete, $datatable->get_delete_handler());
$form->add_html_element($menu);


$datatable->add_column(new table_heading($strName));
$datatable->add_column(new table_heading($strOwner));
$datatable->add_column(new table_heading('&nbsp;'));

$GO_GROUPS->get_groups();
while ($GO_GROUPS->next_record())
{
	$row = new table_row($GO_GROUPS->f('id'));
	$row->set_attribute('ondblclick', "document.location='group.php?group_id=".$GO_GROUPS->f("id")."';");
	
	$row->add_cell(new table_cell($GO_GROUPS->f("name")));
	$row->add_cell(new table_cell(show_profile($GO_GROUPS->f("user_id"))));
	
	$img = new image('delete');
	$img->set_attribute('style','width:16px;height:16px;border:0px;margin-right:5px;');
	$img->set_attribute('align','absmiddle');
	      
	$hyperlink = new hyperlink($datatable->get_delete_handler(), $img->get_html(), htmlspecialchars($strDeleteItem." '".$GO_GROUPS->f("name")."'", ENT_QUOTES));
	$cell =  new table_cell($hyperlink->get_html());
	$cell->set_attribute('style','text-align:right');
	$row->add_cell($cell);
	$datatable->add_row($row);
}
$form->add_html_element($datatable);

echo $form->get_html();

require_once($GO_THEME->theme_path."footer.inc");
