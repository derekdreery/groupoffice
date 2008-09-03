<?php
/**
 * @copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1615 $ $Date: 2008-04-25 16:18:36 +0200 (vr, 25 apr 2008) $3
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */


require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('projects');

require_once ($GO_MODULES->modules['projects']['class_path']."projects.class.inc.php");
//require_once ($GO_LANGUAGE->get_language_file('projects'));
$projects = new projects();

//ini_set('display_errors','off');



//we are unsuccessfull by default
$response =array('success'=>false);

try{

	switch($_REQUEST['task'])
	{
		case 'save_fee':
			
			if(!$GO_MODULES->modules['projects']['write_permission'])
			{
				throw AccessDeniedException();
			}
			
			$fee['id']=isset($_POST['fee_id']) ? smart_addslashes($_POST['fee_id']) : 0;
			$fee['name']=smart_addslashes($_POST['name']);
			$fee['time']=Number::to_phpnumber($_POST['time']);
			$fee['internal_value']=Number::to_phpnumber($_POST['internal_value']);
			$fee['external_value']=Number::to_phpnumber($_POST['external_value']);
			
			if($fee['id']>0)
			{
				$projects->update_fee($fee);
			}else
			{
				$response['acl_id']=$fee['acl_id']=$GO_SECURITY->get_new_acl();
				$response['fee_id']=$projects->add_fee($fee);
			}
			
			$response['success']=true;
			
			
		break;
		
		case 'save_hours':
			
			$hours['project_id']=smart_addslashes($_POST['project_id']);
			$project = $projects->get_project($hours['project_id']);
			
			if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $project['acl_write']) && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $project['acl_book']))
				throw new AccessDeniedException();
			
			
			$hours_id=$hours['id']=isset($_POST['hours_id']) ? smart_addslashes($_POST['hours_id']) : 0;
			$hours['comments']=smart_addslashes($_POST['comments']);
			$hours['units']=Number::to_phpnumber($_POST['units']);
			$hours['date']=Date::to_unixtime($_POST['date']);
			$hours['fee_id']=smart_addslashes($_POST['fee_id']);
			
			$hours['user_id']=isset($_POST['user_id']) ? smart_addslashes($_POST['user_id']) : $GO_SECURITY->user_id;
			
			$fee = $projects->get_fee($hours['fee_id']);
			$hours['int_fee_value']=$fee['internal_value'];
			$hours['ext_fee_value']=$fee['external_value'];
			$hours['fee_time']=$fee['time'];
			
			if($hours['id'])
			{
				$projects->update_hours($hours);
			}else
			{
				$projects->add_hours($hours);
			}
			
			$response['success']=true;
			
			
		break;
		
		
		case 'save_milestone':
			
			$milestone['project_id']=smart_addslashes($_POST['project_id']);
			$project = $projects->get_project($milestone['project_id']);
			if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $project['acl_write']))
				throw new AccessDeniedException();
				
			$milestone['id']=isset($_POST['milestone_id']) ? smart_addslashes($_POST['milestone_id']) : 0;
			$milestone['name']=smart_addslashes($_POST['name']);
			$milestone['description']=smart_addslashes($_POST['description']);			
			$milestone['due_time']=Date::to_unixtime($_POST['due_date']);		
			$milestone['project_id']=smart_addslashes($_POST['project_id']);
			$milestone['user_id']=isset($_POST['user_id']) ? smart_addslashes($_POST['user_id']) : $GO_SECURITY->user_id;
			
			if($milestone['id'])
			{
				$projects->update_milestone($milestone);
			}else
			{
				$projects->add_milestone($milestone);
			}
			
			$response['success']=true;		
		break;
		
		case 'check_milestone':
			
			$milestone['id']=smart_addslashes($_POST['milestone_id']);
			if($_POST['checked']=='true')
			{
				$milestone['completion_time']=time();	
			}else
			{
				$milestone['completion_time']=0;
			}
			$projects->update_milestone($milestone);
			$response['success'] = true;			
			
			
			break;

		case 'save_project':
				
			$project_id=$project['id']=isset($_POST['project_id']) ? smart_addslashes($_POST['project_id']) : 0;
			$project['name']=smart_addslashes($_POST['name']);
			$project['description']=smart_addslashes($_POST['description']);
			$project['customer']=smart_addslashes($_POST['customer']);

				
			

			if(empty($project['name']))
			{
				throw new Exception($lang['common']['missingField']);
			}

			if($project['id']>0)
			{
				$old_project = $projects->get_project($project['id']);
				if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_project['acl_write']))
				{
					throw new AccessDeniedException();
				}
				$projects->update_project($project);
				$response['success']=true;

			}else
			{
				if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $GO_MODULES->modules['projects']['acl_write']))
				{
					throw new AccessDeniedException();
				}
				
				$project['user_id']=$GO_SECURITY->user_id;
				
				$response['acl_read']=$project['acl_read']=$GO_SECURITY->get_new_acl('project');
				$response['acl_write']=$project['acl_write']=$GO_SECURITY->get_new_acl('project');
				$response['acl_book']=$project['acl_book']=$GO_SECURITY->get_new_acl('project');
				
				$project_id= $projects->add_project($project);

				if($GO_MODULES->modules['files'])
				{
					require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc');
					$fs = new files();

					$response['files_path']='projects/'.$project_id;
						
					$full_path = $GO_CONFIG->file_storage_path.$response['files_path'];
					if(!file_exists($full_path))
					{
						$fs->mkdir_recursive($full_path);
							
						$folder['user_id']=$GO_SECURITY->user_id;
						$folder['path']=addslashes($full_path);
						$folder['visible']='0';
						$folder['acl_read']=$project['acl_read'];
						$folder['acl_write']=$project['acl_write'];
							
						$fs->add_folder($folder);
					}
				}
				
				

				$response['project_id']=$project_id;
				$response['success']=true;
			}
			
			
			if(isset($GO_MODULES->modules['customfields']))
			{
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$cf->update_fields($GO_SECURITY->user_id, $project_id, 5, $_POST);
			}
				
			if(!empty($_POST['link']))
			{
				$link_props = explode(':', $_POST['link']);
				$GO_LINKS->add_link(
				smart_addslashes($link_props[1]),
				smart_addslashes($link_props[0]),
				$project_id,
				1);
			}
		
			break;

	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}

echo json_encode($response);