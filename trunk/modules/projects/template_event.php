<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.5 $ $Date: 2006/11/22 09:35:41 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once ("../../Group-Office.php");

load_basic_controls();

$GO_HEADER['head'] ='';

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('projects');
require_once ($GO_LANGUAGE->get_language_file('projects'));
require_once ($GO_LANGUAGE->get_language_file('calendar'));


$page_title = $lang_modules['projects'];
require_once ($GO_MODULES->class_path."projects.class.inc");
$projects = new projects();

$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$template_event_id = isset ($_REQUEST['template_event_id']) ? $_REQUEST['template_event_id'] : 0;
$template_id = isset ($_REQUEST['template_id']) ? $_REQUEST['template_id'] : 0;

$return_to = isset ($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = $_SERVER['PHP_SELF'].'?template_event_id='.$template_event_id.'&return_to='.urlencode($return_to);


switch ($task) {
	case 'save_template_event' :
		//translate the given date stamp to unix time
		$template_event['todo'] = smart_addslashes(trim($_POST['todo']));
		$template_event['name'] = smart_addslashes(trim($_POST['name']));
		$template_event['description'] = smart_addslashes(trim($_POST['description']));
		$template_event['reminder'] = $_POST['reminder_value']*$_POST['reminder_multiplier'];
		
		$template_event['time_offset'] = 
			isset ($_POST['time_offset_value']) ? $_POST['time_offset_value']*$_POST['time_offset_multiplier']+
			$_POST['time_offset_hour']*3600+$_POST['time_offset_min']*60 : '0';
			
		$template_event['duration'] = isset ($_POST['duration_value']) ? $_POST['duration_value']*$_POST['duration_multiplier'] : '3600';
	
		if ($template_event_id > 0) {
			if ($template_event['name'] == '') {
				$feedback = $error_missing_field;
			} else {

				$template_event['id'] = $template_event_id;
				if (!$projects->update_template_event($template_event)) {						
					$feedback = $strSaveError;
				} else {
					if ($_POST['close'] == 'true') {
						header('Location: '.$return_to);
						exit ();
					}
				}
			}
		} else {
			if ($template_event['name'] == '') {
				$feedback = $error_missing_field;
			} else {				
				$template_event['template_id'] = $template_id;		
					
				if (!$template_event_id = $projects->add_template_event($template_event)) {
					$feedback = $strSaveError;
				} else {
					if ($_POST['close'] == 'true') {
						header('Location: '.$return_to);
						exit ();
					}
				}
			}
		}
		break;
}

$pm_settings = $projects->get_settings($GO_SECURITY->user_id);

if ($template_event_id > 0) {
	$template_event = $projects->get_template_event($template_event_id);
	
	$tabstrip = new tabstrip('template_event_tabstrip_'.$template_event_id, $template_event['name']);
	$tabstrip->set_attribute('style','width:100%');
	
	
} else {
	$tabstrip = new tabstrip('template_event_tab', $pm_new_template_event);
}
$tabstrip->set_attribute('style','width:100%');
$tabstrip->set_return_to(htmlspecialchars($return_to));

if ($template_event_id == 0 || $task == 'save_template_event') {
	$template_event['todo'] = isset ($_POST['todo']) ? smart_stripslashes($_POST['todo']) : '1';
	$template_event['name'] = isset ($_POST['name']) ? smart_stripslashes($_POST['name']) : '';
	$template_event['description'] = isset ($_POST['description']) ? smart_stripslashes($_POST['description']) : '';
	$template_event['time_offset'] = isset ($_POST['time_offset_value']) ? $_POST['time_offset_value']*$_POST['time_offset_multiplier']+$_POST['time_offset_hour']*3600+$_POST['time_offset_min']*60 : '0';
	$template_event['duration'] = isset ($_POST['duration_value']) ? $_POST['duration_value']*$_POST['duration_multiplier'] : '3600';
	
	require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
	$cal = new calendar();
	
	$cal_settings = $cal->get_settings($GO_SECURITY->user_id);
	
	$template_event['reminder'] = isset ($_POST['reminder_value']) ? $_POST['reminder_value']*$_POST['reminder_multiplier'] : $cal_settings['reminder'];
}



$form = new form('projects_form');
$form->add_html_element(new input('hidden', 'close', 'false'));
$form->add_html_element(new input('hidden', 'template_event_id', $template_event_id, false));
$form->add_html_element(new input('hidden', 'template_id', $template_id, false));
$form->add_html_element(new input('hidden', 'task', '', false));
$form->add_html_element(new input('hidden', 'return_to',$return_to));


$GO_HEADER['body_arguments'] = 'onload="document.forms[0].name.focus();"';

require_once ($GO_THEME->theme_path."header.inc");

		
if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $tabstrip->add_html_element($p);
}

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell($strType.':'));		
$radiogroup = new radiogroup('todo', $template_event['todo']);
$event_button = new radiobutton('event', '0');
$todo_button = new radiobutton('todo', '1');
$row->add_cell(new table_cell($radiogroup->get_option($event_button, $cal_event).$radiogroup->get_option($todo_button, $cal_todo)));
$table->add_row($row);

