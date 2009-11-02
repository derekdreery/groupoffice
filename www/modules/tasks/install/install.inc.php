<?php
$module = $this->get_module('tasks');

global $GO_USERS, $GO_SECURITY;

require_once($module['class_path'].'tasks.class.inc.php');
$tasks = new tasks();

$GO_USERS->get_users();
while($GO_USERS->next_record())
{
	$user = $GO_USERS->record;		
		
	$tasklist['name']=String::format_name($user);
	$tasklist['user_id']=$user['id'];
	$tasklist['acl_id']=$GO_SECURITY->get_new_acl('tasks', $user['id']);
	
	$tasks->add_tasklist($tasklist);
}
