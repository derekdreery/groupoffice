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

		$params['name'] = $params['subject'];

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

		if (!empty($params['freq'])) {
			$rRule = new GO_Base_Util_Icalendar_Rrule();
			$rRule->readJsonArray($params);
			$model->rrule = $rRule->createRrule();
		}else
		{
			$model->rrule="";
		}

		if (isset($params['reminder_value']) && isset($params['reminder_multiplier']))
			$model->reminder = $params['reminder_value'] * $params['reminder_multiplier'];
		else
			$model->reminder = 0;

		return parent::beforeSubmit($response, $model, $params);
	}

	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {

		$isNewEvent = empty($params['id']);
		
		
		$this->_saveParticipants($params, $model, $isNewEvent, $modifiedAttributes);


		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}

	private function _saveParticipants($params, $event, $isNewEvent, $modifiedAttributes) {
		
		$ids = array();
		
		$newParticipantIds = array();
		if (!empty($params['participants'])) {
			
			$newParticipantIds = array();
			
			$participants = json_decode($params['participants'], true);

			foreach ($participants as $p) {
				
				$participant=false;
				if (substr($p['id'], 0, 4) != 'new_') {
					$participant = GO_Calendar_Model_Participant::model()->findByPk($p['id']);
				}
				if(!$participant)
					$participant = new GO_Calendar_Model_Participant();
								
				unset($p['id']);
				$participant->setAttributes($p);
				$participant->is_organizer=$event->user_id==$participant->user_id;
				$participant->event_id=$event->id;
				
				
				//Add new event for the participant if requested. Set the status to accepted automatically.
				if(!empty($params['add_to_participant_calendars']) && $participant->user_id>0 && $participant->user_id!=$event->user_id){
					$calendar = GO_Calendar_Model_Calendar::model()->findDefault($participant->user_id);
					
					if($calendar && $calendar->getPermissionLevel()>=GO_Base_Model_Acl::WRITE_PERMISSION){
						
						$participantEvent = GO_Calendar_Model_Event::model()->findParticipantEvent($calendar->id, $event->uuid);
						if(!$participantEvent)
							$participantEvent = $event->duplicate(array('calendar_id'=>$calendar->id));				
						
						//TODO: Do we want this?
						//$participant->status=GO_Calendar_Model_Participant::STATUS_ACCEPTED;
					}
				}
				
				if($isNewEvent || !empty($modifiedAttributes)){
					//reset status on when event is modified or new
					$participant->status = GO_Calendar_Model_Participant::STATUS_PENDING;
				}
				
				$new = $participant->isNew;
				
				$participant->save();
				
				if($new)
					$newParticipantIds[]=$participant->id;
				
				
				$ids[]=$participant->id;
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
		
		$this->_sendInvitation($params, $newParticipantIds, $event, $isNewEvent, $modifiedAttributes);
		
	}
	
	private function _sendInvitation($params, $newParticipantIds, $event, $isNewEvent, $modifiedAttributes){
		
		if(!empty($params['send_invitation'])){
			
			$stmt = $event->participants();
			
			while($participant = $stmt->fetch()){
				
				if($participant->user_id != GO::user()->id){
					$subject = $isNewEvent ? GO::t('invitation','calendar') : GO::t('invitation_update','calendar');

					$body = '<p>'.GO::t('invited','calendar').'</p>'.
						$event->toHtml().
						'<p><b>'.GO::t('linkIfCalendarNotSupported','calendar').'</b></p>'.
						'<p>'.GO::t('acccept_question','calendar').'</p>'.
						'<a href="'.GO::modules()->calendar->full_url.'invitation.php?event_id='.$event->id.'&task=accept&email='.urlencode($participant->email).'">'.GO::t('accept','calendar').'</a>'.
						'&nbsp;|&nbsp;'.
						'<a href="'.GO::modules()->calendar->full_url.'invitation.php?event_id='.$event->id.'&task=decline&email='.urlencode($participant->email).'">'.GO::t('decline','calendar').'</a>';

					$message = GO_Base_Mail_Message::newInstance(
										$subject
										)->setFrom(GO::user()->email, GO::user()->name)
										->addTo($participant->email, $participant->name);
														
					$message->setHtmlAlternateBody($body);

					GO_Base_Mail_Mailer::newGoInstance()->send($message);
				}
			}
		}		
	}

	protected function beforeLoad(&$response, &$model, &$params) {

		if (!empty($params['exception_date'])) {
			//$params['recurrenceExceptionDate'] is a unixtimestamp. We should return this event with an empty id and the exception date.			
			//this parameter is sent by the view when it wants to edit a single occurence of a repeating event.
			$model->becomeException(strtotime($params['exception_date']));
		}
		return parent::beforeLoad($response, $model, $params);
	}

	public function afterLoad(&$response, &$model, &$params) {

		$response['data']['subject'] = $response['data']['name'];

		$response = $this->_reminderSecondsToForm($response);

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

		return parent::afterLoad($response, $model, $params);
	}

	protected function remoteComboFields() {
		return array(
				//	'category_id'=>'$model->category->name',
				'calendar_id' => '$model->calendar->name'
		);
	}

	private function _reminderSecondsToForm($response) {
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

	public function actionStore($params) {
		$events = GO_Calendar_Model_Event::model()->findForPeriod(false, strtotime("2011-10-03"), strtotime("2011-10-10"));

		var_dump($events);
	}

}