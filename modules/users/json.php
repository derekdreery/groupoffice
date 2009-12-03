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
	case 'user':
		$result['success'] = false;
		$result['data'] = $GO_USERS->get_user($user_id);

		$result['data']['birthday']=Date::format($result['data']['birthday'], false);
	
		//$temp = $GO_LANGUAGE->get_language($result['data']['language']);
		//$result['data']['language_name'] = $temp['description'];
		
		$result['data']['start_module_name'] = isset($GO_MODULES->modules[$result['data']['start_module']]['humanName']) ? $GO_MODULES->modules[$result['data']['start_module']]['humanName'] : ''; 
		
		$result['data']['registration_time'] = Date::get_timestamp($result['data']['registration_time']);
		$result['data']['lastlogin'] = ($result['data']['lastlogin']) ? Date::get_timestamp($result['data']['lastlogin']) : '-';
		if($result['data'])
		{
			$result['success']=true;
		}

		if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
		{
			require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
			$cf = new customfields();
			$values = $cf->get_values($GO_SECURITY->user_id, 8, $user_id);

			if(count($values))
				$result['data']=array_merge($result['data'], $values);
		}

		if($GO_MODULES->has_module('mailings'))
		{
			require_once($GO_MODULES->modules['mailings']['class_path'].'mailings.class.inc.php');

			$ml = new mailings();
			$ml2 = new mailings();

			$count = $ml->get_authorized_mailing_groups('write', $GO_SECURITY->user_id, 0,0);

			while($ml->next_record())
			{
				$result['data']['mailing_'.$ml->f('id')]=$ml2->user_is_in_group($user_id, $ml->f('id')) ? true : false;
			}
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
	 				'read_disabled' => ($user_id && $GO_SECURITY->has_permission($user_id, $module['acl_id'], true)),
					'write_disabled' => ($user_id && $GO_SECURITY->has_permission($user_id, $module['acl_id'], true)>GO_SECURITY::READ_PERMISSION),
	 				'read_permission'=> $user_id > 0 ? $GO_SECURITY->has_permission($user_id, $module['acl_id']) : in_array($module['id'], $modules_read),
	 				'write_permission'=> $user_id > 0 ? $GO_SECURITY->has_permission($user_id, $module['acl_id'])>GO_SECURITY::READ_PERMISSION : in_array($module['id'], $modules_write)
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

		require_once($GO_MODULES->modules['users']['class_path'].'users.class.inc.php');
		$users = new users();

		$result['success'] = true;
		$result['data']=$users->get_register_email();
		echo json_encode($result);
		break;
}

?>