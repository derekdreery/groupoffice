<?php

class GO_Core_Controller_External extends GO_Base_Controller_AbstractController {
	protected function allowGuests() {
		return array('index');
	}
	protected function actionIndex($params) {
		
		//$funcParams = GO_Base_Util_Crypt::decrypt($params['f']);
		
		if(substr($_REQUEST['f'],0,9)=='{GOCRYPT}')
			$funcParams = GO_Base_Util_Crypt::decrypt($_REQUEST['f']);
		else
			$funcParams = json_decode(base64_decode($_REQUEST['f']),true);
		
		$this->render('external', $funcParams);		
	}
}