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
$GO_SECURITY->json_authenticate('log');
require_once ($GO_MODULES->modules['log']['class_path'].'log.class.inc.php');
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
			
			$response['total']=$log->get_entries($query, $sort, $dir, $start, $limit);
			
			$response['results']=array();
			while($entry = $log->next_record())
			{
				log::format_log_entry($entry);
				$response['results'][] = $entry;
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
