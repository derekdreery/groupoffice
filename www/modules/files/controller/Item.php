<?php
class GO_Files_Controller_Item{
	public static function itemFilesFolder($model, $path){
		
		//TODO folder should be a model
		require_once(GO::modules()->modules['files']['class_path'].'files.class.inc.php');
		$files = new files();			

		$folder=$files->create_unique_folder($path);
		if($folder)
		{
			return $folder['id'];
		}else
		{
			return false;
		}
	}
}