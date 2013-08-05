<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * 
 * @property int $reminder The number of seconds prior to the start of the event.
 * @property int $exception_event_id If this event is an exception it holds the id of the original event
 * @property int $recurrence_id If this event is an exception it holds the date (not the time) of the original recurring instance. It can be used to identity it with an vcalendar file.
 * @property boolean $is_organizer True if the owner of this event is also the organizer.
 * @property string $owner_status The status of the owner of this event if this was an invitation
 * @property int $exception_for_event_id
 * @property int $sequence
 * @property int $category_id
 * @property boolean $read_only
 * @property int $files_folder_id
 * @property string $background eg. "EBF1E2"
 * @property string $rrule
 * @property boolean $private
 * @property int $resource_event_id Set this for a resource event. This is the personal event this resource belongs to.
 * @property boolean $busy
 * @property int $mtime
 * @property int $ctime
 * @property int $repeat_end_time
 * @property string $location
 * @property string $description
 * @property string $name
 * @property string $status
 * @property boolean $all_day_event
 * @property int $end_time
 * @property int $start_time
 * @property int $user_id
 * @property int $calendar_id
 * @property string $uuid
 * 
 * @property GO_Calendar_Model_Participant $participants
 * @property int $muser_id
 * 
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Wesley Smits <wsmits@intermesh.nl>
 */
class GO_Calendar_Model_Event extends GO_Base_Db_ActiveRecord {

	const STATUS_TENTATIVE = 'TENTATIVE';
//	const STATUS_DECLINED = 'DECLINED';
//	const STATUS_ACCEPTED = 'ACCEPTED';
	const STATUS_CANCELLED = 'CANCELLED';
	const STATUS_CONFIRMED = 'CONFIRMED';
	const STATUS_NEEDS_ACTION = 'NEEDS-ACTION';
	const STATUS_DELEGATED = 'DELEGATED';

	/**
	 * The date where the exception needs to be created. If this is set on a new event
	 * an exception will automatically be created for the recurring series. exception_for_event_id needs to be set too.
	 * 
	 * @var timestamp 
	 */
	public $exception_date;

	public $dontSendEmails=false;
	
	public $sequence;
	
	/**
	 * Flag used when importing. On import we allow participant events to be 
	 * modified even when they are not the organizer. Because a meeting request
	 * coming from the organizer must be procesed by the participant.
	 * 
	 * @var boolean 
	 */
	private $_isImport=false;
	
	protected function init() {

		$this->columns['calendar_id']['required']=true;
		$this->columns['start_time']['gotype'] = 'unixtimestamp';
		$this->columns['end_time']['greater'] = 'start_time';
		$this->columns['end_time']['gotype'] = 'unixtimestamp';
		$this->columns['repeat_end_time']['gotype'] = 'unixtimestamp';		
		$this->columns['repeat_end_time']['greater'] = 'start_time';
		//$this->columns['category_id']['required'] = GO_Calendar_CalendarModule::commentsRequired();
		
		parent::init();
	}
	
	public function isValidStatus($status){
		return ($status==self::STATUS_CANCELLED || $status==self::STATUS_CONFIRMED || $status==self::STATUS_DELEGATED || $status==self::STATUS_TENTATIVE || $status==self::STATUS_NEEDS_ACTION);			
	}

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Calendar_Model_Event 
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function aclField() {
		return 'calendar.acl_id';
	}

	public function tableName() {
		return 'cal_events';
	}

	public function hasFiles() {
		return true;
	}
	
	public function hasLinks() {
		return true;
	}
	
	public function defaultAttributes() {
		
		
		$defaults = array(
				'status' => self::STATUS_CONFIRMED,
				'start_time'=> GO_Base_Util_Date::roundQuarters(time()), 
				'end_time'=>GO_Base_Util_Date::roundQuarters(time()+3600)				
		);
		
		
		$settings = GO_Calendar_Model_Settings::model()->getDefault(GO::user());
		if($settings){		
			$defaults = array_merge($defaults, array(
				'reminder' => $settings->reminder,
				'calendar_id'=>$settings->calendar_id,
				'background'=>$settings->background
						));
		}
		
		return $defaults;
	}
	
	public function customfieldsModel() {
		return "GO_Calendar_Customfields_Model_Event";
	}


	public function relations() {
		return array(
				'_exceptionEvent'=>array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Event', 'field' => 'exception_for_event_id'),
				'recurringEventException'=>array('type' => self::HAS_ONE, 'model' => 'GO_Calendar_Model_Exception', 'field' => 'exception_event_id'),//If this event is an exception for a recurring series. This relation points to the exception of the recurring series.
				'calendar' => array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Calendar', 'field' => 'calendar_id'),
				'category' => array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Category', 'field' => 'category_id'),
				'participants' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Participant', 'field' => 'event_id', 'delete' => true),
				'exceptions' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Exception', 'field' => 'event_id', 'delete' => true),
				'exceptionEvents' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Event', 'field' => 'exception_for_event_id', 'delete' => true),
				'resources' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Event', 'field' => 'resource_event_id', 'delete' => true)
		);
	}

	protected function getCacheAttributes() {
		
		return array(
				'name' => $this->private ?  GO::t('privateEvent','calendar') : $this->name,' '.GO_Base_Util_Date::get_timestamp($this->start_time, false).')',
				'description' => $this->private ?  "" : $this->description,
				'mtime'=>$this->start_time
		);
	}

	protected function getLocalizedName() {
		return GO::t('event', 'calendar');
	}

	/**
	 * The files module will use this function.
	 */
	public function buildFilesPath() {

		return 'calendar/' . GO_Base_Fs_Base::stripInvalidChars($this->calendar->name) . '/' . date('Y', $this->start_time) . '/' . GO_Base_Fs_Base::stripInvalidChars($this->name).' ('.$this->id.')';
	}

	/**
	 * Get the color for the current status of this event
	 * 
	 * @return string 
	 */
	public function getStatusColor(){
		
		switch($this->status){
			case GO_Calendar_Model_Event::STATUS_TENTATIVE:
				$color = 'FFFF00'; //Yellow
			break;
			case GO_Calendar_Model_Event::STATUS_CANCELLED:
				$color = 'FF0000'; //Red			
			break;
//			case GO_Calendar_Model_Event::STATUS_ACCEPTED:
//				$color = '00FF00'; //Lime
//			break;
			case GO_Calendar_Model_Event::STATUS_CONFIRMED:
				$color = '32CD32'; //LimeGreen
			break;
			case GO_Calendar_Model_Event::STATUS_DELEGATED:
				$color = '0000CD'; //MediumBlue
			break;
		
			default:
			case GO_Calendar_Model_Event::STATUS_NEEDS_ACTION:
				$color = 'FF8C00'; //DarkOrange
			break;
		}
		
		return $color;
	}
	
	/**
	 * Get the date interval for the event.
	 * 
	 * @return array 
	 */
	public function getDiff() {
		$startDateTime = new GO_Base_Util_Date_DateTime(date('c', $this->start_time));
		$endDateTime = new GO_Base_Util_Date_DateTime(date('c', $this->end_time));

		return $startDateTime->diff($endDateTime);
	}

	/**
	 * Add an Exception for the Event if it is recurring
	 * 
	 * @param Unix Timestamp $date The date where the exception belongs to
	 * @param Int $for_event_id The event id of the event where the exception belongs to
	 */
	public function addException($date, $exception_event_id=0) {
		
		if(!$this->isRecurring())
			throw new Exception("Can't add exception to non recurring event ".$this->id);
		
		if(!$this->hasException($date)){
			$exception = new GO_Calendar_Model_Exception();
			$exception->event_id = $this->id;
			$exception->time = mktime(date('G',$this->start_time),date('i',$this->start_time),0,date('n',$date),date('j',$date),date('Y',$date)); // Needs to be a unix timestamp
			$exception->exception_event_id=$exception_event_id;
			$exception->save();
		}
	}

	/**
	 * This Event needs to be reinitialized to become an Exception of its own on the given Unix timestamp.
	 * It will not save the event and doesn't copy participants. Use createExcetionEvent for that.
	 * 
	 * @param int $exceptionDate Unix timestamp
	 */
	public function getExceptionEvent($exceptionDate) {
		

		$att['rrule'] = '';
		$att['repeat_end_time']=0;
		$att['exception_for_event_id'] = $this->id;
		$att['exception_date'] = $exceptionDate;
		
		$diff = $this->getDiff();

		$d = date('Y-m-d', $exceptionDate);
		$t = date('G:i', $this->start_time);

		$att['start_time'] = strtotime($d . ' ' . $t);

		$endTime = new GO_Base_Util_Date_DateTime(date('c', $att['start_time']));
		$endTime->add($diff);
		$att['end_time'] = $endTime->format('U');
		
		
		
		return $this->duplicate($att, false);
	}
	

	public function createException($exceptionDate){
		$stmt = $this->getRelatedParticipantEvents(true);//A meeting can be multiple related events sharing the same uuid
		foreach($stmt as $event){			
			$event->addException($exceptionDate, 0);
		}
	}
	
