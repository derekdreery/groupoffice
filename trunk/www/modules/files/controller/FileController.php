<?php
class GO_Files_Model_File extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Files_Model_File';
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		$response['data']['path']=$model->path;
		$response['data']['size']=GO_Base_Util_Number::formatSize($model->fsFile->size());
		
		
		
		
		return parent::afterLoad($response, $model, $params);
	}
}

