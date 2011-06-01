<?php

/**
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
require_once("../../Group-Office.php");
GO::security()->json_authenticate('groups');
require_once(GO::language()->get_language_file('groups'));

GO::security()->check_token();

require_once(GO::config()->class_path.'base/groups.class.inc.php');
$GO_GROUPS = new GO_GROUPS();

$action = isset($_REQUEST['action']) ? ($_REQUEST['action']) : null;

/* case delete */
$groups = isset($_REQUEST['groups']) ? json_decode(($_REQUEST['groups']), true) : null;

/* case save_group */
$name = isset($_REQUEST['name']) ? ($_REQUEST['name']) : null;

/* case delete user from group */
$delete_users = isset($_REQUEST['delete_users']) ? json_decode(($_REQUEST['delete_users']), true) : null;
$group_id = isset($_REQUEST['group_id']) ? ($_REQUEST['group_id']) : null;

/* case add user to group */
$users = isset($_REQUEST['users']) ? json_decode(($_REQUEST['users']), true) : null;

$feedback = null;

try {
	switch ($action) {
		case 'save_group':
			$response['success'] = true;
			$response['feedback'] = $feedback;

			if(!GO::modules()->modules['groups']['write_permission'])
				$admin_only=-1;
			else
				$admin_only = isset($_POST['admin_only'])? 1 : 0;

			if ($group_id < 1) {

				if(!GO::modules()->modules['groups']['write_permission'])
				{
					throw new AccessDeniedException();
				}

				//insert
				if ($GO_GROUPS->get_group_by_name($name)) {
					$response['feedback'] = $lang['groups']['groupNameAlreadyExists'];
					$response['success'] = false;
				} else {
					$acl_id = GO::security()->get_new_acl('group', GO::security()->user_id);
					$new_group_id = $GO_GROUPS->add_group(GO::security()->user_id, $name, $admin_only, $acl_id);
					if (!$new_group_id) {
						GO::security()->delete_acl($acl_id);
						throw new DatabaseInsertException();
					} else {
						$response['group_id'] = $new_group_id;
						$response['acl_id']=$acl_id;
						$response['admin_only']=$admin_only;
					}
				}
			} else {
				// update
				if (!$GO_GROUPS->update_group($group_id, $name, $admin_only)) {
					throw new DatabaseUpdateException();
				}
			}

			GO::events()->fire_event('save_group', array($group_id, $name, &$response));


			break;
	}
	
} catch (Exception $e) {
	$response['feedback'] = $e->getMessage();
	$response['success'] = false;
}
echo json_encode($response);