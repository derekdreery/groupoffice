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
			$fs = new filesystem();
			$fs->mkdir_recursive($GO_CONFIG->tmpdir.'files_upload');

			$_SESSION['GO_SESSION']['files']['uploaded_files']=array();
			$_SESSION['GO_SESSION']['files']['uploaded_files_props']=array();

			for ($n = 0; $n < count($_FILES['attachments']['tmp_name']); $n++)
			{
				if (is_uploaded_file($_FILES['attachments']['tmp_name'][$n]))
				{
					$tmp_file = $GO_CONFIG->tmpdir.'files_upload/'.$_FILES['attachments']['name'][$n];
					move_uploaded_file($_FILES['attachments']['tmp_name'][$n], $tmp_file);
					chmod($tmp_file, $GO_CONFIG->file_create_mode);

					$_SESSION['GO_SESSION']['files']['uploaded_files'][]=$tmp_file;
					$_SESSION['GO_SESSION']['files']['uploaded_files_props'][]=$_POST;
				}
			}
			$response['success']=true;
			break;




			case 'overwrite':

			require_once($GO_CONFIG->class_path.'base/quota.class.inc.php');
		
			$quota = new quota();

			$fs = new filesystem();
			$files = new files();

			$new = array();
			$modified=array();

			$command = isset($_POST['command']) ? $_POST['command'] : 'ask';

      $thumb_id = strval($_POST['thumb_id']);
			$folder = $files->get_folder($_POST['folder_id']);
			//var_dump($folder);
			if(!$folder)
			{
				throw new FileNotFoundException();
			}
		//	if(!$files->has_write_permission($GO_SECURITY->user_id, $folder))
	//		{
	//			$response['success']=false;
	//			throw new AccessDeniedException();
	//		}

			$rel_path = $files->build_path($folder);
			$full_path = $GO_CONFIG->file_storage_path.$rel_path;
		

			while($tmp_file = array_shift($_SESSION['GO_SESSION']['files']['uploaded_files']))
			{
       

				$filename = utf8_basename($tmp_file);
       // $extension= strtolower(File::get_extension($tmp_filename));
			//	$filename= $thumb_id.'.'.$extension;

				$new_path = $full_path.'/'.$filename;
				$icon_path= $rel_path.'/'.$filename;
				$response['path']=$icon_path;

		//		if(file_exists($new_path) && $command!='yes' && $command!='yestoall')
	//			{
				//	if($command!='no' && $command != 'notoall')
				//	{
				//		array_unshift($_SESSION['GO_SESSION']['files']['uploaded_files'], $tmp_file);
				//		$response['file_exists']=$tmp_filename;// utf8_basename($tmp_file);
				//		throw new Exception('File exists');
				//	}
	//			}else
		//		{
					
					$existing_file = $files->file_exists($folder['id'], $filename);
					if($existing_file)
					{
						$modified[]=$filename;
					}else
					{
						$new[]=$filename;
					}

					if(!$fs->move($tmp_file, $new_path))
					{
						throw new Exception($lang['common']['saveError']);
					}

					$props = isset($_SESSION['GO_SESSION']['files']['uploaded_files_props']) ? array_shift($_SESSION['GO_SESSION']['files']['uploaded_files_props']) : false;
					$comments = isset($props['comments']) ? $props['comments'] : '';

					$file_id = $files->import_file($new_path, $folder['id'], $comments);

			//	}
			//	if($command != 'yestoall' && $command != 'notoall')
			//	{
			//		$command='ask';
			//	}
			}

			$files->touch_folder($folder['id']);

			$files->notify_users($folder, $GO_SECURITY->user_id, $modified, $new);

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

}

echo json_encode($response);




?>
