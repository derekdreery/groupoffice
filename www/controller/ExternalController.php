<?php

class GO_Core_Controller_External extends GO_Base_Controller_AbstractController {
	protected function checkSecurityToken() {
		//we don't check tokens in this controller.
		return true;
	}
	
	public function actionIndex($params) {
		
		$funcParams = GO_Base_Util_Crypt::decrypt($params['f']);
		$this->render('external', $funcParams);		
	}
}