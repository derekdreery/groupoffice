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
GO::security()->json_authenticate();

require_once(GO::config()->class_path.'base/links.class.inc.php');
$GO_LINKS = new GO_LINKS();

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
			foreach(GO::modules()->modules as $module) {
				if($lang_file = GO::language()->get_language_file($module['id'])) {
					GO::language()->require_language_file($module['id']);
				}
			}
			$response['data']=array();
			$response['success']=true;
			foreach($lang['link_type'] as $id=>$name) {
				$v = GO::config()->get_setting('default_link_folder_'.$id);
				if($v)
					$response['data']['default_folders_'.$id]=$v;
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
