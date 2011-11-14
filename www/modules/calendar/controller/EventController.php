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

	function beforeSubmit(&$response, &$model, &$params) {

		$this->_checkConflicts();

		if (!empty($params['exception_date'])) {
			//$params['recurrenceExceptionDate'] is a unixtimestamp. We should return this event with an empty id and the exception date.			
			//this parameter is sent by the view when it wants to edit a single occurence of a repeating event.
			$model = $model->getExceptionEvent($params['exception_date']);
			unset($params['exception_date']);
			unset($params['id']);
		}

		if (isset($params['subject']))
			$params['name'] = $params['subject'];

		if (isset($params['start_time'])) {
			if (isset($params['all_day_event'])) {
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
			$model->reminder = $params['reminder_value'] * $params['reminder_multiplier'];
//		else
//			$model->reminder = 0;

		return parent::beforeSubmit($response, $model, $params);
	}

	private function _checkConflicts() {
		return true;
	}

	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {

		$isNewEvent = empty($params['id']);

		if (!$model->isResource()) {

			$this->_saveParticipants($params, $model, $isNewEvent, $modifiedAttributes);

			$this->_saveResources($params, $model, $isNewEvent, $modifiedAttributes);
		}
		
		
		 if(GO::modules()->files){
			 $f = new GO_Files_Controller_Folder();
			 $f->processAttachments($response, $model, $params);
		 }

		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
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

		if (isset($params['resources'])) {
			$ids = array();
			foreach ($params['resources'] as $resource_calendar_id => $enabled) {
				$resourceEvent = $isNewEvent ? false : GO_Calendar_Model_Event::model()->findResourceForEvent($model->id, $resource_calendar_id);
				if (!$resourceEvent) {
					$resourceEvent = new GO_Calendar_Model_Event();
				}

				$resourceEvent->resource_event_id=$model->id;
				$resourceEvent->calendar_id = $resource_calendar_id;
				$resourceEvent->name = $model->name;
				$resourceEvent->start_time = $model->start_time;
				$resourceEvent->end_time = $model->end_time;
				$resourceEvent->rrule = $model->rrule;
				$resourceEvent->repeat_end_time = $model->repeat_end_time;
				$resourceEvent->status = "NEEDS-ACTION";
				$resourceEvent->user_id = $model->user_id;
				

				if (GO::modules()->customfields)
					$resourceEvent->customfieldsRecord->setAttributes($params['resource_options'][$resource_calendar_id]);

				$resourceEvent->save();

				$ids[] = $resourceEvent->id;
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

	private function _saveParticipants($params, $event, $isNewEvent, $modifiedAttributes) {

		$ids = array();

		$newParticipantIds = array();
		if (!empty($params['participants'])) {

			$newParticipantIds = array();

			$participants = json_decode($params['participants'], true);

			foreach ($participants as $p) {

				$participant = false;
				if (substr($p['id'], 0, 4) != 'new_') {
					$participant = GO_Calendar_Model_Participant::model()->findByPk($p['id']);
				}
				if (!$participant)
					$participant = new GO_Calendar_Model_Participant();

				unset($p['id']);
				$participant->setAttributes($p);
				$participant->is_organizer = $event->user_id == $participant->user_id;
				$participant->event_id = $event->id;


				//Add new event for the participant if requested. Set the status to accepted automatically.
				if (!empty($params['add_to_participant_calendars']) && $participant->user_id > 0 && $participant->user_id != $event->user_id) {
					$calendar = GO_Calendar_Model_Calendar::model()->findDefault($participant->user_id);

					if ($calendar && $calendar->getPermissionLevel() >= GO_Base_Model_Acl::WRITE_PERMISSION) {

						$participantEvent = GO_Calendar_Model_Event::model()->findByUuid($event->uuid,0,$calendar->id);
						if (!$participantEvent)
							$participantEvent = $event->duplicate(array('calendar_id' => $calendar->id));

						//TODO: Do we want this?
						//$participant->status=GO_Calendar_Model_Participant::STATUS_ACCEPTED;
					}
				}

				if ($isNewEvent || !empty($modifiedAttributes)) {
					//reset status on when event is modified or new
					if($participant->is_organizer){
						$participant->status = GO_Calendar_Model_Participant::STATUS_ACCEPTED;
					}else
					{
						$participant->status = GO_Calendar_Model_Participant::STATUS_PENDING;
					}
				}

				$new = $participant->isNew;

				$participant->save();

				if ($new)
					$newParticipantIds[] = $participant->id;


				$ids[] = $participant->id;
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

		if (isset($params['send_invitation']) && $params['send_invitation'] != 'false') {
			$this->_sendInvitation($newParticipantIds, $event, $isNewEvent, $modifiedAttributes);
		}
	}

	private function _sendInvitation($newParticipantIds, $event, $isNewEvent, $modifiedAttributes, $method='REQUEST') {

		
			$stmt = $event->participants();

			while ($participant = $stmt->fetch()) {
				
				if($method=='REQUEST' || $participant->is_organizer){

					if (true || $participant->user_id != GO::user()->id) {
						$subject = $isNewEvent ? GO::t('invitation', 'calendar') : GO::t('invitation_update', 'calendar');

						$body = '<p>' . GO::t('invited', 'calendar') . '</p>' .
										$event->toHtml() .
										'<p><b>' . GO::t('linkIfCalendarNotSupported', 'calendar') . '</b></p>' .
										'<p>' . GO::t('acccept_question', 'calendar') . '</p>' .
										'<a href="' . GO::modules()->calendar->full_url . 'invitation.php?event_id=' . $event->id . '&task=accept&email=' . urlencode($participant->email) . '">' . GO::t('accept', 'calendar') . '</a>' .
										'&nbsp;|&nbsp;' .
										'<a href="' . GO::modules()->calendar->full_url . 'invitation.php?event_id=' . $event->id . '&task=decline&email=' . urlencode($participant->email) . '">' . GO::t('decline', 'calendar') . '</a>';

						$message = GO_Base_Mail_Message::newInstance(
														$subject
										)->setFrom(GO::user()->email, GO::user()->name)
										->addTo($participant->email, $participant->name);
						
						$ics=$event->toICS($method);
						
						$message->setHtmlAlternateBody($body);
						//$message->setBody($body, 'text/html','UTF-8');
						$a = Swift_Attachment::newInstance($event->toICS($method), GO_Base_Fs_File::stripInvalidChars($event->name) . '.ics', 'text/calendar; METHOD="'.$method.'"');
						$a->setEncoder(new Swift_Mime_ContentEncoder_PlainContentEncoder("8bit"));
						$a->setDisposition("inline");
						$message->attach($a);
						GO_Base_Mail_Mailer::newGoInstance()->send($message);
					}
				}
			
		}
	}
	
	protected function beforeDisplay(&$response, &$model, &$params) {
		
		if($model->private && $model->user_id != GO::user()->id)
			throw new GO_Base_Exception_AccessDenied();
		
		return parent::beforeDisplay($response, $model, $params);
	}

	protected function beforeLoad(&$response, &$model, &$params) {
		
	
		if($model->private && $model->user_id != GO::user()->id)
			throw new GO_Base_Exception_AccessDenied();
	
		if (!empty($params['exception_date'])) {
			//$params['recurrenceExceptionDate'] is a unixtimestamp. We should return this event with an empty id and the exception date.			
			//this parameter is sent by the view when it wants to edit a single occurence of a repeating event.
			$model = $model->getExceptionEvent($params['exception_date']);
		}
		return parent::beforeLoad($response, $model, $params);
	}

	public function afterLoad(&$response, &$model, &$params) {

		$response['data']['subject'] = $response['data']['name'];

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
			$settings = GO_Calendar_Model_Settings::model()->findByPk($model->calendar->user_id);
			if($settings)
				$response['data']['background']=$settings->background;
		}
		
		if(!$model->isResource() && $model->id>0)
			$this->_loadResourceEvents($model, $response);

		return parent::afterLoad($response, $model, $params);
	}


	protected function remoteComboFields() {
		return array(
				//	'category_id'=>'$model->category->name',
				'calendar_id' => '$model->calendar->name'
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
			$response['data']['status_'.$resourceEvent->calendar->id] = $resourceEvent->status;
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
		$response['data']['reminder_value'] = 0;

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

		return parent::afterDisplay($response, $model, $params);
	}

//	protected function getStoreMultiSelectProperties(){
//		return array(
//				'requestParam'=>'calendars',
//				'permissionsModel'=>'GO_Calendar_Model_Calendar',
//				'titleAttribute'=>'name'
//				);
//	}	

	public function actionStore($params) {
		$events = GO_Calendar_Model_Event::model()->findForPeriod(false, strtotime("2011-10-03"), strtotime("2011-10-10"));

		var_dump($events);
	}

	public function actionIcs($params) {
		$event = GO_Calendar_Model_Event::model()->findByPk($params['id']);
		header('Content-Type: text/plain');
		echo $event->toICS();
	}
	
	
	public function actionDelete($params){
		
		$event = GO_Calendar_Model_Event::model()->findByPk($params['id']);
		
		if(!empty($params['send_cancellation']))
		{
			//todo
		}
		
		if(!empty($params['exception_date'])){
			$event->addException($params['exception_date']);
		}else
		{			
			if($event)
				$event->delete();
		}
		
		$response['success']=true;
		
		return $response;
	}
	
	/**
	 *
	 * @param array $params
	 * @return Sabre_VObject_Component 
	 */
	private function _getVObjectFromMail($params){
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);		
		$message = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'],$params['uid']);

		$attachments = $message->getAttachments();
		
		foreach($attachments as $attachment){
			if($attachment['mime']=='text/calendar'){
				$data = $message->getImapConnection()->get_message_part_decoded($message->uid, $attachment['number'], $attachment['encoding']);
				
				$vcalendar = GO_Base_VObject_Reader::read($data);

				return $vcalendar->vevent[0];
			}
		}
		return false;
	}
	
	
	public function actionAcceptInvitation($params){
		
		$response['success']=false;
		
		
		//todo calendar should be associated with mail account!
		//GO::user()->id must be replaced with $account->calendar->user_id

		$vevent = $this->_getVObjectFromMail($params);
		
		//if a recurrence-id if passed then convert it to a unix time stamp.
		//it is an update just for a particular occurrence.
		$recurrenceDate=false;
		$recurrence = $vevent->select('recurrence-id');
		//var_dump($recurrence);exit();
		if(count($recurrence)){
			$firstMatch = array_shift($recurrence);
			$recurrenceDate=$firstMatch->getDateTime()->format('U');
		}
		
		//find existing event
		$event = GO_Calendar_Model_Event::model()->findByUuid((string)$vevent->uid, GO::user()->id, 0, $recurrenceDate);				

		$userIsOrganizer=false;
		if($event){

			$participant = GO_Calendar_Model_Participant::model()
							->findSingleByAttributes(array('event_id'=>$event->id, 'user_id'=>GO::user()->id));
			if($participant)
				$userIsOrganizer = $participant->is_organizer;

			//If the user is not the organizer simply delete the old event and
			//import the update. If it's the organizer then we must update just the
			//participant status.
			if(!$userIsOrganizer)
				$event->delete();
		}				

		if($userIsOrganizer)
		{
			//because it's the organizer the event should be there. Wheter it's a recurrence or
			//a normal event.
			if(!$event)
				throw new Exception("The event wasn't found in your calendar");
			
			$participant = $event->importVObjectAttendee($event, $vevent->attendee);			
			
			//todo should we send update to other participants?

		}else
		{	
			$importAttributes=array();
			if($recurrenceDate){
				//if a particular recurrence-id was send then we queried for that particular
				//recurrence. We need to get the master event to add a new exception.
				$masterEvent = GO_Calendar_Model_Event::model()->findByUuid((string)$vevent->uid, GO::user()->id);				
				if($masterEvent){
					$importAttributes=array(
							'exception_for_event_id'=>$masterEvent->id,
							'exception_date'=>$recurrenceDate
					);
					
					//old exception might be there. Delete it because it will be recreated by the import.
					$exception = GO_Calendar_Model_Exception::model()->findSingleByAttributes(array('event_id'=>$masterEvent->id, 'time'=>$recurrenceDate));
					if($exception)
						$exception->delete();
				}
			}
			
			//import it
			$event = GO_Calendar_Model_Event::model()->importVObject($vevent, $importAttributes);

			if(!empty($params['status'])){
				//Update participant status.
				$participant = GO_Calendar_Model_Participant::model()
								->findSingleByAttributes(array('event_id'=>$event->id, 'user_id'=>$event->calendar->user_id));

				$participant->status=$params['status'];
				$participant->save();

				//When the status changes we should notify the organizer.
				$this->_sendInvitation(array(), $event, false, array(), 'REPLY');
			}
		}
		$response['success']=true;
		
		return $response;
	}
	
	
	
	public function actionImportIcs($params){
		
		$file = new GO_Base_Fs_File($params['file']);
		
		$data = $file->getContents();
		
		//var_dump($data);

		$vcalendar = GO_Base_VObject_Reader::read($data);
		
		foreach($vcalendar->vevent as $vevent)
			$event = GO_Calendar_Model_Event::model()->importVObject($vevent);
	}
	
	

}