<?php

class GO_Core_Controller_External extends GO_Base_Controller_AbstractController {
	protected function allowGuests() {
		return array('index');
	}
	public function actionIndex($params) {
		
		$funcParams = GO_Base_Util_Crypt::decrypt($params['f']);
		$this->render('external', $funcParams);		
	}
}