<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: GO_Calendar_Controller_Event.php 7607 2011-09-14 10:06:07Z <<USERNAME>> $
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */

/**
 * The GO_Calendar_Controller_Event controller
 *
 */
class GO_Calendar_Controller_Event extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Calendar_Model_Event';
	
	private $_uuidEvents = array();
	
	
	protected function allowGuests() {
		return array('invitation');
	}
	
	protected function ignoreAclPermissions() {
		return array('invitation');
	}
	
	private function _changeTimeParams(&$params){
		if (isset($params['start_date'])) {
			if (!empty($params['all_day_event'])) {
				$params['all_day_event'] = '1';
				$start_time = "00:00";
				$end_time = '23:59';
			} else {
				$params['all_day_event'] = '0';
				$start_time = $params['start_time'];
				$end_time = $params['end_time'];
			}

			$params['start_time'] = $params['start_date'] . ' ' . $start_time;
			$params['end_time'] = $params['end_date'] . ' ' . $end_time;
		}
	}
	
	private function _setEventAttributes($model, $params){
		

		//Grid sends move request
		if (isset($params['offset'])) {
			$model->start_time = GO_Base_Util_Date::roundQuarters($model->start_time + $params['offset']);
			$model->end_time = GO_Base_Util_Date::roundQuarters($model->end_time + $params['offset']);
		}
		if (isset($params['offset_days'])) {
			$model->start_time = GO_Base_Util_Date::date_add($model->start_time, $params['offset_days']);
			$model->end_time = GO_Base_Util_Date::date_add($model->end_time, $params['offset_days']);
		}

		//when a user resizes an event
		if (isset($params['duration_end_time'])) {
			//only use time for the update
			$old_end_date = getdate($model->end_time);
			$new_end_time = getdate($params['duration_end_time']);

			$model->end_time = mktime($new_end_time['hours'], $new_end_time['minutes'], 0, $old_end_date['mon'], $old_end_date['mday'], $old_end_date['year']);
		}

		if (!empty($params['freq'])) {
			$rRule = new GO_Base_Util_Icalendar_Rrule();
			$rRule->readJsonArray($params);
			$model->rrule = $rRule->createRrule();
		} elseif (isset($params['freq'])) {
			$model->rrule = "";
		}

		if (isset($params['reminder_value']) && isset($params['reminder_multiplier']))
			$model->reminder = GO_Base_Util_Number::unlocalize ($params['reminder_value']) * $params['reminder_multiplier'];
//		else
//			$model->reminder = 0;

		$model->setAttributes($params);
	}

	protected function beforeSubmit(&$response, &$model, &$params) {

		//when duplicating in the calendar with right click
		if(!empty($params['duplicate'])){
			$model = $model->duplicate(array('uuid'=>null));
			$params['id']=$model->id;
		}
		
		$this->_changeTimeParams($params);

		$this->_setEventAttributes($model, $params);
		
		if(!$this->_checkConflicts($response, $model, $params)){
			return false;
		}
		
		
		if (!empty($params['exception_date'])) {
			//reset the original attributes other wise create exception can fail
			$model->resetAttributes();
			//$params['recurrenceExceptionDate'] is a unixtimestamp. We should return this event with an empty id and the exception date.			
			//this parameter is sent by the view when it wants to edit a single occurence of a repeating event.
			$recurringEvent = GO_Calendar_Model_Event::model()->findByPk($params['exception_for_event_id']);
			$model = $recurringEvent->createExceptionEvent($params['exception_date'], array(), true);
			unset($params['exception_date']);
			unset($params['id']);
			
			if(!$model)
				throw new Exception("Could not create exception!");
			
			$this->_setEventAttributes($model, $params);
		}
				
		return parent::beforeSubmit($response, $model, $params);
	}

	private function _checkConflicts(&$response, GO_Calendar_Model_Event &$event, &$params) {
		

		if(!empty($params["check_conflicts"]) && $event->busy){		

			$params['exception_for_event_id']=empty($params['exception_for_event_id']) ? 0 : $params['exception_for_event_id'];
			if(count($event->getConflictingEvents($params['exception_for_event_id'])))
				throw new Exception('Ask permission');
		}
//		
//		/* Check for conflicts with other events in the calendar */		
//		$findParams = GO_Base_Db_FindParams::newInstance();
//		$findParams->getCriteria()->addCondition("calendar_id", $event->calendar_id);
//		if(!$event->isNew)
//			$findParams->getCriteria()->addCondition("resource_event_id", $event->id, '<>');
//		
//		$conflictingEvents = GO_Calendar_Model_Event::model()->findCalculatedForPeriod($findParams, $event->start_time, $event->end_time, true);
//		
//		while($conflictEvent = array_shift($conflictingEvents)) {
//			
//			GO::debug("Conflict: ".$event->id." ".$event->name);
//
//			if($conflictEvent["id"]!=$event->id && (empty($params['exception_for_event_id']) || $params['exception_for_event_id']!=$conflictEvent["id"])){
//				throw new Exception('Ask permission');
//			}
//		}
		
		/* Check for conflicts regarding resources */
		if (!$event->isResource() && isset($params['resources'])) {
			//TODO code does not work right. Should be refactored in 4.1
			$resources=array();
			foreach ($params['resources'] as $resource_calendar_id => $enabled) {
				if($enabled=='on')
					$resources[]=$resource_calendar_id;
			}
			
			if (count($resources) > 0) {
				
				$findParams = GO_Base_Db_FindParams::newInstance();
				$findParams->getCriteria()->addInCondition("calendar_id", $resources);
				if(!$event->isNew)
					$findParams->getCriteria()->addCondition("resource_event_id", $event->id, '<>');
				
				$conflictingEvents = GO_Calendar_Model_Event::model()->findCalculatedForPeriod($findParams, $event->start_time, $event->end_time, true);
				
				$resourceConlictsFound=false;
			
				foreach ($conflictingEvents as $conflictEvent) {
					if ($conflictEvent->getEvent()->id != $event->id) {
						$resourceCalendar = $conflictEvent->getEvent()->calendar;
						$resourceConlictsFound=true;
						$response['resources'][] = $resourceCalendar->name;						
					}
				}

				if ($resourceConlictsFound){
					$response["feedback"]="Resource conflict";
					$response["success"]=false;
					return false;
				}
			}
		}
		
		return true;
	}

	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {

		$isNewEvent = empty($params['id']);

		if (!$model->isResource()) {
			$response['participants']=$this->_saveParticipants($params, $model, $isNewEvent, $modifiedAttributes);
			$this->_saveResources($params, $model, $isNewEvent, $modifiedAttributes);
		}
		
		if(GO::modules()->files){
			//handle attachment when event is saved from an email.
			$f = new GO_Files_Controller_Folder();
			$f->processAttachments($response, $model, $params);
		}
		 
		 // Send the status and status background color with the response
		$response['status_color'] = $model->getStatusColor();
		$response['status'] = $model->status;
		$response['is_organizer'] = $model->is_organizer?true:false;
		
		
		if($model->is_organizer){
			//$model->sendMeetingRequest();
			
			if($model->hasOtherParticipants())// && isset($modifiedAttributes['start_time']))
			{			
				$response['isNewEvent']=$isNewEvent;
				$response['askForMeetingRequest']=true;
			}
		}
		
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	
	protected function actionSendMeetingRequest($params){
		$event = GO_Calendar_Model_Event::model()->findByPk($params['event_id']);
		$response['success']=$event->sendMeetingRequest();
		
		return $response;
	}

	/**
	 * Handles the saving of related resource bookings of an event.
	 * 
	 * @param type $params
	 * @param type $model
	 * @param type $isNewEvent
	 * @param type $modifiedAttributes 
	 */
	private function _saveResources($params, $model, $isNewEvent, $modifiedAttributes) {
		if (isset($params['submitresources'])) {
			$ids = array();

			if (isset($params['resources'])) {

				foreach ($params['resources'] as $resource_calendar_id => $enabled) {

					if (!$isNewEvent)
						$resourceEvent = GO_Calendar_Model_Event::model()->findResourceForEvent($model->id, $resource_calendar_id);
					else
						$resourceEvent = false;

					if (empty($resourceEvent))
						$resourceEvent = new GO_Calendar_Model_Event();

					$resourceEvent->resource_event_id = $model->id;
					$resourceEvent->calendar_id = $resource_calendar_id;
					$resourceEvent->name = $model->name;
					$resourceEvent->start_time = $model->start_time;
					$resourceEvent->end_time = $model->end_time;
					$resourceEvent->rrule = $model->rrule;
					$resourceEvent->repeat_end_time = $model->repeat_end_time;
					$resourceEvent->status = "NEEDS-ACTION";
					$resourceEvent->user_id = $model->user_id;

					$resourceEvent->busy = !$resourceEvent->calendar->group->show_not_as_busy;


					if (GO::modules()->customfields && isset($params['resource_options'][$resource_calendar_id]))
						$resourceEvent->customfieldsRecord->setAttributes($params['resource_options'][$resource_calendar_id]);

					$resourceEvent->save(true);

					$ids[] = $resourceEvent->id;
				}
			}
			//delete all other resource events
			$stmt = GO_Calendar_Model_Event::model()->find(
							GO_Base_Db_FindParams::newInstance()
											->criteria(
															GO_Base_Db_FindCriteria::newInstance()
															->addInCondition('id', $ids, 't', true, true)
															->addCondition('resource_event_id', $model->id)
											)
			);
			$stmt->callOnEach('delete');
		}
	}

	private function _saveParticipants($params, GO_Calendar_Model_Event $event, $isNewEvent, $modifiedAttributes) {

		$response = array();
		
		$ids = array();
		if (!empty($params['participants'])) {
			$participants = json_decode($params['participants'], true);
			
			//don't save a single organizer participant
			if(count($participants)>1){				
				foreach ($participants as $p) {

					$participant = false;
//					if (substr($p['id'], 0, 4) != 'new_') {
//						$participant = GO_Calendar_Model_Participant::model()->findByPk($p['id']);
//					}
					//better to search on e-mail so that when creating exception events won't fail
					$participant = GO_Calendar_Model_Participant::model()->findSingleByAttributes(array(
							'email'=> $p['email'],
							'event_id'=>$event->id
					));
					if (!$participant)
						$participant = new GO_Calendar_Model_Participant();

					unset($p['id']);
					$participant->setAttributes($p);
					$participant->event_id = $event->id;
					if(!$participant->save()){
						throw new Exception("Could not save participant ".var_export($participant->getValidationErrors(), true));
					}
					$ids[] = $participant->id;

					$response[]=$participant->toJsonArray($event->start_time, $event->end_time);
				}
			}

			$stmt = GO_Calendar_Model_Participant::model()->find(
							GO_Base_Db_FindParams::newInstance()
											->criteria(
															GO_Base_Db_FindCriteria::newInstance()
															->addInCondition('id', $ids, 't', true, true)
															->addCondition('event_id', $event->id)
											)
			);
			$stmt->callOnEach('delete');
		}
		
		return $response;
	}
	/**
	 *
	 * @param type $newParticipantIds
	 * @param type $event
	 * @param type $isNewEvent
	 * @param type $modifiedAttributes
	 * @param type $method
	 * @param GO_Calendar_Model_Participant $sendingParticipant 
	 */
	private function _sendInvitation($newParticipantIds, $event, $isNewEvent, $modifiedAttributes, $method='REQUEST', $sendingParticipant=false) {

		
			$stmt = $event->participants();

			while ($participant = $stmt->fetch()) {		
				
				$shouldSend = ($method=='REQUEST' && !$participant->is_organizer) || 
					($method=='REPLY' && $participant->is_organizer) || 
					($method=='CANCEL' && !$participant->is_organizer);
									
				if($shouldSend){
					if($isNewEvent){
						$subject = GO::t('invitation', 'calendar').': '.$event->name;
					}elseif($sendingParticipant)
					{							
						$updateReponses = GO::t('updateReponses','calendar');
						$subject= sprintf($updateReponses[$sendingParticipant->status], $sendingParticipant->name, $event->name);
					}elseif($method == 'CANCEL')
					{
						$subject = GO::t('cancellation','calendar').': '.$event->name;
					}else
					{
						$subject = GO::t('invitation_update', 'calendar').': '.$event->name;
					}


					$acceptUrl = GO::url("calendar/event/invitation",array("id"=>$event->id,'accept'=>1,'email'=>$participant->email,'participantToken'=>$participant->getSecurityToken()),false);
					$declineUrl = GO::url("calendar/event/invitation",array("id"=>$event->id,'accept'=>0,'email'=>$participant->email,'participantToken'=>$participant->getSecurityToken()),false);

					if($method=='REQUEST' && $isNewEvent){
						$body = '<p>' . GO::t('invited', 'calendar') . '</p>' .
										$event->toHtml() .
										'<p><b>' . GO::t('linkIfCalendarNotSupported', 'calendar') . '</b></p>' .
										'<p>' . GO::t('acccept_question', 'calendar') . '</p>' .
										'<a href="'.$acceptUrl.'">'.GO::t('accept', 'calendar') . '</a>' .
										'&nbsp;|&nbsp;' .
										'<a href="'.$declineUrl.'">'.GO::t('decline', 'calendar') . '</a>';
					}elseif($method=='CANCEL') {
						$body = '<p>' . GO::t('cancelMessage', 'calendar') . '</p>' .
										$event->toHtml();
					}else // on update event
					{
						$body = '<p>' . GO::t('invitation_update', 'calendar') . '</p>' .
										$event->toHtml() .
										'<p><b>' . GO::t('linkIfCalendarNotSupported', 'calendar') . '</b></p>' .
										'<p>' . GO::t('acccept_question', 'calendar') . '</p>' .
										'<a href="'.$acceptUrl.'">'.GO::t('accept', 'calendar') . '</a>' .
										'&nbsp;|&nbsp;' .
										'<a href="'.$declineUrl.'">'.GO::t('decline', 'calendar') . '</a>';
					}

					$fromEmail = GO::user() ? GO::user()->email : $sendingParticipant->email;
					$fromName = GO::user() ? GO::user()->name : $sendingParticipant->name;

					
					$toEm = $participant->email;
          $toName = $participant->name;

          GO::debug("SEND EVENT INVITATION FROM: ".$fromEmail."(".$fromName.") TO: ".$toEm."(".$toName.")");

					$message = GO_Base_Mail_Message::newInstance($subject)
									->setFrom($fromEmail, $fromName)
									->addTo($participant->email, $participant->name);

					$ics=$event->toICS($method, $sendingParticipant);

					$message->setHtmlAlternateBody($body);
					//$message->setBody($body, 'text/html','UTF-8');
					$a = Swift_Attachment::newInstance($ics, GO_Base_Fs_File::stripInvalidChars($event->name) . '.ics', 'text/calendar; METHOD="'.$method.'"');
					$a->setEncoder(new Swift_Mime_ContentEncoder_PlainContentEncoder("8bit"));
					$a->setDisposition("inline");
					$message->attach($a);
					GO_Base_Mail_Mailer::newGoInstance()->send($message);
				}
				
			
		}
	}
	
	protected function beforeDisplay(&$response, &$model, &$params) {
		
		if($model->private && $model->user_id != GO::user()->id)
			throw new GO_Base_Exception_AccessDenied();
		
		return parent::beforeDisplay($response, $model, $params);
	}
	
	protected function actionLoad($params) {
		
		$this->_changeTimeParams($params);
		
		return parent::actionLoad($params);
	}

	protected function beforeLoad(&$response, &$model, &$params) {
		
	
		if($model->private && $model->user_id != GO::user()->id)
			throw new GO_Base_Exception_AccessDenied();
	
		if (!empty($params['exception_date'])) {
			//$params['exception_date'] is a unixtimestamp. We should return this event with an empty id and the exception date.			
			//this parameter is sent by the view when it wants to edit a single occurence of a repeating event.
			$model = $model->getExceptionEvent($params['exception_date']);
		}
		return parent::beforeLoad($response, $model, $params);
	}

	public function afterLoad(&$response, &$model, &$params) {

//		if (isset($response['data']['name']))
//			$response['data']['subject'] = $response['data']['name'];

		$response = self::reminderSecondsToForm($response);

		$response['data']['start_time'] = date(GO::user()->time_format, $model->start_time);
		$response['data']['end_time'] = date(GO::user()->time_format, $model->end_time);

		if (isset($response['data']['rrule']) && !empty($response['data']['rrule'])) {
			$rRule = new GO_Base_Util_Icalendar_Rrule();
			$rRule->readIcalendarRruleString($model->start_time, $model->rrule);
			$createdRule = $rRule->createJSONOutput();

			$response['data'] = array_merge($response['data'], $createdRule);
		}

		$response['data']['start_date'] = GO_Base_Util_Date::get_timestamp($model->start_time, false);
		$response['data']['end_date'] = GO_Base_Util_Date::get_timestamp($model->end_time, false);

		if (GO::modules()->customfields)
			$response['customfields'] = GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Calendar_Model_Event", $model->calendar->group_id);

		$response['group_id'] = $model->calendar->group_id;
		
		
		if(!$model->id){
			
			
			$days = array('SU','MO','TU','WE','TH','FR','SA');
			
			$response['data'][$days[date('w', $model->start_time)]]=1;
		}
		
		if(!$model->isResource() && $model->id>0)
			$this->_loadResourceEvents($model, $response);
		
//		$response['data']['has_other_participants']=$model->hasOtherParticipants(GO::user()->id);
		
		$response['data']['user_name']=$model->user ? $model->user->name : "Unknown";
		
		if(empty($params['id'])){
			$participantModel = $model->getDefaultOrganizerParticipant();

			$response['participants']=array('results'=>array($participantModel->toJsonArray($model->start_time, $model->end_time)),'total'=>1,'success'=>true);
		}else
		{
			$particsStmt = GO_Calendar_Model_Participant::model()->findByAttribute('event_id',$params['id']);
			$response['participants']=array('results'=>array(),'total'=>0,'success'=>true);

			while ($participantModel = $particsStmt->fetch()) {

				$record=$participantModel->toJsonArray($model->start_time, $model->end_time);
				
				if(!empty($params['exception_date']))
					unset($record['id']);
				
					$response['participants']['results'][] = $record;
				$response['participants']['total']+=1;
			}
			
			if($response['participants']['total']==0){
				$participantModel = $model->getDefaultOrganizerParticipant();

				$response['participants']=array('results'=>array($participantModel->toJsonArray($model->start_time, $model->end_time)),'total'=>1,'success'=>true);
			}
		}
		

		return parent::afterLoad($response, $model, $params);
	}


	protected function remoteComboFields() {
		return array(
				//	'category_id'=>'$model->category->name',
				'calendar_id' => '$model->calendar->name',
				'category_id' => '$model->category->name'
		);
	}
	
	/**
	 *
	 * @param GO_Calendar_Model_Event $event
	 * @param array $response 
	 */
	private function _loadResourceEvents($event, &$response){
		
		$response['data']['resources_checked']=array();
		
		$stmt = $event->resources();		
		while($resourceEvent = $stmt->fetch()){
			$response['data']['resources'][$resourceEvent->calendar->id] = array();
			$response['data']['status_'.$resourceEvent->calendar->id] = $resourceEvent->localizedStatus;
			$response['data']['resources_checked'][] = $resourceEvent->calendar->id;
			
			if(GO::modules()->customfields){
				
				$attr = $resourceEvent->customfieldsRecord->getAttributes('formatted');
				foreach($attr as $key=>$value){
					$resource_options = 'resource_options['.$resourceEvent->calendar->id.']['.$key.']';
					$response['data'][$resource_options] = $value;
				}
			}
		}			
	}

	public static function reminderSecondsToForm($response) {
		$multipliers[] = 604800;
		$multipliers[] = 86400;
		$multipliers[] = 3600;
		$multipliers[] = 60;

		$response['data']['reminder_multiplier'] = 60;
		$response['data']['reminder_value'] = "";
		
		if (!empty($response['data']['reminder'])) {
			for ($i = 0; $i < count($multipliers); $i++) {
				$devided = $response['data']['reminder'] / $multipliers[$i];
				$match = (int) $devided;
				if ($match == $devided) {
					$response['data']['reminder_multiplier'] = $multipliers[$i];
					$response['data']['reminder_value'] = $devided;
					break;
				}
			}
		}
		return $response;
	}

	protected function afterDisplay(&$response, &$model, &$params) {

		$response['data']['event_html'] = $model->toHtml();
		$response['data']['calendar_name'] = $model->calendar->name;

		return parent::afterDisplay($response, $model, $params);
	}	
	
	protected function actionViewStore($params) {
		$view = GO_Calendar_Model_View::model()->findByPk($params['view_id']);
		if (!$view)
			throw new GO_Base_Exception_NotFound();
		
		$response['title']=$view->name;
		
		$print = isset($params['print']);

		unset($params['view_id'], $params['print']);

		//$calendars = $view->calendars;
		$calendars=array();
		$unsortedCalendars = array_merge($view->getGroupCalendars()->fetchAll(), $view->calendars->fetchAll());
		foreach($unsortedCalendars as $calendar){
			$calendars[$calendar->name]=$calendar;
		}
		ksort($calendars);
		$calendars = array_values($calendars);

		$response['success'] = true;
		$response['results'] = array();

		$results = array();
		foreach ($calendars as $calendar) {
			$params['calendars'] = '[' . $calendar->id . ']';
		//	$params['events_only']=true;
			if (!isset($results[$calendar->id]))
				$results[$calendar->id] = $this->actionStore($params);
		}
		$response['results'] = array_values($results);
		
		
		// If you have clicked on the "print" button
		if($print)
			$this->_createPdf($response, true);

		return $response;
	}

	/**
	 *
	 * @param type $params
	 * @return boolean 
	 */
	protected function actionStore($params) {
		
		$colors = array(
			'F0AE67','FFCC00','FFFF00','CCFF00','66FF00',
			'00FFCC','00CCFF','0066FF','95C5D3','6704FB',
			'CC00FF','FF00CC','CC99FF','FB0404','FF6600',
			'C43B3B','996600','66FF99','999999','00FFFF'
		);
		
		$this->_uuidEvents=array();
		
		$response = array();
		$response['calendar_id']='';
		$response['title']= '';
		$response['results'] = array();
		
		if(!empty($params['start_time']))
			$startTime = $params['start_time'];
		else
			$startTime = date('Y-m-d h:m',time());
		
		if(!empty($params['end_time']))
			$endTime = $params['end_time'];
		else
			$endTime = date('Y-m-d h:m',strtotime(date("Y-m-d", strtotime($startTime)) . " +3 months"));
		
		// Check for the given calendars if they have events in the given period
		if(!empty($params['view_id'])){
				$view = GO_Calendar_Model_View::model()->findByPk($params['view_id']);
				if(!$view)
					throw new GO_Base_Exception_NotFound();
				
				//$calendarModels = $view->calendars;
				$calendarModels = array_merge($view->getGroupCalendars()->fetchAll(), $view->calendars->fetchAll());
				$calendars=array();
				foreach($calendarModels as $calendar){
					$calendars[]=$calendar->id;
				}
		}else
		{
			if(!isset($params['calendars']))
				throw new Exception("Missing parameter 'calendars'");
			
			$calendars = json_decode($params['calendars']);
		}
		
		
		$colorIndex = 0;
		
		$response['start_time'] = strtotime($startTime);
		$response['end_time'] = strtotime($endTime);
		
		// Set the count of the total activated calendars in the response.
		$response['calendar_count'] = count($calendars);

		$holidaysAdded=false;
		$bdaysAdded=false;
		
		$calendarModels=array();
		foreach($calendars as $calendarId){
			// Get the calendar model that $calendarIdis used for these events
			try{
				$calendar = GO_Calendar_Model_Calendar::model()->findByPk($calendarId);
				if(!$calendar)
					throw new GO_Base_Exception_NotFound();
				
				$calendarModels[]=$calendar;
				
				
				if(!isset($response['view_calendar_id'])){
					$response['view_calendar_id']=$calendar->id;
					$response['view_calendar_name']=$calendar->name;
				}
				

				// Set the colors for each calendar
				$calendar->displayColor = $colors[$colorIndex];
				if($colorIndex < count($colors)-1)
					$colorIndex++;
				else
					$colorIndex=0;


				if($response['calendar_count'] > 1){
					$background = $calendar->getColor(GO::user()->id);


					if(empty($background)){
						$background = $calendar->displayColor;
					}
					$response['backgrounds'][$calendar->id]=$background;
				}


				$response['title'] .= $calendar->name.' & ';

				if(!isset($response['comment'])){
					$response['count']=0;
					$response['comment']=$calendar->comment;
				}
				
				if(empty($params['events_only'])){
					if(!$bdaysAdded && $calendar->show_bdays && GO::modules()->addressbook){
						$bdaysAdded=true;
						$response = $this->_getBirthdayResponseForPeriod($response,$calendar,$startTime,$endTime);
					}

					if (!$holidaysAdded && !empty($calendar->show_holidays)) {
						$holidaysAdded=true;
						$response = $this->_getHolidayResponseForPeriod($response,$calendar,$startTime,$endTime);
					}

				}
				
					
				if(GO::modules()->tasks && empty($params['events_only'])){
					$response = $this->_getTaskResponseForPeriod($response,$calendar,$startTime,$endTime);
				}

				$response = $this->_getEventResponseForPeriod($response,$calendar,$startTime,$endTime);
			}	catch(GO_Base_Exception_AccessDenied $e){
				//skip calendars without permission
			}
		}
		
		// Get the best default calendar to add new events
		$defaultWritableCalendar = $this->_getDefaultWritableCalendar($calendarModels);
		if($defaultWritableCalendar){
			$response['calendar_id']=$defaultWritableCalendar->id;
			$response['write_permission']= $defaultWritableCalendar->permissionLevel >= GO_Base_Model_Acl::WRITE_PERMISSION?true:false;
//			$response['calendar_name']=$defaultWritableCalendar->name;
			$response['permission_level']=$defaultWritableCalendar->permissionLevel;
		}else
		{
			$response['calendar_id']=0;
			
			// If the calendars parameter is given then use the first one as $response['calendar_id']
			if(!empty($params['calendars'])){
				$calendars = json_decode($params['calendars']);
				if(is_array($calendars))
					$response['calendar_id']= $calendars[0];
			}
				
			$response['write_permission']= false;
//			$response['calendar_name']=$defaultWritableCalendar->name;
			$response['permission_level']=false;
		}
		
		//Sanitize the title so there is no & on the end.
		$response['title'] = trim($response['title'],' &');

		ksort($response['results']);
		
		//Remove the index from the response array
		$response['results']= array_values($response['results']);

		$response['success']=true;
			
		// If you have clicked on the "print" button
		if(isset($params['print']))
			$this->_createPdf($response);
				
		return $response;
	}
	
	/**
	 * Get the best writable calendar for the current user/view
	 * @param array $calendarModels
	 * @return Go_Calendar_Model_Calendar
	 */
	private function _getDefaultWritableCalendar(array $calendarModels){
		
		$defaultCalendar = GO_Calendar_Model_Calendar::model()->findDefault(GO::user()->id);
		$calendar = false;
		
		foreach($calendarModels as $cal){
			if($cal->id == $defaultCalendar->id)
				return $cal;
			
			if(empty($calendar) && $cal->checkPermissionLevel(GO_Base_Model_Acl::CREATE_PERMISSION))
				$calendar = $cal;
		}
		
		return $calendar;
	}
	
	/**
	 * Fill the response array with the tasks thas are in the visible tasklists 
	 * for this calendar between the start and end time
	 * 
	 * @param array $response
	 * @param GO_Calendar_Model_Calendar $calendar
	 * @param string $startTime
	 * @param string $endTime
	 * @return array 
	 */
	private function _getTaskResponseForPeriod($response,$calendar,$startTime,$endTime){
		$resultCount = 0;
		$dayString = GO::t('full_days');
		
		$tasklists = $calendar->visible_tasklists;

		$this->_tasklists = array();
		while($tasklist = $tasklists->fetch()){
			$lists[$tasklist->id] = $tasklist->name;
		}
		if(!empty($lists)){
			
		  
			$taskFindCriteria = GO_Base_Db_FindCriteria::newInstance()
							->addCondition('due_time', strtotime($startTime),'>=')
							->addCondition('due_time', strtotime($endTime), '<=');
			
			// Remove tasks that are completed
			if(!$calendar->show_completed_tasks)
				$taskFindCriteria->addCondition('percentage_complete', 100, '<');

			$taskFindCriteria->addInCondition('tasklist_id', array_keys($lists));
	

			$taskFindParams = GO_Base_Db_FindParams::newInstance()
							->criteria($taskFindCriteria);

			$tasks = GO_Tasks_Model_Task::model()->find($taskFindParams);

			while($task = $tasks->fetch()){

				$startTime = date('Y-m-d',$task->due_time).' 00:00';
				$endTime = date('Y-m-d',$task->due_time).' 23:59';

				$resultCount++;

				
				$taskname = $task->name.' ('.$task->percentage_complete.'%)';

				$response['results'][$this->_getIndex($response['results'], $task->due_time).'task'.$task->id] = array(
					'id'=>$response['count']++,
					'link_count'=>$task->countLinks(),
					'name'=>$taskname,
					'description'=>$lists[$task->tasklist_id],
					'time'=>'00:00',
					'start_time'=>$startTime,
					'end_time'=>$endTime,
					'all_day_event'=>1,
					'model_name'=>'GO_Tasks_Model_Task',
					//'background'=>$calendar->displayColor,
					'background'=>'EBF1E2',
					'day'=>$dayString[date('w', ($task->due_time))].' '.GO_Base_Util_Date::get_timestamp($task->due_time,false),
					'read_only'=>true,
					'task_id'=>$task->id
				);
			}
		}
		// Set the count of the tasks
		$response['count_tasks_only'] = $resultCount;
		
		return $response;
	}
	
	private function _getIndex($results, $start_time,$name=''){

		while(isset($results[$start_time.'_'.$name])) {
			$start_time++;
		}
		return $start_time.'_'.$name;
	}
	
	/**
	 * Fill the response array with the holidays between the start and end time
	 * 
	 * @param array $response
	 * @param GO_Calendar_Model_Calendar $calendar
	 * @param string $startTime
	 * @param string $endTime
	 * @return array 
	 */
	private function _getHolidayResponseForPeriod($response,$calendar,$startTime,$endTime){
		$resultCount = 0;

		
		if(!$calendar->user && empty($calendar->user->holidayset))
			return $response;
		
		
		//$holidays = GO_Base_Model_Holiday::model()->getHolidaysInPeriod($startTime, $endTime, $calendar->user->language);
		$holidays = GO_Base_Model_Holiday::model()->getHolidaysInPeriod($startTime, $endTime, $calendar->user->holidayset);

		if($holidays){
			while($holiday = $holidays->fetch()){ 
				$resultCount++;
				$record = $holiday->getJson();
				$record['calendar_id']=$calendar->id;
				$record['id']=$response['count']++;
				$response['results'][$this->_getIndex($response['results'],strtotime($holiday->date))] = $record;
			}
		}

		// Set the count of the holidays
		$response['count_holidays_only'] = $resultCount;

		
		return $response;
	}
	
	/**
	 * Fill the response array with the birthdays of the contacts in the 
	 * addressbooks between the start and end time
	 * 
	 * @param array $response
	 * @param GO_Calendar_Model_Calendar $calendar
	 * @param string $startTime
	 * @param string $endTime
	 * @return array 
	 */
	private function _getBirthdayResponseForPeriod($response,$calendar,$startTime,$endTime){
		$adressbooks = GO_Addressbook_Model_Addressbook::model()->find(GO_Base_Db_FindParams::newInstance()->permissionLevel(GO_Base_Model_Acl::READ_PERMISSION));
		
		$resultCount = 0;
		$dayString = GO::t('full_days');
		$addressbookKeys = array();

		while($addressbook = $adressbooks->fetch()){
			$addressbookKeys[] = $addressbook->id;
		}

		$alreadyProcessed = array();
		$contacts = $this->_getBirthdays($startTime,$endTime,$addressbookKeys);

		foreach ($contacts as $contact){

			if(!in_array($contact->id, $alreadyProcessed)){
				$alreadyProcessed[] = $contact->id;

				$name = GO_Base_Util_String::format_name($contact->last_name, $contact->first_name, $contact->middle_name);
				$start_arr = explode('-',$contact->upcoming);

				$start_unixtime = mktime(0,0,0,$start_arr[1],$start_arr[2],$start_arr[0]);
				
				$resultCount++;
				
				$response['results'][$this->_getIndex($response['results'],strtotime($contact->upcoming.' 00:00'))] = array(
					'id'=>$response['count']++,
					'name'=>htmlspecialchars(str_replace('{NAME}',$name,GO::t('birthday_name','calendar')), ENT_COMPAT, 'UTF-8'),
					'description'=>htmlspecialchars(str_replace(array('{NAME}','{AGE}'), array($name,$contact->upcoming-$contact->birthday), GO::t('birthday_desc','calendar')), ENT_COMPAT, 'UTF-8'),
					'time'=>date(GO::user()->time_format, $start_unixtime),												
					'start_time'=>$contact->upcoming.' 00:00',
					'end_time'=>$contact->upcoming.' 23:59',
					'model_name'=>'GO_Adressbook_Model_Contact',
//					'background'=>$calendar->displayColor,
					'background'=>'EBF1E2',
					'calendar_id'=>$calendar->id,
					'all_day_event'=>1,
					'day'=>$dayString[date('w', $start_unixtime)].' '.GO_Base_Util_Date::get_timestamp($start_unixtime,false),
					'read_only'=>true,
					'contact_id'=>$contact->id
				);
			}
		}
		
		// Set the count of the birthdays
		$response['count_birthdays_only'] = $resultCount;
		
			return $response;
	}
	
	/**
	 * Fill the response array with the events of the given calendar between 
	 * the start and end time
	 * 
	 * @param array $response
	 * @param GO_Calendar_Model_Calendar $calendar
	 * @param string $startTime
	 * @param string $endTime
	 * @return array 
	 */
	private function _getEventResponseForPeriod($response,$calendar,$startTime,$endTime){	
		$resultCount = 0;
	
		// Get all the localEvent models between the given time period
		$events = GO_Calendar_Model_Event::model()->findCalculatedForPeriod(
								GO_Base_Db_FindParams::newInstance()->criteria(
									GO_Base_Db_FindCriteria::newInstance()->addCondition('calendar_id', $calendar->id)
								)->select(),
								strtotime($startTime), 
								strtotime($endTime)
							);
		
		$this->_uuidEvents = array();

		// Loop through each event and prepare the view for it.
		foreach($events as $event){
			
			// Check for a double event, and merge them if they are double
			$key = $event->getUuid().$event->getAlternateStartTime();
		
			if(isset($this->_uuidEvents[$key]))
			{
				if($event->getEvent()->calendar_id==$this->_uuidEvents[$key]->getEvent()->calendar_id){
					//this is an erroneous situation. events with the same start time and the same uuid may not appear in the same calendar.
					//if we merge it then the user can't edit the events anymore.
					$key+=$event->getEvent()->id;
					$this->_uuidEvents[$key] = $event;
				}else
				{
					$this->_uuidEvents[$key]->mergeWithEvent($event);
				}
			}else{
				$this->_uuidEvents[$key] = $event;
			}
			
//			$this->_uuidEvents[]=$event;
			
			// If you are showing more than one calendar, then change the display 
			// color of the current event to the color of the calendar it belongs to.
			if($response['calendar_count'] > 1){
				$background = $calendar->getColor(GO::user()->id);
				if(empty($background))
					$background = $calendar->displayColor;				
				$event->setBackgroundColor($background);
			}
			
			// Set the id of the event, this is a count of the displayed events 
			// in the view.
//			$event->displayId = $response['count']++;

			$resultCount++; // Add one to the global result count;
		}
		
		foreach($this->_uuidEvents as $uuidEvent) // Add the event to the results array
			$response['results'][$this->_getIndex($response['results'],$uuidEvent->getAlternateStartTime(),$uuidEvent->getName())]=$uuidEvent->getResponseData();
		
		$response['count_events_only'] = $resultCount; // Set the count of the events

		return $response;
	}
		
	protected function actionIcs($params) {
		$event = GO_Calendar_Model_Event::model()->findByPk($params['id']);
		//header('Content-Type: text/plain');
		GO_Base_Util_Http::outputDownloadHeaders(new GO_Base_FS_File('calendar.ics'));
		echo $event->toICS();
	}
	
	protected function actionDelete($params){
		
		$event = GO_Calendar_Model_Event::model()->findByPk($params['id']);
		if(!$event)
			throw new GO_Base_Exception_NotFound();
		
		if(!isset($params['send_cancel_notice']) && $event->hasOtherParticipants()){
			return array(
					'askForCancelNotice'=>true,
					'is_organizer'=>$event->is_organizer,
					'success'=>true
			);
		}  else {			
			if(!empty($params['exception_date']))
				$event = $event->createExceptionEvent($params['exception_date']);
			
			if(!empty($params['send_cancel_notice'])){
				if($event->is_organizer){
					$event->sendCancelNotice();
				}else{
					
					$participant = $event->getParticipantOfCalendar();
					$participant->status=GO_Calendar_Model_Participant::STATUS_DECLINED;
					$participant->save();
					
					$event->replyToOrganizer();
				}
			}				
			$event->delete();			
		}

		$response['success']=true;
		
		return $response;
	}
	
	
	/**
	 * Handle's reply from an attendee when the current user is the organizer.
	 * 
	 * @param Sabre\VObject\Component $vevent
	 * @param type $recurrenceDate
	 * @return boolean
	 * @throws GO_Base_Exception_NotFound
	 */
	private function _handleIcalendarReply(Sabre\VObject\Component $vevent, $recurrenceDate){
		//find existing event
		$masterEvent = GO_Calendar_Model_Event::model()->findByUuid((string)$vevent->uid, GO::user()->id);
		if(!$masterEvent)
			throw new GO_Base_Exception_NotFound();
		
		if($recurrenceDate){
			$event = $masterEvent->findException($recurrenceDate);
			
			//create it
			if(!$event)
				$event = $masterEvent->createExceptionEvent($recurrenceDate);
		}else
		{
			$event = $masterEvent;
		}
		
		$participant = $event->importVObjectAttendee($event, $vevent->attendee);

		$response['feedback']=sprintf(GO::t('eventUpdatedIn','calendar'), $event->calendar->name, $participant->statusName);
		$response['success']=true;
		
		return $response;
	}
	
	/**
	 * Handle's a request from an organizer from another externals system
	 * 
	 * @param Sabre\VObject\Component $vevent
	 * @param type $recurrenceDate
	 * @return boolean
	 * @throws GO_Base_Exception_NotFound
	 */
	private function _handleIcalendarRequest(Sabre\VObject\Component $vevent, $recurrenceDate){
		$masterEvent = GO_Calendar_Model_Event::model()->findByUuid((string)$vevent->uid, GO::user()->id);
		
		
		
		//delete existing data		
		if(!$recurrenceDate){
			//if no recurring instance was given delete the master event
			if($masterEvent)
				$masterEvent->delete();
		}  else 
		{
			
			$exceptionEvent = $masterEvent->findException($recurrenceDate);			
				
			if($exceptionEvent)
				$exceptionEvent->delete();
			
			$exception = $masterEvent->hasException($recurrenceDate);
			if($exception)
				$exception->delete();
		}
		
		$eventUpdated=!$recurrenceDate && $masterEvent || $recurrenceDate && !empty($exceptionEvent);
		
		$importAttributes=array('is_organizer'=>false);
		
		//import it
		$event = new GO_Calendar_Model_Event();
		$event->importVObject($vevent, $importAttributes,false,true);
			
		//notify orgnizer
		$participant = $event->getParticipantOfCalendar();

//		if(!$participant)
//		{
//			//this is a bad situation. The import thould have detected a user for one of the participants.
//			//It uses the E-mail account aliases to determine a user. See GO_Calendar_Model_Event::importVObject
//			$participant = new GO_Calendar_Model_Participant();
//			$participant->event_id=$event->id;
//			$participant->user_id=$event->calendar->user_id;
//			$participant->email=$event->calendar->user->email;	
//			$participant->save();
//		}		
		
//		if($status)
//				$participant->status=$status;
//			$participant->save();
		
//		$event->replyToOrganizer();
		
		
		$langKey = $eventUpdated ? 'eventUpdatedIn' : 'eventScheduledIn';
		
		$response['attendance_event_id']=$event->id;
		$response['feedback']=sprintf(GO::t($langKey,'calendar'), $event->calendar->name, $participant->statusName);
		$response['success']=true;
		
		return $response;
	}
	
	private function _handleIcalendarCancel(Sabre\VObject\Component $vevent, $recurrenceDate){
		$masterEvent = GO_Calendar_Model_Event::model()->findByUuid((string)$vevent->uid, GO::user()->id);
				
		//delete existing data		
		if(!$recurrenceDate){
			//if no recurring instance was given delete the master event
			if($masterEvent)
				$masterEvent->delete();
		}  else {
			$exceptionEvent = $masterEvent->findException($recurrenceDate);
			if($exceptionEvent)
				$exceptionEvent->delete();
			
			$exception = $masterEvent->hasException($recurrenceDate);
			if(!$exception)
				$masterEvent->addException($recurrenceDate);
		}
		
		
		$response['feedback']=sprintf(GO::t('eventDeleted','calendar'));
		$response['success']=true;
		
		return $response;
	}
	
	protected function actionAcceptInvitation($params){
		
		$response['success']=false;
		
		
		//todo calendar should be associated with mail account!
		//GO::user()->id must be replaced with $account->calendar->user_id

//		$vevent = $this->_getVObjectFromMail($params);
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);		
		$message = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'],$params['uid']);
		$vcalendar = $message->getInvitationVcalendar();
		
	
		$vevent = $vcalendar->vevent[0];
		
		//if a recurrence-id if passed then convert it to a unix time stamp.
		//it is an update just for a particular occurrence.
		
		//todo check if $vevent->{'recurrence-id'} works
		
		$recurrenceDate=false;
		$recurrence = $vevent->select('recurrence-id');
		//var_dump($recurrence);exit();
		if(count($recurrence)){
			$firstMatch = array_shift($recurrence);
			$recurrenceDate=$firstMatch->getDateTime()->format('U');
		}
		
		
		switch($vcalendar->method){
			case 'REPLY':
				return $this->_handleIcalendarReply($vevent, $recurrenceDate);
				break;
			
			case 'REQUEST':
				//$status = !empty($params['status']) ? $params['status'] : false;
				return $this->_handleIcalendarRequest($vevent, $recurrenceDate);
				break;
			
			case 'CANCEL':
				return $this->_handleIcalendarCancel($vevent, $recurrenceDate);
				break;
			
			default:
				throw new Exception("Unsupported method: ".$vcalendar->method);
				
		}
	}
	
