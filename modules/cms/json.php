<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: action.tpl 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require('../../Group-Office.php');
$GO_SECURITY->json_authenticate('cms');
require_once ($GO_MODULES->modules['cms']['class_path'].'cms.class.inc.php');
$cms = new cms();


function get_folder_nodes($folder_id, $site, $path=''){

	$cms = new cms();
	
	$response = array();
	
	$items = $cms->get_items($folder_id);
	while($item = array_shift($items))
	{
		if($item['fstype']=='file')
		{
			$response[] = array(
				'text'=>$item['name'],
				'id'=>'file_'.$item['id'],
				'iconCls'=>'filetype-html',
				'site_id'=>$site['id'],
				'file_id'=>$item['id'],
				'folder_id'=>$item['folder_id'],
				'template'=>$site['template'],
				'root_folder_id'=>$item['files_folder_id'],
				'leaf'=>true,
				'path'=> $path.'/'.urlencode($item['name'])
			);	
		}else
		{
			$folderNode = array(
				'text'=>$item['name'],
				'id'=>'folder_'.$item['id'],
				'iconCls'=> $item['disabled']=='1' ? 'cms-folder-disabled' : 'filetype-folder',
				'site_id'=>$site['id'],
				'folder_id'=>$item['id'],
				'template'=>$site['template'],
				'root_folder_id'=>$site['files_folder_id'],
				'default_template'=>$item['default_template'],
				'path'=> $path.'/'.urlencode($item['name'])
			);
			
			$subitems = $cms->get_items($item['id']);
			
			if(!count($subitems))
			{
				$folderNode['expanded']=true;
				$folderNode['children']=array();
			}
			
			$response[] = $folderNode;
		}
	}
	
	return $response;
	
}


