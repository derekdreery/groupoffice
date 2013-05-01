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
$GLOBALS['GO_SECURITY']->json_authenticate();

require_once($GLOBALS['GO_CONFIG']->class_path.'base/groups.class.inc.php');
$GO_GROUPS = new GO_GROUPS();

require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

$GLOBALS['GO_SECURITY']->check_token();

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
//	case 'user_with_items':
//	case 'user':
//
//		
//		$response['success'] = false;
//		$response['data'] = $GO_USERS->get_user($user_id);
//
//		//if(!$GLOBALS['GO_SECURITY']->has_permission($GLOBALS['GO_SECURITY']->user_id, $response['data']['acl_id'])){
//		if(!$GLOBALS['GO_MODULES']->modules['users']['read_permission']){
//			throw new AccessDeniedException();
//		}
//
//		$response['data']['write_permission']=$GLOBALS['GO_MODULES']->modules['users']['read_permission'];
//
//
//		//$response['data']['birthday']=Date::format($response['data']['birthday'], false);
//	
//		//$temp = $GLOBALS['GO_LANGUAGE']->get_language($response['data']['language']);
//		//$response['data']['language_name'] = $temp['description'];
//		
//		$response['data']['start_module_name'] = isset($GLOBALS['GO_MODULES']->modules[$response['data']['start_module']]['humanName']) ? $GLOBALS['GO_MODULES']->modules[$response['data']['start_module']]['humanName'] : '';
//		
//		$response['data']['registration_time'] = Date::get_timestamp($response['data']['registration_time']);
//		$response['data']['lastlogin'] = ($response['data']['lastlogin']) ? Date::get_timestamp($response['data']['lastlogin']) : '-';
//		if($response['data'])
//		{
//			$response['success']=true;
//		}
//
//		if($task=='user'){
//			if(isset($GLOBALS['GO_MODULES']->modules['customfields']) && $GLOBALS['GO_MODULES']->modules['customfields']['read_permission'])
//			{
//				require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
//				$cf = new customfields();
//				$values = $cf->get_values($GLOBALS['GO_SECURITY']->user_id, 8, $user_id);
//
//				if(count($values))
//					$response['data']=array_merge($response['data'], $values);
//			}
//
//			if($GLOBALS['GO_MODULES']->has_module('mailings'))
//			{
//				require_once($GLOBALS['GO_MODULES']->modules['mailings']['class_path'].'mailings.class.inc.php');
//
//				$ml = new mailings();
//				$ml2 = new mailings();
//
//				$count = $ml->get_authorized_mailing_groups('write', $GLOBALS['GO_SECURITY']->user_id, 0,0);
//
//				while($ml->next_record())
//				{
//					$response['data']['mailing_'.$ml->f('id')]=$ml2->user_is_in_group($user_id, $ml->f('id')) ? true : false;
//				}
//			}
//		}else
//		{
//			if($GLOBALS['GO_MODULES']->has_module('customfields'))
//			{
//				require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
//				$cf = new customfields();
//				$response['data']['customfields']=$cf->get_all_fields_with_values($GLOBALS['GO_SECURITY']->user_id, 8, $response['data']['id']);
//			}
//
//			if($GLOBALS['GO_MODULES']->has_module('comments'))
//			{
//				require_once ($GLOBALS['GO_MODULES']->modules['comments']['class_path'].'comments.class.inc.php');
//				$comments = new comments();
//
//				$response['data']['comments']=$comments->get_comments_json($response['data']['id'], 8);
//			}
//
//			$response['data']['links'] = array();
//			/* loadContactDetails - contact sidepanel */
//
//
//			require_once($GLOBALS['GO_CONFIG']->class_path.'/base/search.class.inc.php');
//			$search = new search();
//
//			$links_json = $search->get_latest_links_json($GLOBALS['GO_SECURITY']->user_id, $response['data']['id'], 8);
//
//			$response['data']['links']=$links_json['results'];
//
//			if(isset($GLOBALS['GO_MODULES']->modules['files']))
//			{
//				require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');
//				$fs = new files();
//				$response['data']['files']=$fs->get_content_json($response['data']['files_folder_id']);
//			}else
//			{
//				$response['data']['files']=array();
//			}
//
//
//			$values = array('address_no', 'address', 'zip', 'city', 'state', 'country');
//
//			$af = $GLOBALS['GO_LANGUAGE']->get_address_format_by_iso($GLOBALS['GO_CONFIG']->language);
//
//			$response['data']['formatted_address'] = $af['format'];
//
//			foreach($values as $val)
//				$response['data']['formatted_address'] = str_replace('{'.$val.'}', $response['data'][$val], $response['data']['formatted_address']);
//
//			$response['data']['formatted_address'] = preg_replace("/(\r\n)+|(\n|\r)+/", "<br />", $response['data']['formatted_address']);
//			$response['data']['google_maps_link']='http://maps.google.com/maps?q=';
//
//			$response['data']['google_maps_link']=google_maps_link($response['data']['address'], $response['data']['address_no'], $response['data']['city'], $response['data']['country']);
//
//			$response['data']['name']=String::format_name($response['data']);
//			
//		}
//		
//		$params['response']=&$response;
//		
//		$GLOBALS['GO_EVENTS']->fire_event('load_user', $params);
//		
//		echo json_encode($response);
//		break;
	case 'modules':

			if(empty($user_id))
			{
				$modules_read = array_map('trim', explode(',',$GLOBALS['GO_CONFIG']->register_modules_read));
				$modules_write = array_map('trim', explode(',',$GLOBALS['GO_CONFIG']->register_modules_write));
			}
		
			foreach($GLOBALS['GO_MODULES']->modules as $module)
			{
				
				$record = array(
		 			'id' => $module['id'],
		 			'name' => $module['humanName'],
	 				'read_disabled' => ($user_id && $GLOBALS['GO_SECURITY']->has_permission($user_id, $module['acl_id'], true)),
					'write_disabled' => ($user_id && $GLOBALS['GO_SECURITY']->has_permission($user_id, $module['acl_id'], true)>GO_SECURITY::READ_PERMISSION),
	 				'read_permission'=> $user_id > 0 ? $GLOBALS['GO_SECURITY']->has_permission($user_id, $module['acl_id']) : in_array($module['id'], $modules_read),
	 				'write_permission'=> $user_id > 0 ? $GLOBALS['GO_SECURITY']->has_permission($user_id, $module['acl_id'])>GO_SECURITY::READ_PERMISSION : in_array($module['id'], $modules_write)
				);
				$records[] = $record;
			}
		
		echo '({total:'.count($records).',results:'.json_encode($records).'})';
		break;
	case 'groups':

		if(empty($user_id))
		{
			$user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GLOBALS['GO_CONFIG']->register_user_groups)));
		
			if(!in_array($GLOBALS['GO_CONFIG']->group_everyone, $user_groups))
			{
				$user_groups[]=$GLOBALS['GO_CONFIG']->group_everyone;
			}
		}

		$groups = new GO_GROUPS();
			
		$GO_GROUPS->get_groups();
		while($GO_GROUPS->next_record())
		{
			if(($user_id == 1 && $GO_GROUPS->f('id') == $GLOBALS['GO_CONFIG']->group_root) || $GO_GROUPS->f('id')==$GLOBALS['GO_CONFIG']->group_everyone)
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
			$visible_user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GLOBALS['GO_CONFIG']->register_visible_user_groups)));
		}
		
		$visible_user_groups[]=$GLOBALS['GO_CONFIG']->group_root;
		
		$GO_GROUPS->get_groups();
		$groups = new GO_GROUPS();

		while($GO_GROUPS->next_record())
		{
			if($GO_GROUPS->f('id') == $GLOBALS['GO_CONFIG']->group_root)
			{
				$disabled = true;
			}else {
				$disabled = false;
			}

			$record = array(
	 			'id' => $GO_GROUPS->f('id'),
 				'disabled' => $disabled, 
	 			'group' => $GO_GROUPS->f('name'),
 				'visible_permission'=> $user_id > 0 ? $GLOBALS['GO_SECURITY']->group_in_acl($GO_GROUPS->f('id'), $user['acl_id']) : in_array($GO_GROUPS->f('id'), $visible_user_groups)
			);
			$records[] = $record;
		}
		
		echo '({total:'.count($records).',results:'.json_encode($records).'})';
		break;
	

	case 'language':
		$languages = $GLOBALS['GO_LANGUAGE']->get_languages();
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

		require_once($GLOBALS['GO_MODULES']->modules['users']['class_path'].'users.class.inc.php');
		$users = new users();

		$response['success'] = true;
		$response['data']=$users->get_register_email();
		echo json_encode($response);
		break;

}
?>