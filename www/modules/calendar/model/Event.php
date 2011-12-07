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
 * 
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Wesley Smits <wsmits@intermesh.nl>
 */
class GO_Calendar_Model_Event extends GO_Base_Db_ActiveRecord {

	/**
	 * The date where the exception needs to be created. If this is set on a new event
	 * an exception will automatically be created for the recurring series. exception_for_event_id needs to be set too.
	 * 
	 * @var timestamp 
	 */
	public $exception_date;

	protected function init() {

		$this->columns['start_time']['gotype'] = 'unixtimestamp';
		$this->columns['end_time']['gotype'] = 'unixtimestamp';
		$this->columns['repeat_end_time']['gotype'] = 'unixtimestamp';

		parent::init();
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
		$settings = GO_Calendar_Model_Settings::model()->getDefault(GO::user());
		
		$defaults = array(
				//'description'=>'DIT IS DE BESCHRIJVING DIE STANDAARD WORDT INGEVULD',
				'status' => "NEEDS-ACTION",
				'start_time'=> GO_Base_Util_Date::roundQuarters(time()), 
				'end_time'=>GO_Base_Util_Date::roundQuarters(time()+3600),
				'reminder' => $settings->reminder,
				'calendar_id'=>$settings->calendar_id
		);
		
		return $defaults;
	}

	public function customfieldsModel() {
		return "GO_Calendar_Model_EventCustomFieldsRecord";
	}


	public function relations() {
		return array(
				'exceptionEvent'=>array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Event', 'field' => 'exception_for_event_id'),
				'recurringEventException'=>array('type' => self::HAS_ONE, 'model' => 'GO_Calendar_Model_Exception', 'field' => 'exception_event_id'),//If this event is an exception for a recurring series. This relation points to the exception of the recurring series.
				'calendar' => array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Calendar', 'field' => 'calendar_id'),
				'participants' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Participant', 'field' => 'event_id', 'delete' => true),
				'exceptions' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Exception', 'field' => 'event_id', 'delete' => true),
				'resources' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Event', 'field' => 'resource_event_id', 'delete' => true)
		);
	}

	protected function getCacheAttributes() {
		return array(
				'name' => $this->name,
				'description' => $this->description
		);
	}

	protected function getLocalizedName() {
		return GO::t('event', 'calendar');
	}

	/**
	 * The files module will use this function.
	 */
	public function buildFilesPath() {

		return 'calendar/' . GO_Base_Fs_Base::stripInvalidChars($this->calendar->name) . '/' . date('Y', $this->start_time) . '/' . GO_Base_Fs_Base::stripInvalidChars($this->name);
	}

	/**
	 * Get the date interval for the event.
	 * 
	 * @return array 
	 */
	public function getDiff() {
		$startDateTime = new GO_Base_Util_Date_DateTime(date('c', $this->start_time));
		$endDateTime = new GO_Base_Util_Date_DateTime(date('c', $this->end_time));
		return $startDateTime->getDiffCompat($endDateTime);
	}

	/**
	 * Add an Exception for the Event if it is recurring
	 * 
	 * @param Unix Timestamp $date The date where the exception belongs to
	 * @param Int $for_event_id The event id of the event where the exception belongs to
	 */
	public function addException($date, $exception_event_id=0) {
		$exception = new GO_Calendar_Model_Exception();
		$exception->event_id = $this->id;
		$exception->time = $date; // Needs to be a unix timestamp
		$exception->exception_event_id=$exception_event_id;
		$exception->save();
	}

	/**
	 * This Event needs to be reinitialized to become an Exception of its own on the given Unix timestamp.
	 * 
	 * @param int $exceptionDate Unix timestamp
	 */
	public function getExceptionEvent($exceptionDate) {
		

		$att['rrule'] = '';
		$att['exception_for_event_id'] = $this->id;
		$att['exception_date'] = $exceptionDate;
		
		$diff = $this->getDiff();

		$d = date('Y-m-d', $exceptionDate);
		$t = date('G:i', $this->start_time);

		$att['start_time'] = strtotime($d . ' ' . $t);

		$endTime = new GO_Base_Util_Date_DateTime(date('c', $att['start_time']));
		$endTime->addDiffCompat($diff);
		$att['end_time'] = $endTime->format('U');
		
		return $this->duplicate($att, false);
	}
	
	protected function beforeSave() {
		
		if($this->isResource()){
			if($this->status=='ACCEPTED'){
				$this->background='CCFFCC';
			}else
			{
				$this->background='FF6666';
			}
		}
		
		return parent::beforeSave();
	}
	
