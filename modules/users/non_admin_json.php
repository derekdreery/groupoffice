<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: non_admin_json.php 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate();

$task = isset($_REQUEST['task']) ? smart_addslashes($_REQUEST['task']) : '';

switch($task)
{
	
	case 'start_module':
		foreach($GO_MODULES->modules as $module)
		{
			if($module['read_permission'] && $module['admin_menu']=='0')
			{
				$record = array(
					'id' => $module['id'],
					'name' => $module['humanName'] 
				);

				$records[] = $record;
			}
		}

		echo '{total:'.count($records).',results:'.json_encode($records).'}';
		break;		
	
		case 'themes':
		$themes = $GO_THEME->get_themes();
		foreach($themes as $theme)
		{
			$record = array(
				'id' => $theme,
				'theme' => $theme 
			);
				
			$records[] = $record;
		}
		echo '{total:'.count($records).',results:'.json_encode($records).'}';
		break;
}