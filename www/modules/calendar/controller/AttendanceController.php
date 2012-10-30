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
		
		if(!empty($params['exception_date']))
		{
			$event = $event->createExceptionEvent($params['exception_date']);
		}
		
		$event->replyToOrganizer($params['status'], !empty($params['notify_organizer']));
		
		return $response;
	}
}