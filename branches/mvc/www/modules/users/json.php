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
GO::security()->json_authenticate();

require_once(GO::config()->class_path.'base/groups.class.inc.php');
$GO_GROUPS = new GO_GROUPS();

require_once(GO::config()->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

GO::security()->check_token();

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
	case 'user_with_items':
	case 'user':

		
		$response['success'] = false;
		$response['data'] = $GO_USERS->get_user($user_id);

		//if(!GO::security()->has_permission(GO::security()->user_id, $response['data']['acl_id'])){
		if(!GO::modules()->modules['users']['read_permission']){
			throw new AccessDeniedException();
		}

		$response['data']['write_permission']=GO::modules()->modules['users']['read_permission'];


		$response['data']['birthday']=Date::format($response['data']['birthday'], false);
	
		//$temp = GO::language()->get_language($response['data']['language']);
		//$response['data']['language_name'] = $temp['description'];
		
		$response['data']['start_module_name'] = isset(GO::modules()->modules[$response['data']['start_module']]['humanName']) ? GO::modules()->modules[$response['data']['start_module']]['humanName'] : '';
		
		$response['data']['registration_time'] = Date::get_timestamp($response['data']['registration_time']);
		$response['data']['lastlogin'] = ($response['data']['lastlogin']) ? Date::get_timestamp($response['data']['lastlogin']) : '-';
		if($response['data'])
		{
			$response['success']=true;
		}

		if($task=='user'){
			if(isset(GO::modules()->modules['customfields']) && GO::modules()->modules['customfields']['read_permission'])
			{
				require_once(GO::modules()->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$values = $cf->get_values(GO::security()->user_id, 8, $user_id);

				if(count($values))
					$response['data']=array_merge($response['data'], $values);
			}

			if(GO::modules()->has_module('mailings'))
			{
				require_once(GO::modules()->modules['mailings']['class_path'].'mailings.class.inc.php');

				$ml = new mailings();
				$ml2 = new mailings();

				$count = $ml->get_authorized_mailing_groups('write', GO::security()->user_id, 0,0);

				while($ml->next_record())
				{
					$response['data']['mailing_'.$ml->f('id')]=$ml2->user_is_in_group($user_id, $ml->f('id')) ? true : false;
				}
			}
		}else
		{
			if(GO::modules()->has_module('customfields'))
			{
				require_once(GO::modules()->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$response['data']['customfields']=$cf->get_all_fields_with_values(GO::security()->user_id, 8, $response['data']['id']);
			}

			if(GO::modules()->has_module('comments'))
			{
				require_once (GO::modules()->modules['comments']['class_path'].'comments.class.inc.php');
				$comments = new comments();

				$response['data']['comments']=$comments->get_comments_json($response['data']['id'], 8);
			}

			$response['data']['links'] = array();
			/* loadContactDetails - contact sidepanel */


			require_once(GO::config()->class_path.'/base/search.class.inc.php');
			$search = new search();

			$links_json = $search->get_latest_links_json(GO::security()->user_id, $response['data']['id'], 8);

			$response['data']['links']=$links_json['results'];

			if(isset(GO::modules()->modules['files']))
			{
				require_once(GO::modules()->modules['files']['class_path'].'files.class.inc.php');
				$fs = new files();
				$response['data']['files']=$fs->get_content_json($response['data']['files_folder_id']);
			}else
			{
				$response['data']['files']=array();
			}


			$values = array('address_no', 'address', 'zip', 'city', 'state', 'country');

			$af = GO::language()->get_address_format_by_iso(GO::config()->language);

			$response['data']['formatted_address'] = $af['format'];

			foreach($values as $val)
				$response['data']['formatted_address'] = str_replace('{'.$val.'}', $response['data'][$val], $response['data']['formatted_address']);

			$response['data']['formatted_address'] = preg_replace("/(\r\n)+|(\n|\r)+/", "<br />", $response['data']['formatted_address']);
			$response['data']['google_maps_link']='http://maps.google.com/maps?q=';

			$response['data']['google_maps_link']=google_maps_link($response['data']['address'], $response['data']['address_no'], $response['data']['city'], $response['data']['country']);

			$response['data']['name']=String::format_name($response['data']);
			
		}
		
		$params['response']=&$response;
		
		GO::events()->fire_event('load_user', $params);
		
		echo json_encode($response);
		break;
	case 'modules':

			if(empty($user_id))
			{
				$modules_read = array_map('trim', explode(',',GO::config()->register_modules_read));
				$modules_write = array_map('trim', explode(',',GO::config()->register_modules_write));
			}
		
			foreach(GO::modules()->modules as $module)
			{
				
				$record = array(
		 			'id' => $module['id'],
		 			'name' => $module['humanName'],
	 				'read_disabled' => ($user_id && GO::security()->has_permission($user_id, $module['acl_id'], true)),
					'write_disabled' => ($user_id && GO::security()->has_permission($user_id, $module['acl_id'], true)>GO_SECURITY::READ_PERMISSION),
	 				'read_permission'=> $user_id > 0 ? GO::security()->has_permission($user_id, $module['acl_id']) : in_array($module['id'], $modules_read),
	 				'write_permission'=> $user_id > 0 ? GO::security()->has_permission($user_id, $module['acl_id'])>GO_SECURITY::READ_PERMISSION : in_array($module['id'], $modules_write)
				);
				$records[] = $record;
			}
		
		echo '({total:'.count($records).',results:'.json_encode($records).'})';
		break;
	case 'groups':

		if(empty($user_id))
		{
			$user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',GO::config()->register_user_groups)));
		
			if(!in_array(GO::config()->group_everyone, $user_groups))
			{
				$user_groups[]=GO::config()->group_everyone;
			}
		}

		$groups = new GO_GROUPS();
			
		$GO_GROUPS->get_groups();
		while($GO_GROUPS->next_record())
		{
			if(($user_id == 1 && $GO_GROUPS->f('id') == GO::config()->group_root) || $GO_GROUPS->f('id')==GO::config()->group_everyone)
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
			$visible_user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',GO::config()->register_visible_user_groups)));
		}
		$GO_GROUPS->get_groups();
		$groups = new GO_GROUPS();

		while($GO_GROUPS->next_record())
		{
			if($GO_GROUPS->f('id') == GO::config()->group_root)
			{
				$disabled = true;
			}else {
				$disabled = false;
			}

			$record = array(
	 			'id' => $GO_GROUPS->f('id'),
 				'disabled' => $disabled, 
	 			'group' => $GO_GROUPS->f('name'),
 				'visible_permission'=> $user_id > 0 ? GO::security()->group_in_acl($GO_GROUPS->f('id'), $user['acl_id']) : in_array($GO_GROUPS->f('id'), $visible_user_groups)
			);
			$records[] = $record;
		}
		
		echo '({total:'.count($records).',results:'.json_encode($records).'})';
		break;
	

	case 'language':
		$languages = GO::language()->get_languages();
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

		require_once(GO::modules()->modules['users']['class_path'].'users.class.inc.php');
		$users = new users();

		$response['success'] = true;
		$response['data']=$users->get_register_email();
		echo json_encode($response);
		break;

}
?>