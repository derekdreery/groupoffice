<?php

class GO_Files_Controller_File extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Files_Model_File';

	protected function afterDisplay(&$response, &$model, &$params) {

		$response['data']['path'] = $model->path;
		$response['data']['size'] = GO_Base_Util_Number::formatSize($model->fsFile->size());
		$response['data']['extension']=$model->fsFile->extension();
		$response['data']['type']=GO::t($model->fsFile->extension(),'base','filetypes');

		if (!empty($model->random_code) && time() < $model->expire_time) {
			$response['data']['expire_time'] = $model->getAttribute('expire_time','formatted');
			$response['data']['download_link'] = $model->downloadURL;
		} else {
			$response['data']['expire_time'] = "";
			$response['data']['download_link'] = "";
		}
		
		if($model->fsFile->isImage())
			$response['data']['thumbnail_url']=$model->thumbURL;
		else
			$response['data']['thumbnail_url']="";


		return parent::afterDisplay($response, $model, $params);
	}

}

