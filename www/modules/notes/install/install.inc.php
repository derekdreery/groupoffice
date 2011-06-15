<?php
$module = $this->get_module('notes');

global $GO_LANGUAGE, $GO_USERS, $GO_SECURITY, $GO_CONFIG;

require(GO::language()->get_language_file('notes'));

require_once($module['class_path'].'notes.class.inc.php');
$notes = new notes();

$category['name']=$lang['notes']['general'];
$category['user_id']=1;
$category['acl_id']=GO::security()->get_new_acl('notes', 1);


$notes->add_category($category);
GO::security()->add_group_to_acl(GO::config()->group_everyone, $category['acl_id'],2);

require_once(GO::config()->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

$GO_USERS->get_users();
while($user = $GO_USERS->next_record())
{
	$category['name']=String::format_name($user);
	$category['user_id']=$user['id'];
	$category['acl_id']=GO::security()->get_new_acl('category', $user['id']);
	$notes->add_category($category);
}
