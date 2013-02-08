<?php
class GO_Calendar_Controller_Attendance extends GO_Base_Controller_AbstractController{
	protected function actionLoad($params){
		
		$event = GO_Calendar_Model_Event::model()->findByPk($params['id']);
		if(!$event)
			throw new GO_Base_Exception_NotFound();
		
		$participant=$event->getParticipantOfCalendar();
		if(!$participant)
			throw new Exception("The participant of this event is missing");
		
		$organizer = $event->getOrganizer();
		if(!$participant)
			throw new Exception("The organizer of this event is missing");
		
		$response = array("success"=>true, 'data'=>array(
				'status'=>$participant->status, 
				'organizer'=>$organizer->name,
				'info'=>$event->toHtml()
						));		
		return $response;
	}
	
	protected function actionSubmit($params){
		$response = array("success"=>true);
		
		$event = GO_Calendar_Model_Event::model()->findByPk($params['id']);
		if(!$event)
			throw new GO_Base_Exception_NotFound();
		
		if(!empty($params['exception_date']))
		{
			$event = $event->createExceptionEvent($params['exception_date']);
		}
		
		$participant=$event->getParticipantOfCalendar();
		$participant->status=$params['status'];
//		$participant->notifyOrganizer=!empty($params['notify_organizer']);
		$participant->save();
		
		if(!empty($params['notify_organizer']))
			$event->replyToOrganizer();
		
		
		
		return $response;
	}
}