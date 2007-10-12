<?php
/**
 * @copyright Copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 *
 * This file is part of Group-Office.
 *
 * Group-Office is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 *
 * See file /LICENSE.GPL
 */
require_once("Group-Office.php");

$GO_SECURITY->json_authenticate();

switch($_POST['task'])
{
	case 'modules':

		$result['success']=true;
		$result['modules'] = $GO_MODULES->modules;

		echo json_encode($result);
		break;

	case 'links':


		require_once($GO_CONFIG->class_path.'/base/search.class.inc');
		$search = new search();

		$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
		$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 0;

		$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'mtime';
		$dir= isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC';

		if(isset($_REQUEST['link_id']))
		{
			$links = $GO_LINKS->get_links($_REQUEST['link_id']);
			$link_ids=array();
			foreach($links as $link)
			{
				$link_ids[]=$link['link_id'];
			}

			$count = $search->global_search($GO_SECURITY->user_id, '', $start, $limit, $sort,$dir, $link_ids);
		}else {
			$count = $search->global_search($GO_SECURITY->user_id, smart_addslashes($_REQUEST['query']), $start, $limit, $sort,$dir);
		}

		foreach($GO_MODULES->modules as $module)
		{
			$GO_THEME->load_module_theme($module['id']);
		}


		$last_type='';

		$records=array();
		if($count)
		{
			while($search->next_record())
			{
				/*if($last_type!=$search->f('type'))
				 {
				 $last_type=$search->f('type');
				 	
				 $records[]=array(
				 'link_id'=>$search->f('type'),
				 'link_type'=>0,
				 'name'=>'<h2>'.$search->f('type').'</h2>',
				 'description'=>'',
				 'url'=>'',
				 'mtime'=>''
				 );
				 }*/

				if(isset($GO_THEME->images['link_type_'.$search->f('link_type')]))
				{
					$icon = $GO_THEME->images['link_type_'.$search->f('link_type')];
				}else {
					$icon = $GO_THEME->images['unknown_link_type'];
				}
				$records[]=array(
					'icon'=>$icon,
					'link_id'=>$search->f('link_id'),
					'link_type'=>$search->f('link_type'),
					'type_name'=>'('.$search->f('type').') '.$search->f('name'),
					'name'=>$search->f('name'),
					'type'=>$search->f('type'),
					'description'=>$search->f('description'),
					'url'=>$search->f('url'),
					'mtime'=>get_timestamp($search->f('mtime')),
					'module'=>$search->f('module'),
					'id'=>$search->f('id')
				);
			}
		}

		echo '({"total":"'.$count.'","results":'.json_encode($records).'})';


		break;
}

