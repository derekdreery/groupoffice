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
$GO_SECURITY->json_authenticate('users');

$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'username';
$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
$query = empty($_REQUEST['query']) ? '' : '%'.($_REQUEST['query']).'%';
$search_field = isset($_REQUEST['search_field']) ? ($_REQUEST['search_field']) : null;
$task = isset($_REQUEST['task']) ? ($_REQUEST['task']) : null;
$user_id = isset($_REQUEST['user_id']) ? ($_REQUEST['user_id']) : null;

$records = array();

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


		if(isset($user_id))
		{
			$user = $GO_USERS->get_user($user_id);
		}else
		{
			$user=false;
			$response['total'] = $GO_USERS->search($query, $search_field, 0, $start, $limit, $sort,$dir);
		}

		while($user || $GO_USERS->next_record())
		{
			$user=false;
				
			$name = String::format_name($GO_USERS->f('last_name'),$GO_USERS->f('first_name'),$GO_USERS->f('middle_name'));
			$address = $GO_USERS->f('address').' '.$GO_USERS->f('address_no');
			$waddress = $GO_USERS->f('work_address').' '.$GO_USERS->f('work_address_no');
				
			$records[]=array(
				'id'=>$GO_USERS->f('id'),
				'username'=>$GO_USERS->f('username'), 
				'name'=>htmlspecialchars($name), 
				'company'=>$GO_USERS->f('company'),
				'logins'=>$GO_USERS->f('logins'),
				'lastlogin'=>Date::get_timestamp($GO_USERS->f('lastlogin')), 
				'registration_time'=>Date::get_timestamp($GO_USERS->f('registration_time')),
				'address' => $address,
				'zip' => $GO_USERS->f('zip'),
				'city' => $GO_USERS->f('city'),
				'state' => $GO_USERS->f('state'),
				'country' => $GO_USERS->f('country'),
				'phone' => $GO_USERS->f('phone'),
				'email' => $GO_USERS->f('email'),
				'waddress' => $waddress,
				'wzip' => $GO_USERS->f('work_zip'),
				'wcity' => $GO_USERS->f('work_city'),
				'wstate' => $GO_USERS->f('work_state'),
				'wcountry' => $GO_USERS->f('work_country'),
				'wphone' => $GO_USERS->f('work_phone')
			);
		}

		$response['results']=$records;

		echo json_encode($response);
		break;

	case 'user':
		$result['success'] = false;
		$result['data'] = $GO_USERS->get_user($user_id);

		$result['data']['birthday']=Date::format($result['data']['birthday'], false);
	
		//$temp = $GO_LANGUAGE->get_language($result['data']['language']);
		//$result['data']['language_name'] = $temp['description'];
		
		$result['data']['start_module_name'] = isset($GO_MODULES->modules[$result['data']['start_module']]['humanName']) ? $GO_MODULES->modules[$result['data']['start_module']]['humanName'] : ''; 
		
		$result['data']['registration_time'] = date("d-m-Y  H:i", $result['data']['registration_time']);
		$result['data']['lastlogin'] = date("d-m-Y  H:i", $result['data']['lastlogin']);
		if($result['data'])
		{
			$result['success']=true;
		}
		
		$params['response']=&$result;
		
		$GO_EVENTS->fire_event('load_user', $params);
		
		echo json_encode($result);
		break;
	case 'modules':

			if(empty($user_id))
			{
				$modules_read = array_map('trim', explode(',',$GO_CONFIG->register_modules_read));
				$modules_write = array_map('trim', explode(',',$GO_CONFIG->register_modules_write));
			}
		
			foreach($GO_MODULES->modules as $module)
			{
				
				$record = array(
		 			'id' => $module['id'],
		 			'name' => $module['humanName'],
	 				'read_disabled' => ($user_id && $GO_SECURITY->has_permission($user_id, $module['acl_read'], true)), 
					'write_disabled' => ($user_id && $GO_SECURITY->has_permission($user_id, $module['acl_write'], true)),
	 				'read_permission'=> $user_id > 0 ? $GO_SECURITY->has_permission($user_id, $module['acl_read']) : in_array($module['id'], $modules_read),
	 				'write_permission'=> $user_id > 0 ? $GO_SECURITY->has_permission($user_id, $module['acl_write']) : in_array($module['id'], $modules_write)
				);
				$records[] = $record;
			}
		
		echo '({total:'.count($records).',results:'.json_encode($records).'})';
		break;
	case 'groups':
		
		if(empty($user_id))
		{
			$user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_user_groups)));
		
			if(!in_array($GO_CONFIG->group_everyone, $user_groups))
			{
				$user_groups[]=$GO_CONFIG->group_everyone;
			}
		}

		$groups = new GO_GROUPS();
			
		$GO_GROUPS->get_groups();
		while($GO_GROUPS->next_record())
		{
			if(($user_id == 1 && $GO_GROUPS->f('id') == $GO_CONFIG->group_root) || $GO_GROUPS->f('id')==$GO_CONFIG->group_everyone)
			{
				$disabled = true;
			}else {
				$disabled = false;
			}
			
			if($user_id > 0)
			{
				$permission = $groups->is_in_group($user_id, $GO_GROUPS->f('id'));
			}else
			{
				$permission = in_array($GO_GROUPS->f('id'), $user_groups);
			}

			$record = array(
	 			'id' => $GO_GROUPS->f('id'),
 				'disabled' => $disabled, 
	 			'group' => $GO_GROUPS->f('name'),
 				'group_permission'=> $permission,
			);
			$records[] = $record;
		}
	
		echo '({total:'.count($records).',results:'.json_encode($records).'})';
		break;
	case 'visible':
		if ($user_id)
		{
			$user = $GO_USERS->get_user($user_id);
		}else
		{			
			$visible_user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_visible_user_groups)));
		}
		$GO_GROUPS->get_groups();
		$groups = new GO_GROUPS();

		while($GO_GROUPS->next_record())
		{
			if($GO_GROUPS->f('id') == $GO_CONFIG->group_root)
			{
				$disabled = true;
			}else {
				$disabled = false;
			}

			$record = array(
	 			'id' => $GO_GROUPS->f('id'),
 				'disabled' => $disabled, 
	 			'group' => $GO_GROUPS->f('name'),
 				'visible_permission'=> $user_id > 0 ? $GO_SECURITY->group_in_acl($GO_GROUPS->f('id'), $user['acl_id']) : in_array($GO_GROUPS->f('id'), $visible_user_groups)
			);
			$records[] = $record;
		}
		
		echo '({total:'.count($records).',results:'.json_encode($records).'})';
		break;
	

	case 'language':
		$languages = $GO_LANGUAGE->get_languages();
		foreach($languages as $language)
		{
				
			$record = array(
				'id' => $language['code'],
				'language' => $language['description']				
			);
			$records[] = $record;
		}

		echo '({total:'.count($records).',results:'.json_encode($records).'})';
		break;
	case 'settings':

		$result['success'] = true;
		$result['data']=array(
			'confirmed_subject' => $GO_CONFIG->get_setting('registration_confirmation_subject'),
			'unconfirmed_subject' => $GO_CONFIG->get_setting('registration_unconfirmed_subject'),
			'confirmed' => $registration_confirmation = $GO_CONFIG->get_setting('registration_confirmation'),
			'unconfirmed' => $registration_unconfirmed = $GO_CONFIG->get_setting('registration_unconfirmed')
		);

		echo json_encode($result);

		//echo '({total":'.count($record).',"data":'.json_encode($result).'})';
		break;
}

?>