	/**
	 * Create an exception for a recurring series.
	 * 
	 * @param int $exceptionDate
	 * @return GO_Calendar_Model_Event
	 */
	public function createExceptionEvent($exceptionDate, $attributes=array(), $dontSendEmails=false){
		
		
		if(!$this->isRecurring()){
			throw new Exception("Can't create exception event for non recurring event ".$this->id);
		}
		
		$oldIgnore = GO::setIgnoreAclPermissions();
		$returnEvent = false;
		if($this->isResource())
			$stmt = array($this); //resource is never a group of events
		else
			$stmt = $this->getRelatedParticipantEvents(true);//A meeting can be multiple related events sharing the same uuid
		
		$resources = array();
		
		foreach($stmt as $event){
			
			//workaround for old events that don't have the exception ID set. In this case
			//getRelatedParticipantEvents fails. This won't happen with new events
			if(!$event->isRecurring())
				continue;
			
			GO::debug("Creating exception for related participant event ".$event->name." (".$event->id.")");
			
			$exceptionEvent = $event->getExceptionEvent($exceptionDate);
			$exceptionEvent->dontSendEmails = $dontSendEmails;
			$exceptionEvent->setAttributes($attributes);
			if(!$exceptionEvent->save())
				throw new Exception("Could not create exception: ".var_export($exceptionEvent->getValidationErrors(), true));
			$event->addException($exceptionDate, $exceptionEvent->id);

			$event->duplicateRelation('participants', $exceptionEvent);
			
			
			if(!$event->isResource() && $event->is_organizer){
				$stmt = $event->resources();		
				foreach($stmt as $resource){
					$resources[]=$resource;
				}
				$resourceExceptionEvent = $exceptionEvent;
			}
			
			if($event->id==$this->id)
				$returnEvent=$exceptionEvent;
		}
		
		foreach($resources as $resource){
			GO::debug("Creating exception for resource: ".$resource->name);
			$resource->createExceptionEvent($exceptionDate, array('resource_event_id'=>$resourceExceptionEvent->id), $dontSendEmails);
		}		

		GO::setIgnoreAclPermissions($oldIgnore);
		return $returnEvent;
	}

	public function attributeLabels() {
		$attr = parent::attributeLabels();
		$attr['repeat_end_time']=GO::t('repeatUntil','calendar');
		$attr['start_time']=GO::t('startsAt','calendar');
		$attr['end_time']=GO::t('endsAt','calendar');
		return $attr;
	}
	
	public function validate() {
		if($this->rrule != ""){			
			$rrule = new GO_Base_Util_Icalendar_Rrule();
			$rrule->readIcalendarRruleString($this->start_time, $this->rrule);						
			$this->repeat_end_time = $rrule->until;
		}		
		return parent::validate();
	}

	protected function beforeSave() {
		
		if($this->rrule != ""){			
			$rrule = new GO_Base_Util_Icalendar_Rrule();
			$rrule->readIcalendarRruleString($this->start_time, $this->rrule);						
			$this->repeat_end_time = intval($rrule->until);
		}
		
		//if this is not the organizer event it may only be modified by the organizer
		if(!$this->_isImport && !$this->isNew && $this->isModified(array("name","start_time","end_time","location","description","calendar_id","rrule","repeat_end_time"))){		
			$organizerEvent = $this->getOrganizerEvent();
			if($organizerEvent && !$organizerEvent->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION) || !$organizerEvent && !$this->is_organizer){
				GO::debug($this->getModifiedAttributes());
				GO::debug($this->_attributes);
				throw new GO_Base_Exception_AccessDenied();
			}			
		}
		
		//Don't set reminders for the superadmin
		if($this->calendar->user_id==1 && GO::user()->id!=1 && !GO::config()->debug)
			$this->reminder=0;
		
		
		if($this->isResource()){
			if($this->status=='CONFIRMED'){
				$this->background='CCFFCC';
			}else
			{
				$this->background='FF6666';
			}			
		}	
		
