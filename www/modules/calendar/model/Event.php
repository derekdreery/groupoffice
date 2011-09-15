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
	
	public function beforeDelete() {
		
		if($this->go_user_id>0)			
			throw new Exception("This contact belongs to a user account. Please delete this account first.");
		
		return parent::beforeDelete();
	}
	
	/**
	 * Get the date interval for the event.
	 * 
	 * @return DateInterval 
	 */
	public function getDiff(){
		$startDateTime = new GO_Base_Util_DateTime(date('c',$this->start_time));
		$endDateTime= new GO_Base_Util_DateTime(date('c',$this->end_time));
		return $startDateTime->diff($endDateTime, true); //todo find out if this returns 40 days and not 1 month and 10 days.
		
	}
	
	
	public function addException($date){
		
	}

}