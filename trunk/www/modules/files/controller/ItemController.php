<?php
/**
 * @todo refactor this code in the new MVC style
 */
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
		
		if(!$model->getIsNew() && !empty($model->files_folder_id))
		{			
			//TODO folder should be a model
			$files = new files();	
			return $files->check_folder_location($model->files_folder_id, $model->buildFilesPath());
		}
	}
	
	public function actionCreateFolder($params){
		
		$model = $params['model']::model()->findByPk($params['id']);
		
		
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

