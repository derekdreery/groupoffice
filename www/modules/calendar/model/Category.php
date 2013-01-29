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
 * The GO_Calendar_Model_Category model
 *
 * @package GO.modules.Calendar
 * @version $Id: GO_Calendar_Model_Category.php 7607 2011-09-28 10:29:10Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 *
 * @property int $id
 * @property String $name
 * @property String $color eg. "EBF1E2"
 * @property int $calendar_id
 */

class GO_Calendar_Model_Category extends GO_Base_Db_ActiveRecord{

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Calendar_Model_Category
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	protected function init() {
		$this->columns['name']['unique']=array("calendar_id");
		return parent::init();
	}
	
	public function getLogMessage($action) {		
		return $this->name;
	}

	/**
	 * Enable this function if you want this model to check the acl's automatically.
	 */
	// public function aclField(){
	//	 return 'acl_id';	
	// }

	/**
	 * Returns the table name
	 */
	 public function tableName() {
		 return 'cal_categories';
	 }

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	 public function relations() {
		 return array();
	 }
	 
	 /**
	  * Find a category by name. It searches the global and calendar categories
	  * 
	  * @param int $calendar_id
	  * @param string $name
	  * @return GO_Calendar_Model_Category
	  */
	 public function findByName($calendar_id, $name){
		 
		 $findParams = GO_Base_Db_FindParams::newInstance()->single();
		 
		 $findParams->getCriteria()
						 ->addCondition('name', $name)
						 ->mergeWith(GO_Base_Db_FindCriteria::newInstance()
							->addCondition('calendar_id', $calendar_id)
							->addCondition('calendar_id', 0,'=','t',false)
										 );
		 
		 return GO_Calendar_Model_Category::model()->find($findParams);
	 }
	 
	 protected function afterSave($wasNew) {
		 
		 $c = new GO_Base_Db_Connection();		 
		 $c->createStatement()->update(
						 GO_Calendar_Model_Event::model()->tableName(), 
						 array('background'=>$this->color),
						 'category_id=:category_id',
						 array('category_id'=>$this->id));
		 
		 return parent::afterSave($wasNew);
	 }
}