<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.14 $ $Date: 2006/04/29 11:48:40 $
 *
   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 */

/**
 * Creates a table that uses tabs to show multiple screens
 * 
 * @package Framework
  * 
 * @access public
 */
 
require_once($GO_LANGUAGE->get_language_file('calendar'));
require_once($GO_MODULES->modules['calendar']['class_path']."calendar.class.inc");
$cal = new calendar();

if($task == 'save_calendar_settings')
{
	$reminder_value = isset($_POST['reminder_value']) ? $_POST['reminder_value'] : '0';
	$reminder_multiplier = isset($_POST['reminder_multiplier']) ? $_POST['reminder_multiplier'] : '60';
	$cal_settings['reminder'] = $reminder_value * $reminder_multiplier;
	$cal_settings['refresh_rate'] =  smart_addslashes($_POST['refresh_rate']);
	$cal_settings['permissions'] =  smart_addslashes($_POST['permissions']);
	$cal_settings['weekview'] =  smart_addslashes($_POST['weekview']);
	$cal_settings['show_todos'] =  isset($_POST['show_todos']) ? '1' : '0';
	$cal_settings['check_conflicts'] =  isset($_POST['check_conflicts']) ? '1' : '0';
	$cal_settings['email_reminders'] =  isset($_POST['email_reminders']) ? '1' : '0';
	
	$cal->update_settings($cal_settings);
}else
{
	$cal_settings = $cal->get_settings($GO_SECURITY->user_id);
}

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($cal_weekview.':'));

$cell = new table_cell();

$select = new select('weekview', $cal_settings['weekview']);
$select->add_value('7', $cal_mo_to_su);
$select->add_value('5', $cal_mo_to_fr);


$cell->add_html_element($select);

$row->add_cell($cell);
$table->add_row($row);


$row = new table_row();
$row->add_cell(new table_cell($cal_default_reminder.':'));

$cell = new table_cell();

$multipliers[] = 604800;
$multipliers[] = 86400;
$multipliers[] = 3600;
$multipliers[] = 60;

$reminder_multiplier = 60;
$reminder_value = 60;
if($cal_settings['reminder'] > 0)
{
	for ($i = 0; $i < count($multipliers); $i ++) {
		$devided = $cal_settings['reminder'] / $multipliers[$i];
		$match = (int) $devided;
		if ($match == $devided) {
			$reminder_multiplier = $multipliers[$i];
			$reminder_value = $devided;
			break;
		}
	}
}

$select = new select('reminder_value', $reminder_value);

$select->add_value('0', $cal_no_reminder);
for ($i = 1; $i < 60; $i ++) {
	$select->add_value($i, $i);
}

$cell->add_html_element($select);

$select = new select('reminder_multiplier', $reminder_multiplier);
$select->add_value('60', $sc_mins);
$select->add_value('3600', $sc_hours);
$select->add_value('86400', $sc_days);
$select->add_value('604800', $sc_weeks);
$cell->add_html_element($select);

$row->add_cell($cell);

$table->add_row($row);
$row = new table_row();
$row->add_cell(new table_cell($cal_refresh_rate.':'));
$input = new input('text', 'refresh_rate', $cal_settings['refresh_rate']);
$input->set_attribute('maxlength','5');
$input->set_attribute('size','3');

$row->add_cell(new table_cell($input->get_html().'&nbsp;'.$sc_seconds));
$table->add_row($row);


$row = new table_row();
$cell = new table_cell($cal_default_permissions.':');
$cell->set_attribute('valign','top');
$row->add_cell($cell);

$cell = new table_cell();

$radiogroup = new radiogroup('permissions', $cal_settings['permissions']);
$rb = new radiobutton('rb_participants_write', PARTICIPANTS_WRITE);
$cell->innerHTML.=$radiogroup->get_option($rb, $cal_participants_write);
$cell->add_html_element(new html_element('br'));

$rb = new radiobutton('rb_everybody_read', EVERYBODY_READ);
$cell->innerHTML.=$radiogroup->get_option($rb, $cal_everybody_read);
$cell->add_html_element(new html_element('br'));

$rb = new radiobutton('rb_everybody_write', EVERYBODY_WRITE);
$cell->innerHTML.=$radiogroup->get_option($rb, $cal_everybody_write);
$cell->add_html_element(new html_element('br'));

$rb = new radiobutton('rb_private', PRIVATE_EVENT);
$cell->innerHTML.=$radiogroup->get_option($rb, $sc_private_event);
$cell->add_html_element(new html_element('br'));
$row->add_cell($cell);
$table->add_row($row);

$tabstrip->add_html_element($table);

$div = new html_element('div');
$div->add_html_element(new checkbox('show_todos','show_todos','1', $cal_show_todos, $cal_settings['show_todos']));
$div->innerHTML.='<br />';
$div->add_html_element(new checkbox('check_conflicts','check_conflicts','1', $cal_check_conflicts, $cal_settings['check_conflicts']));
$div->innerHTML.='<br />';
$div->add_html_element(new checkbox('email_reminders','email_reminders','1', $cal_email_reminders, $cal_settings['email_reminders']));
$tabstrip->add_html_element($div);

$tabstrip->add_html_element(new button($cmdSave, "javascript:_save('save_calendar_settings');"));
?>
