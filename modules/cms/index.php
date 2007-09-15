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
$GO_MODULES->authenticate('cms');

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
if($GO_MODULES->write_permission)
{
	$menu->add_button('add', $cms_cmd_new_site, 'site.php');
}

$menu->add_button('appearance', $cms_themes_menu, 'templates.php');
$menu->add_button('delete_big', $cmdDelete, $datatable->get_delete_handler());

$form->add_html_element($menu);
if ($cms->get_authorized_templates($GO_SECURITY->user_id) == 0)
{
	$form->add_html_element(new html_element('p',$cms_no_themes));

	if($GO_MODULES->write_permission)
	{
		$form->add_html_element(new button($cmdOk, "javascript:document.location='".$GO_MODULES->url."index.php?tabindex=templates.inc';"));
	}
}else
{	
	if($datatable->task == 'delete')
	{
		foreach($datatable->selected as $site_id)
		{
			if ($site = $cms->get_site($site_id))
			{
			  if ($GO_SECURITY->user_id == $site['user_id'] || $GO_SECURITY->has_permission($GO_SECURITY->user_id, $site['acl_write']))
			  {
			    $cms->delete_site($site_id);
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
	$count = $cms->get_authorized_sites($GO_SECURITY->user_id);
	
	$datatable->add_column(new table_heading($cms_domain));
	$datatable->add_column(new table_heading($strOwner));
	
	while($cms->next_record())
	{
	  $row = new table_row($cms->f('id'));
	  $row->set_attribute('ondblclick', "javascript:document.location='browse.php?site_id=".$cms->f('id')."';");
	  
	  $row->add_cell(new table_cell($cms->f('domain')));
	  $row->add_cell(new table_cell(show_profile($cms->f('user_id'))));
	  $datatable->add_row($row);
	}
	
	$cell = new table_cell($count.' '.$cms_sites);
	$cell->set_attribute('class','small');
	$cell->set_attribute('colspan','99');
	$datatable->add_footer($cell);
	
	$form->add_html_element($datatable);
}
echo $form->get_html();
require_once($GO_THEME->theme_path."footer.inc");
