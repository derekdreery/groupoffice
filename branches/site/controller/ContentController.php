<?php

class GO_Site_Controller_Content extends GO_Base_Controller_AbstractJsonController {

	protected $model = 'GO_Site_Model_Content';
	
	/**
	 * TODO: fix the action
	 * @param array $params the $_REQUEST 
	 */
	protected function actionRedirectToFront($params){
		$content = GO_Site_Model_Content::model()->findByPk($params['id']);
		$site = GO_Site_Model_Site::model()->findByPk($content->site_id);
		
		$url = "http://www.".$site->domain."/".$content->getUrl(); 
		header('Location: '.$url);
		exit();
	}
	
	protected function getStoreParams($params) {
		$fp = GO_Base_Db_FindParams::newInstance()->order('sort_order');
		
		$fp->getCriteria()->addCondition('site_id', $params['site_id']);
		
		return $fp;
	}
	
	
	protected function actionSaveSort($params){		
		$items = json_decode($params['content'], true);
		$sort = 0;
		foreach ($items as $item) {
			$model = GO_Site_Model_Content::model()->findByPk($item['id']);
			$model->sort_order=$sort;
			$model->save();
			$sort++;
		}		
		
		return array('success'=>true);
	}	
	
	
	protected function actionLoad($params){
		if(empty($params['id']))
			Throw new Exception('No ID given!');
		
		$model = $this->_loadModel($params['id']);
		
		$remoteComboFields = array();
		
		$this->renderForm($model, $remoteComboFields);
	}
	
	protected function actionUpdate($params){
		
		if(empty($params['id']))
			Throw new Exception('No ID given!');
		
		$model = $this->_loadModel($params['id']);
			
		unset($params['id']); // unset because it doesn't need to be updated
		
		$model->setAttributes($params);
		$model->save();
		$this->renderSubmit($model);
	}
	
	protected function actionCreate($params) {
		$model = new GO_Site_Model_Content();
		$model->setAttributes($params);
		$model->save();

		$this->renderSubmit($model);
  }
		
	protected function actionDelete($params) {
		if(empty($params['id']))
			Throw new Exception('No ID given!');
		
		$model = $this->_loadModel($params['id']);
		
		$response = array();
		
		$response['success'] = $model->delete();
		
		$this->renderJson($response);
	}
	
	private function _loadModel($id){
		$model = GO_Site_Model_Content::model()->findByPk($id);
		
		if(!$model)
			Throw new Exception('No content item found with the following id: '.$id);
		
		return $model;
	}
}
?>
