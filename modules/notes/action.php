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
$GO_SECURITY->json_authenticate('notes');

require_once ($GO_MODULES->modules['notes']['class_path']."notes.class.inc.php");
//require_once ($GO_LANGUAGE->get_language_file('notes'));

$notes = new notes();


try{

	switch($_REQUEST['task'])
	{
				
		case 'save_category':
				
			$category_id=$category['id']=isset($_POST['category_id']) ? ($_POST['category_id']) : 0;
			$category['user_id'] = (isset($_REQUEST['user_id']) && $_REQUEST['user_id']) ? $_REQUEST['user_id'] : $GO_SECURITY->user_id;
			$category['name']=$_POST['name'];

			if($category['id']>0)
			{
				$old_category = $notes->get_category($category['id']);
				
				if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_category['acl_id'])<GO_SECURITY::WRITE_PERMISSION)
				{
					throw new AccessDeniedException();
				}

				if(!$GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
				{
					unset($category['user_id']);
				}
				
				$notes->update_category($category, $old_category);
				$response['success']=true;

			}else
			{				
				$response['acl_id']=$category['acl_id']=$GO_SECURITY->get_new_acl('notes', $category['user_id']);
				
				$category_id= $notes->add_category($category);							

				$response['category_id']=$category_id;
				$response['success']=true;
			}		
			break;
		
		case 'save_note':				
			$note_id=$note['id']=isset($_POST['note_id']) ? ($_POST['note_id']) : 0;
			
			$category = $notes->get_category((trim($_POST['category_id'])));
			
			if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $category['acl_id'])<GO_SECURITY::WRITE_PERMISSION)
			{
				throw new AccessDeniedException();
			}
			
			$note['category_id']=$_POST['category_id'];
			$note['name']=$_POST['name'];
			$note['content']=$_POST['content'];

			if($note['id']>0)
			{
				$notes->update_note($note, $category);
				$response['success']=true;
				$insert=false;
			}else
			{
				$note['user_id']=$GO_SECURITY->user_id;
				
				$note_id= $notes->add_note($note, $category);

				$response['note_id']=$note_id;
				$response['success']=true;
				$insert=true;
			}
			
			
			if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
			{
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$cf->update_fields($GO_SECURITY->user_id, $note_id, 4, $_POST, $insert);
			}			
			
			if(!empty($_POST['tmp_files']) && $GO_MODULES->has_module('files'))
			{
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
				$files = new files();
				$fs = new filesystem();
				
				$note = $notes->get_note($note_id);
				$path = $files->build_path($note['files_folder_id']);
					
				$tmp_files = json_decode($_POST['tmp_files'], true);
				while($tmp_file = array_shift($tmp_files))
				{
					if(!empty($tmp_file['tmp_file'])){
						$new_path = $GO_CONFIG->file_storage_path.$path.'/'.$tmp_file['name'];
						$fs->move($tmp_file['tmp_file'], $new_path);
						$files->import_file($new_path, $note['files_folder_id']);
					}
				}
			}
				
			if(!empty($_POST['link']))
			{
				require_once($GO_CONFIG->class_path.'base/links.class.inc.php');
				$GO_LINKS = new GO_LINKS();
				
				$link_props = explode(':', $_POST['link']);
				$GO_LINKS->add_link(
				($link_props[1]),
				($link_props[0]),
				$note_id,
				4);
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