		return parent::beforeSave();
	}

	protected function afterDbInsert() {
		if(empty($this->uuid)){
			$this->uuid = GO_Base_Util_UUID::create('event', $this->id);
			return true;
		}else
		{
			return false;
		}
	}
	
	protected function afterDelete() {

		$this->deleteReminders();
		
		if($this->is_organizer){
			$stmt = $this->getRelatedParticipantEvents();
			
			foreach($stmt as $event){
				$event->delete(true);
//				$event->status=self::STATUS_CANCELLED;
//				$event->save(true);
			}
		}else
		{
			$participants = $this->getParticipantsForUser();
			
			foreach($participants as $participant){
				$participant->status=GO_Calendar_Model_Participant::STATUS_DECLINED;
				$participant->save();
			}
		}
		
		return parent::afterDelete();
	}
	
	public static function reminderDismissed($reminder, $userId){		
		
		//this listener function is added in GO_Calendar_CalendarModule
		
		if($reminder->model_type_id==GO_Calendar_Model_Event::model()->modelTypeId()){			
			$event = GO_Calendar_Model_Event::model()->findByPk($reminder->model_id);
			if($event && $event->isRecurring() && $event->reminder>0){
				$rRule = new GO_Base_Util_Icalendar_Rrule();
				$rRule->readIcalendarRruleString($event->start_time, $event->rrule);				
				$rRule->setRecurpositionStartTime(time()+$event->reminder);
				$nextTime = $rRule->getNextRecurrence();
				
				if($nextTime){
					$event->addReminder($event->name, $nextTime-$event->reminder, $userId, $nextTime);
				}				
			}			
		}
	}
	
	/**
	 * Check if this event is recurring
	 * 
	 * @return boolean 
	 */
	public function isRecurring(){
		return $this->rrule!="";
	}
	

	protected function afterSave($wasNew) {
		
		//add exception model for the original recurring event
//		if ($wasNew && $this->exception_for_event_id > 0 && !empty($this->exception_date)) {
//			
//			$newExeptionEvent = GO_Calendar_Model_Event::model()->findByPk($this->exception_for_event_id);
//			$newExeptionEvent->addException($this->exception_date, $this->id);
//		}
		
//			
//			//copy particpants to new exception
//			$stmt = $newExeptionEvent->participants();
//			while($participant = $stmt->fetch()){
//				$newParticipant = new GO_Calendar_Model_Participant();
//				$newParticipant->setAttributes($participant->getAttributes());
//				unset($newParticipant->id);
//				$newParticipant->event_id=$this->id;
//				if(!$newParticipant->is_organizer){
//					$newParticipant->status=GO_Calendar_Model_Participant::STATUS_PENDING;
//				}
//				$newParticipant->save();
//			}
//		}
		
		//if this event belongs to a recurring series we must touch the master event 
		//too or it won't be synchronized with z-push or caldav
		if($exceptionEvent = $this->_exceptionEvent){
			$exceptionEvent->touch();
		}
		
		//move exceptions if this event was moved in time
		if(!$wasNew && !empty($this->rrule) && $this->isModified('start_time')){
			$diffSeconds = $this->getOldAttributeValue('start_time')-$this->start_time;
			$stmt = $this->exceptions();
			while($exception = $stmt->fetch()){
				$exception->time+=$diffSeconds;
				$exception->save();
			}
		}
	
		if($this->isResource()){
			$this->_sendResourceNotification($wasNew);
		}else
		{
			if(!$wasNew && $this->hasModificationsForParticipants())
				$this->_updateResourceEvents();
		}

		if($this->reminder>0){
			$remindTime = $this->start_time-$this->reminder;
			if($remindTime>time()){
				$this->deleteReminders();
				$this->addReminder($this->name, $remindTime, $this->calendar->user_id, $this->start_time);
			}
		}	
		
		//update events that belong to this organizer event
		if($this->is_organizer && !$wasNew && !$this->isResource()){
			$updateAttr = array(
					'name'=>$this->name,
					'start_time'=>$this->start_time, 
					'end_time'=>$this->end_time, 
					'location'=>$this->location,
					'description'=>$this->description,
					'rrule'=>$this->rrule,
					'status'=>$this->status,
					'repeat_end_time'=>$this->repeat_end_time
							);
			
			if($this->isModified(array_keys($updateAttr))){

				$events = $this->getRelatedParticipantEvents();

				foreach($events as $event){
					GO::debug("updating related event: ".$event->id);

					if($event->id!=$this->id && $this->is_organizer!=$event->is_organizer){ //this should never happen but to prevent an endless loop it's here.
						$event->setAttributes($updateAttr, false);
						$event->save(true);

//						$stmt = $event->participants;
//						$stmt->callOnEach('delete');
//	
//						$this->duplicateRelation('participants', $event);
					}
				}
			}
		}

		return parent::afterSave($wasNew);
	}
	
	/**
	 * Get's all related events that are in the participant's calendars.
	 * 
	 * @return GO_Calendar_Model_Event
	 */
	public function getRelatedParticipantEvents($includeThisEvent=false){
		$findParams = GO_Base_Db_FindParams::newInstance()->ignoreAcl();
		
		$start_time = $this->isModified('start_time') ? $this->getOldAttributeValue('start_time') : $this->start_time;
		
		$findParams->getCriteria()
						->addCondition("uuid", $this->uuid) //recurring series and participants all share the same uuid
						->addCondition('start_time', $start_time) //make sure start time matches for recurring series
						->addCondition("exception_for_event_id", 0, $this->exception_for_event_id==0 ? '=' : '!='); //the master event or a single occurrence can start at the same time. Therefore we must check if exception event has a value or is 0.
		
		if(!$includeThisEvent)
			$findParams->getCriteria()->addCondition('id', $this->id, '!=');
		
						
		$stmt = GO_Calendar_Model_Event::model()->find($findParams);
		
		return $stmt;
	}
	
	
	
	/**
	 * If this is a resource of the current user ignore ACL permissions when deleting 
	 */
	public function delete($ignoreAcl=false)
	{
		if(!empty($this->resource_event_id) && $this->user_id == GO::user()->id)
			parent::delete(true);
		else
			parent::delete($ignoreAcl);
	}
	
	public function hasModificationsForParticipants(){
		return $this->isModified("start_time") || $this->isModified("end_time") || $this->isModified("name") || $this->isModified("location") || $this->isModified('status');
	}
	
	
	/**
	 * Events may have related resource events that must be updated aftersave
	 */
	private function _updateResourceEvents(){
		$stmt = $this->resources();
		
		while($resourceEvent = $stmt->fetch()){
			
			$resourceEvent->name=$this->name;
			$resourceEvent->start_time=$this->start_time;
			$resourceEvent->end_time=$this->end_time;
			$resourceEvent->rrule=$this->rrule;
			$resourceEvent->repeat_end_time=$this->repeat_end_time;				
			$resourceEvent->status="NEEDS-ACTION";
			$resourceEvent->user_id=$this->user_id;
			$resourceEvent->save(true);
		}
	}
		
	private function _sendResourceNotification($wasNew){
		
		if(!$this->dontSendEmails && $this->hasModificationsForParticipants()){			
			$url = GO::createExternalUrl('calendar', 'showEventDialog', array('event_id' => $this->id));		

			$stmt = $this->calendar->group->admins;
			while($user = $stmt->fetch()){
				if($user->id!=GO::user()->id){
					if($wasNew){
						$body = sprintf(GO::t('resource_mail_body','calendar'),$this->user->name,$this->calendar->name).'<br /><br />'
										. $this->toHtml()
										. '<br /><a href="'.$url.'">'.GO::t('open_resource','calendar').'</a>';

						$subject = sprintf(GO::t('resource_mail_subject','calendar'),$this->calendar->name, $this->name, GO_Base_Util_Date::get_timestamp($this->start_time,false));
					}else
					{
						$body = sprintf(GO::t('resource_modified_mail_body','calendar'),$this->user->name,$this->calendar->name).'<br /><br />'
										. $this->toHtml()
										. '<br /><a href="'.$url.'">'.GO::t('open_resource','calendar').'</a>';

						$subject = sprintf(GO::t('resource_modified_mail_subject','calendar'),$this->calendar->name, $this->name, GO_Base_Util_Date::get_timestamp($this->start_time,false));
					}

					$message = GO_Base_Mail_Message::newInstance(
										$subject
										)->setFrom(GO::user()->email, GO::user()->name)
										->addTo($user->email, $user->name);

					$message->setHtmlAlternateBody($body);					

					GO_Base_Mail_Mailer::newGoInstance()->send($message);
				}
			}
			
			
			//send update to user
			if($this->user_id!=GO::user()->id){
				if($this->isModified('status')){				
					if($this->status==GO_Calendar_Model_Event::STATUS_CONFIRMED){
						$body = sprintf(GO::t('your_resource_accepted_mail_body','calendar'),GO::user()->name,$this->calendar->name).'<br /><br />'
								. $this->toHtml();
								//. '<br /><a href="'.$url.'">'.GO::t('open_resource','calendar').'</a>';

						$subject = sprintf(GO::t('your_resource_accepted_mail_subject','calendar'),$this->calendar->name, $this->name, GO_Base_Util_Date::get_timestamp($this->start_time,false));
					}else
					{
						$body = sprintf(GO::t('your_resource_declined_mail_body','calendar'),GO::user()->name,$this->calendar->name).'<br /><br />'
								. $this->toHtml();
								//. '<br /><a href="'.$url.'">'.GO::t('open_resource','calendar').'</a>';

						$subject = sprintf(GO::t('your_resource_declined_mail_subject','calendar'),$this->calendar->name, $this->name, GO_Base_Util_Date::get_timestamp($this->start_time,false));
					}
				}else
				{
					$body = sprintf(GO::t('your_resource_modified_mail_body','calendar'),$user->name,$this->calendar->name).'<br /><br />'
								. $this->toHtml();
//								. '<br /><a href="'.$url.'">'.GO::t('open_resource','calendar').'</a>';
					$subject = sprintf(GO::t('your_resource_modified_mail_subject','calendar'),$this->calendar->name, $this->name, GO_Base_Util_Date::get_timestamp($this->start_time,false));
				}
				
				$url = GO::createExternalUrl('calendar', 'openCalendar', array(
					'unixtime'=>$this->start_time
				));
		
				$body .= '<br /><a href="'.$url.'">'.GO::t('openCalendar','calendar').'</a>';

				$message = GO_Base_Mail_Message::newInstance(
									$subject
									)->setFrom(GO::user()->email, GO::user()->name)
									->addTo($this->user->email, $this->user->name);

				$message->setHtmlAlternateBody($body);					

				GO_Base_Mail_Mailer::newGoInstance()->send($message);
			}

		}
	}

	/**
	 *
	 * @var GO_Calendar_Model_LocalEvent
	 */
	private $_calculatedEvents;
	
	
	/**
	 * Finds a specific occurence for a date.
	 * 
	 * @param int $exceptionDate
	 * @return GO_Calendar_Model_Event
	 * @throws Exception
	 */
	public function findException($exceptionDate) {

		if ($this->exception_for_event_id != 0)
			throw new Exception("This is not a master event");

		$startOfDay = GO_Base_Util_Date::clear_time($exceptionDate);
		$endOfDay = GO_Base_Util_Date::date_add($startOfDay, 1);

		$findParams = GO_Base_Db_FindParams::newInstance();



		//must be an exception and start on the must start on the exceptionTime
		$exceptionJoinCriteria = GO_Base_Db_FindCriteria::newInstance()
						->addCondition('id', 'e.exception_event_id', '=', 't', true, true);

		$findParams->join(GO_Calendar_Model_Exception::model()->tableName(), $exceptionJoinCriteria, 'e');

//			$dayStart = GO_Base_Util_Date::clear_time($exceptionDate);
//			$dayEnd = GO_Base_Util_Date::date_add($dayStart,1);	
		$whereCriteria = GO_Base_Db_FindCriteria::newInstance()
						->addCondition('exception_for_event_id', $this->id)
						->addCondition('time', $startOfDay, '>=', 'e')
						->addCondition('time', $endOfDay, '<', 'e');
		$findParams->criteria($whereCriteria);
//		$findParams->getCriteria()
//						->addCondition('exception_for_event_id', $this->id)
//						->addCondition('start_time', $startOfDay,'>=')
//						->addCondition('end_time', $endOfDay,'<=');

		$findParams->debugSql();
		
		$event = GO_Calendar_Model_Event::model()->findSingle($findParams);

		return $event;
	}
	
	/**
	 * 
	 * @param int $exception_for_event_id
	 * @return GO_Calendar_Model_LocalEvent
	 */
	public function getConflictingEvents($exception_for_event_id=0){
		
		$conflictEvents=array();
		
		$findParams = GO_Base_Db_FindParams::newInstance();
		$findParams->getCriteria()->addCondition("calendar_id", $this->calendar_id);
		if(!$this->isNew)
			$findParams->getCriteria()->addCondition("resource_event_id", $this->id, '<>');
		
		//find all events including repeating events that occur on that day.
		$conflictingEvents = GO_Calendar_Model_Event::model()->findCalculatedForPeriod($findParams, 
						$this->start_time, 
						$this->end_time,
						true);
		
		while($conflictEvent = array_shift($conflictingEvents)) {			
			//GO::debug("Conflict: ".$conflictEvent->getEvent()->id." ".$conflictEvent->getName()." ".GO_Base_Util_Date::get_timestamp($conflictEvent->getAlternateStartTime())." - ".GO_Base_Util_Date::get_timestamp($conflictEvent->getAlternateEndTime()));
			if($conflictEvent->getEvent()->id!=$this->id && (empty($exception_for_event_id) || $exception_for_event_id!=$conflictEvent->getEvent()->id)){
				$conflictEvents[]=$conflictEvent;
			}
		}
		
		return $conflictEvents;
	}

	/**
	 * Find events that occur in a given time period. They will be sorted on 
	 * start_time and name. Recurring events are calculated and added to the array.
	 * 
	 * @param GO_Base_Db_FindParams $findParams
	 * @param int $periodStartTime
	 * @param int $periodEndTime
	 * @param boolean $onlyBusyEvents
	 * 
	 * @return GO_Calendar_Model_LocalEvent  
	 */
	public function findCalculatedForPeriod($findParams, $periodStartTime, $periodEndTime, $onlyBusyEvents=false) {

		GO::debug("findCalculatedForPeriod ".date('c', $periodStartTime)." - ".date('c', $periodEndTime));
		
		$stmt = $this->findForPeriod($findParams, $periodStartTime, $periodEndTime, $onlyBusyEvents);

		$this->_calculatedEvents = array();

		while ($event = $stmt->fetch()) {
			$this->_calculateRecurrences($event, $periodStartTime, $periodEndTime);
		}
		
		ksort($this->_calculatedEvents);

		return array_values($this->_calculatedEvents);
	}
	
	/**
	 * Find events that occur in a given time period. 
	 * 
	 * Recurring events are not calculated. If you need recurring events use
	 * findCalculatedForPeriod.
	 * 
	 * @param GO_Base_Db_FindParams $findParams
	 * @param int $periodStartTime
	 * @param int $periodEndTime
	 * @param boolean $onlyBusyEvents
	 * @return GO_Base_Db_ActiveStatement
	 */
	public function findForPeriod($findParams, $periodStartTime, $periodEndTime=0, $onlyBusyEvents=false){
		if (!$findParams)
			$findParams = GO_Base_Db_FindParams::newInstance();

		$findParams->order('start_time', 'ASC')->select("t.*");
		
//		if($periodEndTime)
//			$findParams->getCriteria()->addCondition('start_time', $periodEndTime, '<');
		
		$findParams->getCriteria()->addModel(GO_Calendar_Model_Event::model(), "t");
		
		if ($onlyBusyEvents)
			$findParams->getCriteria()->addCondition('busy', 1);
		
		$normalEventsCriteria = GO_Base_Db_FindCriteria::newInstance()
					->addModel(GO_Calendar_Model_Event::model())					
					->addCondition('end_time', $periodStartTime, '>');
		
		if($periodEndTime)
			$normalEventsCriteria->addCondition('start_time', $periodEndTime, '<');
		
		$recurringEventsCriteria = GO_Base_Db_FindCriteria::newInstance()
					->addModel(GO_Calendar_Model_Event::model())
					->addCondition('rrule', "", '!=')
					->mergeWith(
									GO_Base_Db_FindCriteria::newInstance()
										->addModel(GO_Calendar_Model_Event::model())					
//										->addCondition('repeat_end_time', $periodStartTime, '>=')
										->addRawCondition('`t`.`repeat_end_time`', '('.intval($periodStartTime).'-(`t`.`end_time`-`t`.`start_time`))', '>=', true)
										->addCondition('repeat_end_time', 0,'=','t',false))
					->addCondition('start_time', $periodStartTime, '<');
		
		$normalEventsCriteria->mergeWith($recurringEventsCriteria, false);
		
		$findParams->getCriteria()->mergeWith($normalEventsCriteria);

		

		return $this->find($findParams);
	}

	private function _calculateRecurrences($event, $periodStartTime, $periodEndTime) {
		
		$origPeriodStartTime=$periodStartTime;
		$origPeriodEndTime=$periodEndTime;
		
		//recurrences can only be calculated correctly if we use the start of the day and the end of the day.
		//we'll use the original times later to check if they really overlap.
		$periodStartTime= GO_Base_Util_Date::clear_time($periodStartTime)-1;
		$periodEndTime= GO_Base_Util_Date::clear_time(GO_Base_Util_Date::date_add($periodEndTime,1));

		$localEvent = new GO_Calendar_Model_LocalEvent($event, $origPeriodStartTime, $origPeriodEndTime);
		
		if(!$localEvent->isRepeating()){
			$this->_calculatedEvents[$event->start_time.'-'.$event->name.'-'.$event->id] = $localEvent;
		} else {
			
			GO::debug("Calculating recurrences for event: ".$event->id);
			$rrule = new GO_Base_Util_Icalendar_Rrule();
			$rrule->readIcalendarRruleString($localEvent->getEvent()->start_time, $localEvent->getEvent()->rrule, true);
			
			//we need to start searching for the next occurrence at the period start
			//time minus the duration of the event in days rounded up. Because an 
			//occurrence may start before the period but end in it.
			$rrule->setRecurpositionStartTime(GO_Base_Util_Date::date_add($periodStartTime,-ceil(( ($event->end_time-$event->start_time) /86400))));

//			var_dump('=====');
//			var_dump(GO_Base_Util_Date::get_timestamp(GO_Base_Util_Date::date_add($periodStartTime,-ceil(( ($event->end_time-$event->start_time) /86400)))));
			
			$origEventAttr = $localEvent->getEvent()->getAttributes('formatted');
			while ($occurenceStartTime = $rrule->getNextRecurrence(false,$periodEndTime)) {	
				
//				var_dump(GO_Base_Util_Date::get_timestamp($occurenceStartTime));
				
				if ($occurenceStartTime > $localEvent->getPeriodEndTime())
					break;

				$localEvent->setAlternateStartTime($occurenceStartTime);

				$diff = $event->getDiff();

				$endTime = new GO_Base_Util_Date_DateTime(date('c', $occurenceStartTime));
				$endTime->add($diff);
				
				$localEvent->setAlternateEndTime($endTime->format('U'));

				if($localEvent->getAlternateStartTime()<$origPeriodEndTime && $localEvent->getAlternateEndTime()>$origPeriodStartTime){
					if(!$event->hasException($occurenceStartTime))
						$this->_calculatedEvents[$occurenceStartTime.'-'.$origEventAttr['name'].'-'.$origEventAttr['id']] = $localEvent;
				}
				
				$localEvent = new GO_Calendar_Model_LocalEvent($event, $periodStartTime, $periodEndTime);
			}
		}
		
		
	
		
		
//		if (empty($event->rrule)) {
//			//not a recurring event
//			$this->_calculatedEvents[] = $event->getAttributes('formatted');
//		} else {
//			$rrule = new GO_Base_Util_Icalendar_Rrule();
//			$rrule->readIcalendarRruleString($event->start_time, $event->rrule);
//
//			$rrule->setRecurpositionStartTime($periodStartTime);
//
//			$origEventAttr = $event->getAttributes('formatted');
//
//			while ($occurenceStartTime = $rrule->getNextRecurrence()) {
//
//				if ($occurenceStartTime > $periodEndTime)
//					break;
//
//				$origEventAttr['start_time'] = GO_Base_Util_Date::get_timestamp($occurenceStartTime);
//
//				$diff = $this->getDiff();
//
//				$endTime = new GO_Base_Util_Date_DateTime(date('c', $occurenceStartTime));
//				$endTime->addDiffCompat($diff);
//				$origEventAttr['end_time'] = GO_Base_Util_Date::get_timestamp($endTime->format('U'));
//
//				$this->_calculatedEvents[$occurenceStartTime . '-' . $origEventAttr['id']] = $origEventAttr;
//			}
//
//			ksort($this->_calculatedEvents);
//		}
	}
	
	/**
	 * Check if this event has an exception for a given day.
	 * 
	 * @param int $time
	 * @return GO_Calendar_Model_Exception
	 */
	public function hasException($time){
		$startDay = GO_Base_Util_Date::clear_time($time);
		$endDay = GO_Base_Util_Date::date_add($startDay, 1);

		$findParams = GO_Base_Db_FindParams::newInstance();
		$findParams->getCriteria()
						->addCondition('event_id', $this->id)
						->addCondition('time', $startDay,'>=')
						->addCondition('time', $endDay, '<');

		return GO_Calendar_Model_Exception::model()->findSingle($findParams);

	}
	
	/**
	 * Create a localEvent model from this event model
	 * 
	 * @param GO_Calendar_Model_Event $event
	 * @param string $periodStartTime
	 * @param string $periodEndTime
	 * @return GO_Calendar_Model_LocalEvent 
	 */
	public function getLocalEvent($event, $periodStartTime, $periodEndTime){
		$localEvent = new GO_Calendar_Model_LocalEvent($event, $periodStartTime, $periodEndTime);
		
		return $localEvent;
	}
	
	/**
	 * Find an event based on uuid field for a user. Either user_id or calendar_id
	 * must be supplied.
	 * 
	 * Optionally exceptionDate can be specified to find a specific exception.
	 * 
	 * @param string $uuid
	 * @param int $user_id
	 * @param int $calendar_id
	 * @param int $exceptionDate
	 * @return GO_Calendar_Model_Event 
	 */
	public function findByUuid($uuid, $user_id, $calendar_id=0, $exceptionDate=false){
		
		$whereCriteria = GO_Base_Db_FindCriteria::newInstance()												
										->addCondition('uuid', $uuid);

		//todo exception date

		$params = GO_Base_Db_FindParams::newInstance()
						->ignoreAcl()
						->single();							
		
		if(!$calendar_id){
			$joinCriteria = GO_Base_Db_FindCriteria::newInstance()
							->addCondition('calendar_id', 'c.id','=','t',true, true)
							->addCondition('user_id', $user_id,'=','c');
			
			$params->join(GO_Calendar_Model_Calendar::model()->tableName(), $joinCriteria, 'c');
		}else
		{
			$whereCriteria->addCondition('calendar_id', $calendar_id);
		}
		
		if($exceptionDate){
			//must be an exception and start on the must start on the exceptionTime
			$exceptionJoinCriteria = GO_Base_Db_FindCriteria::newInstance()
							->addCondition('id', 'e.exception_event_id','=','t',true,true);
			
			$params->join(GO_Calendar_Model_Exception::model()->tableName(),$exceptionJoinCriteria,'e');
			
			$dayStart = GO_Base_Util_Date::clear_time($exceptionDate);
			$dayEnd = GO_Base_Util_Date::date_add($dayStart,1);	
			$whereCriteria 
							->addCondition('time', $dayStart, '>=','e')
							->addCondition('time', $dayEnd, '<','e',false);
			
//			//the code below only find exceptions on the same day which is wrong
//			$whereCriteria->addCondition('exception_for_event_id', 0,'>');
//			
//			$dayStart = GO_Base_Util_Date::clear_time($exceptionDate);
//			$dayEnd = GO_Base_Util_Date::date_add($dayStart,1);
//			
//			$dateCriteria = GO_Base_Db_FindCriteria::newInstance()
//							->addCondition('start_time', $dayStart, '>=')
//							->addCondition('start_time', $dayEnd, '<','t',false);
//			
//			$whereCriteria->mergeWith($dateCriteria);
			
		}else
		{
			$whereCriteria->addCondition('exception_for_event_id', 0);
		}

		$params->criteria($whereCriteria);

		return $this->find($params);			
	}

