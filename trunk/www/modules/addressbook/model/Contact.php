<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */

/**
 * @property String $name Full name of the contact
 */
class GO_Addressbook_Model_Contact extends GO_Base_Db_ActiveRecord {
	
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Addressbook_Model_Contact 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	public function linkType(){
		return 2;	
	}
	
	public function aclField(){
		return 'addressbook.acl_id';	
	}
	
	public function tableName(){
		return 'ab_contacts';
	}
	
	public function hasFiles(){
		return true;
	}
	
	public function customfieldsModel() {
		
		return "GO_Addressbook_Model_ContactCustomFieldsRecord";
	}

	public function relations(){
            return array(
                'addressbook' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Addressbook_Model_Addressbook', 'field'=>'addressbook_id'),
                'company' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Addressbook_Model_Company', 'field'=>'company_id')
            );
	}


	
	/**
	 *
	 * @return String Full formatted name of the user
	 */
	protected function getName(){
		return GO_Base_Util_String::format_name($this->last_name, $this->first_name, $this->middle_name);
	}

	protected function getCacheAttributes() {
		return array(
				'name' => $this->name,
				'type' => GO::t('contact','addressbook')
		);
	}

	protected function afterSave($wasNew) {
// Obsolete according to Merijn.
//		if (isset(GO::modules()->customfields))
//			GO_Customfields_Controller_Item::saveCustomFields($this, "GO_Addressbook_Model_ContactCustomFieldsRecord");

		return parent::afterSave($wasNew);
	}

	/**
	 * The files module will use this function.
	 */
	public function buildFilesPath() {
		
		$new_folder_name = GO_Base_Util_File::strip_invalid_chars($this->name);
		$last_part = empty($this->last_name) ? '' : GO_Addressbook_Utils::getIndexChar($this->last_name);
		$new_path = 'contacts/'.GO_Base_Util_File::strip_invalid_chars($this->addressbook->name);
		if(!empty($last_part)) {
			$new_path .= '/'.$last_part;
		}else {
			$new_path .= '/0 no last name';
		}
		
		if(empty($new_folder_name))
			$new_folder_name='unnamed';
			
		$new_path .= '/'.$new_folder_name;
		return $new_path;
	}
	
	public function beforeDelete() {
		
		if($this->go_user_id>0)			
			throw new Exception("This contact belongs to a user account. Please delete this account first.");
		
		return parent::beforeDelete();
	}

}