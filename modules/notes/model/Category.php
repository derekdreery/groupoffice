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
 * @property int $acl_id
 * @property int $user_id
 */
class GO_Notes_Model_Category extends GO_Base_Model_AbstractUserDefaultModel {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Notes_Model_Category 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function aclField() {
		return 'acl_id';
	}

	public function tableName() {
		return 'no_categories';
	}
	
	public function hasFiles(){
		return true;
	}

	public function relations() {
		return array(
				'notes' => array('type' => self::HAS_MANY, 'model' => 'GO_Notes_Model_Note', 'field' => 'category_id', 'delete' => true)		);
	}
	
	protected function init() {
		$this->columns['name']['unique']=true;
		return parent::init();
	}
}