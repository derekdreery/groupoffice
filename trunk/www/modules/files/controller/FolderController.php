<?php
class GO_Files_Controller_Folder extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Files_Model_Folder';
	
	
	public function actionTree($params){
		if(empty($params['node']) || $params['node']=='root'){
			$folder = GO_Files_Model_Folder::model()->findByPath('users/'.GO::user()->username, true);
			
			$folder->syncFilesystem();
			
			
			
		}
	}
}

