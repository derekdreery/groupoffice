<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: json.php 2315 2008-07-08 08:18:39Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Boy Wijnmaalen <bwijnmaalen@intermesh.nl>
 */

require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('groups');

$sort = isset($_REQUEST['sort']) ? smart_addslashes($_REQUEST['sort']) : 'name';
$dir = isset($_REQUEST['dir']) ? smart_addslashes($_REQUEST['dir']) : 'ASC';
$start = isset($_REQUEST['start']) ? smart_addslashes($_REQUEST['start']) : '0';
$limit = isset($_REQUEST['limit']) ? smart_addslashes($_REQUEST['limit']) : '0';

$action = isset($_REQUEST['action']) ? smart_addslashes($_REQUEST['action']) : 'null';
$group_id = isset($_REQUEST['group_id']) ? smart_addslashes($_REQUEST['group_id']) : 'null';
$query = isset($_REQUEST['query']) ? '%'.smart_addslashes($_REQUEST['query']).'%' : null;
$search_field = isset($_REQUEST['search_field']) ? smart_addslashes($_REQUEST['search_field']) : null;

switch ($action)
{
	case 'groups':


		if(isset($_POST['delete_keys']))
		{
			try{
				$response['deleteSuccess']=true;
				$groups = json_decode(smart_stripslashes($_POST['delete_keys']));

				foreach($groups as $group_id)
				{
					if ($group_id == 1)
					{
						throw new Exception($lang['groups']['noDeleteAdmins']);
					} elseif($group_id == 2) {
						throw new Exception($lang['groups']['noDeleteEveryone']);
					} else {
						$GO_GROUPS->delete_group($group_id);
					}
				}
			}catch(Exception $e)
			{
				$response['deleteSuccess']=false;
				$response['deleteFeedback']=$e->getMessage();
			}
		}

		$response['total'] = $GO_GROUPS->get_groups(null, $start, $limit, $sort, $dir);
		$response['results']=array();
		while($GO_GROUPS->next_record())
		{
			if ($GO_GROUPS->f('id') != 2)
			{
				$record = array(
					'id' => $GO_GROUPS->f('id'),
					'name' => $GO_GROUPS->f('name'),
					'user_id' => $GO_GROUPS->f('user_id'),
					'user_name' => String::format_name($GO_GROUPS->f('last_name'), $GO_GROUPS->f('first_name'), $GO_GROUPS->f('middle_name'))
				);
				$response['results'][] = $record;
			}
		}

		echo json_encode($response);
		break;
	case 'users_in_group':

		$response=array();
		if(isset($_POST['add_users']))
		{
			$users = json_decode(smart_stripslashes($_POST['add_users']));

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
		}


		$response['total'] = $GO_GROUPS->get_users_in_group($group_id, $start, $limit, $sort, $dir);
		$response['results']=array();
		while($GO_GROUPS->next_record())
		{
			$record = array(
				'id' => $GO_GROUPS->f('id'),
				'user_id' => $GO_GROUPS->f('user_id'),
				'name' => String::format_name($GO_GROUPS->f('last_name'), $GO_GROUPS->f('first_name'), $GO_GROUPS->f('middle_name')),
				'email' => $GO_GROUPS->f('email')
			);
			$response['results'][] = $record;
		}
		echo json_encode($response);
		break;
	case 'users':
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