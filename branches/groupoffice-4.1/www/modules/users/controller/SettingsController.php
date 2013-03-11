<?php
class GO_Users_Controller_Settings extends GO_Base_Controller_AbstractController{
	
	protected function actionLoad($params) {
		
		$settings =  GO_Users_Model_Settings::load();
		
		if(empty($settings->register_email_subject))
			$settings->register_email_subject = GO::t('register_email_subject','users');
		
		if(empty($settings->register_email_body))
			$settings->register_email_body = GO::t('register_email_body','users');
		
		// GET ALL CUSTOMFIELDS TABS AND CREATE AN OPTION TO SELECT/DESELECT THEM
		
		// GET OPTION TO SHOW OR HIDE THE ADDRESSLIST PANEL
			
		return array(
				'success'=>true,
				'data'=>$settings->getArray()
		);
	}
	
	protected function actionSubmit($params) {
		
		$settings =  GO_Users_Model_Settings::load();

		return array(
				'success'=>$settings->saveFromArray($params),
				'data'=>$settings->getArray()
		);
	}
	
}