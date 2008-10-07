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
 * @author Boy Wijnmaalen <bwijnmaalen@intermesh.nl>
 */

require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('groups');
require_once($GO_LANGUAGE->get_language_file('groups'));

$action = isset($_REQUEST['action']) ? smart_addslashes($_REQUEST['action']) : null;

/* case delete */
$groups = isset($_REQUEST['groups']) ? json_decode(smart_stripslashes($_REQUEST['groups']), true) : null;

/* case save_group*/
$name = isset($_REQUEST['name']) ? smart_addslashes($_REQUEST['name']) : null;

/* case delete user from group */
$delete_users = isset($_REQUEST['delete_users']) ? json_decode(smart_stripslashes($_REQUEST['delete_users']), true) : null;
$group_id = isset($_REQUEST['group_id']) ? smart_addslashes($_REQUEST['group_id']) : null;

/* case add user to group */
$users = isset($_REQUEST['users']) ? json_decode(smart_stripslashes($_REQUEST['users']), true) : null;

$feedback = null;

switch($action)
{
	case 'delete':
		$result['success'] = true;
		$result['feedback'] = $feedback;

		foreach($groups as $id)
		{
			if ($id == 1)
			{
				$result['success'] = false;
				$feedback = $lang['groups']['noDeleteAdmins'];
			} elseif($id == 2) {
				$result['success'] = false;
				$feedback = $lang['groups']['noDeleteEveryone'];
			} else {
				if(!$GO_GROUPS->delete_group($id))
				{
					$result['success'] = false;
					$result['feedback'] = $lang['common']['deleteError'];
				}
			}
		}

		echo json_encode($result);
		break;
	case 'save_group':
		$result['success'] = true;
		$result['feedback'] = $feedback;

		if ($group_id < 1)
		{
			//insert
			if ($GO_GROUPS->get_group_by_name($name))
			{
				$result['feedback'] = $lang['groups']['groupNameAlreadyExists'];
				$result['success'] = false;
			} else {
				$new_group_id = $GO_GROUPS->add_group($GO_SECURITY->user_id, $name);
				if (!$new_group_id)
				{
					$result['feedback'] = $lang['common']['saveError'];
					$result['success'] = false;
				} else {
					$result['group_id'] = $new_group_id;
				}
			}
		} else {
			// update
			if(!$GO_GROUPS->update_group($group_id, $name))
			{
				$result['feedback'] = $lang['common']['saveError'];
				$result['success'] = false;
			} else {
				$result['group_id'] = $group_id;
			}
		}
		echo json_encode($result);
		break;
	case 'delete_user_from_group':
		$result['success'] = true;
		$result['feedback'] = $feedback;

		foreach($delete_users as $user_id)
		{
			if(!$GO_GROUPS->delete_user_from_group($user_id, $group_id))
			{
				$result['success'] = false;
				$result['feedback'] = $lang['common']['deleteError'];
			}
		}

		echo json_encode($result);
		break;
	case 'add_user_to_group':
		$result['success'] = true;
		$result['feedback'] = $feedback;

		foreach($users as $user_id)
		{
			if (!$GO_GROUPS->is_in_group($user_id, $group_id))
			{
				if(!$GO_GROUPS->add_user_to_group($user_id, $group_id))
				{
					$result['success'] = false;
					$result['feedback'] = $lang['common']['saveError'];
				}
			}
		}

		echo json_encode($result);
		break;
}
?>