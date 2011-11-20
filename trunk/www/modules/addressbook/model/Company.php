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
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Addressbook_Model_Company 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	protected function getLocalizedName() {
		return GO::t('company', 'addressbook');
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
	
	public function hasLinks() {
		return true;
	}
	
	public function customfieldsModel() {
		
		return "GO_Addressbook_Model_CompanyCustomFieldsRecord";
	}
	
	public function defaultAttributes() {
		return array(
				'country'=>GO::config()->default_country,
				'post_country'=>GO::config()->default_country
		);
	}

	public function relations(){
		return array(
			'addressbook' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Addressbook_Model_Addressbook', 'field'=>'addressbook_id'),
			'contacts' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Contact', 'field'=>'company_id', 'delete'=>false),
			'addresslists' => array('type'=>self::MANY_MANY, 'model'=>'GO_Addressbook_Model_Addresslist', 'field'=>'company_id', 'linkModel' => 'GO_Addressbook_Model_AddresslistCompany'),
		);
	}


	protected function getCacheAttributes() {
		return array(
				'name' => $this->name,
				'type' => GO::t('company','addressbook')
		);
	}
	/**
	 * The files module will use this function.
	 */
	public function buildFilesPath() {
		
		$new_folder_name = GO_Base_Fs_Base::stripInvalidChars($this->name);
		$new_path = $this->addressbook->buildFilesPath().'/companies';
		
		if(empty($new_folder_name))
			$new_folder_name='unnamed';
			
		$new_path .= '/'.$new_folder_name;
		return $new_path;
	}

}