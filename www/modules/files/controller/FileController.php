<?php

class GO_Files_Controller_File extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Files_Model_File';

	protected function afterDisplay(&$response, &$model, &$params) {

		$response['data']['path'] = $model->path;
		$response['data']['size'] = GO_Base_Util_Number::formatSize($model->fsFile->size());
		$response['data']['extension'] = $model->fsFile->extension();
		$response['data']['type'] = GO::t($model->fsFile->extension(), 'base', 'filetypes');

		if (!empty($model->random_code) && time() < $model->expire_time) {
			$response['data']['expire_time'] = $model->getAttribute('expire_time', 'formatted');
			$response['data']['download_link'] = $model->downloadURL;
		} else {
			$response['data']['expire_time'] = "";
			$response['data']['download_link'] = "";
		}

		if ($model->fsFile->isImage())
			$response['data']['thumbnail_url'] = $model->thumbURL;
		else
			$response['data']['thumbnail_url'] = "";

		if(GO::modules()->filesearch){
			$filesearch = GO_Filesearch_Model_Filesearch::model()->findByPk($model->id);
			if(!$filesearch){
				$filesearch = GO_Filesearch_Model_Filesearch::model()->createFromFile($model);
			}
					
			$response['data']=array_merge($response['data'],$filesearch->getAttributes('formatted'));
			
			if (!empty($params['query_params'])) {
				$qp = json_decode($params['query_params'], true);
				if (isset($qp['content_all'])){
					
					$c = new GO_Filesearch_Controller_Filesearch();
					
					$response['data']['text'] = $c->highlightSearchParams($qp, $response['data']['text']);
				}
			}
		}

		return parent::afterDisplay($response, $model, $params);
	}

	protected function afterLoad(&$response, &$model, &$params) {

		$response['data']['path'] = $model->path;
		$response['data']['size'] = GO_Base_Util_Number::formatSize($model->fsFile->size());
		$response['data']['extension'] = $model->fsFile->extension();
		$response['data']['type'] = GO::t($model->fsFile->extension(), 'base', 'filetypes');
		
		if (GO::modules()->customfields)
			$response['customfields'] = GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Files_Model_File", $model->folder_id);

		return parent::afterLoad($response, $model, $params);
	}

	public function actionDownload($params) {
		GO::session()->closeWriting();

		$file = GO_Files_Model_File::model()->findByPk($params['id']);
		GO_Base_Util_Common::outputDownloadHeaders($file->fsFile, false, !empty($params['cache']));
		$file->fsFile->output();
	}

	
	/**
	 *
	 * @param type $params 
	 * @todo
	 */
	public function actionEmailDownloadLink($params){
		
	}
	

}

