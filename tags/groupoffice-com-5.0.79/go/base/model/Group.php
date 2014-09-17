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
 * @property int $id
 * @property String $name
 * @property int $user_id
 * @property int $acl_id
 * @property bool $admin_only Obsolete!
 * 
 * @method GO_Base_Model_User users
 *
 */
class GO_Base_Model_Group extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Base_Model_Group 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	protected function init() {		
		$this->columns['name']['unique']=true;
		return parent::init();
	}
	
	protected function getLocalizedName() {
		return GO::t('userGroup');
	}
	
  public function aclField(){
		return 'acl_id';	
	}
  
	public function tableName() {
		return 'go_groups';
	}
	
	protected function beforeDelete() {
		if($this->id==GO::config()->group_root){
			throw new Exception(GO::t('noDeleteAdmins','groups'));
		}	
		if($this->id==GO::config()->group_everyone){
			throw new Exception(GO::t('noDeleteEveryone','groups'));
		}
		return parent::beforeDelete();
	}
	
	protected function afterSave($wasNew) {
		
		if($wasNew){
			$this->acl->addGroup($this->id, GO_Base_Model_Acl::READ_PERMISSION);
		}
		
		return parent::afterSave($wasNew);
	}
  
//  public function searchFields() {
//    return array(
//      'concat(first_name,last_name)',
//      'username'
//      );
//  }
  
  public function relations() {
    
    return array(
				'users' => array('type'=>self::MANY_MANY, 'model'=>'GO_Base_Model_User', 'field'=>'group_id', 'linkModel' => 'GO_Base_Model_UserGroup'),
		);
  }
  
  public function addUser($user_id){
		if(!$this->hasUser($user_id)){
			$userGroup = new GO_Base_Model_UserGroup();
			$userGroup->group_id = $this->id;
			$userGroup->user_id = $user_id;
			return $userGroup->save();
		}else
		{
			return true;
		}
  }
	
	public function removeUser($user_id){
		$model = GO_Base_Model_UserGroup::model()->findByPk(array('user_id'=>$user_id, 'group_id'=>$this->pk));
		if($model)
			return $model->delete();
		else
			return true;
	}
  
  /**
   * Check if this group has a user
   * 
   * @param type $user_id
   * @return GO_Base_Model_UserGroup or false 
   */
  public function hasUser($user_id){
    return GO_Base_Model_UserGroup::model()->findByPk(array('user_id'=>$user_id, 'group_id'=>$this->pk));
  }
	
	public function checkDatabase() {
		
		if($this->id==GO::config()->group_everyone){
			$stmt = GO_Base_Model_User::model()->find(GO_Base_Db_FindParams::newInstance()->ignoreAcl());
			while($user = $stmt->fetch())
				$this->addUser ($user->id);
		}
		
		if($this->id==GO::config()->group_root){
			$this->addUser(1);
		}
		
		return parent::checkDatabase();
	}
  
}