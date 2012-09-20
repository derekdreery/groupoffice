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
 * @property bool $users true if this addressbook is the special addressbook that holds the Group-Office users.
 * @property string $default_salutation
 * @property boolean $shared_acl
 * @property int $acl_id
 * @property int $user_id
 */

 class GO_Addressbook_Model_Addressbook extends GO_Base_Model_AbstractUserDefaultModel{
		 
	 /**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Addressbook_Model_Addressbook 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	public function aclField(){
		return 'acl_id';	
	}
	
	public function tableName(){
		return 'ab_addressbooks';
	}
	
	public function hasFiles(){
		return true;
	}
	
	protected function init() {
		$this->columns['name']['unique']=true;
		return parent::init();
	}
	
	public function relations(){
		return array(
				'contacts' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Contact', 'field'=>'addressbook_id', 'delete'=>true),
				'companies' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Company', 'field'=>'addressbook_id', 'delete'=>true)
		);
	}
	
	protected function beforeSave() {
		
		if(!isset($this->default_salutation))
			$this->default_salutation=GO::t("defaultSalutationTpl","addressbook");
		
		//TODO deprecated.
		$this->default_iso_address_format='NL';
		
		if(!isset($this->shared_acl))
			$this->shared_acl=0;
			
		return parent::beforeSave();
	}
	
	public function beforeDelete() {
		
		if($this->users)			
			throw new Exception("You can't delete the users addressbook");
		
		return parent::beforeDelete();
	}
	
	/**
	 * Get the addressbook for the user profiles. If it doesn't exist it will be
	 * created.
	 * 
	 * @return GO_Addressbook_Model_Addressbook 
	 */
	public function getUsersAddressbook(){
		$ab = GO_Addressbook_Model_Addressbook::model()->findSingleByAttribute('users', '1'); //GO::t('users','base'));
		if (!$ab) {
			$ab = new GO_Addressbook_Model_Addressbook();
			$ab->name = GO::t('users');
			$ab->users = true;
			$ab->save();
		}
		return $ab;
	}
	
	public function defaultAttributes() {
		$attr = parent::defaultAttributes();
		$attr['default_salutation']=GO::t('defaultSalutationTpl','addressbook');
		return $attr;
	}

}