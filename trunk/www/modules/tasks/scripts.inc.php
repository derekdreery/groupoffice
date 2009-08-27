<?php
require_once($GO_MODULES->modules['tasks']['class_path'].'tasks.class.inc.php');
$tasks = new tasks();


$settings = $tasks->get_settings($GO_SECURITY->user_id);
$tasklist = $tasks->get_tasklist($settings['default_tasklist_id']);
if(!$tasklist){
	$tasklist=array('id'=>0, 'name'=>'');
}

$GO_SCRIPTS_JS .='GO.tasks.defaultTasklist = {id: '.$tasklist['id'].', name: "'.$tasklist['name'].'"};
GO.tasks.showInactive=';

if($GO_CONFIG->get_setting("tasks_show_inactive", $GO_SECURITY->user_id)=='1') $GO_SCRIPTS_JS .= 'true'; else $GO_SCRIPTS_JS .= 'false';

$GO_SCRIPTS_JS .= ';GO.tasks.showCompleted=';

if($GO_CONFIG->get_setting('tasks_show_completed', $GO_SECURITY->user_id)=='1') $GO_SCRIPTS_JS .= 'true'; else $GO_SCRIPTS_JS .= 'false';

$GO_SCRIPTS_JS .= ';GO.tasks.remind="'.$settings['remind'].'";
GO.tasks.reminderDaysBefore=parseInt('.$settings['reminder_days'].');
GO.tasks.reminderTime="'.$settings['reminder_time'].'"';