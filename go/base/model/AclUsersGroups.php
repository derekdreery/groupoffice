<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * 
 * The Group model
 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits <wsmits@intermesh.nl>
 * @package GO.base.model
 * 
 * @property int $acl_id
 * @property int $user_id
 * @property int $group_id
 * @property int $level {@see GO_Base_Model_Acl::READ_PERMISSION etc}
 */
class GO_Base_Model_AclUsersGroups extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Base_Model_AclUsersGroups 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName() {
		return 'go_acl';
	}
	
	/**
	 * The ACL record itself never has an ACL field so always return false
	 * @return boolean
	 */
	public function aclField() {
	  return false;
	}
  
  public function primaryKey() {
    return array('acl_id','user_id','group_id');
  }
	
	public function defaultAttributes() {
		$attr = parent::defaultAttributes();
		$attr['user_id'] = 0;
		return $attr;
	}
	
	public function relations() {
		return array('aclItem'=>array(
			"type"=>self::BELONGS_TO,
			"model"=>"GO_Base_Model_Acl",
			"field"=>'acl_id'
		));
	}
	
	protected function afterSave($wasNew) {
		
		$this->aclItem->touch();
		
		return parent::afterSave($wasNew);
	}
}