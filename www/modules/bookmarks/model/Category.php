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
 * @package GO.modules.bookmarks.model
 * @version $Id: example.php 7607 20120101Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
 
/**
 * The GO_Bookmarks_Model_Category model
 *
 * @package GO.modules.bookmarks.model
 * @property string $name
 * @property int $acl_id
 * @property int $user_id
 * @property int $id
 */

class GO_Bookmarks_Model_Category extends GO_Base_Db_ActiveRecord {
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Presidents_Model_President 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName() {
		return 'bm_categories';
	}
	public function aclField() {
		return 'acl_id';
	}
	
	public function primaryKey() {
		return 'id';
	}
	
	protected function init() {
		$this->columns['name']['unique']=array('user_id');
		return parent::init();
	}
	
	protected function getPermissionLevelForNewModel(){
		return \GO_Base_Model_Acl::DELETE_PERMISSION;
	}
}