//	/**
//	 * Find an event that belongs to a group of participant events. They all share the same uuid field.
//	 * 
//	 * @param int $calendar_id
//	 * @param string $uuid
//	 * @return GO_Calendar_Model_Event 
//	 */
//	public function findParticipantEvent($calendar_id, $uuid) {
//		return $this->findSingleByAttributes(array('uuid' => $event->uuid, 'calendar_id' => $calendar->id));
//	}
	
	/**
	 * Find the resource booking that belongs to this event
	 * 
	 * @param int $event_id
	 * @param int $resource_calendar_id
	 * @return GO_Calendar_Model_Event 
	 */
	public function findResourceForEvent($event_id, $resource_calendar_id){
		return $this->findSingleByAttributes(array('resource_event_id' => $event_id, 'calendar_id' => $resource_calendar_id));
	}
	
	/**
	 * Get the status translated into the current language setting
	 * @return string 
	 */
	public function getLocalizedStatus(){
		$statuses = GO::t('statuses','calendar');
		
		return isset($statuses[$this->status]) ? $statuses[$this->status] : $this->status;
						
	}

	/**
	 * Get the event in HTML markup
	 * 
	 * @todo Add recurrence info
	 * @return string 
	 */
	public function toHtml() {
		$html = '<table id="event-'.$this->uuid.'">' .
						'<tr><td>' . GO::t('subject', 'calendar') . ':</td>' .
						'<td>' . $this->name . '</td></tr>';
		
		if($this->calendar){
			$html .= '<tr><td>' . GO::t('calendar', 'calendar') . ':</td>' .
						'<td>' . $this->calendar->name . '</td></tr>';
		}
		
		$html .= '<tr><td>' . GO::t('startsAt', 'calendar') . ':</td>' .
						'<td>' . GO_Base_Util_Date::get_timestamp($this->start_time, empty($this->all_day_event)) . '</td></tr>' .
						'<tr><td>' . GO::t('endsAt', 'calendar') . ':</td>' .
						'<td>' . GO_Base_Util_Date::get_timestamp($this->end_time, empty($this->all_day_event)) . '</td></tr>';

		$html .= '<tr><td>' . GO::t('status', 'calendar') . ':</td>' .
						'<td>' . $this->getLocalizedStatus() . '</td></tr>';


		if (!empty($this->location)) {
			$html .= '<tr><td style="vertical-align:top">' . GO::t('location', 'calendar') . ':</td>' .
							'<td>' . GO_Base_Util_String::text_to_html($this->location) . '</td></tr>';
		}
		
		if(!empty($this->description)){
			$html .= '<tr><td style="vertical-align:top">' . GO::t('strDescription') . ':</td>' .
							'<td>' . GO_Base_Util_String::text_to_html($this->description) . '</td></tr>';
		}
		
		if($this->isRecurring()){
			$html .= '<tr><td colspan="2">' .$this->getRecurrencePattern()->getAsText().'</td></tr>';;
		}

		//don't calculate timezone offset for all day events
//		$timezone_offset_string = GO_Base_Util_Date::get_timezone_offset($this->start_time);
//
//		if ($timezone_offset_string > 0) {
//			$gmt_string = '(\G\M\T +' . $timezone_offset_string . ')';
//		} elseif ($timezone_offset_string < 0) {
//			$gmt_string = '(\G\M\T -' . $timezone_offset_string . ')';
//		} else {
//			$gmt_string = '(\G\M\T)';
//		}

		//$html .= '<tr><td colspan="2">&nbsp;</td></tr>';

		
		
		$html .= '</table>';
		
		$stmt = $this->participants();
		
		if($stmt->rowCount()){
			
			$html .= '<table>';
			
			$html .= '<tr><td colspan="3"><br /></td></tr>';
			$html .= '<tr><td><b>'.GO::t('participant','calendar').'</b></td><td><b>'.GO::t('status','calendar').'</b></td><td><b>'.GO::t('organizer','calendar').'</b></td></tr>';
			while($participant = $stmt->fetch()){
				$html .= '<tr><td>'.$participant->name.'&nbsp;</td><td>'.$participant->statusName.'&nbsp;</td><td>'.($participant->is_organizer ? GO::t('yes') : '').'</td></tr>';
			}
			$html .='</table>';
		}
		

		return $html;
	}
	
	/**
	 * Get the recurrence pattern object
	 * 
	 * @return GO_Base_Util_Icalendar_Rrule
	 */
	public function getRecurrencePattern(){
		
		if(!$this->isRecurring())
			return false;
		
		$rRule = new GO_Base_Util_Icalendar_Rrule();
		$rRule->readIcalendarRruleString($this->start_time, $this->rrule);
		
		return $rRule;
	}
	
	
	/**
	 * Get this event as a VObject. This can be turned into a vcalendar file data.
	 * 
	 * @param string $method REQUEST, REPLY or CANCEL
	 * @param GO_Calendar_Model_Participant $updateByParticipant The participant that is generating this ICS for a response.
	 * @param int $recurrenceTime Export for a specific recurrence time for the recurrence-id. 
	 * @param boolean $includeExdatesForMovedEvents Funambol need EXDATE lines even for appointments that have been moved. CalDAV doesn't need those lines.
	 * 
	 * If this event is an occurence and has a exception_for_event_id it will automatically determine this value. 
	 * This option is only useful for cancelling a single occurence. Because in that case there is no event model for the occurrence. There's just an exception.
	 * 
	 * @return Sabre\VObject\Component 
	 */
	public function toVObject($method='REQUEST', $updateByParticipant=false, $recurrenceTime=false,$includeExdatesForMovedEvents=false){
		$e=new Sabre\VObject\Component('vevent');
		
		if(empty($this->uuid)){
			$this->uuid = GO_Base_Util_UUID::create('event', $this->id);
			$this->save();
		}
			
		$e->uid=$this->uuid;		
		
		if(isset($this->sequence))
			$e->sequence=$this->sequence;
		
		$dtstamp = new Sabre\VObject\Property\DateTime('dtstamp');
		$dtstamp->setDateTime(new DateTime(), Sabre\VObject\Property\DateTime::UTC);		
		//$dtstamp->offsetUnset('VALUE');
		$e->add($dtstamp);
		
		$mtimeDateTime = new DateTime('@'.$this->mtime);
		$lm = new Sabre\VObject\Property\DateTime('LAST-MODIFIED');
		$lm->setDateTime($mtimeDateTime, Sabre\VObject\Property\DateTime::UTC);		
		//$lm->offsetUnset('VALUE');
		$e->add($lm);
		
		$ctimeDateTime = new DateTime('@'.$this->mtime);
		$ct = new Sabre\VObject\Property\DateTime('created');
		$ct->setDateTime($ctimeDateTime, Sabre\VObject\Property\DateTime::UTC);		
		//$ct->offsetUnset('VALUE');
		$e->add($ct);
		
    $e->summary = (string) $this->name;
		
//		switch($this->owner_status){
//			case GO_Calendar_Model_Participant::STATUS_ACCEPTED:
//				$e->status = "CONFIRMED";
//				break;
//			case GO_Calendar_Model_Participant::STATUS_DECLINED:
//				$e->status = "CANCELLED";
//				break;
//			default:
//				$e->status = "TENTATIVE";
//				break;			
//		}
		
		$e->status = $this->status;
		
		
		$dateType = $this->all_day_event ? Sabre\VObject\Property\DateTime::DATE : Sabre\VObject\Property\DateTime::LOCALTZ;
		
		if($this->all_day_event)
			$e->{"X-FUNAMBOL-ALLDAY"}=1;
		
		if($this->exception_for_event_id>0){
			//this is an exception
			
			$exception = $this->recurringEventException(); //get master event from relation
			if($exception){
				$recurrenceTime=$exception->getStartTime();				
			}
		}
		if($recurrenceTime){
			$recurrenceId =new Sabre\VObject\Property\DateTime("recurrence-id",$dateType);
			$dt = GO_Base_Util_Date_DateTime::fromUnixtime($recurrenceTime);
			$recurrenceId->setDateTime($dt);
			$e->add($recurrenceId);
		}
		
		
		$dtstart = new Sabre\VObject\Property\DateTime('dtstart',$dateType);
		$dtstart->setDateTime(GO_Base_Util_Date_DateTime::fromUnixtime($this->start_time), $dateType);		
		//$dtstart->offsetUnset('VALUE');
		$e->add($dtstart);
		
		if($this->all_day_event){
			$end_time = GO_Base_Util_Date::clear_time($this->end_time);			
			$end_time = GO_Base_Util_Date::date_add($end_time,1);			
		}else{
			$end_time = $this->end_time;
		}
		
		
		
		$dtend = new Sabre\VObject\Property\DateTime('dtend',$dateType);
		$dtend->setDateTime(GO_Base_Util_Date_DateTime::fromUnixtime($end_time), $dateType);		
		//$dtend->offsetUnset('VALUE');
		$e->add($dtend);

		if(!empty($this->description))
			$e->description=$this->description;
		
		if(!empty($this->location))
			$e->location=$this->location;

		if(!empty($this->rrule)){
			
			$rRule = $this->getRecurrencePattern();
			$rRule->shiftDays(false);
			$e->rrule=str_replace('RRULE:','',$rRule->createRrule());			
			
			$findParams = GO_Base_Db_FindParams::newInstance();
			
			if(!$includeExdatesForMovedEvents)
				$findParams->getCriteria()->addCondition('exception_event_id', 0);
			
			$stmt = $this->exceptions($findParams);
			while($exception = $stmt->fetch()){
				$exdate = new Sabre\VObject\Property\DateTime('exdate',Sabre\VObject\Property\DateTime::DATE);
				$dt = GO_Base_Util_Date_DateTime::fromUnixtime($exception->getStartTime());				
				$exdate->setDateTime($dt);		
				$e->add($exdate);
			}
		}
		
		
		$stmt = $this->participants();
		while($participant=$stmt->fetch()){
			
			if($participant->is_organizer || $method=='REQUEST' || ($updateByParticipant && $updateByParticipant->id==$participant->id)){
				//var_dump($participant->email);
				if($participant->is_organizer){
					$p = new Sabre\VObject\Property('organizer','mailto:'.$participant->email);				
				}else
				{
					$p = new Sabre\VObject\Property('attendee','mailto:'.$participant->email);				
				}
				$p['CN']=$participant->name;
				$p['RSVP']="true";
				$p['PARTSTAT']=$this->_exportVObjectStatus($participant->status);
	
				//If this is a meeting REQUEST then we must send all participants.
				//For a CANCEL or REPLY we must send the organizer and the current user.
			
				$e->add($p);
			}
		}
		
		if($this->category){
			$e->categories=$this->category->name;
		}
		
		
		//todo alarms
		
		if($this->reminder>0){
//			BEGIN:VALARM
//ACTION:DISPLAY
//TRIGGER;VALUE=DURATION:-PT5M
//DESCRIPTION:Default Mozilla Description
//END:VALARM
			$a=new Sabre\VObject\Component('valarm');
			$a->action='DISPLAY';
			$trigger = new Sabre\VObject\Property('trigger','-PT'.($this->reminder/60).'M');
			$trigger['VALUE']='DURATION';
			$a->add($trigger);
			$a->description="Alarm";
			
			$e->add($a);
						
			//for funambol compatibility, the GO_Base_VObject_Reader class use this to convert it to a vcalendar 1.0 aalarm tag.
			$e->{"X-GO-REMINDER-TIME"}=date('Ymd\THis', $this->start_time-$this->reminder);
		}
		
		return $e;
	}
	


	/**
	 * Get vcalendar data for an *.ics file.
	 * 
	 * @param string $method REQUEST, REPLY or CANCEL
	 * @param GO_Calendar_Model_Participant $updateByParticipant The participant that is generating this ICS for a response.
	 * @param int $recurrenceTime Export for a specific recurrence time for the recurrence-id. 
	 * If this event is an occurence and has a exception_for_event_id it will automatically determine this value. 
	 * This option is only useful for cancelling a single occurence. Because in that case there is no event model for the occurrence. There's just an exception.
	 * 
	 * Set this to a unix timestamp of the start of an occurence if it's an update
	 * for a particular recurrence date.
	 * 
	 * @return type 
	 */
	
	public function toICS($method='REQUEST', $updateByParticipant=false, $recurrenceTime=false) {		
		
		$c = new GO_Base_VObject_VCalendar();		
		$c->method=$method;
		
		$c->add(new GO_Base_VObject_VTimezone());
		
		$c->add($this->toVObject($method, $updateByParticipant, $recurrenceTime));		
		return $c->serialize();		
	}
	
	public function toVCS(){
		$c = new GO_Base_VObject_VCalendar();		
		$vobject = $this->toVObject('',false,false,true);
		$c->add($vobject);		
		
		GO_Base_VObject_Reader::convertICalendarToVCalendar($c);
		
		return $c->serialize();		
	}
	
	/**
	 * Check if this event is a resource booking;
	 * 
	 * @return boolean
	 */
	public function isResource(){
		return $this->calendar->group_id>1;
	}
	
	
	public $importedParticiants=array();
	
	
	private function _utcToLocal(DateTime $date){
		//DateTime from SabreDav is date without time in UTC timezone. We store it in the users timezone so we must
		//add the timezone offset.
		$timezone = new DateTimeZone(GO::user()->timezone);

		$offset = $timezone->getOffset($date);		
		$sub = $offset>0;
		if(!$sub)
			$offset *= -1;

		$interval = new DateInterval('PT'.$offset.'S');	
		if(!$sub){
			$date->add($interval);
		}else{
			$date->sub($interval);		

		}
	}
	
	
	/**
	 * Import an event from a VObject 
	 * 
	 * @param Sabre\VObject\Component $vobject
	 * @param array $attributes Extra attributes to apply to the event. Raw values should be past. No input formatting is applied.
	 * @param boolean $dontSave. Don't save the event. WARNING. Event can't be fully imported this way because participants and exceptions need an ID. This option is useful if you want to display info about an ICS file.
	 * @return GO_Calendar_Model_Event 
	 */
	public function importVObject(Sabre\VObject\Component $vobject, $attributes=array(), $dontSave=false, $makeSureUserParticipantExists=false){

		$uid = (string) $vobject->uid;
		if(!empty($uid))
			$this->uuid = $uid;
		
		$this->name = (string) $vobject->summary;
		if(empty($this->name))
			$this->name = GO::t('unnamed');
		
		$dtstart = $vobject->dtstart ? $vobject->dtstart->getDateTime() : new DateTime();
		$dtend = $vobject->dtend ? $vobject->dtend->getDateTime() : new DateTime();
		
		//funambol sends this special parameter
		if((string) $vobject->{"X-FUNAMBOL-ALLDAY"}=="1"){
			$this->all_day_event=1;
		}else
		{
			$this->all_day_event = isset($vobject->dtstart['VALUE']) && $vobject->dtstart['VALUE']=='DATE' ? 1 : 0;
		}
		
		if($this->all_day_event){
			if($dtstart->getTimezone()->getName()=='UTC'){
				$this->_utcToLocal($dtstart);
			}
			if($dtend->getTimezone()->getName()=='UTC'){
				$this->_utcToLocal($dtend);
			}
		}
		
		
		
		$this->start_time =intval($dtstart->format('U'));	
		$this->end_time = intval($dtend->format('U'));
		
		if($vobject->duration){
			$duration = GO_Base_VObject_Reader::parseDuration($vobject->duration);
			$this->end_time = $this->start_time+$duration;
		}
		if($this->end_time<=$this->start_time)
			$this->end_time=$this->start_time+3600;
				
		
		if($vobject->description)
			$this->description = (string) $vobject->description;
		
		//TODO needs improving
		if($this->all_day_event)
			$this->end_time-=60;
		
		if((string) $vobject->rrule != ""){			
			$rrule = new GO_Base_Util_Icalendar_Rrule();
			$rrule->readIcalendarRruleString($this->start_time, (string) $vobject->rrule);	
			$rrule->shiftDays(true);
			$this->rrule = $rrule->createRrule();
			$this->repeat_end_time = $rrule->until;
		}else
		{
			$this->rrule="";
			$this->repeat_end_time = 0;
		}
			
		if($vobject->{"last-modified"})
			$this->mtime=intval($vobject->{"last-modified"}->getDateTime()->format('U'));
		
		if($vobject->location)
			$this->location=(string) $vobject->location;
		
		//var_dump($vobject->status);
		if($vobject->status){
			$status = (string) $vobject->status;
			if($this->isValidStatus($status))
				$this->status=$status;			
		}
		
		if(isset($vobject->class)){
			$this->private = strtoupper($vobject->class)!='PUBLIC';
		}
		
		if($vobject->valarm && $vobject->valarm->trigger){
			
			$duration = GO_Base_VObject_Reader::parseDuration($vobject->valarm->trigger);
			$this->reminder = $duration*-1;
			
		}elseif($vobject->aalarm){ //funambol sends old vcalendar 1.0 format
			$aalarm = explode(';', (string) $vobject->aalarm);
			if(isset($aalarm[0])) {				
				$p = Sabre\VObject\Property\DateTime::parseData($aalarm[0]);
				$this->reminder = $this->start_time-$p[1]->format('U');
			}
		
		}else
		{
			$this->reminder=0;
		}
		
		$this->setAttributes($attributes, false);
		
		$recurrenceIds = $vobject->select('recurrence-id');
		if(count($recurrenceIds)){
			
			//this is a single instance of a recurring series.
			//attempt to find the exception of the recurring series event by uuid
			//and recurrence time so we can set the relation cal_exceptions.exception_event_id=cal_events.id
			
			$firstMatch = array_shift($recurrenceIds);
			$recurrenceTime=$firstMatch->getDateTime()->format('U');
			
			$whereCriteria = GO_Base_Db_FindCriteria::newInstance()
							->addCondition('calendar_id', $this->calendar_id,'=','ev')
							->addCondition('uuid', $this->uuid,'=','ev')
							->addCondition('time', $recurrenceTime,'=','t');
			
			$joinCriteria = GO_Base_Db_FindCriteria::newInstance()
							->addCondition('event_id', 'ev.id','=','t',true, true);
			
			
			$findParams = GO_Base_Db_FindParams::newInstance()
							->single()
							->criteria($whereCriteria)
							->join(GO_Calendar_Model_Event::model()->tableName(),$joinCriteria,'ev');
			
			$exception = GO_Calendar_Model_Exception::model()->find($findParams);
			if($exception){
				$this->exception_for_event_id=$exception->event_id;
			}else
			{				
				//exception was not found for this recurrence. Find the recurring series and add the exception.
				$recurringEvent = GO_Calendar_Model_Event::model()->findByUuid($this->uuid, 0, $this->calendar_id);
				if($recurringEvent){
					//aftersave will create GO_Calendar_Model_Exception
					$this->exception_for_event_id=$recurringEvent->id;
					
					//will be saved later
					$exception = new GO_Calendar_Model_Exception();
					$exception->time=$recurrenceTime;
					$exception->event_id=$recurringEvent->id;
				}
			}			
		}
		
		if($vobject->valarm){
			$reminderTime = $vobject->valarm->getEffectiveTriggerTime();
			//echo $reminderTime->format('c');
			$this->reminder = $this->start_time-$reminderTime->format('U');
		}
		
		
		if(!empty($vobject->categories)){
			//Group-Office only supports a single category.
			$cats = explode(',',$vobject->categories);
			$categoryName = array_shift($cats);
			
			$category = GO_Calendar_Model_Category::model()->findByName($this->calendar_id, $categoryName);
			if(!$category && !$dontSave && $this->calendar_id){
				$category = new GO_Calendar_Model_Category();
				$category->name=$categoryName;
				$category->calendar_id=$this->calendar_id;
				$category->save();
			}			
			
			if($category){
				$this->category_id=$category->id;			
				$this->background=$category->color;
			}
		}
		
		//set is_organizer flag
		if($vobject->organizer && $this->calendar){
			$organizerEmail = str_replace('mailto:','', strtolower((string) $vobject->organizer));
			$this->is_organizer=$organizerEmail == $this->calendar->user->email;
		}		
		
		
		if(!$dontSave){
			$this->cutAttributeLengths();
//			try {
				$this->_isImport=true;
				
				//make sure no duplicates are imported.
				$this->setValidationRule('uuid', 'unique', array('calendar_id','start_time'));
				
				if(!$this->save()){
					throw new GO_Base_Exception_Validation(implode("\n", $this->getValidationErrors())."\n");
				}
				$this->_isImport=false;
//			} catch (Exception $e) {
//				throw new Exception($this->name.' ['.GO_Base_Util_Date::get_timestamp($this->start_time).' - '.GO_Base_Util_Date::get_timestamp($this->end_time).'] '.$e->getMessage());
//			}
			
			if(!empty($exception)){			
				//save the exception we found by recurrence-id
				$exception->exception_event_id=$this->id;
				$exception->save();
			}		
			
			
//			$test = (bool) $vobject->organizer;
			
//			var_dump($test);
//			exit();
//			

			if($vobject->organizer)
				$p = $this->importVObjectAttendee($this, $vobject->organizer, true);

			$calendarParticipantFound=!empty($p) && $p->user_id==$this->calendar->user_id;
			
			$attendees = $vobject->select('attendee');
			foreach($attendees as $attendee){
				$p = $this->importVObjectAttendee($this, $attendee, false);
				
				if($p->user_id==$this->calendar->user_id){
					$calendarParticipantFound=true;
				}
			}
			
			//if the calendar owner is not in the participants then we should chnage the is_organizer flag because otherwise the event can't be opened or accepted.
			if(!$calendarParticipantFound){
				
				if($makeSureUserParticipantExists){
					//this is a bad situation. The import thould have detected a user for one of the participants.
					//It uses the E-mail account aliases to determine a user. See GO_Calendar_Model_Event::importVObject
					$participant = new GO_Calendar_Model_Participant();
					$participant->event_id=$this->id;
					$participant->user_id=$this->calendar->user_id;
					$participant->email=$this->calendar->user->email;	
					$participant->save();
				}else
				{
					$this->is_organizer=true;
					$this->save();
				}
			}

			if($vobject->exdate){
				if (strpos($vobject->exdate,';')!==false) {
					$timesArr = explode(';',$vobject->exdate->value);
					$exDateTimes = array();
					foreach ($timesArr as $time) {
						list(
								$dateType,
								$dateTime
						) =  Sabre\VObject\Property\DateTime::parseData($time,$vobject->exdate);
						$this->addException($dateTime->format('U'));
					}
				} else {
					$exDateTimes = $vobject->exdate->getDateTimes();
					foreach($exDateTimes as $dt){
						$this->addException($dt->format('U'));
					}
				}
			}
		}
				
		

		return $this;
	}	
	
	
	/**
	 * Will import an attendee from a VObject to a given event. If the attendee
	 * already exists it will update it.
	 * 
	 * @param GO_Calendar_Model_Event $event
	 * @param Sabre\VObject\Property $vattendee
	 * @param boolean $isOrganizer
	 * @return GO_Calendar_Model_Participant 
	 */
	public function importVObjectAttendee(GO_Calendar_Model_Event $event, Sabre\VObject\Property $vattendee, $isOrganizer=false){
			
		$attributes = $this->_vobjectAttendeeToParticipantAttributes($vattendee);
		$attributes['is_organizer']=$isOrganizer;
		
		if($isOrganizer)
			$attributes['status']= GO_Calendar_Model_Participant::STATUS_ACCEPTED;
	
		$p= GO_Calendar_Model_Participant::model()
						->findSingleByAttributes(array('event_id'=>$event->id, 'email'=>$attributes['email']));
		if(!$p){
			$p = new GO_Calendar_Model_Participant();
			$p->is_organizer=$isOrganizer;		
			$p->event_id=$event->id;			
			if(GO::modules()->email){
				$account = GO_Email_Model_Account::model()->findByEmail($attributes['email']);
				if($account)
					$p->user_id=$account->user_id;
			}
			
			if(!$p->user_id){
				$user = GO_Base_Model_User::model()->findSingleByAttribute('email', $attributes['email']);
				if($user)
					$p->user_id=$user->id;
			}		
		}else
		{
			//the organizer might be added as a participant too. We don't want to 
			//import that a second time but we shouldn't update the is_organizer flag if
			//we found an existing participant.
			unset($attributes['is_organizer']);
		}

		$p->setAttributes($attributes);
		$p->save();
		
		return $p;
	}
	
	private function _vobjectAttendeeToParticipantAttributes(Sabre\VObject\Property $vattendee){
		return array(
				'name'=>(string) $vattendee['CN'],
				'email'=>str_replace('mailto:','', strtolower((string) $vattendee)),
				'status'=>$this->_importVObjectStatus((string) $vattendee['PARTSTAT']),
				'role'=>(string) $vattendee['ROLE']
		);
	}
	
	private function _importVObjectStatus($status)
	{
		$statuses = array(
			'NEEDS-ACTION' => GO_Calendar_Model_Participant::STATUS_PENDING,
			'ACCEPTED' => GO_Calendar_Model_Participant::STATUS_ACCEPTED,
			'DECLINED' => GO_Calendar_Model_Participant::STATUS_DECLINED,
			'TENTATIVE' => GO_Calendar_Model_Participant::STATUS_TENTATIVE
		);

		return isset($statuses[$status]) ? $statuses[$status] : GO_Calendar_Model_Participant::STATUS_PENDING;
	}
	private function _exportVObjectStatus($status)
	{
		$statuses = array(
			GO_Calendar_Model_Participant::STATUS_PENDING=>'NEEDS-ACTION',	
			GO_Calendar_Model_Participant::STATUS_ACCEPTED=>'ACCEPTED',
			GO_Calendar_Model_Participant::STATUS_DECLINED=>'DECLINED',
			GO_Calendar_Model_Participant::STATUS_TENTATIVE=>'TENTATIVE'
		);

		return isset($statuses[$status]) ? $statuses[$status] : 'NEEDS-ACTION';
	}
	
	protected function afterDuplicate(&$duplicate) {
		
		if (!$duplicate->isNew) {
			
			$stmt = $duplicate->participants;
			
			if (!$stmt->rowCount())
				$this->duplicateRelation('participants', $duplicate);

			if($duplicate->isRecurring() && $this->isRecurring())
				$this->duplicateRelation('exceptions', $duplicate);		
		}
		
		return parent::afterDuplicate($duplicate);
	}
	
	/**
	 * Add a participant to this calendar
	 * 
	 * This function sets the event_id for the participant and saves it.
	 * 
	 * @param GO_Calendar_Model_Participant $participant
	 * @return bool Save of participant is successfull
	 */
	public function addParticipant($participant){
		$participant->event_id = $this->id;
		return $participant->save();
	}
	
	/**
	 * 
	 * @param GO_Calendar_Model_Participant $participant
	 * @return GO_Calendar_Model_Event 
	 */
	public function createCopyForParticipant(GO_Calendar_Model_Participant $participant){
//		$calendar = GO_Calendar_Model_Calendar::model()->getDefault($user);
//		
//		return $this->duplicate(array(
//			'user_id'=>$user->id,
//			'calendar_id'=>$calendar->id,
//			'is_organizer'=>false
//		));
		
		//create event in participant's default calendar if the current user has the permission to do that
		$calendar = $participant->getDefaultCalendar();
		if ($calendar && $calendar->userHasCreatePermission()){
			
			$existing = GO_Calendar_Model_Event::model()->findSingleByAttributes(array('calendar_id'=>$calendar->id, 'uuid'=>$this->uuid));
			
			if(!$existing){
				//ignore acl permissions because we allow users to schedule events directly when they have access through
				//the special freebusypermissions module.			
				$participantEvent = $this->duplicate(array(
						'calendar_id' => $calendar->id,
						'user_id'=>$participant->user_id,
						'is_organizer'=>false, 
	//					'status'=>  GO_Calendar_Model_Event::STATUS_NEEDS_ACTION
						),
								true,true);			
				return $participantEvent;
			}
			
		}
		return false;
				
	}
	
	/**
	 * Get the default participant model for a new event.
	 * The default is the calendar owner except if the owner is admin. In that
	 * case it will default to the logged in user.
	 * 
	 * @return \GO_Calendar_Model_Participant
	 */
	public function getDefaultOrganizerParticipant(){
		$calendar = $this->calendar;
		
		$user = $calendar->user_id==1 ? GO::user() : $calendar->user;
		
		$participant = new GO_Calendar_Model_Participant();
		$participant->event_id=$this->id;
		$participant->user_id=$user->id;
		$participant->name=$user->name;
		$participant->email=$user->email;
		$participant->status=GO_Calendar_Model_Participant::STATUS_ACCEPTED;
		$participant->is_organizer=1;
		
		return $participant;
	}
	
	/**
	 * Get's the organizer's event if this event belongs to a meeting.
	 * 
	 * @return GO_Calendar_Model_Event
	 */
	public function getOrganizerEvent(){
		if($this->is_organizer)
			return false;
		
		return GO_Calendar_Model_Event::model()->findSingleByAttributes(array('uuid'=>$this->uuid, 'is_organizer'=>1));
	}
	
	/**
	 * Check if this event has other participant then the given user id.
	 * 
	 * @param int|array $user_id
	 * @return boolean 
	 */
	public function hasOtherParticipants($user_id=0){
		
		if(empty($user_id))
			$user_id=array($this->calendar->user_id,GO::user()->id);
		elseif(!is_array($user_id))
			$user_id = array($user_id);
		
		if(empty($this->id))
			return false;
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->single();
		
		$findParams->getCriteria()
						->addInCondition('user_id', $user_id,'t', true, true)
						->addCondition('event_id', $this->id);
						
		
		$p = GO_Calendar_Model_Participant::model()->find($findParams);
		
		return $p ? true : false;
	}
	
	/**
	 * When checking all Event models make sure there is a UUID if not create one
	 */
	public function checkDatabase() {

	  if(empty($this->uuid))
		$this->uuid = GO_Base_Util_UUID::create('event', $this->id);
	  
		//in some cases on old databases the repeat_end_time is set but the UNTIL property in the rrule is not. We correct that here.
		if($this->repeat_end_time>0 && strpos($this->rrule,'UNTIL=')===false){
			$rrule = new GO_Base_Util_Icalendar_Rrule();
			$rrule->readIcalendarRruleString($this->start_time, $this->rrule);						
			$rrule->until=$this->repeat_end_time;
			$this->rrule= $rrule->createRrule();	
		}
		
		parent::checkDatabase();
	}
	
	
	/**
	 * Get the organizer model of this event
	 * 
	 * @return GO_Calendar_Model_Participant
	 */
	public function getOrganizer(){
		return GO_Calendar_Model_Participant::model()->findSingleByAttributes(array(
				'is_organizer'=>true,
				'event_id'=>$this->id
		));
	}
	
	
	/**
	 * Get the participant model where the user matches the calendar user
	 * 
	 * @return GO_Calendar_Model_Participant
	 */
	public function getParticipantOfCalendar(){
		return GO_Calendar_Model_Participant::model()->findSingleByAttributes(array(
				'user_id'=>$this->calendar->user_id,
				'event_id'=>$this->id
		));
	}
	
	/**
	 * Returns all participant models for this event and all the related events for a meeting.
	 * 
	 * @return GO_Calendar_Model_Participant
	 */
	public function getParticipantsForUser(){
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
						->addCondition('user_id', $this->user_id)
						->addCondition('uuid', $this->uuid,'=','e')  //recurring series and participants all share the same uuid
						->addCondition('start_time', $this->start_time,'=','e') //make sure start time matches for recurring series
						->addCondition("exception_for_event_id", 0, $this->exception_for_event_id==0 ? '=' : '!=','e');//the master event or a single occurrence can start at the same time. Therefore we must check if exception event has a value or is 0.
		
		return GO_Calendar_Model_Participant::model()->find($findParams);			
		
	}
	
	
