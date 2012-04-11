<?php
/**
 * This file can be put in the same directory as your config.php file is located.
 * You can add your own listeners to do some actions at particular events.
 */

//Register the listener. This will be fired when a user is added
if(isset($events)){
	$events->add_listener('add_user', __FILE__, '', 'local_add_user_listener');
}

//The function to be called. This example adds a user to the user group
//domain.com. Where domain.com is the domain part of the user's e-mail address.

function local_add_user_listener($user){

	global $GO_CONFIG, $GO_SECURITY;

	//load the group management class
	require_once($GLOBALS['GO_CONFIG']->class_path.'base/groups.class.inc.php');
	$GO_GROUPS = new GO_GROUPS();

	$arr = explode('@', $user['email']);

	$domain = $arr[1];

	//get the group.
	$group = $GO_GROUPS->get_group_by_name($domain);
	if(!$group){
		//group doesn't exist so create it.
		$group_id = $GO_GROUPS->add_group(1, $domain, 0, $GLOBALS['GO_SECURITY']->get_new_acl('groups'));
	}else
	{
		$group_id = $group['id'];
	}

	//add the user to the group
	$GO_GROUPS->add_user_to_group($user['id'], $group_id);

}

