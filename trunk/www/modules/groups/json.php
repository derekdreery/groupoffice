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

$GO_SECURITY->check_token();

require_once($GO_CONFIG->class_path.'base/groups.class.inc.php');
$GO_GROUPS = new GO_GROUPS();

require_once ($GO_LANGUAGE->get_language_file('groups'));

$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';

$action = isset($_REQUEST['action']) ? ($_REQUEST['action']) : 'null';
$group_id = isset($_REQUEST['group_id']) ? ($_REQUEST['group_id']) : 'null';
$query = !empty($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : null;
$search_field = isset($_REQUEST['search_field']) ? ($_REQUEST['search_field']) : null;

switch ($action)
{
	case 'users_in_group':
		
		$response=array();
		if(isset($_POST['delete_keys']))
		{
			try{
				$response['deleteSuccess']=true;
				$users = json_decode($_POST['delete_keys'], true);

				foreach($users as $user_id)
				{
					if($group_id==1 && $user_id==1)
					{
						throw new Exception($lang['groups']['dontRemoveAdmin']);
					}
					$GO_GROUPS->delete_user_from_group($user_id, $group_id);
				}

				$GO_EVENTS->fire_event('delete_users_to_group', array($group_id, $users));
			}catch(Exception $e)
			{
				$response['deleteSuccess']=false;
				$response['deleteFeedback']=$e->getMessage();
			}
		}

		
		if(isset($_POST['add_users']))
		{
			$users = json_decode($_POST['add_users'], true);

			foreach($users as $user_id)
			{
				if (!$GO_GROUPS->is_in_group($user_id, $group_id))
				{
					if(!$GO_GROUPS->add_user_to_group($user_id, $group_id))
					{
						$response['addSuccess'] = false;
						$response['addFeedback'] = $lang['common']['saveError'];
						break;
					}
				}
			}
			$GO_EVENTS->fire_event('add_users_to_group', array($group_id, $users));
		}


		$response['total'] = $GO_GROUPS->get_users_in_group($group_id, $start, $limit, $sort, $dir);
		$response['results']=array();
		while($GO_GROUPS->next_record())
		{
			$record = array(
				'id' => $GO_GROUPS->f('id'),
				'user_id' => $GO_GROUPS->f('user_id'),
				'acl_id' => $GO_GROUPS->f('acl_id'),
				'admin_only' => $GO_GROUPS->f('admin_only'),
				'name' => String::format_name($GO_GROUPS->f('last_name'), $GO_GROUPS->f('first_name'), $GO_GROUPS->f('middle_name')),
				'email' => $GO_GROUPS->f('email')
			);
			$response['results'][] = $record;
		}
		echo json_encode($response);
		break;
	case 'users':

		require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		$GO_USERS->search($query, $search_field, 0, $start, $limit, $sort,$dir);

		while($GO_USERS->next_record())
		{
			$record[]=array(
				'id'=>$GO_USERS->f('id'),
				'name'=>String::format_name($GO_USERS->f('last_name'),$GO_USERS->f('first_name'),$GO_USERS->f('middle_name')),
				'email' => $GO_USERS->f('email')
			);
		}

		echo '{total:'.count($record).',results:'.json_encode($record).'}';
		break;
}


?>