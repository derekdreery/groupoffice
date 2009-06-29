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

$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'sort_order';
$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';

require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('modules');
require_once ($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();


try{
	$records=array();

	switch($_POST['task'])
	{
		case 'available_modules':

			$folders = $fs->get_folders($GO_CONFIG->module_path);

			while($module = array_shift($folders))
			{
				if($GO_MODULES->module_is_allowed($module['name']))
				{
					$installed_module = $GO_MODULES->get_module($module['name']);

					if(!$installed_module)
					{
						//require language file to obtain module name in the right language
						$language_file = $GO_LANGUAGE->get_language_file($module['name']);


						if(file_exists($language_file))
						{
							require_once($language_file);
						}


						$record = array(
							'id' => $module['name'],
							'name' => isset($lang[$module['name']]['name']) ? $lang[$module['name']]['name'] : $module['name'],
							'description' => isset($lang[$module['name']]['description']) ? $lang[$module['name']]['description'] : '');

						$records[] = $record;

					}
				}
			}
			break;

		case 'installed_modules':
				
			if(isset($_POST['uninstall_modules']))
			{

				try{
					$modules = json_decode(($_POST['uninstall_modules']),true);					
					
					$response['uninstallSuccess']=true;
					foreach($modules as $module_id)
					{
						if ($module_id == 'modules')
						{
							throw new Exception($lang['modules']['deleteModule']);							
						} else {
							$GO_MODULES->delete_module($module_id);							
						}						
					}
					
				}catch(Exception $e)
				{
					$response['uninstallFeedback']=$e->getMessage();
					$response['uninstallSuccess']=false;
				}
				
				$GO_MODULES->load_modules();
			}
				
			foreach($GO_MODULES->modules as $module)
			{
				$language_file = $GO_LANGUAGE->get_language_file($module['id']);
				if(file_exists($language_file))
				{
					require($language_file);
				}
				$record = array(
	 			'id' => $module['id'],
	 			'name' => $module['humanName'],
	 			'description' => $module['description'],
	 			'sort_order' => $module['sort_order'],
	 			'admin_menu' => $module['admin_menu'],
 				'acl_read' => $module['acl_read'],
 				'acl_write' => $module['acl_write']
				);
				$records[] = $record;

			}
			break;
	}
}catch(Exception $e)
{
	$response['success']=false;
	$response['feedback']=$e->getMessage();
	exit();
}


$response['total']= count($records);
$response['results']= $records;

echo json_encode($response);
?>