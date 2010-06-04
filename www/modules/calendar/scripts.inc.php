<?php
if(isset($GO_MODULES->modules['customfields']))
{
	require($GO_LANGUAGE->get_language_file('calendar'));
	require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	$GO_SCRIPTS_JS .= $cf->get_javascript(1, $lang['calendar']['name']);
}

require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc.php');
$cal = new calendar();


$settings = $cal->get_settings($GO_SECURITY->user_id);
$calendar = $cal->get_calendar($settings['calendar_id']);
if(!$calendar){
	$calendar=array('id'=>0,'name'=>'');
}

$group = $cal->get_group(1);
$calendar['fields'] = $group['fields'];

$reminder = $cal->reminder_seconds_to_form_input($settings['reminder']);

$GO_SCRIPTS_JS .= 'GO.calendar.defaultCalendar = '.json_encode($calendar).';
GO.calendar.defaultBackground="'.$settings['background'].'";
GO.calendar.defaultReminderValue="'.$reminder['reminder_value'].'";
GO.calendar.defaultReminderMultiplier="'.$reminder['reminder_multiplier'].'";
GO.calendar.defaultGroupFields="'.$calendar['fields'].'";';
?>
