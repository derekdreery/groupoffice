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

$GO_SECURITY->json_authenticate('notes');

require_once ($GO_MODULES->modules['notes']['class_path'].'notes.class.inc.php');
$notes = new notes();


$task=isset($_REQUEST['task']) ? smart_addslashes($_REQUEST['task']) : '';

try{

	switch($task)
	{
		case 'category':
			$category = $notes->get_category(smart_addslashes($_REQUEST['category_id']));
			$user = $GO_USERS->get_user($category['user_id']);
			$category['user_name']=String::format_name($user);
			$response['data']=$category;
			$response['success']=true;		
			break;
				
		case 'categories':
			$auth_type = isset($_POST['auth_type']) ? smart_addslashes($_POST['auth_type']) : 'write';
			
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_categories = json_decode(smart_stripslashes($_POST['delete_keys']));

					foreach($delete_categories as $category_id)
					{
						$notes->delete_category(addslashes($category_id));
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			$sort = isset($_REQUEST['sort']) ? smart_addslashes($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? smart_addslashes($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? smart_addslashes($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? smart_addslashes($_REQUEST['limit']) : '0';
			
			$query = isset($_REQUEST['query']) ? '%'.smart_addslashes($_REQUEST['query']).'%' : '';
			
			$response['total'] = $notes->get_authorized_categories($auth_type, $GO_SECURITY->user_id, $query, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($notes->next_record())
			{
				$category = $notes->Record;			
				
				$user = $GO_USERS->get_user($category['user_id']);
				$category['user_name']=String::format_name($user);
								
				$response['results'][] = $category;
			}

			break;
			
		case 'note_with_items':
		case 'note':

			$note = $notes->get_note(smart_addslashes($_REQUEST['note_id']));
			
			$category = $notes->get_category($note['category_id']);
			$note['category_name']=$category['name'];	
			
			
			$user = $GO_USERS->get_user($note['user_id']);
			$note['user_name']=String::format_name($user);			
			
			$note['mtime']=Date::get_timestamp($note['mtime']);
			$note['ctime']=Date::get_timestamp($note['ctime']);			
			
			$response['data']=$note;
			
			$response['data']['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $category['acl_write']);
			if(!$response['data']['write_permission'] && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $category['acl_read']))
			{
				throw new AccessDeniedException();
			}
			
			if(isset($GO_MODULES->modules['files']))
			{
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc');
				$fs = new files();

				$response['data']['files_path']='notes/'.$response['data']['id'];

				$full_path = $GO_CONFIG->file_storage_path.$response['data']['files_path'];
				if(!file_exists($full_path))
				{
					$fs->mkdir_recursive($full_path);

					if(!$fs->get_folder(addslashes($full_path)))
					{
						$folder['user_id']=$response['data']['user_id'];
						$folder['path']=addslashes($full_path);
						$folder['visible']='0';
						
						
						$folder['acl_read']=$category['acl_read'];
						$folder['acl_write']=$category['acl_write'];
						

						$fs->add_folder($folder);
					}
				}
			}
						
			$response['success']=true;
			
			
			if($task=='note')
			{
				if(isset($GO_MODULES->modules['customfields']))
				{
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$values = $cf->get_values($GO_SECURITY->user_id, 4, $response['data']['id']);				
					$response['data']=array_merge($response['data'], $values);			
				}
				break;
			}else
			{
					
				if(isset($GO_MODULES->modules['customfields']))
				{
					require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
					$cf = new customfields();
					$response['data']['customfields']=
						$cf->get_all_fields_with_values(
							$GO_SECURITY->user_id, 4, $response['data']['id']);			
				}
				
				$response['data']['content']=String::text_to_html($response['data']['content']);

				
				require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
				$search = new search();
			
				$links_json = $search->get_latest_links_json($GO_SECURITY->user_id, $response['data']['id'], 4);				
				$response['data']['links']=$links_json['results'];
				
				if(isset($GO_MODULES->modules['files']))
				{
					$response['data']['files']=$fs->get_content_json($full_path);
				}else
				{
					$response['data']['files']=array();				
				}
				break;
			}
				
		case 'notes':
			$category_id=smart_addslashes($_POST['category_id']);
			$category = $notes->get_category($category_id);
			$response['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $category['acl_write']);
			if(!$response['write_permission'] && !$GO_SECURITY->has_permission($GO_SECURITY->user_id, $category['acl_read']))
			{
				throw new AccessDeniedException();
			}			

			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_notes = json_decode(smart_stripslashes($_POST['delete_keys']));

					foreach($delete_notes as $note_id)
					{
						$notes->delete_note(addslashes($note_id));
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			$sort = isset($_REQUEST['sort']) ? smart_addslashes($_REQUEST['sort']) : 'id';
			$dir = isset($_REQUEST['dir']) ? smart_addslashes($_REQUEST['dir']) : 'DESC';
			$start = isset($_REQUEST['start']) ? smart_addslashes($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? smart_addslashes($_REQUEST['limit']) : '0';

			$response['total'] = $notes->get_notes($category_id, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($notes->next_record())
			{
				$note = $notes->Record;				
				
				$user = $GO_USERS->get_user($note['user_id']);
				$note['user_name']=String::format_name($user);
				$note['mtime']=Date::get_timestamp($note['mtime']);
				$note['ctime']=Date::get_timestamp($note['ctime']);				
								
				$response['results'][] = $note;
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