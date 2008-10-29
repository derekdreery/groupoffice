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
		
			if(isset($_POST['user_id']))
				$category['user_id']=$_POST['user_id'];
				
			$category['name']=$_POST['name'];

			if($category['id']>0)
			{
				$notes->update_category($category);
				$response['success']=true;

			}else
			{
				$category['user_id']=$GO_SECURITY->user_id;
				
				$response['acl_read']=$category['acl_read']=$GO_SECURITY->get_new_acl('category');
				$response['acl_write']=$category['acl_write']=$GO_SECURITY->get_new_acl('category');
				
				$category_id= $notes->add_category($category);							

				$response['category_id']=$category_id;
				$response['success']=true;
			}		
			break;
		
		case 'save_note':				
			$note_id=$note['id']=isset($_POST['note_id']) ? ($_POST['note_id']) : 0;
			
			$category = $notes->get_category((trim($_POST['category_id'])));
			
			if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $category['acl_write']))
			{
				throw new AccessDeniedException();
			}			
			
			$note['category_id']=$_POST['category_id'];
			$note['name']=$_POST['name'];
			$note['content']=$_POST['content'];

			if($note['id']>0)
			{
				$notes->update_note($note);
				$response['success']=true;

			}else
			{
				$note['user_id']=$GO_SECURITY->user_id;
				
				$note_id= $notes->add_note($note);
				
				if($GO_MODULES->modules['files'])
				{
					require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc');
					$fs = new files();

					$response['files_path']='notes/'.$note_id;
						
					$full_path = $GO_CONFIG->file_storage_path.$response['files_path'];
					if(!file_exists($full_path))
					{
						$fs->mkdir_recursive($full_path);
							
						$folder['user_id']=$GO_SECURITY->user_id;
						$folder['path']=$full_path;
						$folder['visible']='0';
						
						
						$folder['acl_read']=$category['acl_read'];
						$folder['acl_write']=$category['acl_write'];
						
							
						$fs->add_folder($folder);
					}
				}
								

				$response['note_id']=$note_id;
				$response['success']=true;
			}
			
			
			if(isset($GO_MODULES->modules['customfields']))
			{
				require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
				$cf = new customfields();
				$cf->update_fields($GO_SECURITY->user_id, $note_id, 4, $_POST);
			}			
				
			if(!empty($_POST['link']))
			{
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