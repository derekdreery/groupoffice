<?php
/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @package GO.modules.calendar.model
 * @version $Id: example.php 7607 20120101Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
 
/**
 * The GO_Calendar_Model_Settings model
 *
 * @package GO.modules.calendar.model
 * @property int $calendar_id
 * @property string $background
 * @property int $reminder
 * @property int $user_id
 */

class GO_Calendar_Model_Settings extends GO_Base_Model_AbstractUserDefaultModel{
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Calendar_Model_Settings 
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'cal_settings';
	}
	
	public function primaryKey() {
		return 'user_id';
	}
	
	public function relations() {
		return array(
				'calendar' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Calendar_Model_Calendar', 'field'=>'calendar_id')
		);
	}
	
}