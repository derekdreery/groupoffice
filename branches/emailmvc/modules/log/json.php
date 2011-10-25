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
require('../../Group-Office.php');
$GLOBALS['GO_SECURITY']->json_authenticate('log');
require_once ($GLOBALS['GO_MODULES']->modules['log']['class_path'].'log.class.inc.php');
$log = new log();
$task=isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
try{
	switch($task)
	{
		case 'entries':		
			
			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'id';
			$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC';
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';
			$query = !empty($_REQUEST['query']) ? '%'.trim($_REQUEST['query']).'%' : '';

			$advanced_query = !empty($_POST['advanced_query']) ? $_POST['advanced_query'] : '';
			
			$response['total']=$log->get_entries($query, $sort, $dir, $start, $limit, $advanced_query);
			
			$response['results']=array();
			while($entry = $log->next_record())
			{
				log::format_log_entry($entry);
				$response['results'][] = $entry;
			}
			
			break;

		case 'advanced_query_fields':
			$response['results'] = array();

			foreach($GLOBALS['GO_MODULES']->modules as $module) {
				$GLOBALS['GO_LANGUAGE']->require_language_file($module['id']);
			}


			foreach($lang['link_type'] as $id=>$name) {				
				$link_types[] = array($id, $name);
			}

			$response['results'][] = array('name' => 'Type', 'value' => '`link_type`', 'type' => 'combobox', 'fields' => $link_types);
			$response['results'][] = array('name' => 'Text', 'value' => '`text`', 'type' => 'textfield');	
			$response['results'][] = array('name' => 'Time', 'value' => 'FROM_UNIXTIME(`time`)', 'type' => 'date');
			$response['results'][] = array('name' => 'User', 'value' => '`user_id`', 'type' => 'user');
			
			$response['total'] = count($response['results']);
			$response['success'] = true;
		break;
			
/* {TASKSWITCH} */
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
