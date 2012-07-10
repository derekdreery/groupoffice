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
	
	
	private $_initials = array();
	private $_calendarNames = array();
	
	private $_isMerged = false;
	
	private $_backgroundColor = '';
	
	public $displayId = 0;
	
	private $_displayName = '';
	
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
		$this->_backgroundColor = $event->background;
		$this->_displayName = $event->name;
		
		$this->_calendar = $this->_event->calendar;	
		
		$this->_initials[] = $event->user->getShortName();
		$this->_calendarNames[] = $this->_calendar->name;
	}
	
	public function setBackgroundColor($color){
		$this->_backgroundColor = $color;
	}
	
	public function getUuid(){
		return $this->_event->uuid;
	}
	
	public function getResponseData(){
		
		$dayString = GO::t('full_days');
		
		$response = $this->_event->getAttributes('formatted');

		if($this->isAllDay()){
			$response['time'] =  $event->getFormattedTime();
		} else {
			if (date(GO::user()->date_format, $this->getAlternateStartTime()) != date(GO::user()->date_format, $this->getAlternateEndTime()))
				$response['time'] =  $this->getFormattedTime();
			else
				$response['time'] =  $this->getFormattedTime();
		}
		
		$response['status'] = $this->_event->status;
		$response['username'] = $this->_event->user->getName();
		
		if($this->_isMerged){
			$response['name'] = $response['name'] .' ('.implode(',',$this->_initials).')';
			$response['calendar_name'] = implode('; ',$this->_calendarNames);
			unset($response['status']); // unset this, it is not relevant to show this in merge view
			unset($response['username']); // unset this, it is not relevant to show this in merge view.
		}
		
		$response['id'] = $this->displayId;
		$response['background'] = $this->_backgroundColor;
		$response['start_time'] = date('Y-m-d H:i', $this->getAlternateStartTime());
		$response['end_time'] = date('Y-m-d H:i',  $this->getAlternateEndTime());	
		$response['ctime'] = date('Y-m-d H:i',  $this->_event->ctime);
		$response['event_id'] = $this->_event->id;
		$response['has_other_participants'] = $this->hasOtherParticipants();
		$response['link_count'] = $this->getLinkCount();
		
		$response['description'] = nl2br(htmlspecialchars(GO_Base_Util_String::cut_string($this->_event->description, 800), ENT_COMPAT, 'UTF-8'));
		$response['private'] = $this->isPrivate();
		$response['status_color'] = $this->_event->getStatusColor();
		
		$response['repeats'] = $this->isRepeating();
		$response['all_day_event'] = $this->isAllDay();
		$response['day'] = $dayString[date('w', ($this->_event->start_time))].' '.GO_Base_Util_Date::get_timestamp($this->_event->start_time,false);  // date(implode(GO::user()->date_separator,str_split(GO::user()->date_format,1)), ($eventModel->start_time));
		$response['read_only'] = $this->isReadOnly();
		$response['model_name'] = $this->_event->className();
		
		
		$duration = $this->getDurationInMinutes();

		if($duration >= 60){
			$durationHours = floor($duration / 60);
			$durationRestMinutes = $duration % 60;
			$response['duration'] = $durationHours.' '.GO::t('hours').', '.$durationRestMinutes.' '.GO::t('mins');
		} else {
			$response['duration'] = $duration.'m';
		}
		
		return $response;
	}
	
	public function getName(){
		return $this->_displayName;
	}
	
	public function mergeWithEvent($event){
		
		$this->_isMerged = true;
		$this->_initials[] = $event->getEvent()->user->getShortName();
		$this->_calendarNames[] = $event->getCalendar()->name;
		$this->_backgroundColor = 'FFFFFF';
		
		//append start_time for recurring events.
////		$merge_index = $current_event['uuid'].'-'.$current_event['start_time'];
////		
////		
////		if (array_key_exists($merge_index,$uuid_array)) {
////			
////			$uuid_array[$merge_index][] = $event_nr;
////			if (count($uuid_array[$merge_index])==2) {
////				$merged_event_nr = $uuid_array[$merge_index][0];
////				
////				$chosen_events[$merged_event_nr]['background'] = 'FFFFFF';
////				$chosen_events[$merged_event_nr]['username'] = '';//$lang['calendar']['non_selected'];
////				
////				$name_exploded = explode('(',$chosen_events[$merged_event_nr]['name']);
////				if (count($name_exploded)>1) array_pop($name_exploded);
////				$chosen_events[$merged_event_nr]['name'] = implode('(',$name_exploded);
////				$chosen_events[$merged_event_nr]['name'] .= ' ('.String::get_first_letters($calendar_names[$chosen_events[$merged_event_nr]['calendar_id']]).')';
////			}
////			if (count($uuid_array[$merge_index])>=2) {
////				$merged_event_nr = $uuid_array[$merge_index][0];
////				
////				$chosen_events[$merged_event_nr]['calendar_name'] .= '; '.$calendar_names[$current_event['calendar_id']];
////				$chosen_events[$merged_event_nr]['name'] = substr($chosen_events[$merged_event_nr]['name'],0,-1);
////				$chosen_events[$merged_event_nr]['name'] .= ','.String::get_first_letters($calendar_names[$current_event['calendar_id']]).')';
////				//$chosen_events[$merged_event_nr]['name'] .= ', '.$participating_calendar['name'];
////				//if ($current_event['invitation_uuid']=='') {
////					//$chosen_events[$merged_event_nr]['username'] = $GO_USERS->get_user_realname($current_event['user_id']);
////					//$chosen_events[$merged_event_nr]['num_participants']++;
////				//}
////				return true;
////			}
////		} else {
////			$uuid_array[$merge_index] = array($event_nr);
////		}
//		
//		return $response;
		
		
	}

	/**
	 * Get the start time of the recurring event in the selected period
	 * 
	 * @return int Unix timestamp 
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
	 * @return int Unix timestamp 
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
	 * @param int $time  Unix timestamp
	 */
	public function setAlternateStartTime($time){

		$this->_alternateStartTime = $time;
		
	}
	
	/**
	 * Set the end time of the recurring event in the selected period
	 * 
	 * @param int $time  Unix timestamp
	 */
	public function setAlternateEndTime($time){
		$this->_alternateEndTime = $time;
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