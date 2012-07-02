<?php

// An event model that is used by the calendar view to show the correct event data.
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * 
 * @property GO_Calendar_Model_Event $_event
 * @property GO_Calendar_Model_Calendar $_calendar
 * @property string $_startTime
 * @property string $_endTime
 * @property string $_alternateEndTime
 * @property string $_alternateStartTime
 * 
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */
class GO_Calendar_Model_LocalEvent extends GO_Base_Model {
	
	/**
	 *
	 * @var GO_Calendar_Model_Event 
	 */
	private $_event;
	
	/**
	 *
	 * @var GO_Calendar_Model_Calendar 
	 */
	private $_calendar;
	
	/**
	 *
	 * @var string 
	 */
	private $_startTime;
	
	/**
	 *
	 * @var string 
	 */
	private $_endTime;

	/**
	 * The end time of an recurring event in the current period
	 * 
	 * @var string 
	 */
	private $_alternateEndTime;
	
	/**
	 * The start time of an recurring event in the current period
	 * 
	 * @var string 
	 */
	private $_alternateStartTime;
	
	/**
	 * Constructor
	 * 
	 * @param GO_Calendar_Model_Event $event
	 * @param string $periodStartTime
	 * @param string $periodEndTime 
	 */
	public function GO_Calendar_Model_LocalEvent(GO_Calendar_Model_Event $event, $periodStartTime, $periodEndTime){
		$this->_event = $event;
		$this->_startTime = $periodStartTime;
		$this->_endTime = $periodEndTime;
		
		$this->_calendar = $this->_event->calendar;	
	}
	
	/**
	 * Get the start time of the recurring event in the selected period
	 * 
	 * @return string TIMESTAMP 
	 */
	public function getAlternateStartTime(){
		if(empty($this->_alternateStartTime))
			return $this->_event->start_time;
		else
			return $this->_alternateStartTime;
	}
	
	/**
	 * Get the end time of the recurring event in the selected period
	 * 
	 * @return string TIMESTAMP 
	 */
	public function getAlternateEndTime(){
		if(empty($this->_alternateEndTime))
			return $this->_event->end_time;
		else
			return $this->_alternateEndTime;
	}
	
	/**
	 * Set the start time of the recurring event in the selected period
	 * 
	 * @param string $time  TIMESTAMP
	 */
	public function setAlternateStartTime($time){
		$this->_alternateStartTime = strtotime($time);
	}
	
	/**
	 * Set the end time of the recurring event in the selected period
	 * 
	 * @param string $time  TIMESTAMP
	 */
	public function setAlternateEndTime($time){
		$this->_alternateEndTime = strtotime($time);
	}
	
	/**
	 * Get the period start time
	 * 
	 * @return string 
	 */
	public function getPeriodStartTime(){
		return $this->_startTime;
	}
	
	/**
	 * Get the period end time
	 * 
	 * @return string 
	 */
	public function getPeriodEndTime(){
		return $this->_endTime;
	}
	
	
	/**
	 *
	 * @return GO_Calendar_Model_Event 
	 */
	public function getEvent(){
		return $this->_event;
	}
	
	/**
	 *
	 * @return GO_Calendar_Model_Calendar 
	 */
	public function getCalendar(){
		return $this->_calendar;
	}
	
	/**
	 * Get the number of links that this events has
	 * 
	 * @return int 
	 */
	public function getLinkCount(){
		return $this->_event->countLinks();
	}
	
	/**
	 * Get the formatted starting date of this event
	 * 
	 * @return string 
	 */
	public function getFormattedDate(){
		return date(GO::user()->date_format,$this->_event->start_time);
	}
	
	/**
	 * Get the formatted starting date and time of this event
	 * 
	 * @return string 
	 */
	public function getFormattedDateAndTime(){
		return date(GO::user()->date_format.' '.GO::user()->time_format,$this->_event->start_time);
	}
	
	/**
	 * Get the formatted starting time of this event
	 * 
	 * @return string 
	 */
	public function getFormattedTime(){
		return date(GO::user()->time_format,$this->_event->start_time);
	}
	
	/**
	 * Get the day this event starts on.
	 * 
	 * @return string 
	 */
	public function getDay(){
		$dayString = GO::t('full_days','common');
		return $dayString[date('w',$this->_event->start_time)];
	}
	
	/**
	 * Get the time of the event duration in minutes
	 * 
	 * @return int 
	 */
	public function getDurationInMinutes(){
		
		$durationMinutes = ($this->_event->end_time-$this->_event->start_time)/60;

		return $durationMinutes;
	}

	/**
	 * Is this an all day event
	 * 
	 * @return boolean 
	 */
	public function isAllDay(){
		return $this->_event->all_day_event;
	}
	
	/**
	 * Is this a repeating event
	 * 
	 * @return boolean 
	 */
	public function isRepeating(){
		return !empty($this->_event->rrule);
	}
	
	/**
	 * Is this a private event
	 * 
	 * @return boolean 
	 */
	public function isPrivate(){
		return $this->_event->private && (GO::user()->id != $this->_event->user_id);
	}
	
	/**
	 * Is this a read only event
	 * 
	 * @return boolean 
	 */
	public function isReadOnly(){
		return $this->_event->read_only || 
						$this->isPrivate() && GO::user()->id != $this->_event->user_id || 
						$this->_event->permissionLevel < GO_Base_Model_Acl::WRITE_PERMISSION;
	}
	
	/**
	 * Does this event have more participants
	 * 
	 * @return boolean 
	 */
	public function hasOtherParticipants(){
		return $this->_event->hasOtherParticipants();
	}

}