$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';
//try{
	switch($task)
	{
		
		
		case 'tree':
			
			$cms2 = new cms();
			
			if(isset($_REQUEST['node']) && strpos($_REQUEST['node'],'_'))
			{
				$node = explode('_',$_REQUEST['node']);
				$node_type=$node[0];
				$folder_id=$node[1];
			}else {
				$node_type='root';
				$folder_id=0;				
			}
			
			if($node_type=='site')
			{
				$site = $cms->get_site($folder_id);
				$response = get_folder_nodes($site['root_folder_id'], $site);			
			}else
			{	
				$response=array();
				if($folder_id==0)
				{					
					$cms->get_authorized_sites($GO_SECURITY->user_id);
				//go_log(LOG_DEBUG, $count);	
					while($cms->next_record())
					{						
						$response[] = array(
							'text'=>$cms->f('name'),
							'id'=>'folder_'.$cms->f('root_folder_id'),
							'iconCls'=>'folder-account',
							'expanded'=>count($response)==0,
							'type'=>'folder',
							'site_id'=>$cms->f('id'),
							'folder_id'=>$cms->f('root_folder_id'),
							'template'=>$cms->f('template'),
							'files_folder_id'=>$cms->f('files_folder_id'),
                            'root_folder_id'=>$cms->f('files_folder_id'),
							'path'=>'',
							'children'=>get_folder_nodes($cms->f('root_folder_id'), $cms->record),
							'draggable'=>false
							);
					}
				}else
				{				
				
					$folder = $cms->get_folder($folder_id);
					$site = $cms->get_site($folder['site_id']);
					
					$path = $cms->build_path($folder_id, $site['root_folder_id']);
					
					$response = get_folder_nodes($folder_id, $site, $path);
				}
			}
			
			break;
			
		
		
		case 'site':
		
			if(!$GO_MODULES->modules['cms']['write_permission'])
			{
				throw new AccessDeniedException();
			}
			
			$site = $cms->get_site(($_REQUEST['site_id']));
			
			$user = $GO_USERS->get_user($site['user_id']);
			$site['user_name']=String::format_name($user);			
			
			$response['data']=$site;
			
			$response['success']=true;
			break;			
			
			
				
		case 'sites':
		
			
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_sites = json_decode(($_POST['delete_keys']));
					foreach($delete_sites as $site_id)
					{
						$cms->delete_site($site_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$response['total'] = $cms->get_authorized_sites($GO_SECURITY->user_id, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($cms->next_record())
			{
				$site = $cms->record;
				
				$user = $GO_USERS->get_user($site['user_id']);
				$site['user_name']=String::format_name($user);
				$response['results'][] = $site;
			}
			break;
			
		
		case 'folder':
		
			
			if(!empty($_REQUEST['folder_id']))
			{
				$folder = $cms->get_folder(($_REQUEST['folder_id']));			
				$site = $cms->get_site($folder['site_id']);
				
				$folder['mtime']=Date::get_timestamp($folder['mtime']);			
				$folder['ctime']=Date::get_timestamp($folder['ctime']);		

				$folder['option_values']=$cms->get_template_values($folder['option_values']);
				
				$response['data']=$folder;
				
				$reponse['data']['authentication']=$folder['acl']>0;


				$response['data']['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $site['acl_write']);
				if(!$response['data']['write_permission'])
				{
					throw new AccessDeniedException();
				}
			}else
			{
				//load an empty file to get the template options
				$folder=$cms->get_folder(($_REQUEST['parent_id']));
				$site = $cms->get_site($folder['site_id']);
				$response['data']['acl']=0;
				$response['data']['parent_id']=$_REQUEST['parent_id'];
				$response['data']['option_values']=$cms->get_template_values($folder['option_values']);
			}	
			
			$response['data']['config']=$cms->get_template_config($site['template']);
			
			$response['success']=true;			
			break;
			
			
				
		case 'folders':
			
			$site_id=$_POST['site_id'];
			$site = $cms->get_site($site_id);
			$response['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $site['acl_write']);
			if(!$response['write_permission'])
			{
				throw new AccessDeniedException();
			}
			
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_folders = json_decode(($_POST['delete_keys']));
					foreach($delete_folders as $folder_id)
					{
						$cms->delete_folder($folder_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$response['total'] = $cms->get_folders($site_id, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($cms->next_record())
			{
				$folder = $cms->record;
				$folder['mtime']=Date::get_timestamp($folder['mtime']);				
				$folder['ctime']=Date::get_timestamp($folder['ctime']);				
								
				$response['results'][] = $folder;
			}
			break;
			
		
		case 'file':
			
			if(!empty($_REQUEST['file_id']))
			{
				$file = $cms->get_file(($_REQUEST['file_id']));			
				$folder = $cms->get_folder($file['folder_id']);
				$site = $cms->get_site($folder['site_id']);
				
				$file['mtime']=Date::get_timestamp($file['mtime']);
				$file['ctime']=Date::get_timestamp($file['ctime']);
				$file['show_until']=Date::get_timestamp($file['show_until'],false);


				$response['data']=$file;
        $response['data']['root_folder_id']=$site['files_folder_id'];
                
				$response['data']['config']=$cms->get_template_config($site['template']);				
				
				$response['data']['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $site['acl_write']);
				if(!$response['data']['write_permission'])
				{
					throw new AccessDeniedException();
				}
			}else
			{
				//load an empty file to get the template options
				$folder = $cms->get_folder($_REQUEST['folder_id']);
				$site = $cms->get_site($folder['site_id']);

        $response['data']['files_folder_id']=0;
        $response['data']['root_folder_id']=$site['files_folder_id'];
				$response['data']['type']=$folder['type'];
				$response['data']['config']=$cms->get_template_config($site['template']);
				if(!empty($folder['default_template']))
				{
					for($i=0;$i<count($response['data']['config']['templates']);$i++)
					{
						if($response['data']['config']['templates'][$i][0]==$folder['default_template'])
						{
							$response['data']['content']=$response['data']['config']['templates'][$i][1];
							break;		
						}
					}					
				}				
				$response['data']['option_values']=$cms->get_template_values($folder['option_values']);			
			}	
			
			$response['success']=true;
			break;

			
				
		case 'files':
			$site_id=$_POST['site_id'];
			$site = $cms->get_site($site_id);
			$response['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $site['acl_write']);
			if(!$response['write_permission'])
			{
				throw new AccessDeniedException();
			}
			
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_files = json_decode(($_POST['delete_keys']));
					foreach($delete_files as $file_id)
					{
						$cms->delete_file($file_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$response['total'] = $cms->get_files($site_id, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($cms->next_record())
			{
				$file = $cms->record;
				$file['mtime']=Date::get_timestamp($file['mtime']);
				$file['ctime']=Date::get_timestamp($file['ctime']);
				
				$response['results'][] = $file;
			}
			break;		
			
				
		case 'templates':	
			
			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			$fs = new filesystem();
			
			$response['results']=$fs->get_folders($GO_MODULES->modules['cms']['path'].'templates');
	
			break;		
			
		
		case 'comment':			
			$comment = $cms->get_comment(($_REQUEST['comment_id']));
			
			
			$user = $GO_USERS->get_user($comment['user_id']);
			$comment['user_name']=String::format_name($user);
			
			$comment['ctime']=Date::get_timestamp($comment['ctime']);
			$response['data']=$comment;
						
			$response['success']=true;
			break;
			
			
				
		case 'comments':
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_comments = json_decode(($_POST['delete_keys']));
					foreach($delete_comments as $comment_id)
					{
						$cms->delete_comment($comment_id);
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$response['total'] = $cms->get_comments($sort, $dir, $start, $limit);
			$response['results']=array();
			while($cms->next_record())
			{
				$comment = $cms->record;				
				
				$user = $GO_USERS->get_user($comment['user_id']);
				$comment['user_name']=String::format_name($user);
				
				$comment['ctime']=Date::get_timestamp($comment['ctime']);
				
								
				$response['results'][] = $comment;
			}
			break;
			/* {TASKSWITCH} */
	}
/*}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}*/
echo json_encode($response);