//	protected function actionImportIcs($params){
//		
//		$file = new GO_Base_Fs_File($params['file']);
//		$file->convertToUtf8();
//		$data = $file->getContents();
//		
//		//var_dump($data);
//
//		$vcalendar = GO_Base_VObject_Reader::read($data);
//		
//		foreach($vcalendar->vevent as $vevent){
//			$event = new GO_Calendar_Model_Event();
//			$event->importVObject($vevent);
//		}
//	}
	
//	protected function actionImportVcs($params){
//		
//		$file = new GO_Base_Fs_File($params['file']);
//		
//		$data = $file->getContents();
//		
//		$vcalendar = GO_Base_VObject_Reader::read($data);
//		
//		GO_Base_VObject_Reader::convertICalendarToVCalendar($vcalendar);
//		
//		foreach($vcalendar->vevent as $vevent){
//			$event = new GO_Calendar_Model_Event();
//			$event->importVObject($vevent);		
//		}
//	}

	public function actionInvitation($params){
		
		$participant = GO_Calendar_Model_Participant::model()->findSingleByAttributes(array(
				'event_id'=>$params['id'],
				'email'=>$params['email']
		));
		
		if(!$participant){
			throw new Exception("Could not find the event");
		}
		
		if($participant->getSecurityToken()!=$params['participantToken']){
			throw new Exception("Invalid request");
		}
		
		if(empty($params['accept']))		
			$participant->status=GO_Calendar_Model_Participant::STATUS_DECLINED;
		else
			$participant->status=GO_Calendar_Model_Participant::STATUS_ACCEPTED;
		
		//save will be handled by organizer when he get's an email
		$participant->save();
		
		$event = $participant->getParticipantEvent();
		if($event){
			$event->replyToOrganizer();
		}else {
			$participant->event->replyToOrganizer(false, $participant, false);
		}
		
		$this->render('invitation', array('participant'=>$participant, 'event'=>$event));
	}
	
	/**
	 * Get the birthdays of the contacts in the given addressbooks between 
	 * the given start and end time.
	 * 
	 * @param string $start_time
	 * @param string $end_time
	 * @param array $abooks
	 * @return GO_Base_Db_ActiveStatement 
	 */
	private function _getBirthdays($start_time,$end_time,$abooks=array()) {

		$start = date('Y-m-d',strtotime($start_time));
		$end = date('Y-m-d',strtotime($end_time));

		$select = "t.id, birthday, first_name, middle_name, last_name, "
			."IF (STR_TO_DATE(CONCAT(YEAR('$start'),'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e') >= '$start', "
			."STR_TO_DATE(CONCAT(YEAR('$start'),'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e') , "
			."STR_TO_DATE(CONCAT(YEAR('$start')+1,'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e')) "
			."as upcoming ";
		
		$findCriteria = GO_Base_Db_FindCriteria::newInstance()
						->addCondition('birthday', '0000-00-00', '!=')
						->addRawCondition('birthday', 'NULL', 'IS NOT');
		
		if(count($abooks)) {
			$abooks=array_map('intval', $abooks);
			$findCriteria->addInCondition('addressbook_id', $abooks);
		}
		
		$having = "upcoming BETWEEN '$start' AND '$end'";
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->distinct()
						->select($select)
						->criteria($findCriteria)
						->having($having)
						->order('upcoming');

		$contacts = GO_Addressbook_Model_Contact::model()->find($findParams);
		
		return $contacts;
	}

	/**
	 * Create a PDF file from the response that is also send to the view.
	 *  
	 * @param array $response 
	 */
	private function _createPdf($response, $view=false){
		$pdf = new GO_Calendar_Views_Pdf_CalendarPdf('L', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false);
		$pdf->setParams($response, $view);
		$pdf->Output(GO_Base_Fs_File::stripInvalidChars($response['title']).'.pdf');
	}
	
	
	
	public function actionParticipantEmailRecipients($params){
		$event = GO_Calendar_Model_Event::model()->findByPk($params['event_id']);
		$participants = $event->participants;
		
		$to = new GO_Base_Mail_EmailRecipients();
		
		while($participant = $participants->fetch()){
			$to->addRecipient($participant->email, $participant->name);
		}
		
		$response['success']=true;
		$response['to']=(string) $to;
		
		return $response;
	}
	
	
	public function actionDeleteOld($params){
		$this->requireCli();
		
		if(!GO::user()->isAdmin())
			throw new Exception("You must be admin");
		
		$this->checkRequiredParameters(array('date'), $params);

		$params['date']=strtotime($params['date']);
		
		if($params['date']>GO_Base_Util_Date::date_add(time(), 0, 0, -1)){
			throw new Exception("Please give a date at least one year in the past.");
		}
		
		$sure = readline("If you continue all events older than '".GO_Base_Util_Date::get_timestamp($params['date'], false)."' will be deleted. Are you sure? (y/n)");
		echo "\n";
		if($sure=='y'){
			
			echo "Deleting...\n";
			
			$findParams = GO_Base_Db_FindParams::newInstance()->ignoreAcl();

			$findParams->getCriteria()
							->addCondition('start_time',$params['date'], '<')
							->addCondition('repeat_end_time',$params['date'], '<');

			$stmt = GO_Calendar_Model_Event::model()->find($findParams);

			foreach($stmt as $event){
				$event->delete();
				echo '.';
			}
			echo "\n";
		
			echo "All done!\n";
		}else
		{
			echo "User aborted\n";
		}
		
	}
}