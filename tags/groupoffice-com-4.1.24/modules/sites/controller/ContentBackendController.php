<?php

class GO_Sites_Controller_ContentBackend extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Sites_Model_Content';
	
	protected function actionRedirectToFront($params){
		$content = GO_Sites_Model_Content::model()->findByPk($params['id']);
		
		header('Location: '.$content->getUrl(array(), false, false));
		exit();
	}
	
}