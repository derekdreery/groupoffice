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
 * The GO_Sites_Model_Page model
 *
 * @package GO.modules.Tasks
 * @version $Id: GO_Tasks_Model_Tasklist.php 7607 2011-09-20 10:07:07Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 *
 */

class GO_Sites_Model_Page extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Sites_Model_Page 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function tableName() {
		return 'si_pages';
	}
	
	public function relations() {
		return array(
				'pages' => array('type' => self::HAS_MANY, 'model' => 'GO_Sites_Model_Page', 'field' => 'parent_id', 'delete' => true),
				'site'=>array('type'=>self::BELONGS_TO, 'model'=>"GO_Sites_Model_Site", 'field'=>'site_id')
				);
	}	
	
	public function getParent(){
		if($this->parent_id==0){
			return $this->site();
		}else
		{
			return GO_Sites_Model_Page::model()->findByPk($this->parent_id);
		}
	}	
	
	public function getUrl(){
		
		return GO::url('sites/site/index', 'p='.urlencode($this->path));
		
	}
}