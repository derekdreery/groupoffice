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
 * @author Twan Verhofstad
 */


require_once('../../Group-Office.php');

//Authenticate the user
$GO_SECURITY->json_authenticate('bookmarks');

//Require the module class
require_once ($GO_MODULES->modules['bookmarks']['class_path'].'bookmarks.class.inc.php');
$bookmarks = new bookmarks();

$task=isset($_REQUEST['task']) ? $_REQUEST['task'] : '';


// delete bookmark
$bm_id=isset($_REQUEST['bm_id']) ? $_REQUEST['bm_id'] : '';
$usr_id=isset($_REQUEST['usr_id']) ? $_REQUEST['usr_id'] : '';

try {
	switch($task) {
		case 'upload':
			$response['success']=true;
			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			$fs = new filesystem();
			$fs->mkdir_recursive($GO_CONFIG->tmpdir.'files_upload');

			$relpath = 'public/bookmarks/';
			$path = $GO_CONFIG->file_storage_path.$relpath;

			if(!is_dir($path))
				mkdir($path,0755, true);

			$response['logo']=$relpath.$_POST['thumb_id'].'.'.File::get_extension($_FILES['attachments']['name'][0]);
			
			if (is_uploaded_file($_FILES['attachments']['tmp_name'][0]))
			{
				move_uploaded_file($_FILES['attachments']['tmp_name'][0], $GO_CONFIG->file_storage_path.$response['logo']);
			}

			$response['success']=true;
			break;

			
		case 'delete_bookmark':
    
      try {
      $response['deleteSuccess']=true;
			if ($usr_id!=$GO_SECURITY->user_id) {throw new AccessDeniedException();}

			$bookmarks->delete_bookmark($bm_id);

			}
			catch(Exception $e) {
				$response['deleteSuccess']=false;
				$response['deleteFeedback']=$e->getMessage();

			}
		  break;


		case 'save_bookmark':
			$bookmark_id=$bookmark['id']=isset($_POST['id']) ? ($_POST['id']) : 0;
  		//$category = $bookmarks->get_category((trim($_POST['category_id'])));
     

			$bookmark['user_id']=$GO_SECURITY->user_id;
			$bookmark['category_id']=$_POST['category_id'];
			$bookmark['name']=$_POST['name'];
			$bookmark['content']=$_POST['content'];
			$bookmark['description']=$_POST['description'];
			$bookmark['open_extern']=isset($_POST['open_extern']) ? '1' : '0';
                        $bookmark['behave_as_module']=isset($_POST['behave_as_module']) ? '1' : '0';
      $bookmark['logo']=$_POST['logo'];
			$bookmark['public_icon']=$_POST['public_icon'];

			if($bookmark['id']>0)
			{
				$bookmarks->update_bookmark($bookmark);
				$response['success']=true;
				$insert=false;
			}else
			{
				$bookmark_id= $bookmarks->add_bookmark($bookmark);
				$response['bookmark_id']=$bookmark_id;
				$response['success']=true;
				$insert=true;
			}
			break;
			


			case 'save_category':
			$category_id=$category['id']=isset($_POST['category_id']) ? ($_POST['category_id']) : 0;
			if(isset($_POST['user_id']))
				$category['user_id']=$_POST['user_id'];
			$category['name']=$_POST['name'];
			if($category['id']>0)
			{
				$old_category = $bookmarks->get_category($category['id']);

				if($old_category['user_id'] != $GO_SECURITY->user_id && $GO_SECURITY->has_permission($GO_SECURITY->user_id, $old_category['acl_id'])<GO_SECURITY::WRITE_PERMISSION)
				{
					throw new AccessDeniedException();
				}

				if(isset($_POST['public']) && empty($old_category['acl_id'])){
					$category['acl_id']=$GO_SECURITY->get_new_acl('bookmarks');
					$response['acl_id']=$category['acl_id'];
				}elseif(!isset($_POST['public']) && !empty($old_category['acl_id'])){
					$category['acl_id']=0;
					$response['acl_id']=0;
					
					$GO_SECURITY->delete_acl($old_category['acl_id']);
				}

				$bookmarks->update_category($category, $old_category);
				$response['success']=true;

			}else
			{
				$category['user_id']=$GO_SECURITY->user_id;

        //$category['acl_id']= $GO_SECURITY->get_new_acl('bookmarks');
				$category['acl_id']=isset($_POST['public'] ) ? $GO_SECURITY->get_new_acl('bookmarks') : 0;
        $response['acl_id']=$category['acl_id'];
				$category_id= $bookmarks->add_category($category);

				$response['category_id']=$category_id;
				$response['success']=true;
			}
			break;







	}

} catch(Exception $e) {
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}

echo json_encode($response);