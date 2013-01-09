<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The Linkedin Auto Import model
 * 
 * @property int $addressbook_id
 * @property string $access_info
 * @property boolean $auto_import_enabled
 */
class GO_Linkedin_Model_AutoImport extends GO_Base_Db_ActiveRecord {
		
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function primaryKey() {
		return 'addressbook_id';
	}
	
	public function aclField(){
		return 'addressbook.acl_id';	
	}
	
	public function tableName(){
		return 'li_auto_imports';
	}
	
	public function relations(){
		return array(	
			'addressbook' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Addressbook_Model_Addressbook', 'field'=>'addressbook_id') );
	}

	protected function beforeSave() {
		
		if ($this->isModified('access'))
			$this->access = GO_Base_Util_Crypt::encrypt($this->access);
		
		return parent::beforeSave();
	}
	
	public function getDecryptedAccessInfo() {
		return GO_Base_Util_Crypt::decrypt($this->access);
	}
		
}