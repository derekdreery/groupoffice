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
 * The GO_Tasks_Model_Category model
 *
 * @package GO.modules.Tasks
 * @version $Id: GO_Tasks_Model_Category.php 7607 2011-09-20 10:09:35Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 *
 * @property int $id
 * @property String $name
 * @property int $user_id
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
	
	protected function init() {
		$this->columns['name']['unique']=array("user_id");
		return parent::init();
	}

	public function relations() {
		return array(
				'tasks' => array('type' => self::HAS_MANY, 'model' => 'GO_Tasks_Model_Task', 'field' => 'category_id', 'delete' => true),
				);
	}
}