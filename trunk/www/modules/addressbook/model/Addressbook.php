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
 * The Addressbook model
 * 
 * @property String $name The name of the Addressbook
 * @property int $files_folder_id
 */

 class GO_Addressbook_Model_Addressbook extends GO_Base_Db_ActiveRecord{
		 
	public function aclField(){
		return 'acl_id';	
	}
	
	public function tableName(){
		return 'ab_addressbooks';
	}
	
	public function relations(){
		return array(
				'contacts' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Contact', 'field'=>'addressbook_id', 'delete'=>true),
				'companies' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Company', 'field'=>'addressbook_id', 'delete'=>true),
				'user' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Base_Model_User', 'field'=>'user_id')
		);
	}
	
	protected function beforeSave() {
		
		if(!isset($this->default_salutation))
			$this->default_salutation=GO::t("defaultSalutation","addressbook");
		
		//TODO deprecated.
		$this->default_iso_address_format='NL';
		
		if(!isset($this->shared_acl))
			$this->shared_acl=0;
		
		return parent::beforeSave();
	}
}