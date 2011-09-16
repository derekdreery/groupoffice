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
		
		$this->columns['start_time']['gotype']='unixtimestamp';
		$this->columns['end_time']['gotype']='unixtimestamp';
		$this->columns['repeat_end_time']['gotype']='unixtimestamp';
		
		parent::init();
	}
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Calendar_Model_Event 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function aclField(){
		return 'calendar.acl_id';	
	}
	
	public function tableName(){
		return 'cal_events';
	}
	
	public function hasFiles(){
		return true;
	}
	
//	public function customfieldsModel() {
//		
//		return "GO_Addressbook_Model_ContactCustomFieldsRecord";
//	}

	public function relations(){
		return array(
				'calendar' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Calendar_Model_Calendar', 'field'=>'calendar_id')
		);
	}


	protected function getCacheAttributes() {
		return array(
				'name' => $this->name
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
	public function getDiff(){
		$startDateTime = new GO_Base_Util_Date_DateTime(date('c',$this->start_time));
		$endDateTime= new GO_Base_Util_Date_DateTime(date('c',$this->end_time));
		return $startDateTime->diff($endDateTime, true); 
	}
	
	/**
	 * Add an Exception for the Event if it is recurring
	 * 
	 * @param Unix Timestamp $date The date where the exception belongs to
	 * @param Int $for_event_id The event id of the event where the exception belongs to
	 */
	public function addException($date, $for_event_id){
		
		
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
	public function becomeException($exceptionDate){
		
		$this->rrule='';
		$this->exception_for_event_id=$this->id;
		$this->exception_date = $exceptionDate;
		
		$this->id=0;
		$this->setIsNew(true);
	
		$diff = $this->getDiff();
		
		$d = date('Y-m-d', $exceptionDate);
		$t = date('G:i', $this->start_time);
		
		$this->start_time=strtotime($d.' '.$t);
		
		$endTime = new GO_Base_Util_Date_DateTime(date('c', $this->start_time));
		$endTime->add($diff);		
		$this->end_time = $endTime->format('U');
	}
		
	protected function afterSave($wasNew) {
		//add exception model for the original recurring event
		if($wasNew && $this->exception_for_event_id>0){
			$newExeptionEvent = GO_Calendar_Model_Event::model()->findByPk($this->exception_for_event_id);
			$newExeptionEvent->addException($this->exception_date,$this->exception_for_event_id);			
		}
		
		return parent::afterSave($wasNew);
	}
}