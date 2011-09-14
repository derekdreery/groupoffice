<?php
/**
 * @todo refactor this code in the new MVC style
 */

//todo build in new style. Now it's necessary for old library functions
require_once(GO::config()->root_path.'Group-Office.php');
		
require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');

class GO_Files_Controller_Item extends GO_Base_Controller_AbstractController{
	
	/**
	 * Creates a folder for notes, contacts, appointents etc.
	 * 
	 * @param type $model
	 * @param type $path
	 * @return type 
	 */
	public static function itemFilesFolder($model){
		
		
//		if($model->isNew){
//			
//			if(!$model->joinAclField){
//				//only new models that have it's own acl field should always have a folder.
//				//otherwise it will be created when first accessed.
//								
//				$f = new GO_Base_Fs_Folder(GO::config()->file_storage_path.$model->buildFilesPath());
//				$fullPath = $f->appendNumberToNameIfExists();
//				$relPath = str_replace(GO::config()->file_storage_path, '', $fullPath);
//				
//				
//				$folder = GO_Files_Model_Folder::model()->findByPath($relPath, true);
//				if(!$folder->acl_id){
//					//folder is not shared.
//					$folder->acl_id=$model->findAclId();
//					$folder->visible=0;
//					$folder->readonly=1;
//					$folder->save();
//				}
//				return $folder->id;
//			}
//			
//		}else
//		{
//			$folder=false;
//			if(!empty($model->files_folder_id)){
//				$folder = GO_Files_Model_Folder::model()->findByPk($model->files_folder_id);
//				if($folder){
//					$currentPath = $folder->path;
//					
//					if($currentPath != $relPath){
//						$folder->move();
//					}
//				}
//			}
//			
//		}
//		
		if(!$model->getIsNew() && !empty($model->files_folder_id))
		{			
			//TODO folder should be a model
			$files = new files();	
			
			$folder_id = $model->files_folder_id;
			$new_folder_id=$folder_id;

			$current_path = $files->build_path($folder_id);
			
			
			$path = $model->buildFilesPath();

			//strip the (n) part at the end of the path that is added when a duplicate
			//is found.
			$check_current_path = preg_replace('/ \([0-9]+\)$/', '', $current_path);

			//echo $current_path.' -> '.$path.'<br />';
			
			

			if (!$current_path) {
				//$new_folder = $files->resolve_path($path, true, 1, '1');
				
				return 0;
			} else {
				
				$f = new GO_Base_Fs_Folder(GO::config()->file_storage_path.$current_path);
				//If it doesn't exists then fine. Store it as 0.
				if(!$f->exists() || count($f->ls())==0 && $f->mtime()<(time()-60)){
					$files->delete_folder($folder_id);
					return 0;
				}
				
				if($check_current_path == $path)
					return $folder_id;
					

				$fs = new filesystem();

				$destfolder_path = dirname($path);
				$destfolder = $files->resolve_path($destfolder_path, true);
				$base = $folder_name = utf8_basename($path);
				$count = 1;
				while ($existing_folder = $files->folder_exists($destfolder['id'], $folder_name)) {
					if ($use_existing) {
						return $existing_folder['id'];
					}
					$folder_name = $base . ' (' . $count . ')';
					$count++;
				}

				$full_source_path = $GLOBALS['GO_CONFIG']->file_storage_path . $current_path;
				$full_dest_path = $GLOBALS['GO_CONFIG']->file_storage_path . $destfolder_path . '/' . $folder_name;

				if (is_dir($full_source_path)) {
					if ($fs->is_sub_dir($full_dest_path, $full_source_path)) {
						//moving into it's own sub path? Strange we must create a new folder
						$folder = $files->create_unique_folder($files->strip_server_path($full_dest_path));
						return $folder['id'];
					} else {
						$fs->move($full_source_path, $full_dest_path);
					}
				} else {
					$fs->mkdir_recursive($GLOBALS['GO_CONFIG']->file_storage_path . $destfolder_path . '/' . $folder_name);
				}

				$sourcefolder = $files->get_folder($folder_id);

				$up_folder['id'] = $new_folder_id;
				$up_folder['parent_id'] = $destfolder['id'];
				$up_folder['name'] = $folder_name;
				$up_folder['readonly'] = '1';

				$files->update_folder($up_folder);
			} 
			return $new_folder_id;
		}
	}
	
	public function actionCreateFolder($params){
		
		$model = call_user_func(array($params['model'],'model'))->findByPk($params['id']);
		
		
		//TODO folder should be a model
		$files = new files();
		
		if(isset($model->acl_id))
		{
			$folder =$files->check_share($model->buildFilesPath(), $model->user_id, $model->acl_id);
		}else
		{
			$folder=$files->create_unique_folder($model->buildFilesPath());
		}
		if($folder)
		{
			$response['files_folder_id']=$model->files_folder_id=$folder['id'];
			$response['success']=$model->save();
			return $response;
		}else
		{
			throw new Exception("Could not create folder");
		}		
	}

	public static function deleteFilesFolder($id){
		$files = new files();
		try{
			$files->delete_folder($id);
		}
		catch(Exception $e){}
	}
}

