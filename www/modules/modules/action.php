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
GO::security()->json_authenticate('modules');

$task = isset($_REQUEST['task']) ? ($_REQUEST['task']) : null;

$response =array();
try{
	switch($task)
	{
		case 'update':
			$modules = isset($_REQUEST['modules']) ? json_decode(($_REQUEST['modules'])) : null;

			foreach($modules as $module)
			{
				if(!GO::modules()->update_module($module->id,$module->sort_order, $module->admin_menu))
				{
					throw new Exception($lang['common']['saveError']);
				}
				$response['success']=true;
			}
			GO::modules()->load_modules();
			break;

		case 'install':
			$modules = explode(',', $_REQUEST['modules']);

			foreach($modules as $module)
			{
				if (!GO::modules()->add_module($module)) {
					throw new Exception($lang['common']['saveError']);
				}
				$response['success']=true;
			}

			GO::modules()->load_modules();
			break;
	}
}
catch(Exception $e)
{
	$response['success']=false;
	$response['feedback']=$e->getMessage();
}
echo json_encode($response);
?>