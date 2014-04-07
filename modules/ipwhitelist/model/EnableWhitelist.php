<?php
/**
 * 
 * The EnableWhitelist model
 * 
 * @property int $group_id
 */

class GO_Ipwhitelist_Model_EnableWhitelist extends GO_Base_Db_ActiveRecord{
		 
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function primaryKey() {
		return 'group_id';
	}
	
//	public function aclField(){
//		return 'acl_id';	
//	}
	
	public function tableName(){
		return 'wl_enabled_groups';
	}
		
//	public function relations(){
//		return array(
//				'contacts' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Contact', 'field'=>'addressbook_id', 'delete'=>true),
//				'companies' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Company', 'field'=>'addressbook_id', 'delete'=>true)
//		);
//	}
	
}
?>