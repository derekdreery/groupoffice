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
 */
class GO_Calendar_Model_Calendar extends GO_Base_Db_ActiveRecord {

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
		return "GO_Calendar_Model_CalendarCustomFieldsRecord";
	}

	public function relations() {
		return array(
			'group' => array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Group', 'field' => 'group_id'),
			'events' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Event', 'field' => 'calendar_id', 'delete' => true),
			'tasklist' => array('type' => self::BELONGS_TO, 'model' => 'GO_Tasks_Model_Tasklist', 'field' => 'tasklist_id')		
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
}