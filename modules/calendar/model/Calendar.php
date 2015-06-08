<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @property int $group_id
 * @property int $user_id
 * @property int $acl_id
 * @property string $name
 * @property int $start_hour
 * @property int $end_hour
 * @property string $background
 * @property int $time_interval
 * @property boolean $public
 * @property boolean $shared_acl
 * @property boolean $show_bdays
 * @property boolean $show_completed_tasks
 * @property string $comment
 * @property int $project_id
 * @property int $tasklist_id
 * @property int $files_folder_id
 * @property boolean $show_holidays
 * @property boolean $enable_ics_import
 * @property string $ics_import_url
 */

class GO_Calendar_Model_Calendar extends GO_Base_Model_AbstractUserDefaultModel {
	
	/**
	 * The default color to display this calendar in the view
	 * 
	 * @var string 
	 */
	public $displayColor = false;
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Calendar_Model_Calendar 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function aclField() {
		return 'acl_id';
	}

	public function tableName() {
		return 'cal_calendars';
	}
	
	public function hasFiles(){
		return true;
	}
	
	public function customfieldsModel() {
		return "GO_Calendar_Customfields_Model_Calendar";
	}

	public function relations() {
		return array(
			'group' => array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Group', 'field' => 'group_id'),
			'events' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Event', 'field' => 'calendar_id', 'delete' => true),
			'categories' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Category', 'field' => 'calendar_id', 'delete' => true),
			'tasklist' => array('type' => self::BELONGS_TO, 'model' => 'GO_Tasks_Model_Tasklist', 'field' => 'tasklist_id'),
			'visible_tasklists' => array('type' => self::MANY_MANY, 'model' => 'GO_Tasks_Model_Tasklist', 'linkModel'=>'GO_Calendar_Model_CalendarTasklist', 'field'=>'calendar_id', 'linksTable' => 'cal_visible_tasklists', 'remoteField'=>'tasklist'),
			);
	}
	
	public function findDefault($userId){
		$findParams = GO_Base_Db_FindParams::newInstance()
						->single()
						->join("cal_settings", GO_Base_Db_FindCriteria::newInstance()
										->addCondition('id', 's.calendar_id','=','t',true,true)
										->addCondition('user_id', $userId,'=','s'),
										's');
		
		return $this->find($findParams);
	}
	
	
	public function settingsModelName() {
		return "GO_Calendar_Model_Settings";
	}
	
	public function settingsPkAttribute() {
		return 'calendar_id';
	}	
	
	/**
	 * Get the color of this calendar from the Calendar_user_Color table.
	 * 
	 * @param int $userId
	 * @return string The color or false if no color is found 
	 */
	public function getColor($userId){
		$userColor = GO_Calendar_Model_CalendarUserColor::model()->findByPk(array('calendar_id'=>$this->id,'user_id'=>$userId));

		if($userColor)
			return $userColor->color;
		else
			return false;
	}
	
	/**
	 * Get's a unique URI for the calendar. This is used by CalDAV
	 * 
	 * @return string
	 */
	public function getUri(){
		return preg_replace('/[^\w-]*/', '', (strtolower(str_replace(' ', '-', $this->name)))).'-'.$this->id;
	}

	/**
	 * Check if the current user may create events in this calendar. Here we deviate
	 * from the standard if the "freebusypermissions" module is installed. When a 
	 * user has access to the freebusy info he may also schedule a meeting in the user's calendar. 
	 * 
	 * @return boolean
	 */
	public function userHasCreatePermission(){
//		if(GO_Base_Model_Acl::hasPermission($this->getPermissionLevel(),GO_Base_Model_Acl::CREATE_PERMISSION)){
//			return true;
//		}else 
		if(GO::modules()->isInstalled('freebusypermissions')){
			return GO_Freebusypermissions_FreebusypermissionsModule::hasFreebusyAccess(GO::user()->id, $this->user_id);
		}  else {
			return true;
		}
	}
	
	protected function afterSave($wasNew) {
		
		if($wasNew && $this->group){
			$stmt = $this->group->admins;
		 
		 foreach($stmt as $user){
			 if($user->user_id!=$this->user_id)//the owner has already been added automatically with manage permission
				$this->acl->addUser($user->user_id, GO_Base_Model_Acl::DELETE_PERMISSION);
		 }
		}
		
		$file = new GO_Base_Fs_File($this->getPublicIcsPath());
		
		if(!$this->public){
			if($file->exists())
				$file->delete ();
		} else {
			if(!$file->exists())
				$file->touch(true);
			
			$file->putContents($this->toVObject());
		}
		
		return parent::afterSave($wasNew);
	}

	/**
	 * Remove all events
	 */
	public function truncate(){
		$events = $this->events;
		
		foreach($events as $event){
			$event->delete();
		}
	}
	
	
	/**
	 * 
	 * @param \GO_Base_Model_User $user
	 * @return GO_Tasks_Model_Tasklist
	 */
	public function getDefault(\GO_Base_Model_User $user, &$createdNew=false) {
		$default = parent::getDefault($user, $createdNew);
	
		if($createdNew){
			$pt = new GO_Calendar_Model_PortletCalendar();
			$pt->user_id=$user->id;
			$pt->calendar_id=$default->id;
			$pt->save();
		}
	
		return $default;
	}
	
	/**
	 * Get the Vobject of this calendar
	 * 
	 * @return string
	 */
	public function toVObject(){

		//$stmt = $this->events(GO_Base_Db_FindParams::newInstance()->select("t.*"));
		$findParams = GO_Base_Db_FindParams::newInstance()->select("t.*");
		$findParams->getCriteria()->addCondition("calendar_id", $this->id);
	
		$stmt = GO_Calendar_Model_Event::model()->findForPeriod($findParams, GO_Base_Util_Date::date_add(time(), 0, -1));
		
		$string = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//Intermesh//NONSGML ".GO::config()->product_name." ".GO::config()->version."//EN\r\n";

			$t = new GO_Base_VObject_VTimezone();
			$string .= $t->serialize();

			while($event = $stmt->fetch()){
				$v = $event->toVObject();
				$string .= $v->serialize();
			}

			$string .= "END:VCALENDAR\r\n";
			
			return $string;
	}
	
	public function getEventsForPeriod($start, $end) {
		return GO_Calendar_Model_Event::model()->findCalculatedForPeriod(
			GO_Base_Db_FindParams::newInstance()->criteria(
				GO_Base_Db_FindCriteria::newInstance()->addCondition('calendar_id', $this->id)
			)->select(),
			$start, 
			$end
		);
	}
	
	/**
	 * Get the url to the published ICS file.
	 * @return string
	 */
	public function getPublicIcsUrl(){
		return GO::config()->full_url.'public/calendar/'.$this->id.'/calendar.ics';
	}
	
	/**
	 * Get the url to the published ICS file.
	 * @return string
	 */
	public function getPublicIcsPath(){
		return GO::config()->file_storage_path.'public/calendar/'.$this->id.'/calendar.ics';
	}
}