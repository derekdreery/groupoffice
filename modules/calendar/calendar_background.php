<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.5 $ $Date: 2006/11/21 16:25:36 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
require_once("../../Group-Office.php");
load_basic_controls();

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');
require_once($GO_LANGUAGE->get_language_file('calendar'));

require_once($GO_MODULES->path.'classes/calendar.class.inc');
$cal = new calendar();


$task = isset($_POST['task']) ? $_POST['task'] : '';
$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$calendar_id = isset($_REQUEST['calendar_id']) ? $_REQUEST['calendar_id'] : 0;
$calendar_background_id = isset($_REQUEST['calendar_background_id']) ? $_REQUEST['calendar_background_id'] : 0;

$hours = array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23");
$mins = array ("00", "15",  "30",  "45");


if($task == 'save')
{
	$calendar_background['weekday'] = $_POST['weekday'];
	$calendar_background['start_time'] = $_POST['start_hour']*3600+$_POST['start_min']*60;
	$calendar_background['end_time'] = $_POST['end_hour']*3600+$_POST['end_min']*60;
	$calendar_background['background_id'] = $_POST['background_id'];
	$calendar_background['calendar_id'] = $_POST['calendar_id'];
	
	if($calendar_background_id>0)
	{
		$calendar_background['id'] = $calendar_background_id;
		$cal->update_calendar_background($calendar_background);
	}else
	{
		$calendar_background_id = $cal->add_calendar_background($calendar_background);
	}
	
	if($_POST['close']=='true')
	{
		header('Location: '.$return_to);
		exit();
	}
}



if ($calendar_background_id > 0 && $task != 'save')
{
	$calendar_background = $cal->get_calendar_background($calendar_background_id);
}else
{
	$calendar_background['calendar_id'] = $calendar_id;
	if(isset($_POST['start_hour']))
	{	
		$calendar_background['start_time'] = $_POST['start_hour']*3600+$_POST['start_min']*60;
		$calendar_background['end_time'] = $_POST['end_hour']*3600+$_POST['end_min']*60;
	}else
	{
		$calendar_background['start_time'] = 0;
		$calendar_background['end_time'] = 23*3600;
	}
	
	$calendar_background['background_id'] = isset($_POST['background_id']) ? $_POST['background_id'] : '';
	$calendar_background['weekday'] = isset($_POST['weekday']) ? $_POST['weekday'] : '0';
}

$tabstrip = new tabstrip('calendar_background_strip', $cal_background_color);
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to($return_to);


require_once($GO_THEME->theme_path.'header.inc');

$form = new form('calendar_background_form');
$form->set_attribute('enctype','multipart/form-data');

$form->add_html_element(new input('hidden', 'calendar_id',$calendar_background['calendar_id'],false));
$form->add_html_element(new input('hidden', 'calendar_background_id',$calendar_background_id,false));
$form->add_html_element(new input('hidden', 'task','',false));
$form->add_html_element(new input('hidden', 'close','false',false));
$form->add_html_element(new input('hidden', 'return_to',$return_to,false));



if(isset($feedback))
{
	$p = new html_element('p',$feedback);
	$p->set_attribute('class','Error');
	$tabstrip->add_html_element($p);
}

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($cal_weekday.': '));

$select = new select('weekday', $calendar_background['weekday']);
for($i=0;$i<7;$i++)
{
	$select->add_value($i, $full_days[$i]);
}

$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);
		
$row = new table_row();
$row->add_cell(new table_cell($sc_start_at.':'));

$start_hour = gmdate('G', $calendar_background['start_time']);
$start_min = gmdate('i', $calendar_background['start_time']);

$end_hour = gmdate('G', $calendar_background['end_time']);
$end_min = gmdate('i', $calendar_background['end_time']);

$select_hour = new select("start_hour", $start_hour);
$select_hour->set_attribute('onchange','javascript:update_end_hour();');
for ($i = 0; $i < 24; $i ++) {
	$select_hour->add_value($i, str_replace(':00', '', date($_SESSION['GO_SESSION']['time_format'], mktime($i, 0, 0))));
}

$select_min = new select('start_min', $start_min);
$select_min->set_attribute('onchange','javascript:document.event_form.end_min.value=this.value;');
$select_min->add_arrays($mins, $mins);

$row->add_cell(new table_cell($select_hour->get_html().'&nbsp;:&nbsp;'.$select_min->get_html()));
$table->add_row($row);			

$row = new table_row();
$row->add_cell(new table_cell($sc_end_at.':'));

$select_hour = new select("end_hour", $end_hour);
for ($i = 0; $i < 24; $i ++) {
	$select_hour->add_value($i, str_replace(':00', '', date($_SESSION['GO_SESSION']['time_format'], mktime($i, 0, 0))));
}
$select_min = new select('end_min', $end_min);
$select_min->add_arrays($mins, $mins);

$row->add_cell(new table_cell($select_hour->get_html().'&nbsp;:&nbsp;'.$select_min->get_html()));		
$table->add_row($row);			

$row = new table_row();
$row->add_cell(new table_cell($cal_background.': '));

$select = new select('background_id', $calendar_background['background_id']);
$cal->get_backgrounds();
while($cal->next_record())
{
	$select->add_value($cal->f('id'), $cal->f('name'));
}
$row->add_cell(new table_cell($select->get_html()));
$table->add_row($row);

$tabstrip->add_html_element($table);

$tabstrip->add_html_element(new button($cmdOk,"javascript:document.forms[0].close.value='true';document.forms[0].task.value='save';document.forms[0].submit()"));
$tabstrip->add_html_element(new button($cmdApply,"javascript:document.forms[0].task.value='save';document.forms[0].submit()"));
$tabstrip->add_html_element(new button($cmdClose,"javascript:document.location='".htmlspecialchars($return_to)."'"));	

$form->add_html_element($tabstrip);

echo $form->get_html();

require_once($GO_THEME->theme_path.'footer.inc');
?>
