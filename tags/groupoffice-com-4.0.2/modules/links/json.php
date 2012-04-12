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
$GLOBALS['GO_SECURITY']->json_authenticate();

require_once($GLOBALS['GO_CONFIG']->class_path.'base/links.class.inc.php');
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
			foreach($GLOBALS['GO_MODULES']->modules as $module) {
				if($lang_file = $GLOBALS['GO_LANGUAGE']->get_language_file($module['id'])) {
					$GLOBALS['GO_LANGUAGE']->require_language_file($module['id']);
				}
			}
			$response['data']=array();
			$response['success']=true;
			foreach($lang['link_type'] as $id=>$name) {
				$v = $GLOBALS['GO_CONFIG']->get_setting('default_link_folder_'.$id);
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
