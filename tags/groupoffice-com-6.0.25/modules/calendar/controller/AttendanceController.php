<?php

namespace GO\Calendar\Controller;


class AttendanceController extends \GO\Base\Controller\AbstractController{
	protected function actionLoad($params){
		
		$event = \GO\Calendar\Model\Event::model()->findByPk($params['id']);
		if(!$event)
			throw new \GO\Base\Exception\NotFound();
		
		$participant=$event->getParticipantOfCalendar();
		if(!$participant)
			throw new \Exception("The participant of this event is missing");
		
		$organizer = $event->getOrganizer();
		if(!$organizer)
			throw new \Exception("The organizer of this event is missing");
		
		$response = array("success"=>true, 'data'=>array(
				'notify_organizer'=>true,
				'status'=>$participant->status, 
				'organizer'=>$organizer->name,
				'info'=>$event->toHtml()
						));		
		return $response;
	}
	
	protected function actionSubmit($params){
		$response = array("success"=>true);
		
		$event = \GO\Calendar\Model\Event::model()->findByPk($params['id']);
		if(!$event)
			throw new \GO\Base\Exception\NotFound();
		
		if(!empty($params['exception_date']))
		{
			$event = $event->createExceptionEvent($params['exception_date']);
		}
		
		$participant=$event->getParticipantOfCalendar();
		if($params['status']!=$participant->status){
			$participant->status=$params['status'];
			$participant->save();
		
			if(!empty($params['notify_organizer']))
				$event->replyToOrganizer();
		}
		
		
		
		return $response;
	}
}