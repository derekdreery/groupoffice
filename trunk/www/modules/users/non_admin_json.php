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
$GO_SECURITY->json_authenticate();

$task = isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';

switch($task)
{
	case 'users':

		if(isset($_POST['delete_keys']))
		{
			require($GO_LANGUAGE->get_language_file('users'));
			try{
				if(!$GO_MODULES->modules['users']['read_permission'])
				{
					throw new AccessDeniedException();
				}

				$response['deleteSuccess']=true;
				$users = json_decode(($_POST['delete_keys']));

				foreach($users as $delete_user_id)
				{
					if ($delete_user_id == 1)
					{
						throw new Exception($lang['users']['deletePrimaryAdmin']);
					} elseif($delete_user_id == $GO_SECURITY->user_id) {
						throw new Exception($lang['users']['deleteYourself']);
					} else {
						$GO_USERS->delete_user($delete_user_id);
					}
				}
			}catch(Exception $e)
			{
				$response['deleteSuccess']=false;
				$response['deleteFeedback']=$e->getMessage();
			}
		}

		$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
		$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
		$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
		$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
		$query = isset($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : null;
		$search_field = isset($_REQUEST['search_field']) ? ($_REQUEST['search_field']) : null;
		//$user_id = isset($_REQUEST['user_id']) ? ($_REQUEST['user_id']) : null;
		

		$user_id = (!$GO_MODULES->modules['users']['read_permission']) ? $GO_SECURITY->user_id : 0;
		$response['total'] = $GO_USERS->search($query, $search_field, $user_id, $start, $limit, $sort,$dir);

		if($GO_MODULES->has_module('customfields')) {
			require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
			$cf = new customfields();
		}
		
		
		$response['results']=array();
		while($user=$GO_USERS->next_record())
		{			
			$user['name'] = String::format_name($GO_USERS->f('last_name'),$GO_USERS->f('first_name'),$GO_USERS->f('middle_name'));
			$user['lastlogin']=Date::get_timestamp($user['lastlogin']);
			$user['registration_time']=Date::get_timestamp($user['registration_time']);

			if(isset($cf)){
				$cf->format_record($user, 8, true);
			}
				
			$response['results'][]=$user;
		}

		echo json_encode($response);
		break;
	case 'start_module':
		$records=array();
		foreach($GO_MODULES->modules as $module)
		{
			if($module['admin_menu']=='0' &&
					(($module['read_permission'] && (empty($_POST['user_id']) || $_POST['user_id']==$GO_SECURITY->user_id)) ||
						(!empty($_POST['user_id']) && $GO_SECURITY->has_permission($_POST['user_id'], $module['acl_id']))
					)
				)
			{
				$record = array(
					'id' => $module['id'],
					'name' => $module['humanName'] 
				);

				$records[] = $record;
			}
		}

		echo '{total:'.count($records).',results:'.json_encode($records).'}';
		break;

	case 'themes':
		$themes = $GO_THEME->get_themes();
		foreach($themes as $theme)
		{
			$record = array(
				'id' => $theme,
				'theme' => $theme 
			);

			$records[] = $record;
		}
		echo '{total:'.count($records).',results:'.json_encode($records).'}';
		break;
}