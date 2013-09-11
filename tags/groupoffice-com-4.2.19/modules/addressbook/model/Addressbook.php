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
	
	public function relations(){
		return array(
				'contacts' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Contact', 'field'=>'addressbook_id', 'delete'=>true),
				'companies' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Company', 'field'=>'addressbook_id', 'delete'=>true),
				'linkedinAutoImport' => array( 'type'=>self::HAS_ONE, 'model'=>'GO_Linkedin_Model_AutoImport', 'field'=>'addressbook_id', 'delete'=>true)
		);
	}
	
	/**
	 * Get's a unique URI for the calendar. This is used by CalDAV
	 * 
	 * @return string
	 */
	public function getUri(){
		return preg_replace('/[^\w-]*/', '', (strtolower(str_replace(' ', '-', $this->name)))).'-'.$this->id;
	}
	
	protected function beforeSave() {
		
		if(!isset($this->default_salutation))
			$this->default_salutation=GO::t("defaultSalutationTpl","addressbook");
			
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

	/**
	 * Remove all contacts and companies from the addressbook
	 */
	public function truncate(){
		$contacts = $this->contacts;
		
		foreach($contacts as $contact){
			$contact->delete();
		}
		
		$companies = $this->companies;
		
		foreach($companies as $company){
			$company->delete();
		}
	}
	
	/**
	 * joining on the addressbooks can be very expensive. That's why this 
	 * session cached useful can be used to optimize addressbook queries.
	 * 
	 * @return array
	 */
	public function getAllReadableAddressbookIds(){
		if(!isset(GO::session()->values['addressbook']['readable_addressbook_ids'])){
			GO::session()->values['addressbook']['readable_addressbook_ids']=array();
			$stmt = $this->find();
			while($ab = $stmt->fetch()){
				GO::session()->values['addressbook']['readable_addressbook_ids'][]=$ab->id;
			}
		}
		
		return GO::session()->values['addressbook']['readable_addressbook_ids'];
	}
}