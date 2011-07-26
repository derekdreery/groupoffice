<?php
/**
 * @todo refactor this code in the new MVC style
 */
require_once($GLOBALS['GO_MODULES']->modules['files']['class_path'].'files.class.inc.php');

class GO_Files_Controller_Item{
	
	/**
	 * Creates a folder for notes, contacts, appointents etc.
	 * 
	 * @param type $model
	 * @param type $path
	 * @return type 
	 */
	public static function itemFilesFolder($model, $path){
		
		//TODO folder should be a model
		$files = new files();			

		if($model->getIsNew())
		{
			if(isset($model->acl_id))
			{
				$folder =$files->check_share($path, $model->user_id, $model->acl_id);
			}else
			{
				$folder=$files->create_unique_folder($path);
			}
			if($folder)
			{
				return $folder['id'];
			}else
			{
				return false;
			}
		}else
		{
			return $files->check_folder_location($model->files_folder_id, $path);
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

