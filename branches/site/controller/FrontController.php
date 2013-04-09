<?php

class GO_Site_Controller_Front extends GO_Site_Controller_Abstract {
	protected function allowGuests() {
		return array('content');
	}
	protected function actionContent($params){
		$content = GO_Site_Model_Content::model()->findBySlug($params['slug']);
		$this->render($content->template,array('content'=>$content));
	}
	
}