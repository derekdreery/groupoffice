<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: json.php 2030 2011-08-24 10:12:13Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */
require_once('../../Group-Office.php');
$GO_SECURITY->json_authenticate('admin2userlogin');

// Dependency on the base users class file.
require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
	$GO_USERS = new GO_USERS();

$task=isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
try{
	switch($task)
	{

		case 'usersgrid':
			
		$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
		$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
		$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
		$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
		$query = !empty($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : null;
		$search_field = isset($_REQUEST['search_field']) ? ($_REQUEST['search_field']) : null;
		//$user_id = isset($_REQUEST['user_id']) ? ($_REQUEST['user_id']) : null;

		$user_id = (!$GO_MODULES->modules['users']['read_permission']) ? $GO_SECURITY->user_id : 0;

		$response['results']=array();
		$response['total']=0;
		//if($user_id==0 || !empty($query)){

		$response['total'] = $GO_USERS->search($query, $search_field, $user_id, $start, $limit, $sort,$dir);

		while($user=$GO_USERS->next_record())
		{
			$user['name'] = String::format_name($GO_USERS->f('last_name'),$GO_USERS->f('first_name'),$GO_USERS->f('middle_name'));
			$user['lastlogin']=Date::get_timestamp($user['lastlogin']);
			$user['registration_time']=Date::get_timestamp($user['registration_time']);
			$user['cf']=$user['id'].':'.$user['name'];//special field used by custom fields. They need an id an value in one.

			if(isset($cf)){
				$cf->format_record($user, 8, true);
			}

			$response['results'][]=$user;
		}
		break;
/* {TASKSWITCH} */
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