$row = new table_row();

$row->add_cell(new table_cell($strName.':*'));		

$input = new input('text', 'name', $template_event['name']);
$input->set_attribute('maxlength','50');
$input->set_attribute('style','width:250px;');
$row->add_cell(new table_cell($input->get_html()));			
$table->add_row($row);


$row = new table_row();
$cell = new table_cell($strComments.':');
$cell->set_attribute('style','vertical-align:top');
$row->add_cell($cell);		
$textarea = new textarea('description', $template_event['description']);
$textarea->set_attribute('style','width:500px; height:80px;');
$row->add_cell(new table_cell($textarea->get_html()));			
$table->add_row($row);



$row = new table_row();
$cell = new table_cell($pm_template_offset.':');
$row->add_cell($cell);

$cell = new table_cell();

$multipliers[] = 604800;
$multipliers[] = 86400;





$time_offset_multiplier = 86400;
$time_offset_value = 0;
$time_offset_hour = date('G', get_time());
$time_offset_min = '00';


if($template_event['time_offset'] != 0)
{
	$time_offset_days = floor($template_event['time_offset']/86400);
	
	$time_offset_weeks = $time_offset_days/7;
	$match = (int)$time_offset_weeks;
	
	if($match == $time_offset_weeks)
	{
		$time_offset_multiplier = 604800;
		$time_offset_value = $time_offset_weeks;
	}else
	{
		$time_offset_multiplier = 86400;
		$time_offset_value = $time_offset_days;	
	}
	
	//$time_left = $template_event['time_offset'] - $time_offset_days*86400;
	
	$time_offset_hour = gmdate('G', $template_event['time_offset']);
	$time_offset_min = gmdate('i', $template_event['time_offset']);
	
}
$select = new select('time_offset_value', $time_offset_value);
for ($i = 0; $i < 60; $i ++) {
	$select->add_value($i, "$i");
}
$cell->add_html_element($select);

$select = new select('time_offset_multiplier', $time_offset_multiplier);
$select->add_value('86400', $sc_days);
$select->add_value('604800', $sc_weeks);
$cell->add_html_element($select);

$cell->innerHTML .= ' '.$pm_template_after;


$mins = array ("00", "05", "10", "15", "20", "25", "30", "35", "40", "45", "50", "55");

$select = new select("time_offset_hour", $time_offset_hour);
for ($i = 0; $i < 24; $i ++) {
	$select->add_value($i, str_replace(':00', '', date($_SESSION['GO_SESSION']['time_format'], mktime($i, 0, 0))));
}

$cell->innerHTML .= '&nbsp;'.$select->get_html().'&nbsp;:&nbsp;';

$select = new select('time_offset_min', $time_offset_min);
$select->add_arrays($mins, $mins);
$cell->add_html_element($select);




$row->add_cell($cell);
$table->add_row($row);

$multipliers[] = 3600;
$multipliers[] = 60;

$row = new table_row();
$cell = new table_cell($pm_duration.':');
$row->add_cell($cell);

$cell = new table_cell();

$duration_multiplier = 60;
$duration_value = 0;

for ($i = 0; $i < count($multipliers); $i ++) {
	$devided = $template_event['duration'] / $multipliers[$i];
	
	$match = (int) $devided;
	if ($match == $devided) {
		$duration_multiplier = $multipliers[$i];
		$duration_value = $devided;
		break;
	}
}

$select = new select('duration_value', $duration_value);
for ($i = 0; $i < 60; $i ++) {
	$select->add_value($i, $i);
}
$cell->add_html_element($select);

$select = new select('duration_multiplier', $duration_multiplier);
$select->add_value('60', $sc_mins);
$select->add_value('3600', $sc_hours);
$select->add_value('86400', $sc_days);
$select->add_value('604800', $sc_weeks);
$cell->add_html_element($select);

$row->add_cell($cell);
$table->add_row($row);


$row = new table_row();

$row->add_cell(new table_cell($sc_reminder.':'));

$cell = new table_cell();

$reminder_multiplier = 60;
$reminder_value = 0;

if($template_event['reminder'] != 0)
{
	for ($i = 0; $i < count($multipliers); $i ++) {
		$devided = $template_event['reminder'] / $multipliers[$i];
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



$tabstrip->add_html_element($table);


$tabstrip->add_html_element(new button($cmdOk, "javascript:_save('save_template_event', 'true');"));
$tabstrip->add_html_element(new button($cmdApply, "javascript:_save('save_template_event', 'false')"));
$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));				

$form->add_html_element($tabstrip);
echo $form->get_html();
?>
<script type="text/javascript">
function _save(task, close)
{
	document.projects_form.task.value = task;
	document.projects_form.close.value = close;
	document.projects_form.submit();
}
</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");
?>
