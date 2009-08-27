<?php
require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc.php');
$cal = new calendar();


$settings = $cal->get_settings($GO_SECURITY->user_id);
$calendar = $cal->get_calendar($settings['calendar_id']);
if(!$calendar){
	$calendar=array('id'=>0,'name'=>'');
}
$reminder = $cal->reminder_seconds_to_form_input($settings['reminder']);

$GO_SCRIPTS_JS .= 'GO.calendar.defaultCalendar = {id: '.$calendar['id'].', name: "'.$calendar['name'].'"};
GO.calendar.defaultBackground="'.$settings['background'].'";
GO.calendar.defaultReminderValue="'.$reminder['reminder_value'].'";
GO.calendar.defaultReminderMultiplier="'.$reminder['reminder_multiplier'].'";';