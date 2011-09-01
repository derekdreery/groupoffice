<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Settings.php 7607 2011-09-01 11:17:42Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

/**
 * 
 * The Settings model
 * 
 * @param int $user_id
 * @param int $reminder_days
 * @param String $reminder_time
 * @param Boolean $remind
 * @param int $default_tasklist_id
 *  
 */
class GO_Tasks_Model_Settings extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Tasks_Model_Settings
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function tableName() {
		return 'ta_settings';
	}

	public function relations() {
		return array(
				'tasklist' => array('type' => self::HAS_MANY, 'model' => 'GO_Tasks_Model_Tasklist', 'field' => 'default_tasklist_id', 'delete' => false),
				'user' => array('type' => self::HAS_ONE, 'model' => 'GO_Base_Model_User', 'field' => 'user_id', 'delete' => false)
				);
	}
}