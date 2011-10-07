<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 * @author Wesley Smits <wsmits@intermesh.nl>
 */
class GO_Calendar_Model_Event extends GO_Base_Db_ActiveRecord {

	/**
	 * The date where the exception needs to be created
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

	public function customfieldsModel() {
		return "GO_Calendar_Model_EventCustomFieldsRecord";
	}


	public function relations() {
		return array(
				'exceptionEvent'=>array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Event', 'field' => 'exception_for_event_id'),
				'calendar' => array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Calendar', 'field' => 'calendar_id'),
				'participants' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Participant', 'field' => 'event_id', 'delete' => true),
				'exceptions' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Exception', 'field' => 'event_id', 'delete' => true)
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
	 * @return DateInterval 
	 */
	public function getDiff() {
		$startDateTime = new GO_Base_Util_Date_DateTime(date('c', $this->start_time));
		$endDateTime = new GO_Base_Util_Date_DateTime(date('c', $this->end_time));
		return $startDateTime->diff($endDateTime, true);
	}

	/**
	 * Add an Exception for the Event if it is recurring
	 * 
	 * @param Unix Timestamp $date The date where the exception belongs to
	 * @param Int $for_event_id The event id of the event where the exception belongs to
	 */
	public function addException($date) {
		$exception = new GO_Calendar_Model_Exception();
		$exception->event_id = $this->id;
		$exception->time = $date; // Needs to be a unix timestamp
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
		$endTime->add($diff);
		$att['end_time'] = $endTime->format('U');
		
		return $this->duplicate($att, false);
	}

	protected function afterSave($wasNew) {
		
		//add exception model for the original recurring event
		if ($wasNew && $this->exception_for_event_id > 0) {
			
			$newExeptionEvent = GO_Calendar_Model_Event::model()->findByPk($this->exception_for_event_id);
			$newExeptionEvent->addException($this->exception_date);
		}

		if (!$this->uuid) {
			$this->uuid = GO_Base_Util_UUID::create('event', $this->id);
			$this->save();
		}

		return parent::afterSave($wasNew);
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
	public function findForPeriod($findParams, $periodStartTime, $periodEndTime, $onlyBusyEvents=false) {

		if (!$findParams)
			$findParams = GO_Base_Db_FindParams::newInstance();

		$findParams
						->order('start_time', 'ASC');

		$findParams->getCriteria()
						->addModel(GO_Calendar_Model_Event::model())
						->addCondition('start_time', $periodEndTime, '<')
						->addCondition('end_time', $periodStartTime, '>');

		if ($onlyBusyEvents)
			$findParams->getCriteria()->addCondition('busy', 1);

		$stmt = $this->find($findParams);


		$this->_calculatedEvents = array();

		while ($event = $stmt->fetch()) {
			$this->_calculateRecurrences($event, $periodStartTime, $periodEndTime);
		}

		return array_values($this->_calculatedEvents);
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
				$endTime->add($diff);
				$origEventAttr['end_time'] = GO_Base_Util_Date::get_timestamp($endTime->format('U'));

				$this->_calculatedEvents[$occurenceStartTime . '-' . $origEventAttr['id']] = $origEventAttr;
			}

			ksort($this->_calculatedEvents);
		}
	}

	/**
	 * Find an event that belongs to a group of participant events. They all share the same uuid field.
	 * 
	 * @param int $calendar_id
	 * @param string $uuid
	 * @return GO_Calendar_Model_Event 
	 */
	public function findParticipantEvent($calendar_id, $uuid) {
		return GO_Calendar_Model_Event::model()->findSingleByAttributes(array('uuid' => $event->uuid, 'calendar_id' => $calendar->id));
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
	


	public function toICS() {
		
		//require vendor lib SabreDav vobject
		require_once(GO::config()->root_path.'go/vendor/SabreDAV/lib/Sabre/VObject/includes.php');
		
		$c = new Sabre_VObject_Component('vcalendar');
		$c->version='2.0';
		$c->prodid='-//Intermesh//NONSGML Group-Office//EN';
		$c->calscale='GREGORIAN';
		$c->method='REQUEST';
		
		$e=new Sabre_VObject_Component('vevent');
		$e->uuid=$this->uuid;
		
		$dtstart = new Sabre_VObject_Element_DateTime('dtstamp');
		$dtstart->setDateTime(new DateTime(), Sabre_VObject_Element_DateTime::UTC);		
		$e->add($dtstart);
		
		$dtstart = new Sabre_VObject_Element_DateTime('LAST-MODIFIED');
		$dtstart->setDateTime(new DateTime(), Sabre_VObject_Element_DateTime::UTC);		
		$e->add($dtstart);
		
    $e->summary = $this->name;
		
		$dateType = $this->all_day_event ? Sabre_VObject_Element_DateTime::DATE : Sabre_VObject_Element_DateTime::LOCALTZ;
		
		if($this->exceptionEvent){			
			$recurrenceId =new Sabre_VObject_Element_DateTime("recurrence-id",$dateType);
			$recurrenceId->setDateTime(new DateTime(date('c',$this->exceptionEvent->start_time)));
			$e->add($recurrenceId);
		}
		
		
		$dtstart = new Sabre_VObject_Element_DateTime('dtstart',$dateType);
		$dtstart->setDateTime(GO_Base_Util_Date_DateTime::fromUnixtime($this->start_time));		
		$e->add($dtstart);
		
		$dtend = new Sabre_VObject_Element_DateTime('dtend',$dateType);
		$dtend->setDateTime(GO_Base_Util_Date_DateTime::fromUnixtime($this->end_time));		
		$e->add($dtend);
		
		$e->description=$this->description;
		$e->location=$this->location;
		
		//todo exceptions
		if(!empty($this->rrule)){
			$e->rrule=$this->rrule;					
			$stmt = $this->exceptions();
			while($exception = $stmt->fetch()){
				$exdate = new Sabre_VObject_Element_DateTime('exdate',Sabre_VObject_Element_DateTime::DATE);
				$exdate->setDateTime(GO_Base_Util_Date_DateTime::fromUnixtime($exception->time));		
				$e->add($exdate);
			}
		}
		
    $c->add($e);
		
		return $c->serialize();
		
	}

}