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


$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';

try{

	switch($task)
	{
		case 'category':
			$category = $notes->get_category(($_REQUEST['category_id']));
			$user = $GO_USERS->get_user($category['user_id']);
			$category['user_name']=String::format_name($user);
			$category['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $category['acl_id'])>GO_SECURITY::READ_PERMISSION;
			$response['data']=$category;
			$response['success']=true;		
			break;
				
		case 'categories':
			$auth_type = isset($_POST['auth_type']) ? ($_POST['auth_type']) : 'write';
			
			if(isset($_POST['delete_keys']))
			{
				try
				{
					$response['deleteSuccess']=true;
					$delete_categories = json_decode($_POST['delete_keys']);										

					foreach($delete_categories as $category_id)
					{
						$category = $notes->get_category($category_id);
						if(($GO_MODULES->modules['notes']['permission_level'] < GO_SECURITY::MANAGE_PERMISSION) || ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $category['acl_id']) < GO_SECURITY::MANAGE_PERMISSION))
						{
							throw new AccessDeniedException();
						}

						$notes->delete_category($category_id);						
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}

			$categories = $GO_CONFIG->get_setting('notes_categories_filter', $GO_SECURITY->user_id);
			$categories = ($categories) ? explode(',',$categories) : array();

			if(!count($categories))
			{			
				$notes->get_category();
				$default_category_id = $notes->f('id');
			       
				$categories[] = $default_category_id;
				$GO_CONFIG->save_setting('notes_categories_filter',$default_category_id, $GO_SECURITY->user_id);
			}

			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			
			$query = isset($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';
			
			$response['total'] = $notes->get_authorized_categories($auth_type, $GO_SECURITY->user_id, $query, $sort, $dir, $start, $limit);
			if(!$response['total'])
			{
				$notes->get_category();
				$response['total'] = $notes->get_authorized_categories($auth_type, $GO_SECURITY->user_id, $query, $sort, $dir, $start, $limit);
			}
			$response['results']=array();
			while($notes->next_record())
			{
				$category = $notes->record;			
				
				$user = $GO_USERS->get_user($category['user_id']);
				$category['user_name']=String::format_name($user);

				$category['checked'] = in_array($category['id'], $categories);
								
				$response['results'][] = $category;
			}

			break;
			
		case 'note_with_items':
		case 'note':

			$note = $notes->get_note(($_REQUEST['note_id']));
			
			$category = $notes->get_category($note['category_id']);
			$note['category_name']=$category['name'];	
			
			
			$user = $GO_USERS->get_user($note['user_id']);
			$note['user_name']=String::format_name($user);			
			
			$note['mtime']=Date::get_timestamp($note['mtime']);
			$note['ctime']=Date::get_timestamp($note['ctime']);			
			
			$response['data']=$note;
			$response['data']['permission_level']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $category['acl_id']);
			$response['data']['write_permission']=$response['data']['permission_level']>GO_SECURITY::READ_PERMISSION;
			if(!$response['data']['permission_level'])
			{
				throw new AccessDeniedException();
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
					require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
					$files = new files();	
					$response['data']['files']=$files->get_content_json($response['data']['files_folder_id']);
				}else
				{
					$response['data']['files']=array();				
				}
				
				if(isset($GO_MODULES->modules['comments']))
				{
					require_once ($GO_MODULES->modules['comments']['class_path'].'comments.class.inc.php');
					$comments = new comments();
					
					$response['data']['comments']=$comments->get_comments_json($response['data']['id'], 4);
				}
				break;
			}
				
		case 'notes':	

			$response['data']['write_permission'] = false;
			if(isset($_POST['categories']))
			{
				$categories = json_decode($_POST['categories'], true);
				$GO_CONFIG->save_setting('notes_categories_filter',implode(',', $categories), $GO_SECURITY->user_id);
			}else
			{
				$categories = $GO_CONFIG->get_setting('notes_categories_filter', $GO_SECURITY->user_id);
				$categories = ($categories) ? explode(',',$categories) : array();
			}

			if(count($categories))
			{
				$readable_categories = array();
				$writable_categories = array();
				$response['data']['permission_level'] = $permission_level = 0;				
				foreach($categories as $category_id)
				{
					$category = $notes->get_category($category_id);

					$category_names[]=$category['name'];
					$permission_level = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $category['acl_id']);				
					if($permission_level)
					{
						$readable_categories[] = $category_id;
					}
					if($permission_level >= GO_SECURITY::WRITE_PERMISSION)
					{
						$writable_categories[] = $category_id;
					}

					if($permission_level > $response['data']['permission_level'])
					{
						$response['data']['permission_level'] = $permission_level;
					}					
				}

				$response['data']['write_permission']=$response['data']['permission_level']>1;
				if(!$response['data']['permission_level'])
				{
					throw new AccessDeniedException();
				}
			}				

			if(isset($_POST['delete_keys']))
			{				
				try{
					$delete_notes = json_decode($_POST['delete_keys']);
					$notes_deleted = array();
					foreach($delete_notes as $note_id)
					{
						$note = $notes->get_note($note_id);
						if(in_array($note['category_id'], $writable_categories))
						{
							$notes->delete_note($note_id);
							$notes_deleted[] = $note_id;
						}
					}
					if(!count($notes_deleted))
					{
					        throw new AccessDeniedException();
					}					
					if(count($delete_notes) != count($notes_deleted))
					{
						require_once($GO_LANGUAGE->get_language_file('notes'));
						$response['feedback'] = $lang['notes']['incomplete_delete'];
					}
					$response['deleteSuccess']=true;
					
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
			$query = !empty($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';
			
			if(!empty($query))
			{
				$category_id=0;
			}

			$sort = ($sort == 'category_name') ? 'c.name' : 'n.'.$sort;
			$response['total'] = $notes->get_notes($query, $readable_categories, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($notes->next_record())
			{
				$note = $notes->record;				
				
				$user = $GO_USERS->get_user($note['user_id']);
				$note['user_name']=String::format_name($user);
				$note['mtime']=Date::get_timestamp($note['mtime']);
				$note['ctime']=Date::get_timestamp($note['ctime']);			
								
				$response['results'][] = $note;
			}

			if(count($category_names))
			{
				$GO_LANGUAGE->require_language_file('notes');
				$response['grid_title'] = (count($category_names) > 1) ? $lang['notes']['multipleCategoriesSelected'] : $category_names[0];
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