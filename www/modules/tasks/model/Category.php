<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Category.php 7607 2011-09-01 11:17:42Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

/**
 * 
 * The Category model
 * 
 * @param int $id
 * @param String $name
 * @param int $user_id
 *  
 */
class GO_Tasks_Model_Category extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Tasks_Model_Category 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function tableName() {
		return 'ta_categories';
	}

	public function relations() {
		return array(
				'tasks' => array('type' => self::HAS_MANY, 'model' => 'GO_Tasks_Model_Task', 'field' => 'category_id', 'delete' => true),
				'user' => array('type' => self::HAS_ONE, 'model' => 'GO_Base_Model_User', 'field' => 'user_id', 'delete' => false)
				);
	}
}