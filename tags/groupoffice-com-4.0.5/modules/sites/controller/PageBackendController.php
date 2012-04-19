<?php

class GO_Sites_Controller_PageBackend extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Sites_Model_Page';
	
	protected function actionRedirectToFront($params){
		$page = GO_Sites_Model_Page::model()->findByPk($params['id']);
		
		header('Location: '.$page->getUrl(array(), false, false));
		exit();
	}
	
}