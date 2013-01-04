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

require_once ($GO_LANGUAGE->get_language_file('groups'));

require_once($GO_CONFIG->class_path.'base/groups.class.inc.php');
$GO_GROUPS = new GO_GROUPS();

$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';

switch ($_POST['task'])
{
	case 'groups':
		if(isset($_POST['delete_keys']))
		{
			try{
				if(!$GO_MODULES->modules['groups']['write_permission'])
				{
					throw new AccessDeniedException();
				}

				$response['deleteSuccess']=true;
				$groups = json_decode(($_POST['delete_keys']));

				foreach($groups as $group_id)
				{
					if ($group_id == 1)
					{
						throw new Exception($lang['groups']['noDeleteAdmins']);
					} elseif($group_id == 2) {
						throw new Exception($lang['groups']['noDeleteEveryone']);
					} else {
						$GO_EVENTS->fire_event('delete_group', array($group_id));
						$GO_GROUPS->delete_group($group_id);
					}
				}
			}catch(Exception $e)
			{
				$response['deleteSuccess']=false;
				$response['deleteFeedback']=$e->getMessage();
			}
		}
		
		$query = isset($_REQUEST['query']) ? '%'.$_REQUEST['query'].'%' : '';

		//$user_id = (!$GO_MODULES->modules['groups']['read_permission']) ? $GO_SECURITY->user_id : 0;
		if(!empty($_POST['for_managing']))
			$response['total'] = $GO_GROUPS->get_authorized_groups($GO_SECURITY->user_id, $start, $limit, $sort, $dir, $query);
		else{
			
			$user_id = !empty($GO_CONFIG->show_all_user_groups_in_mail) && !empty($_POST['for_mail']) ? 0 : $GO_SECURITY->user_id;
			
			$response['total'] = $GO_GROUPS->get_groups($user_id, $start, $limit, $sort, $dir,$query);
		}
		
		$response['results']=array();
		while($GO_GROUPS->next_record())
		{
			if ($GO_GROUPS->f('id') != 2)
			{
				$record = array(
					'id' => $GO_GROUPS->f('id'),
					'name' => $GO_GROUPS->f('name'),
					'user_id' => $GO_GROUPS->f('user_id'),
					'acl_id' => $GO_GROUPS->f('acl_id'),
					'admin_only' => $GO_GROUPS->f('admin_only'),
					'user_name' => String::format_name($GO_GROUPS->f('last_name'), $GO_GROUPS->f('first_name'), $GO_GROUPS->f('middle_name'))
				);
				$response['results'][] = $record;
			}
		}

		echo json_encode($response);
		break;

	case 'groups_all':
		
		$user_id = (!$GO_MODULES->modules['groups']['read_permission']) ? $GO_SECURITY->user_id : 0;

		$response['total'] = $GO_GROUPS->get_authorized_groups($user_id, $start, $limit, $sort, $dir);
		$response['results']=array();
		while($GO_GROUPS->next_record())
		{
			if ($GO_GROUPS->f('id') != 1)
			{
				$record = array(
					'id' => $GO_GROUPS->f('id'),
					'name' => $GO_GROUPS->f('name'),
					'user_id' => $GO_GROUPS->f('user_id'),
					'acl_id' => $GO_GROUPS->f('acl_id'),
					'admin_only' => $GO_GROUPS->f('admin_only'),
					'user_name' => String::format_name($GO_GROUPS->f('last_name'), $GO_GROUPS->f('first_name'), $GO_GROUPS->f('middle_name'))
				);
				$response['results'][] = $record;
			}
		}

		echo json_encode($response);
		break;

	case 'users_in_group':
		$response=array();

		if(isset($_REQUEST['query'])){$query = $_REQUEST['query'];}else{$query = '';};
		$group_id = $_REQUEST['group_id'];
		$response['total'] = $GO_GROUPS->get_users_in_group($group_id, $start, $limit, $sort, $dir, $query);
		$response['results']=array();
		while($GO_GROUPS->next_record())
		{
			$record = array(
				'id' => $GO_GROUPS->f('id'),
				'user_id' => $GO_GROUPS->f('user_id'),
				'acl_id' => $GO_GROUPS->f('acl_id'),
				'admin_only' => $GO_GROUPS->f('admin_only'),
				'name' => String::format_name($GO_GROUPS->f('last_name'), $GO_GROUPS->f('first_name'), $GO_GROUPS->f('middle_name')),
				'email' => $GO_GROUPS->f('email'),
				'username' => $GO_GROUPS->f('username')
			);
			$response['results'][] = $record;
		}
		echo json_encode($response);
		break;

	case 'user_groups_string':

		require_once($GO_CONFIG->class_path.'mail/RFC822.class.inc');
		$RFC822 = new RFC822();
		$groups = explode(',', $_REQUEST['user_groups']);

		$response = '';
		foreach($groups as $group_id)
		{
			$GO_GROUPS->get_users_in_group($group_id);
			while($user = $GO_GROUPS->next_record())
			{
				if(!empty($user['email']))
					$response .= $RFC822->write_address(String::format_name($user), $user['email']).', ';
			}
		}
		echo $response;
		break;
}


?>