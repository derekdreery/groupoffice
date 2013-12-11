<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */
 
/**
 * The GO_Calendar_Model_CalendarTasklist model
 *
 * @package GO.modules.Calendar
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 *
 * @property int $calendar_id
 * @property int $tasklist_id
 */

class GO_Calendar_Model_CalendarTasklist extends \GO\Base\Db\ActiveRecord{

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Calendar_Model_CalendarTasklist
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}


	/**
	 * Enable this function if you want this model to check the acl's automatically.
	 */
	// public function aclField(){
	//	 return 'acl_id';	
	// }

	public function primaryKey() {
		
		return array('calendar_id','tasklist_id');
	}
	
	/**
	 * Returns the table name
	 */
	 public function tableName() {
		 return 'cal_visible_tasklists';
	 }

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	 public function relations() {
		 return array();
	 }
}