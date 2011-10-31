<?php

class GO_Sites_User_Controller extends GO_Sites_Controller_Site{
	
	public function actionRegister($params){
		
		$userContoller = new GO_Users_Controller_User();
		$response = $userContoller->actionSubmit($params);
		
		
		$response['user'] = GO_Base_Model_User::model()->findByPk($response['id']);
		
		$this->renderPage($params['path'], $response);
				
	}
	
}