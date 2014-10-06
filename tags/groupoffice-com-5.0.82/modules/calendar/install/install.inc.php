<?php
$module = $this->get_module('calendar');

global $GO_CONFIG, $GO_LANGUAGE, $GO_SECURITY;

require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

require_once($module['class_path'].'calendar.class.inc.php');
$cal = new calendar();

require($GLOBALS['GO_LANGUAGE']->get_language_file('calendar'));

$view['name']=$lang['calendar']['groupView'];
$view['user_id']=1;
$view['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('view', 1);

$view_id = $cal->add_view($view);

$GLOBALS['GO_SECURITY']->add_group_to_acl($GLOBALS['GO_CONFIG']->group_internal, $view['acl_id'], 2);

$count=0;
$GO_USERS->get_users();
while($user = $GO_USERS->next_record()) {
	$count++;

	$calendar['name']=String::format_name($user,'','','last_name');
	$calendar['user_id']=$user['id'];
	$calendar['group_id']=1;
	$calendar['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('calendar', $user['id']);


	$calendar_id = $cal->add_calendar($calendar);

	if($count<=20)
		$cal->add_calendar_to_view($calendar_id, '', $view_id);
}

$cal->query("REPLACE INTO go_db_sequence (nextid,seq_name) VALUES (1, 'cal_groups')");

$group['id'] = 1;
$group['user_id']=1;
$group['name']=$lang['calendar']['calendars'];
$cal->add_group($group);
