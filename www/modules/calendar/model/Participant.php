<?php

/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The GO_Calendar_Model_Participant model
 *
 * @package GO.modules.Calendar
 * @version $Id: GO_Calendar_Model_Participant.php 7607 2011-09-28 10:31:03Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property string $email
 * @property int $user_id
 * @property int $status
 * @property string $last_modified
 * @property int $is_organizer
 * @property string $role
 * 
 * @property GO_Calendar_Model_Event $event
 * @property string $statusName;
 * 
 * 
 */
class GO_Calendar_Model_Participant extends GO_Base_Db_ActiveRecord {

	const STATUS_TENTATIVE = "TENTATIVE";
	const STATUS_DECLINED = "DECLINED";
	const STATUS_ACCEPTED = "ACCEPTED";
	const STATUS_PENDING = "NEEDS-ACTION";
	
	public $notifyOrganizer=false;
	
	
	public $updateRelatedParticipants=true;
	
	
	public $notifyRecurrenceTime=false;
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Calendar_Model_Participant
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function validate() {
		if (empty($this->name))
			$this->name = $this->email;

		return parent::validate();
	}

	/**
	 * Enable this function if you want this model to check the acl's automatically.
	 */
	// public function aclField(){
	//	 return 'acl_id';	
	// }

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'cal_participants';
	}

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	public function relations() {
		return array(
				'event' => array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Event', 'field' => 'event_id'),
		);
	}

	/**
	 * Check if the participant is available.
	 * 
	 * Returns a questionmark if the particiant is not a user.
	 * 
	 * @param int $start_time If empty then the related event will be used.
	 * @param int $end_time If empty then the related event will be used.
	 * 
	 * @return boolean/?
	 */
	public function isAvailable($start_time = false, $end_time = false) {
		if (empty($this->user_id) || !$this->_hasFreeBusyAccess()) {
			return '?';
		} else {

			if (!$start_time && $this->event)
				$start_time = $this->event->start_time;

			if (!$end_time && $this->event)
				$end_time = $this->event->end_time;

			if ($start_time && $end_time)
				return self::userIsAvailable($start_time, $end_time, $this->user_id, $this->event);
			else
				return '?';
		}
	}

	/**
	 * @todo
	 */
	private function _hasFreeBusyAccess() {
		return true;
	}

	/**
	 * Check if a user has events between two given times.
	 * 
	 * @param type $periodStartTime
	 * @param type $periodEndTime
	 * @param type $userId
	 * @param type $ignoreEvent
	 * @return boolean 
	 */
	public static function userIsAvailable($periodStartTime, $periodEndTime, $userId, $ignoreEvent = false) {

		$findParams = GO_Base_Db_FindParams::newInstance()
						->ignoreAcl();

		$joinCriteria = GO_Base_Db_FindCriteria::newInstance()
						->addRawCondition('t.calendar_id', 'c.id');

		$findParams->join(GO_Calendar_Model_Calendar::model()->tableName(), $joinCriteria, 'c');

		$findParams->getCriteria()->addCondition('user_id', $userId, '=', 'c');

		if ($ignoreEvent) {
			$findParams->getCriteria()
							->addModel(GO_Calendar_Model_Event::model())
							->addCondition('id', $ignoreEvent->id, '!=')
							->addCondition('uuid', $ignoreEvent->uuid, '!=')
			;
		}

		$events = GO_Calendar_Model_Event::model()->findCalculatedForPeriod($findParams, $periodStartTime, $periodEndTime, true);

		foreach ($events as $event) {
			GO::debug($event->getName());
		}

		return count($events) == 0;
	}

	public function defaultAttributes() {
		$attr = parent::defaultAttributes();
		$attr['user_id'] = 0;
		return $attr;
	}

	public function getSecurityToken() {
		return md5($this->event_id . $this->email . $this->event->ctime);
	}

	public function getStatusName() {
		switch ($this->status) {
			case self::STATUS_TENTATIVE :
				return GO::t('tentative', 'calendar');
				break;

			case self::STATUS_DECLINED :
				return GO::t('declined', 'calendar');
				break;

			case self::STATUS_ACCEPTED :
				return GO::t('accepted', 'calendar');
				break;

			default:
				return GO::t('notRespondedYet', 'calendar');
				break;
		}
	}

	/**
	 * Get related participant event. UUID and user_id of calendar must match. 
	 * Returns false if it doesn't exists.
	 * 
	 * @return GO_Calendar_Model_Event
	 */
	public function getParticipantEvent() {
		return GO_Calendar_Model_Event::model()->findByUuid($this->event->uuid, $this->user_id);
	}

	/**
	 * Get related participant event. UUID and user_id of calendar must match. 
	 * Returns false if it doesn't exists.
	 * 
	 * @return GO_Calendar_Model_Event
	 */
	public function getOrganizerEvent() {
		if ($this->is_organizer)
			return $this->event;
		else
			return GO_Calendar_Model_Event::model()->findSingleByAttributes(array('uuid' => $this->event->uuid, 'is_organizer' => 1));
	}

	/**
	 * Get's the participant's default calendar if it has one.
	 * @return GO_Calendar_Model_Calendar
	 */
	public function getDefaultCalendar() {
		if (empty($this->user_id))
			return false;

		return GO_Calendar_Model_Calendar::model()->findDefault($this->user_id);
	}

	public function toJsonArray($start_time = false, $end_time = false) {
		$record = $this->getAttributes();

		$record['available'] = $this->isAvailable($start_time, $end_time);
		$calendar = $this->getDefaultCalendar();
		$record['create_permission'] = $calendar ? $calendar->userHasCreatePermission() : false;
		return $record;
	}
	
	
	protected function afterSave($wasNew) {
		
		
		if(!$this->isNew && $this->updateRelatedParticipants && $this->isModified('status')){
			$stmt = $this->getRelatedParticipants();
			
			foreach($stmt as $participant){
				
				$participant->updateRelatedParticipants=false;//prevent endless loop. Because it will also process this aftersave
				
				$participant->status=$this->status;
				$participant->save();				
			}
		}
		
//		if($this->notifyOrganizer){
//			$this->_notifyOrganizer();
//		}
		
		return parent::afterSave($wasNew);
	}
	
