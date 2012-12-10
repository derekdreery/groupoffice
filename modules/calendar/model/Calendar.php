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
 */

/**
 * 
 * The Category model
 * 
 * @property String $name The name of the category
 * @property int $files_folder_id
 * @property int $tasklist_id
 * @property int $project_id
 * @property string $comment
 * @property boolean $show_bdays
 * @property boolean $shared_acl
 * @property boolean $public
 * @property int $time_interval
 * @property string $background
 * @property int $end_hour
 * @property int $start_hour
 * @property int $acl_id
 * @property int $user_id
 * @property int $group_id
 * @property int $acl_write
 */
class GO_Calendar_Model_Calendar extends GO_Base_Model_AbstractUserDefaultModel {

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
	 * Remove all events
	 */
	public function truncate(){
		$events = $this->events;
		
		foreach($events as $event){
			$event->delete();
		}
	}
}