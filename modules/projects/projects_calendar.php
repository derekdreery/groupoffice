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
load_basic_controls();
load_control('tooltip');
load_control('date_picker');

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('projects');
require_once($GO_LANGUAGE->get_language_file('projects'));

$page_title=$menu_projects;
require_once($GO_MODULES->class_path."projects.class.inc");
$projects = new projects();

require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
$cal = new calendar();

require_once($GO_MODULES->class_path."projects_monthview.class.inc");
$projects_mv = new projects_monthview('projects_monthview');
$projects_mv->set_return_to($GO_MODULES->url.'projects_calendar.php');

$projects->get_projects_for_period($GO_SECURITY->user_id, $projects_mv->start_time, $projects_mv->end_time);

while($projects->next_record())
{
	$projects_mv->add_project($projects->Record);
	
	$links = $GO_LINKS->get_links($projects->f('link_id'), 1);
	
	$events = $cal->get_events_in_array(0,0,0, $projects_mv->start_time, $projects_mv->end_time, true, true, true, $links);
	foreach($events as $event)
	{
		$projects_mv->add_event($event);
	}
}


$GO_HEADER['head'] = date_picker::get_header();
$GO_HEADER['head'] .= "
<script type=\"text/javascript\">
    function date_picker(calendar) {
			var y = calendar.date.getFullYear();
			var m = calendar.date.getMonth()+1;     // integer, 0..11
			var d = calendar.date.getDate();      // integer, 1..31					
			".$projects_mv->get_date_handler('d','m','y')."			
   }

  function change_calendar()
  {
    document.forms[0].method='get';
    document.forms[0].submit();
  }
</script>";
$GO_HEADER['head'] .= tooltip::get_header();
$GO_HEADER['head'] .= $GO_THEME->get_stylesheet('calendar');
$GO_HEADER['head'] .= $projects_mv->get_header();
require($GO_THEME->theme_path.'header.inc');

$form = new form('projects_form');

$menu = new button_menu();
$menu->add_button('close',$cmdClose, $GO_MODULES->url);

$form->add_html_element($menu);


$table = new table();

$row = new table_row();
$cell = new table_cell();
$cell->add_html_element(new hyperlink('javascript:'.$projects_mv->get_date_handler(1, $projects_mv->clicked_month-1, $projects_mv->clicked_year), '&lt;&lt;'));
$cell->innerHTML .= '&nbsp;&nbsp;'.$months[$projects_mv->clicked_month-1].', '.$projects_mv->clicked_year.'&nbsp;&nbsp;';
$cell->add_html_element(new hyperlink('javascript:'.$projects_mv->get_date_handler(1, $projects_mv->clicked_month+1, $projects_mv->clicked_year), '&gt;&gt;'));

$cell->set_attribute('style', 'text-align:center;font-weight:bold');
$cell->set_attribute('colspan', '2');
$row->add_cell($cell);
$table->add_row($row);

$table->set_attribute('style','width:100%');
$row = new table_row();
$cell = new table_cell();
$cell->set_attribute('valign', 'top');

$div = new html_element('div');
$div->set_attribute('id','date_picker1_container');
$cell->set_attribute('style', 'width:200px;');

$cell->add_html_element($div);
$cell->add_html_element(new date_picker('date_picker1', '',$projects_mv->clicked_month.'/'.$projects_mv->clicked_day.'/'.$projects_mv->clicked_year, 'date_picker1_container', 'date_picker'));


$div = new html_element('div');
$div->set_attribute('id','date_picker2_container');
$div->set_attribute('style', 'width:200px;margin-top: 10px;');

$cell->add_html_element($div);
$cell->add_html_element(new date_picker('date_picker2', '',($projects_mv->clicked_month+1).'/'.$projects_mv->clicked_day.'/'.$projects_mv->clicked_year, 'date_picker2_container', 'date_picker'));

$row->add_cell($cell);

$cell = new table_cell($projects_mv->get_html());
$cell->set_attribute('style', 'vertical-align:top;width:100%');
$row->add_cell($cell);

$table->add_row($row);

$form->add_html_element($table);
echo $form->get_html();
require($GO_THEME->theme_path.'footer.inc');