//	public function sendReply(){
//		if($this->is_organizer)
//			throw new Exception("Meeting reply can only be send from the organizer's event");
//	}	
	
	/**
	 * Update's the participant status on all related meeting events and optionally sends a notification by e-mail to the organizer.
	 * This function has to be called on an event that belongs to the participant and not the organizer.
	 * 
	 * @param int $status Participant status, See GO_Calendar_Model_Participant::STATUS_*
	 * @param boolean $sendMessage
	 * @param int $recurrenceTime Export for a specific recurrence time for the recurrence-id
	 * @throws Exception
	 */
	public function replyToOrganizer($recurrenceTime=false, $sendingParticipant=false, $includeIcs=true){
		
//		if($this->is_organizer)
//			throw new Exception("Meeting reply can't be send from the organizer's event");
		

		//we need to pass the sending participant to the toIcs function. 
		//Only the organizer and current participant should be included
		if(!$sendingParticipant)
			$sendingParticipant = $this->getParticipantOfCalendar();
			

		if(!$sendingParticipant)
			throw new Exception("Could not find your participant model");

		$organizer = $this->getOrganizer();
		if(!$organizer)
			throw new Exception("Could not find organizer to send message to!");

		$updateReponses = GO::t('updateReponses','calendar');
		$subject= sprintf($updateReponses[$sendingParticipant->status], $sendingParticipant->name, $this->name);


		//create e-mail message
		$message = GO_Base_Mail_Message::newInstance($subject)
							->setFrom($sendingParticipant->email, $sendingParticipant->name)
							->addTo($organizer->email, $organizer->name);

		$body = '<p>'.$subject.': </p>'.$this->toHtml();
		
		$url = GO::createExternalUrl('calendar', 'openCalendar', array(
					'unixtime'=>$this->start_time
				));
		
		$body .= '<br /><a href="'.$url.'">'.GO::t('openCalendar','calendar').'</a>';

//		if(!$this->getOrganizerEvent()){
			//organizer is not a Group-Office user with event. We must send a message to him an ICS attachment
		if($includeIcs){
			$ics=$this->toICS("REPLY", $sendingParticipant, $recurrenceTime);				
			$a = Swift_Attachment::newInstance($ics, GO_Base_Fs_File::stripInvalidChars($this->name) . '.ics', 'text/calendar; METHOD="REPLY"');
			$a->setEncoder(new Swift_Mime_ContentEncoder_PlainContentEncoder("8bit"));
			$a->setDisposition("inline");
			$message->attach($a);
			
			//for outlook 2003 compatibility
			$a2 = Swift_Attachment::newInstance($ics, 'invite.ics', 'application/ics');
			$a2->setEncoder(new Swift_Mime_ContentEncoder_PlainContentEncoder("8bit"));
			$message->attach($a2);
		}
//		}

		$message->setHtmlAlternateBody($body);

		GO_Base_Mail_Mailer::newGoInstance()->send($message);

	}
	
	
	public function sendCancelNotice(){
//		if(!$this->is_organizer)
//			throw new Exception("Meeting request can only be send from the organizer's event");
		
		$stmt = $this->participants;

		while ($participant = $stmt->fetch()) {		
			//don't invite organizer
			if($participant->is_organizer)
				continue;

			
			// Set the language of the email to the language of the participant.
			$language = false;
			if(!empty($participant->user_id)){
				$user = GO_Base_Model_User::model()->findByPk($participant->user_id);
				
				if($user)
					GO::language()->setLanguage($user->language);
			}

			$subject =  GO::t('cancellation','calendar').': '.$this->name;

			//create e-mail message
			$message = GO_Base_Mail_Message::newInstance($subject)
								->setFrom($this->user->email, $this->user->name)
								->addTo($participant->email, $participant->name);


			//check if we have a Group-Office event. If so, we can handle accepting and declining in Group-Office. Otherwise we'll use ICS calendar objects by mail
			$participantEvent = $participant->getParticipantEvent();

			$body = '<p>'.GO::t('cancelMessage','calendar').': </p>'.$this->toHtml();					
			
//			if(!$participantEvent){
				

				$ics=$this->toICS("CANCEL");				
				$a = Swift_Attachment::newInstance($ics, GO_Base_Fs_File::stripInvalidChars($this->name) . '.ics', 'text/calendar; METHOD="CANCEL"');
				$a->setEncoder(new Swift_Mime_ContentEncoder_PlainContentEncoder("8bit"));
				$a->setDisposition("inline");
				$message->attach($a);
				
				//for outlook 2003 compatibility
				$a2 = Swift_Attachment::newInstance($ics, 'invite.ics', 'application/ics');
				$a2->setEncoder(new Swift_Mime_ContentEncoder_PlainContentEncoder("8bit"));
				$message->attach($a2);
				
//			}else{
			if($participantEvent){
				$url = GO::createExternalUrl('calendar', 'openCalendar', array(
				'unixtime'=>$this->start_time
				));

				$body .= '<br /><a href="'.$url.'">'.GO::t('openCalendar','calendar').'</a>';
			}

			$message->setHtmlAlternateBody($body);

			// Set back the original language
			if($language !== false)
				GO::language()->setLanguage($language);
			
			GO_Base_Mail_Mailer::newGoInstance()->send($message);
		}

		return true;
		
	}
	
	/**
	 * Sends a meeting request to all participants. If the participant is not a Group-Office user
	 * or the organizer has no permissions to schedule an event it will include an
	 * icalendar attachment so the calendar software can schedule it.
	 * 
	 * @return boolean
	 * @throws Exception
	 */
	public function sendMeetingRequest(){		
		
		if(!$this->is_organizer)
			throw new Exception("Meeting request can only be send from the organizer's event");
		
		$stmt = $this->participants;

			while ($participant = $stmt->fetch()) {		
				//don't invite organizer
				if($participant->is_organizer)
					continue;
				
				// Set the language of the email to the language of the participant.
				$language = false;
				if(!empty($participant->user_id)){
					$user = GO_Base_Model_User::model()->findByPk($participant->user_id);

					if($user)
						GO::language()->setLanguage($user->language);
				}

				//if participant status is pending then send a new inviation subject. Otherwise send it as update
				if($participant->status == GO_Calendar_Model_Participant::STATUS_PENDING){
					$subject = GO::t('invitation', 'calendar').': '.$this->name;
					$bodyLine = GO::t('invited', 'calendar');
				}else
				{
					$subject = GO::t('invitation_update', 'calendar').': '.$this->name;
					$bodyLine = GO::t('eventUpdated', 'calendar');
				}				
				
				//create e-mail message
				$message = GO_Base_Mail_Message::newInstance($subject)
									->setFrom($this->user->email, $this->user->name)
									->addTo($participant->email, $participant->name);
				

				//check if we have a Group-Office event. If so, we can handle accepting and declining in Group-Office. Otherwise we'll use ICS calendar objects by mail
				$participantEvent = $participant->getParticipantEvent();
				
				$body = '<p>'.$bodyLine.': </p>'.$this->toHtml();			
				
				
//				if(!$participantEvent){					

				//build message for external program
				$acceptUrl = GO::url("calendar/event/invitation",array("id"=>$this->id,'accept'=>1,'email'=>$participant->email,'participantToken'=>$participant->getSecurityToken()),false);
				$declineUrl = GO::url("calendar/event/invitation",array("id"=>$this->id,'accept'=>0,'email'=>$participant->email,'participantToken'=>$participant->getSecurityToken()),false);

				if($participantEvent){	
					//hide confusing buttons if user has a GO event.
					$body .= '<div class="go-hidden">';
				}
				$body .= 
					
						'<p><br /><b>' . GO::t('linkIfCalendarNotSupported', 'calendar') . '</b></p>' .
						'<p>' . GO::t('acccept_question', 'calendar') . '</p>' .
						'<a href="'.$acceptUrl.'">'.GO::t('accept', 'calendar') . '</a>' .
						'&nbsp;|&nbsp;' .
						'<a href="'.$declineUrl.'">'.GO::t('decline', 'calendar') . '</a>';
				
				if($participantEvent){	
					$body .= '</div>';
				}

				$ics=$this->toICS("REQUEST");				
				$a = Swift_Attachment::newInstance($ics, GO_Base_Fs_File::stripInvalidChars($this->name) . '.ics', 'text/calendar; METHOD="REQUEST"');
				$a->setEncoder(new Swift_Mime_ContentEncoder_PlainContentEncoder("8bit"));
				$a->setDisposition("inline");
				$message->attach($a);
				
				//for outlook 2003 compatibility
				$a2 = Swift_Attachment::newInstance($ics, 'invite.ics', 'application/ics');
				$a2->setEncoder(new Swift_Mime_ContentEncoder_PlainContentEncoder("8bit"));
				$message->attach($a2);

				if($participantEvent){
					$url = GO::createExternalUrl('calendar', 'openCalendar', array(
					'unixtime'=>$this->start_time
					));

					$body .= '<br /><a href="'.$url.'">'.GO::t('openCalendar','calendar').'</a>';
				}
				
				$message->setHtmlAlternateBody($body);

				// Set back the original language
				if($language !== false)
					GO::language()->setLanguage($language);
			
				GO_Base_Mail_Mailer::newGoInstance()->send($message);
			}
			
			return true;
	}
}