<?php
class GO_Calendar_Controller_Attendance extends GO_Base_Controller_AbstractController{
	protected function actionLoad($params){
		
		$event = GO_Calendar_Model_Event::model()->findByPk($params['id']);
		
		$response = array("success"=>true, 'data'=>array('status'=>$event->status));		
		return $response;
	}
	
	protected function actionSubmit($params){
		$response = array("success"=>true);
		
		$event = GO_Calendar_Model_Event::model()->findByPk($params['id']);
		$event->reply($params['status']);
		
		return $response;
	}
}