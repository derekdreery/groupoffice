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
 * @todo delete files folder, delete custom fields
 * 
 * 
 * The Note model
 * 
 * @property int $id
 * @property int $category_id
 */
class GO_Addressbook_Model_Contact extends GO_Base_Db_ActiveRecord {
	
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
	
	public function hasCustomfields(){
		return true;
	}

	public function relations(){
		return array(
				'addressbook' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Addressbook_Model_Addressbook', 'field'=>'addressbook_id'),
				'user' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Base_Model_User', 'field'=>'user_id'),
				'customfieldRecord' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Notes_Model_CustomFieldsRecord', 'field'=>'id')
		);
	}


	
	/**
	 *
	 * @return String Full formatted name of the user
	 */
	public function getName(){
		return GO_Base_Util_String::format_name($this->last_name, $this->first_name, $this->middle_name);
	}

	protected function getCacheAttributes() {
		return array(
				'name' => $this->name,
				'type' => GO::t('contact','addressbook')
		);
	}

	protected function afterSave() {

		if (isset(GO::modules()->customfields))
			GO_Customfields_Controller_Item::saveCustomFields($this, "GO_Notes_Model_CustomFieldsRecord");

		return parent::afterSave();
	}

	/**
	 * The files module will use this function.
	 */
	protected function buildFilesPath() {
		
		$new_folder_name = GO_Base_Util_File::strip_invalid_chars($this->name);
		$last_part = empty($contact['last_name']) ? '' : $this->get_index_char($contact['last_name']);
		$new_path = 'contacts/'.GO_Base_Util_File::strip_invalid_chars($addressbook['name']);
		if(!empty($last_part)) {
			$new_path .= '/'.$last_part;
		}else {
			$new_path .= '/0 no last name';
		}
		
		if(empty($new_folder_name))
			$new_folder_name='unnamed';
			
		$new_path .= '/'.$new_folder_name;
		return $new_path;

		return 'notes/' . File::strip_invalid_chars($this->category->name) . '/' . date('Y', $this->ctime) . '/' . GO_Base_Util_File::strip_invalid_chars($this->name);
	}

}