<?php
require($GLOBALS['GO_LANGUAGE']->get_language_file('tasks'));

if(isset($GLOBALS['GO_MODULES']->modules['customfields']))
{
	require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	$GO_SCRIPTS_JS .= $cf->get_javascript(12, $lang['tasks']['name']);
}

require_once($GLOBALS['GO_MODULES']->modules['tasks']['class_path'].'tasks.class.inc.php');
$tasks = new tasks();

$settings = $tasks->get_settings($GLOBALS['GO_SECURITY']->user_id);
$tasklist = $tasks->get_tasklist($settings['default_tasklist_id']);
if(!$tasklist){
	$tasklist=array('id'=>0, 'name'=>'');
}else
{
	set_multiselectgrid_selections('tasklists',$tasklist['id'], $GLOBALS['GO_SECURITY']->user_id);
}

$show = $GLOBALS['GO_CONFIG']->get_setting("tasks_filter", $GLOBALS['GO_SECURITY']->user_id);

if(!$show)
	$show='active';

$GO_SCRIPTS_JS .='GO.tasks.defaultTasklist = {id: '.$tasklist['id'].', name: "'.$tasklist['name'].'"};
GO.tasks.show="'.$show.'";';

$GO_SCRIPTS_JS .= ';GO.tasks.remind="'.$settings['remind'].'";
GO.tasks.reminderDaysBefore=parseInt('.$settings['reminder_days'].');
GO.tasks.reminderTime="'.$settings['reminder_time'].'";';