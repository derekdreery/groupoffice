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
 * @author Boy Wijnmaalen <bwijnmaalen@intermesh.nl>
 */

require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('modules');

$task = isset($_REQUEST['task']) ? smart_stripslashes($_REQUEST['task']) : null;


$result =array();


switch($task)
{
	case 'update':

		$modules = isset($_REQUEST['modules']) ? json_decode(smart_stripslashes($_REQUEST['modules'])) : null;
		//var_dump($modules);
		foreach($modules as $module)
		{
			if(!$GO_MODULES->update_module($module->id,$module->sort_order, $module->admin_menu))
			{
				$result['errors']=$lang['comon']['saveError'];
				$result['success']=false;
			} else {
				$result['success']=true;
			}
		}
		$GO_MODULES->load_modules();
		break;

	case 'install':
		
		$id = smart_stripslashes($_REQUEST['id']);		

		if (!$GO_MODULES->add_module($id)) {
			$result['errors']=$lang['comon']['saveError'];
			$result['success']=false;
		}else {
			
			$result['success']=true;
		}
		
		//$GO_MODULES->load_modules();
		break;
}

echo json_encode($result);
?>