<?php
$module = $this->get_module('calendar');

global $GO_USERS, $GO_LANGUAGE, $GO_SECURITY;

require_once($module['class_path'].'calendar.class.inc.php');
$cal = new calendar();

require($GO_LANGUAGE->get_language_file('calendar'));

$view['name']=$lang['calendar']['groupView'];
$view['user_id']=1;
$view['acl_read']=$GO_SECURITY->get_new_acl('view', 1);
$view['acl_write']=$GO_SECURITY->get_new_acl('view', 1);

$view_id = $cal->add_view($view);

$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_internal, $view['acl_write']);
	
$count=0;
$GO_USERS->get_users();
while($GO_USERS->next_record())
{
	$count++;
	$user = $GO_USERS->record;		
		
	$calendar['name']=String::format_name($user);
	$calendar['user_id']=$user['id'];
    $calendar['group_id']=1;
	$calendar['acl_read']=$GO_SECURITY->get_new_acl('category', $user['id']);
	$calendar['acl_write']=$GO_SECURITY->get_new_acl('category', $user['id']);
	
	$calendar_id = $cal->add_calendar($calendar);
	
	if($count<=20)
		$cal->add_calendar_to_view($calendar_id, '', $view_id);
}

$group['id'] = 1;
$group['user_id']=1;
$group['name']=$lang['calendar']['calendars'];
$group['acl_admin'] = $GO_SECURITY->get_new_acl('resource_group');
$cal->add_group($group);
