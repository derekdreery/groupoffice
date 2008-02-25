<?php
/**
 * @copyright Intermesh 2004
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.32 $ $Date: 2006/11/21 16:25:42 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('summary');

load_basic_controls();
load_control('tooltip');

require_once($GO_LANGUAGE->get_language_file('summary'));
require_once($GO_MODULES->class_path."summary.class.inc");
$summary = new summary();

$GO_CONFIG->set_help_url($sum_help_url);

$ab_module = isset($GO_MODULES->modules['addressbook']) ? $GO_MODULES->modules['addressbook'] : false;
if ($ab_module && $ab_module['read_permission'])
{
  require_once($ab_module['class_path'].'addressbook.class.inc');
  $ab = new addressbook();
}

$projects_module = isset($GO_MODULES->modules['projects']) ? $GO_MODULES->modules['projects'] : false;
if ($projects_module && $projects_module['read_permission'])
{
  require_once($projects_module['class_path'].'projects.class.inc');
  $projects = new projects();
}

$link_back = $GO_MODULES->url;

//get the local times
$local_time = get_time();
$year = date("Y", $local_time);
$month = date("m", $local_time);
$day = date("j", $local_time);
$hour = date("H", $local_time);
$min = date("i", $local_time);

$GO_HEADER['auto_refresh']['interval'] = '60';
$GO_HEADER['auto_refresh']['url'] = $_SERVER['PHP_SELF'];
$GO_HEADER['head'] = tooltip::get_header();

require_once($GO_THEME->theme_path."header.inc");

$form = new form('summary_form');

$table = new table();
$table->set_attribute('style','width:100%');
$table->set_attribute('cellspacing','0');

$row = new table_row();

$cell =new table_cell();
$cell->set_attribute('style','width:50%');
$cell->add_html_element(new html_element('h3', $sum_welcome_to.' '.$GO_CONFIG->title.' '.htmlspecialchars($_SESSION['GO_SESSION']['name'])));
$row->add_cell($cell);

$cell =new table_cell();
$cell->set_attribute('style','width:50%;text-align:right');


if(str_replace($_SESSION['GO_SESSION']['date_seperator'], '',$_SESSION['GO_SESSION']['date_format']) == 'dmY')
{
  $cell->add_html_element(new html_element('h3', $full_days[date('w', $local_time)].', '.$day.' '.$months[$month-1] . ' ' . $year)); 
}else
{
  $cell->add_html_element(new html_element('h3', $full_days[date('w', $local_time)].' '.$months[$month-1].' '.$day));
}
$row->add_cell($cell);

$table->add_row($row);

$row = new table_row();
$cell = new table_cell();
$cell->set_attribute('colspan','99');

$menu = new button_menu();


if(isset($GO_MODULES->modules['email']) && $GO_MODULES->modules['email']['read_permission'])
{
	require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc');
	$email = new email();
	if($email->get_accounts($GO_SECURITY->user_id))
	{
		$menu->add_button('compose', $strNewEmail, get_mail_to_href());
	}
}
    
if (isset($GO_MODULES->modules['projects']) && $GO_MODULES->modules['projects']['read_permission'])
{
	$menu->add_button('pr_new_project', $strNewProject, $GO_MODULES->modules['projects']['url'].'project.php?return_to='.rawurlencode($link_back));
}
if (isset($GO_MODULES->modules['notes']) && $GO_MODULES->modules['notes']['read_permission'])
{
	$menu->add_button('ab_notes', $strNewNote, $GO_MODULES->modules['notes']['url'].'note.php?return_to='.rawurlencode($link_back));
}  
if (isset($GO_MODULES->modules['calendar']) && $GO_MODULES->modules['calendar']['read_permission'])
{
	$menu->add_button('cal_compose', $strNewEvent, $GO_MODULES->modules['calendar']['url'].'index.php?link_back='.rawurlencode($link_back));
}  
if (isset($GO_MODULES->modules['todos']) && $GO_MODULES->modules['todos']['read_permission'])
{
	$menu->add_button('todos_new', $strNewTodo, $GO_MODULES->modules['calendar']['url'].'event.php?todo=1&return_to='.rawurlencode($link_back));
}  
$cell->add_html_element($menu);
$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();
$cell = new table_cell();
$cell->set_attribute('style','border: 1px solid black;border-left:0;');
$cell->set_attribute('valign','top');
	  
//require_once('sum_email.inc');
require_once('sum_calendar.inc');
require_once('sum_filesystem.inc');

$cell->innerHTML .= '&nbsp;';
$row->add_cell($cell);

$cell = new table_cell();
$cell->set_attribute('style','border: 1px solid black; border-left:0;border-right:0;');
$cell->set_attribute('valign','top');

if ($GO_MODULES->write_permission)
{
	$link = new hyperlink('announcements.php', $sum_manage_announcements);
	$link->set_attribute('class','normal');
	
	$div = new html_element('div', $link->get_html());  
	$div->set_attribute('style','text-align:right;width:100%;');
  $cell->add_html_element($div);
}


$summary->get_announcements();

while($summary->next_record())
{
  $cell->add_html_element(new html_element('h1',$summary->f('title')));
  $cell->add_html_element(new html_element('p',$summary->f('content')));
}
$cell->innerHTML .= '&nbsp;';
$row->add_cell($cell);
$table->add_row($row);

$form->add_html_element($table);
echo $form->get_html();
require_once($GO_THEME->theme_path."footer.inc");
