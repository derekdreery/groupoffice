<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.13 $ $Date: 2006/11/27 13:07:04 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once("../../Group-Office.php");


$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('summary', true);
require_once($GO_LANGUAGE->get_language_file('summary'));

$page_title=$lang_modules['summary'];
require_once($GO_MODULES->class_path."summary.class.inc");
$summary = new summary();

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$announcement_id = isset($_REQUEST['announcement_id']) ? $_REQUEST['announcement_id'] : 0;

$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = isset($_REQUEST['link_back']) ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];

load_basic_controls();
load_control('datatable');

$form = new form('announcements_form');

$datatable = new datatable('announcements_table');
$datatable->set_attribute('style','width:100%;');

$GO_HEADER['head'] = $datatable->get_header();
require_once($GO_THEME->theme_path."header.inc");


if($datatable->task == 'delete')
{
	foreach($datatable->selected as $announcement_id)
	{
		if($announcement = $summary->get_announcement($announcement_id))
		{		
			if($summary->delete_announcement($announcement_id))
			{
				$GO_SECURITY->delete_acl($announcement['acl_id']);
			}
		}
	}
}


$datatable->add_column(new table_heading($strName, 'name'));
$datatable->add_column(new table_heading($sum_due_time, 'due_time'));
$datatable->add_column(new table_heading($strModifiedAt, 'mtime'));

if($GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
	$count = $summary->get_all_announcements($datatable->start, $datatable->offset);
else
	$count = $summary->get_all_announcements($datatable->start, $datatable->offset, $GO_SECURITY->user_id);

$datatable->set_pagination($count);

$menu = new button_menu();
$menu->add_button('add',$cmdAdd, 'announcement.php');
$menu->add_button('delete_big',$cmdDelete, $datatable->get_delete_handler());

$form->add_html_element($menu);

$str_count = $count == 1 ? $count.' '.$sum_announcements_count_single : $count.' '.$sum_announcements_count;

$div = new html_element('div', $str_count);
$div->set_attribute('style','width:100%;text-align:right');

$form->add_html_element($div);

if ($count > 0)
{	
	while($summary->next_record())
	{
		$row = new table_row($summary->f('id'));
		$row->set_attribute('ondblclick', "javascript:document.location='announcement.php?announcement_id=".$summary->f('id')."&return_to=".rawurlencode($link_back)."';");
		
		$row->add_cell(new table_cell(htmlspecialchars($summary->f('title'))));
		
		$due_time = $summary->f('due_time') > 0 ? get_timestamp($summary->f('due_time')) : '';
		$row->add_cell(new table_cell($due_time));
		$row->add_cell(new table_cell(get_timestamp($summary->f('mtime'))));	
		$datatable->add_row($row);
	}	
}else
{
	$row = new table_row();
	$cell = new table_cell($sum_no_announcements);	
	$cell->set_attribute('colspan','99');
	$row->add_cell($cell);
	$datatable->add_row($row);
}
$form->add_html_element($datatable);

$form->add_html_element(new button($cmdClose, "javascript:document.location='index.php';"));
echo $form->get_html();

require_once($GO_THEME->theme_path."footer.inc");
