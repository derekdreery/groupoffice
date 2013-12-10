<?php

class GO_Site_Controller_Content extends \GO\Base\Controller\AbstractJsonController {
	
	/**
	 * 
	 * 
	 * @param array $params
	 * @return array
	 */
	protected function actionDefaultSlug($params){
		
		$response = array();
		$response['defaultslug']=false;
		$response['success'] = false;
		
		if(empty($params['parentId']))
			Throw new Exception('No Parent ID given!');
		
		$parent = GO_Site_Model_Content::model()->findByPk($params['parentId']);
		
		if(!$parent)
			Throw new Exception('No content item found with the following id: '.$params['parentId']);
		
		$response['defaultslug']=$parent->slug.'/';
		$response['success'] = true;
		
		return $response;
	}
	
	protected function actionTemplateStore($params){
		
		if(empty($params['siteId']))
			Throw new Exception('No Site ID given!');
		
		$site = GO_Site_Model_Site::model()->findByPk($params['siteId']);
		
		if(!$site)
			Throw new Exception('No site found with the following id: '.$id);
		
		$templateFiles = array();
		
		$config = new GO_Site_Components_Config($site);

		if($config->templates){			
			// Read config items and convert to json
			foreach($config->templates as $path=>$name)
				$templateFiles[] = array('path'=>$path,'name'=>$name);
		}
		
		$response = array(
				"success" => true,
				"results" => $templateFiles,
				'total' => count($templateFiles)
		);
		
		echo $this->renderJson($response);
	}
	
//	protected function actionLoad($params){
//
//		$model= GO_Site_Model_Content::model()->createOrFindByParams($params);
//		
//		
//		echo $this->renderForm($model, $remoteComboFields, $extraFields);
//	}
	
	protected function actionUpdate($params){
		
		if(empty($params['id']))
			Throw new Exception('No ID given!');
		
		$model = $this->_loadModel($params['id']);
			
		unset($params['id']); // unset because it doesn't need to be updated
				
		$model->setAttributes($params);
		
		
		if(\GO_Base_Util_Http::isPostRequest()){	
			
			
			$model->save();
			echo $this->renderSubmit($model);
		}  else {
			$remoteComboFields = array();
		
			echo $this->renderForm($model, $remoteComboFields);
		}
	}
	
	protected function actionCreate($params) {
		$model = new GO_Site_Model_Content();
		$model->setAttributes($params);
				
		$model->setDefaultTemplate();

		if(\GO_Base_Util_Http::isPostRequest()){
			$model->save();
			echo $this->renderSubmit($model);
		}  else {
			echo $this->renderForm($model);
		}
  }
		
	protected function actionDelete($params) {
		if(empty($params['id']))
			Throw new Exception('No ID given!');
		
		$model = $this->_loadModel($params['id']);
		
		$response = array();
		
		$response['success'] = $model->delete();
		
		echo $this->renderJson($response);
	}
	
	private function _loadModel($id){
		$model = GO_Site_Model_Content::model()->findByPk($id);
		
		if(!$model)
			Throw new Exception('No content item found with the following id: '.$id);

		return $model;
	}
}
?>
