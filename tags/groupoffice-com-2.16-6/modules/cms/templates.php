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

//load Group-Office
require_once("../../Group-Office.php");

load_basic_controls();
load_control('datatable');

//authenticate the user
$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('cms', true);

//load the CMS module class library
require_once($GO_MODULES->class_path.'cms.class.inc');
$cms = new cms();

/*
MS: If a standard user has only one site redirect him there directly.
*/
if(!$GO_MODULES->write_permission)
{
	if($count = $cms->get_authorized_sites($GO_SECURITY->user_id))
	{
		if($count == 1)
		{
			$cms->next_record();
			header('Location: browse.php?site_id='.$cms->f('id'));
			exit();
		}
	}
}


//get the language file
require_once($GO_LANGUAGE->get_language_file('cms'));


//perform tasks before output to client
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';


//set the page title for the header file
$page_title = $lang_modules['cms'];


$datatable = new datatable('sites_table');
$GO_HEADER['head'] = $datatable->get_header();
//require the header file. This will draw the logo's and the menu
require_once($GO_THEME->theme_path."header.inc");

$form = new form('cms_form');

$menu = new button_menu();
$menu->add_button('add', $cms_new_theme, 'template.php');
$menu->add_button('delete_big', $cmdDelete, $datatable->get_delete_handler());
$menu->add_button('close', $cmdClose, 'index.php');

$form->add_html_element($menu);

if($datatable->task == 'delete')
{
	foreach($datatable->selected as $template_id)
	{
		if($template = $cms->get_template($template_id))
		{
			if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $template['acl_write']))
			{
				if($cms->delete_template($template_id))
				{
					$GO_SECURITY->delete_acl($template['acl_write']);
					$GO_SECURITY->delete_acl($template['acl_read']);
				}
			}else
			{
				$feedback = $strAccessDenied;
			}
		}else
		{
			$feedback = $strDataError;
		}
	}
}

if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $form->add_html_element($p);
}

$datatable->add_column(new table_heading($strName));
$datatable->add_column(new table_heading($strOwner));

$count = $cms->get_authorized_templates($GO_SECURITY->user_id);

while($cms->next_record())
{
  $row = new table_row($cms->f('id'));
  $row->set_attribute('ondblclick', "javascript:document.location='template.php?template_id=".$cms->f('id')."';");
  
  $row->add_cell(new table_cell($cms->f('name')));
  $row->add_cell(new table_cell(show_profile($cms->f('user_id'))));
  $datatable->add_row($row);
}

$cell = new table_cell($count.' '.$cms_sites);
$cell->set_attribute('class','small');
$cell->set_attribute('colspan','99');
$datatable->add_footer($cell);

$form->add_html_element($datatable);

echo $form->get_html();
require_once($GO_THEME->theme_path."footer.inc");
?>