//	private function _notifyOrganizer(){
//
////		if(!$sendingParticipant)
////			throw new Exception("Could not find your participant model");
//
//		$organizer = $this->event->getOrganizer();
//		if(!$organizer)
//			throw new Exception("Could not find organizer to send message to!");
//
//		$updateReponses = GO::t('updateReponses','calendar');
//		$subject= sprintf($updateReponses[$this->status], $this->user->name, $this->event->name);
//
//
//		//create e-mail message
//		$message = GO_Base_Mail_Message::newInstance($subject)
//							->setFrom($this->user->email, $this->user->name)
//							->addTo($organizer->email, $organizer->name);
//
//		$body = '<p>'.$subject.': </p>'.$this->event->toHtml();
//
//		if(!$this->event->getOrganizerEvent()){
//			//organizer is not a Group-Office user with event. We must send a message to him an ICS attachment
//			$ics=$this->event->toICS("REPLY", $this, $this->notifyRecurrenceTime);				
//			$a = Swift_Attachment::newInstance($ics, GO_Base_Fs_File::stripInvalidChars($this->event->name) . '.ics', 'text/calendar; METHOD="REPLY"');
//			$a->setEncoder(new Swift_Mime_ContentEncoder_PlainContentEncoder("8bit"));
//			$a->setDisposition("inline");
//			$message->attach($a);
//		}
//
//		$message->setHtmlAlternateBody($body);
//
//		GO_Base_Mail_Mailer::newGoInstance()->send($message);
//		
//	}
	
	
	/**
	 * Returns all participant models for this event and all the related events for a meeting.
	 * 
	 * @return GO_Calendar_Model_Participant
	 */
	public function getRelatedParticipants(){
		//update all participants with this user and event uuid in the system		
		$findParams = GO_Base_Db_FindParams::newInstance();
		
		$findParams->joinModel(array(
				'model'=>'GO_Calendar_Model_Event',						  
	 			'localTableAlias'=>'t', //defaults to "t"
	 			'localField'=>'event_id', //defaults to "id"	  
	 			'foreignField'=>'id', //defaults to primary key of the remote model
	 			'tableAlias'=>'e', //Optional table alias	  
	 			));
		
		$findParams->getCriteria()
						->addCondition('id', $this->id, '!=')
						->addCondition('email', $this->email)
						->addCondition('uuid', $this->event->uuid,'=','e')  //recurring series and participants all share the same uuid
						->addCondition('start_time', $this->event->start_time,'=','e') //make sure start time matches for recurring series
						->addCondition("exception_for_event_id", 0, $this->event->exception_for_event_id==0 ? '=' : '!=','e');//the master event or a single occurrence can start at the same time. Therefore we must check if exception event has a value or is 0.
		
		return GO_Calendar_Model_Participant::model()->find($findParams);			
		
	}

}