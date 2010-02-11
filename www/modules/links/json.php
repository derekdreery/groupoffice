<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: json.tpl 2030 2008-06-04 10:12:13Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
require('../../Group-Office.php');
$GO_SECURITY->json_authenticate();

$task=isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
try{
	switch($task)
	{
		case 'link_description':
			$link_description = $GO_LINKS->get_link_description($_REQUEST['link_description_id']);
			$response['data']=$link_description;
			$response['success']=true;
			break;

		case 'default_link_folders':
			foreach($GO_MODULES->modules as $module) {
				if($lang_file = $GO_LANGUAGE->get_language_file($module['id'])) {
					$GO_LANGUAGE->require_language_file($module['id']);
				}
			}
			$response['data']=array();
			$response['success']=true;
			foreach($lang['link_type'] as $id=>$name) {
				$response['data']['default_folders_'.$id]=$GO_CONFIG->get_setting('default_link_folder_'.$id);
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
