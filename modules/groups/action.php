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
$GO_SECURITY->json_authenticate('groups');
require_once($GO_LANGUAGE->get_language_file('groups'));

$GO_SECURITY->check_token();

require_once($GO_CONFIG->class_path.'base/groups.class.inc.php');
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

			if(!$GO_MODULES->modules['groups']['write_permission'])
				$admin_only=-1;
			else
				$admin_only = isset($_POST['admin_only'])? 1 : 0;

			if ($group_id < 1) {

				if(!$GO_MODULES->modules['groups']['write_permission'])
				{
					throw new AccessDeniedException();
				}

				//insert
				if ($GO_GROUPS->get_group_by_name($name)) {
					$response['feedback'] = $lang['groups']['groupNameAlreadyExists'];
					$response['success'] = false;
				} else {
					$acl_id = $GO_SECURITY->get_new_acl('group', $GO_SECURITY->user_id);
					$new_group_id = $GO_GROUPS->add_group($GO_SECURITY->user_id, $name, $admin_only, $acl_id);
					if (!$new_group_id) {
						$GO_SECURITY->delete_acl($acl_id);
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

			$GO_EVENTS->fire_event('save_group', array($group_id, $name, &$response));


			break;
	}
	
} catch (Exception $e) {
	$response['feedback'] = $e->getMessage();
	$response['success'] = false;
}
echo json_encode($response);