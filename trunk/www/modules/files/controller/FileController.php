<?php

class GO_Files_Model_File extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Files_Model_File';

	protected function afterLoad(&$response, &$model, &$params) {

		$response['data']['path'] = $model->path;
		$response['data']['size'] = GO_Base_Util_Number::formatSize($model->fsFile->size());
		$response['data']['extension']=$model->fsFile->extension();

		if (!empty($file['random_code']) && time() < $file->expire_time) {
			$response['data']['expire_time'] = $model->expire_time;
			$response['data']['download_link'] = $model->downloadURL;
		} else {
			$response['data']['expire_time'] = "";
			$response['data']['download_link'] = "";
		}


		return parent::afterLoad($response, $model, $params);
	}

}

