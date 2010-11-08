<?php
global $GO_CONFIG;
require_once($GO_CONFIG->root_path.'classes/base/users.class.inc.php');
require_once($GO_CONFIG->root_path.'classes/base/groups.class.inc.php');
$users = new GO_USERS();
$groups = new GO_GROUPS();
$users->get_users();
while ($user = $users->next_record()) {
	$groups->add_user_to_group($user['id'],$GO_CONFIG->group_everyone);
}
?>
