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

class GO_Addressbook_Model_Company extends GO_Base_Db_ActiveRecord {
	
	public function linkType(){
		return 3;
	}
	
	public function aclField(){
		return 'addressbook.acl_id';	
	}
	
	public function tableName(){
		return 'ab_companies';
	}
	
	public function hasFiles(){
		return true;
	}
	
	public function customfieldsModel() {
		
		return "GO_Addressbook_Model_CompanyCustomFieldsRecord";
	}

	public function relations(){
		return array(
                    'addressbook' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Addressbook_Model_Addressbook', 'field'=>'addressbook_id'),
                    'contact' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Contact', 'field'=>'company_id', 'delete'=>'false')
		);
	}


	protected function getCacheAttributes() {
		return array(
				'name' => $this->name,
				'type' => GO::t('company','addressbook')
		);
	}

	protected function afterSave($wasNew) {

		if (isset(GO::modules()->customfields))
			GO_Customfields_Controller_Item::saveCustomFields($this, "GO_Addressbook_Model_CompanyCustomFieldsRecord");

		return parent::afterSave($wasNew);
	}

	/**
	 * The files module will use this function.
	 */
	protected function buildFilesPath() {
		
		$new_folder_name = GO_Base_Util_File::strip_invalid_chars($this->name);
		$new_path = 'companies/'.GO_Base_Util_File::strip_invalid_chars($this->addressbook->name);
		
		if(empty($new_folder_name))
			$new_folder_name='unnamed';
			
		$new_path .= '/'.$new_folder_name;
		return $new_path;
	}

}