	protected function afterDbInsert() {
		if(!$this->uuid){
			$this->uuid = GO_Base_Util_UUID::create('event', $this->id);
			return true;
		}else
		{
			return false;
		}
	}
	
	protected function afterDelete() {
		
		$this->deleteReminders();
		
		return parent::afterDelete();
	}
	
	public static function reminderDismissed($reminder, $userId){		
		
		//this listener function is added in GO_Calendar_CalendarModule
		
		if($reminder->model_type_id==GO_Calendar_Model_Event::model()->modelTypeId()){			
			$event = GO_Calendar_Model_Event::model()->findByPk($reminder->model_id);
			if($event->isRecurring() && $event->reminder>0){
				$rRule = new GO_Base_Util_Icalendar_Rrule();
				$rRule->readIcalendarRruleString($event->start_time, $event->rrule);				
				$rRule->setRecurpositionStartTime(time()+$event->reminder);
				$nextTime = $rRule->getNextRecurrence();
				
				if($nextTime){
					$event->addReminder($event->name, $nextTime-$event->reminder, $user_id);
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
		if ($wasNew && $this->exception_for_event_id > 0 && !empty($this->exception_date)) {
			
			$newExeptionEvent = GO_Calendar_Model_Event::model()->findByPk($this->exception_for_event_id);
			$newExeptionEvent->addException($this->exception_date, $this->id);
			
			//copy particpants to new exception
			$stmt = $newExeptionEvent->participants();
			while($participant = $stmt->fetch()){
				$newParticipant = new GO_Calendar_Model_Participant();
				$newParticipant->setAttributes($participant->getAttributes());
				unset($newParticipant->id);
				$newParticipant->event_id=$this->id;
				if(!$newParticipant->is_organizer){
					$newParticipant->status=GO_Calendar_Model_Participant::STATUS_PENDING;
				}
				$newParticipant->save();
			}
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
			if(!$wasNew)
				$this->_updateResourceEvents();
		}

		if($this->reminder>0){
			$remindTime = $this->start_time-$this->reminder;
			
			$this->deleteReminders();
			$this->addReminder($this->name, $remindTime, $this->user_id);
		}	

		return parent::afterSave($wasNew);
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
			$resourceEvent->save();
		}
	}
	
	private function _sendResourceNotification($wasNew){
		
		$url = GO::createExternalUrl('calendar', 'showEvent', array(array('values'=>array('event_id' => $this->id))));		
		
		$stmt = $this->calendar->group->admins();
		while($user = $stmt->fetch()){
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
		
		if($this->user_id!=GO::user()->id){
			//todo send update to user
			
			if($this->isModified('status')){
				if($this->status=='ACCEPTED'){
					$body = sprintf(GO::t('your_resource_accepted_mail_body','calendar'),$user->name,$this->calendar->name).'<br /><br />'
								. $this->toHtml()
								. '<br /><a href="'.$url.'">'.GO::t('open_resource','calendar').'</a>';
			
					$subject = sprintf(GO::t('your_resource_accepted_mail_subject','calendar'),$this->calendar->name, $this->name, GO_Base_Util_Date::get_timestamp($this->start_time,false));
				}else
				{
						$body = sprintf(GO::t('your_resource_declined_mail_body','calendar'),$user->name,$this->calendar->name).'<br /><br />'
								. $this->toHtml()
								. '<br /><a href="'.$url.'">'.GO::t('open_resource','calendar').'</a>';
			
					$subject = sprintf(GO::t('your_resource_declined_mail_subject','calendar'),$this->calendar->name, $this->name, GO_Base_Util_Date::get_timestamp($this->start_time,false));
				}
			}else
			{
				$body = sprintf(GO::t('your_resource_modified_mail_body','calendar'),$user->name,$this->calendar->name).'<br /><br />'
							. $this->toHtml()
							. '<br /><a href="'.$url.'">'.GO::t('open_resource','calendar').'</a>';
				$subject = sprintf(GO::t('your_resource_modified_mail_subject','calendar'),$this->calendar->name, $this->name, GO_Base_Util_Date::get_timestamp($this->start_time,false));
			}
			
			$message = GO_Base_Mail_Message::newInstance(
								$subject
								)->setFrom(GO::user()->email, GO::user()->name)
								->addTo($this->user->email, $this->user->name);

			$message->setHtmlAlternateBody($body);					
			
			GO_Base_Mail_Mailer::newGoInstance()->send($message);
		}
	}

	private $_calculatedEvents;

	/**
	 * Find events for a given time period.
	 * 
	 * Recurring events are calculated and added to the array.
	 * 
	 * @param GO_Base_Db_FindParams $findParams
	 * @param int $periodStartTime
	 * @param int $periodEndTime
	 * @param boolean $onlyBusyEvents
	 * @return array Note, this are not models but arrays of attributes.
	 */
	public function findCalculatedForPeriod($findParams, $periodStartTime, $periodEndTime, $onlyBusyEvents=false) {

		
		$stmt = $this->findForPeriod($findParams, $periodStartTime, $periodEndTime);

		$this->_calculatedEvents = array();

		while ($event = $stmt->fetch()) {
			$this->_calculateRecurrences($event, $periodStartTime, $periodEndTime);
		}

		return array_values($this->_calculatedEvents);
	}
	
	/**
	 * Find events that occur in a given time period.
	 * 
	 * Recurring events are not calculated.
	 * 
	 * @param GO_Base_Db_FindParams $findParams
	 * @param int $periodStartTime
	 * @param int $periodEndTime
	 * @param boolean $onlyBusyEvents
	 * @return GO_Calendar_Model_Event[] 
	 */
	public function findForPeriod($findParams, $periodStartTime, $periodEndTime=0, $onlyBusyEvents=false){
		if (!$findParams)
			$findParams = GO_Base_Db_FindParams::newInstance();

		$findParams->order('start_time', 'ASC');
		
		if($periodEndTime)
			$findParams->getCriteria()->addCondition('start_time', $periodEndTime, '<');
		
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
										->addCondition('repeat_end_time', $periodStartTime, '>')
										->addCondition('repeat_end_time', 0,'=','t',false))
					->addCondition('start_time', $periodStartTime, '<');
		
		$normalEventsCriteria->mergeWith($recurringEventsCriteria, false);
		
		$findParams->getCriteria()->mergeWith($normalEventsCriteria);

		

		return $this->find($findParams);
	}

	private function _calculateRecurrences($event, $periodStartTime, $periodEndTime) {
		if (empty($event->rrule)) {
			//not a recurring event
			$this->_calculatedEvents[] = $event->getAttributes('formatted');
		} else {
			$rrule = new GO_Base_Util_Icalendar_Rrule();
			$rrule->readIcalendarRruleString($event->start_time, $event->rrule);

			$rrule->setRecurpositionStartTime($periodStartTime);

			$origEventAttr = $event->getAttributes('formatted');

			while ($occurenceStartTime = $rrule->getNextRecurrence()) {

				if ($occurenceStartTime > $periodEndTime)
					break;

				$origEventAttr['start_time'] = GO_Base_Util_Date::get_timestamp($occurenceStartTime);

				$diff = $this->getDiff();

				$endTime = new GO_Base_Util_Date_DateTime(date('c', $occurenceStartTime));
				$endTime->addDiffCompat($diff);
				$origEventAttr['end_time'] = GO_Base_Util_Date::get_timestamp($endTime->format('U'));

				$this->_calculatedEvents[$occurenceStartTime . '-' . $origEventAttr['id']] = $origEventAttr;
			}

			ksort($this->_calculatedEvents);
		}
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
						->debugSql()
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
			
			$whereCriteria->addCondition('time', $exceptionDate,'=','e');			
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
	 * Get the event in HTML markup
	 * 
	 * @todo Add recurrence info
	 * @return string 
	 */
	public function toHtml() {
		$html = '<table>' .
						'<tr><td>' . GO::t('subject', 'calendar') . ':</td>' .
						'<td>' . $this->name . '</td></tr>';

		$html .= '<tr><td>' . GO::t('status', 'calendar') . ':</td>' .
						'<td>' . $this->status . '</td></tr>';


		if (!empty($this->location)) {
			$html .= '<tr><td style="vertical-align:top">' . GO::t('location', 'calendar') . ':</td>' .
							'<td>' . GO_Base_Util_String::text_to_html($this->location) . '</td></tr>';
		}

		//don't calculate timezone offset for all day events
		$timezone_offset_string = GO_Base_Util_Date::get_timezone_offset($this->start_time);

		if ($timezone_offset_string > 0) {
			$gmt_string = '(\G\M\T +' . $timezone_offset_string . ')';
		} elseif ($timezone_offset_string < 0) {
			$gmt_string = '(\G\M\T -' . $timezone_offset_string . ')';
		} else {
			$gmt_string = '(\G\M\T)';
		}

		if ($this->all_day_event == '1') {
			$event_datetime_format = GO::user()->completeDateFormat;
		} else {
			$event_datetime_format = GO::user()->completeDateFormat . ' ' . GO::user()->time_format . ' ' . $gmt_string;
		}

		$html .= '<tr><td colspan="2">&nbsp;</td></tr>';

		$html .= '<tr><td>' . GO::t('startsAt', 'calendar') . ':</td>' .
						'<td>' . date($event_datetime_format, $this->start_time) . '</td></tr>' .
						'<tr><td>' . GO::t('endsAt', 'calendar') . ':</td>' .
						'<td>' . date($event_datetime_format, $this->end_time) . '</td></tr>';

		$html .= '</table>';

		return $html;
	}
	
	
	/**
	 * Get this event as a VObject. This can be turned into a vcalendar file data.
	 * 
	 * @param string $method REQUEST, REPLY or CANCEL
	 * @return Sabre_VObject_Component 
	 */
	public function toVObject($method='REQUEST', $vcalendar1=false){
		$e=new Sabre_VObject_Component('vevent');
		$e->uid=$this->uuid;		
		
		if(isset($this->sequence))
			$e->sequence=$this->sequence;
		
		$dtstamp = new Sabre_VObject_Element_DateTime('dtstamp');
		$dtstamp->setDateTime(new DateTime(), Sabre_VObject_Element_DateTime::UTC);		
		//$dtstamp->offsetUnset('VALUE');
		$e->add($dtstamp);
		
		$mtimeDateTime = new DateTime();
		$mtimeDateTime->setTimestamp($this->mtime);
		$lm = new Sabre_VObject_Element_DateTime('LAST-MODIFIED');
		$lm->setDateTime($mtimeDateTime, Sabre_VObject_Element_DateTime::UTC);		
		//$lm->offsetUnset('VALUE');
		$e->add($lm);
		
		$ctimeDateTime = new DateTime();
		$ctimeDateTime->setTimestamp($this->mtime);
		$ct = new Sabre_VObject_Element_DateTime('created');
		$ct->setDateTime($ctimeDateTime, Sabre_VObject_Element_DateTime::UTC);		
		//$ct->offsetUnset('VALUE');
		$e->add($ct);
		
    $e->summary = $this->name;
		
		$e->status = $this->status;
		
		$dateType = $this->all_day_event ? Sabre_VObject_Element_DateTime::DATE : Sabre_VObject_Element_DateTime::LOCALTZ;
		
		if($this->exception_for_event_id>0){
			//this is an exception
			
			$exception = $this->recurringEventException();
			if($exception){
				$recurrenceId =new Sabre_VObject_Element_DateTime("recurrence-id",$dateType);
				$dt = new DateTime();
				$dt->setTimestamp($exception->time);
				$recurrenceId->setDateTime($dt);
				$e->add($recurrenceId);
			}
		}
		
		
		$dtstart = new Sabre_VObject_Element_DateTime('dtstart',$dateType);
		$dtstart->setDateTime(GO_Base_Util_Date_DateTime::fromUnixtime($this->start_time));		
		//$dtstart->offsetUnset('VALUE');
		$e->add($dtstart);
		
		$dtend = new Sabre_VObject_Element_DateTime('dtend',$dateType);
		$dtend->setDateTime(GO_Base_Util_Date_DateTime::fromUnixtime($this->end_time));		
		//$dtend->offsetUnset('VALUE');
		$e->add($dtend);

		if(!empty($this->description))
			$e->description=$this->description;
		
		if(!empty($this->location))
			$e->location=$this->location;

		if(!empty($this->rrule)){
//			if($vcalendar1)
//			{
//				$r = new GO_Base_Util_Icalendar_Rrule();
//				$r->readIcalendarRruleString($this->start_time, $this->rrule);
//				
//				$e->rrule=str_replace('RRULE:','',$r->createVCalendarRrule());					
//			}else
//			{
				$e->rrule=str_replace('RRULE:','',$this->rrule);					
			//}
			$stmt = $this->exceptions(GO_Base_Db_FindParams::newInstance()->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('exception_event_id', 0)));
			while($exception = $stmt->fetch()){
				$exdate = new Sabre_VObject_Element_DateTime('exdate',Sabre_VObject_Element_DateTime::DATE);
				$exdate->setDateTime(GO_Base_Util_Date_DateTime::fromUnixtime($exception->time));		
				$e->add($exdate);
			}
		}
		
		$stmt = $this->participants();
		while($participant=$stmt->fetch()){
			if($participant->is_organizer){
				$p = new Sabre_VObject_Property('organizer','mailto:'.$participant->email);				
			}else
			{
				$p = new Sabre_VObject_Property('attendee','mailto:'.$participant->email);				
			}
			$p['CN']=$participant->name;
			$p['RSVP']="true";
			$p['PARTSTAT']=$this->_exportVObjectStatus($participant->status);
	
			//If this is a meeting REQUEST then we must send all participants.
			//For a CANCEL or REPLY we must send the organizer and the current user.
			if($participant->is_organizer || $method=='REQUEST' || $this->calendar->user_id==$participant->user_id)
				$e->add($p);
		}
		
		
		//todo alarms
		
		return $e;
	}
	


	/**
	 * Get vcalendar data for an *.ics file.
	 * 
	 * @param string $method REQUEST, REPLY or CANCEL
	 * @param int $forRecurrenceId 
	 * 
	 * Set this to a unix timestamp of the start of an occurence if it's an update
	 * for a particular recurrence date.
	 * 
	 * @return type 
	 */
	public function toICS($method='REQUEST') {		
		
		$c = new GO_Base_VObject_VCalendar();		
		$c->method=$method;
		
		$c->add(new GO_Base_VObject_VTimezone());
		
		$c->add($this->toVObject($method));		
		return $c->serialize();		
	}
	
	public function toVCS(){
		$c = new GO_Base_VObject_VCalendar();		
		$c->version='1.0';
		$c->add($this->toVObject('', true));		
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
	
	
	/**
	 * Import an event from a VObject 
	 * 
	 * @param Sabre_VObject_Component $vobject
	 * @param array $attributes Extra attributes to apply to the event. Raw values should be past. No input formatting is applied.
	 * @return GO_Calendar_Model_Event 
	 */
	public function importVObject(Sabre_VObject_Component $vobject, $attributes=array()){
		//$event = new GO_Calendar_Model_Event();
		
		$this->uuid = (string) $vobject->uid;
		$this->name = (string) $vobject->summary;
		$this->description = (string) $vobject->description;
		$this->start_time = $vobject->dtstart->getDateTime()->format('U');
		$this->end_time = $vobject->dtend->getDateTime()->format('U');
		
		if($vobject->rrule){			
			$rrule = new GO_Base_Util_Icalendar_Rrule();
			$rrule->readIcalendarRruleString($this->start_time, (string) $vobject->rrule);			
			$this->rrule = $rrule->createRrule();
			$this->repeat_end_time = $rrule->until;
		}
		
		if($vobject->dtstamp)
			$this->mtime=$vobject->dtstamp->getDateTime()->format('U');
		
		if($vobject->location)
			$this->location=(string) $vobject->location;
		
		//var_dump($vobject->status);
		if($vobject->status)
			$this->status=(string) $vobject->status;
		
		if($vobject->duration){
			$duration = GO_Base_VObject_Reader::parseDuration($vobject->duration);
			$this->end_time = $this->start_time+$duration;
		}
		
		$this->all_day_event = isset($vobject->dtstart['VALUE']) && $vobject->dtstart['VALUE']=='DATE';
		
		if($vobject->valarm){
			
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
					$this->exception_for_event_id=$recurringEvent->id;
					$this->exception_date=strtotime(date('Y-m-d', $this->start_time).' '.date('G:i', $recurringEvent->start_time));
									
//					$exception = new GO_Calendar_Model_Exception();
//					$exception->event_id=$this->exception_for_event_id;
//					$exception->time=$this->start_time;
//					//$exception->save();
				}
			}
			
		}

		$this->save();
		
		if(!empty($exception)){			
			//save the exception we found by recurrence-id
			$exception->exception_event_id=$this->id;
			$exception->save();
		}		
	
		if($vobject->organizer){
			$this->importVObjectAttendee($this, $vobject->organizer, true);
		}
		
		$attendees = $vobject->select('attendee');
		foreach($attendees as $attendee)
			$this->importVObjectAttendee($this, $attendee, false);

		if($vobject->exdate){
			$exDateTimes = $vobject->exdate->getDateTimes();
			foreach($exDateTimes as $dt){
				$this->addException($dt->format('U'));
			}
		}

		return $this;
	}	
	
	
	/**
	 * Will import an attendee from a VObject to a given event. If the attendee
	 * already exists it will update it.
	 * 
	 * @param GO_Calendar_Model_Event $event
	 * @param Sabre_VObject_Property $vattendee
	 * @param boolean $isOrganizer
	 * @return GO_Calendar_Model_Participant 
	 */
	public function importVObjectAttendee(GO_Calendar_Model_Event $event, Sabre_VObject_Property $vattendee, $isOrganizer=false){
			
		$attributes = $this->_vobjectAttendeeToParticipantAttributes($vattendee);
		
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
		}		
		
		$p->setAttributes($attributes);
		$p->save();
		
		return $p;
	}
	
	private function _vobjectAttendeeToParticipantAttributes(Sabre_VObject_Property $vattendee){
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
	

}