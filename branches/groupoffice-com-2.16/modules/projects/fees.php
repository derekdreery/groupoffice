<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.6 $ $Date: 2006/11/22 09:35:41 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
require_once("../../Group-Office.php");


$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('projects', true);
require_once($GO_LANGUAGE->get_language_file('projects'));

load_basic_controls();
load_control('datatable');

$page_title=$menu_projects;
require_once($GO_MODULES->class_path."projects.class.inc");
$projects = new projects();

$form = new form('fees_form');
$datatable = new datatable('fees_table', true);
$datatable->set_attribute('style', 'width:100%');

if($datatable->task == 'delete')
{
	foreach($datatable->selected as $fee_id)
	{
			$projects->delete_fee($fee_id);
	}
}	

$datatable->add_column(new table_heading($strName));
$datatable->add_column(new table_heading($pm_internal_fee));
$datatable->add_column(new table_heading($pm_external_fee));
$datatable->add_column(new table_heading($pm_fee_time));
$datatable->add_column(new table_heading('&nbsp;'));

$menu = new button_menu();
if ($GO_MODULES->write_permission)
{
	$menu->add_button('add', $pm_new_fee, 'fee.php');
	$menu->add_button('delete_big', $cmdDelete, $datatable->get_delete_handler());
}
$menu->add_button('close', $cmdClose, 'index.php');
$form->add_html_element($menu);
$count = $projects->get_fees();

if($count > 0)
{
	while ($projects->next_record())
	{
		$row = new table_row($projects->f('id'));
		$row->set_attribute('ondblclick', "javascript:document.location='fee.php?fee_id=".$projects->f('id')."';");
		
		$row->add_cell(new table_cell($projects->f('name')));
		$row->add_cell(new table_cell(
			htmlspecialchars($_SESSION['GO_SESSION']['currency']).'&nbsp;'.
			number_format($projects->f('internal_value'), 2, 
				$_SESSION['GO_SESSION']['decimal_seperator'],
				$_SESSION['GO_SESSION']['thousands_seperator'])));
		
		$row->add_cell(new table_cell(
			htmlspecialchars($_SESSION['GO_SESSION']['currency']).'&nbsp;'.
			number_format($projects->f('external_value'), 2, 
				$_SESSION['GO_SESSION']['decimal_seperator'],
				$_SESSION['GO_SESSION']['thousands_seperator'])));
				
		$row->add_cell(new table_cell($projects->f('time').'&nbsp;'.$pm_mins));
				
		$cell = new table_cell();
		$cell->set_attribute('style','text-align:right');
	
		$row->add_cell($cell);
		$datatable->add_row($row);
	}
	$cell = new table_cell($count.' '.$pm_fees);
	$cell->set_attribute('style','font-style:italic');
	$cell->set_attribute('colspan','99');
	$datatable->add_footer($cell);
}else
{
	$row = new table_row();
	$cell = new table_cell($count.' '.$pm_fees);
	$cell->set_attribute('colspan','99');
	$row->add_cell($cell);
	$datatable->add_row($row);
}

$form->add_html_element($datatable);
$GO_HEADER['head'] = $datatable->get_header();
require_once($GO_THEME->theme_path."header.inc");
echo $form->get_html();
require_once($GO_THEME->theme_path."footer.inc");
?>
