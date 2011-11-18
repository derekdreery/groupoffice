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
 * @property String $photo Full path to photo
 * @property String $photoURL URL to photo
 * 
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

	public function aclField(){
		return 'addressbook.acl_id';	
	}
	
	public function tableName(){
		return 'ab_contacts';
	}
	
	public function hasFiles(){
		return true;
	}
	
	public function hasLinks() {
		return true;
	}
	
	protected function init() {
		
		$this->columns['email']['regex']=GO_Base_Util_String::get_email_validation_regex();
		$this->columns['email2']['regex']=GO_Base_Util_String::get_email_validation_regex();
		$this->columns['email3']['regex']=GO_Base_Util_String::get_email_validation_regex();
		
		return parent::init();
	}
	
	public function getFindSearchQueryParamFields($prefixTable = 't', $withCustomFields = true) {
		$fields = parent::getFindSearchQueryParamFields($prefixTable, $withCustomFields);
		$fields[]="CONCAT(t.first_name,t.middle_name,t.last_name)";
		
		return $fields;
	}
	
	public function customfieldsModel() {
		
		return "GO_Addressbook_Model_ContactCustomFieldsRecord";
	}

	public function relations(){
            return array(
                'addressbook' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Addressbook_Model_Addressbook', 'field'=>'addressbook_id'),
                'company' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Addressbook_Model_Company', 'field'=>'company_id'),
								'addresslists' => array('type'=>self::MANY_MANY, 'model'=>'GO_Addressbook_Model_Addresslist', 'field'=>'contact_id', 'linkModel' => 'GO_Addressbook_Model_AddresslistContact'),
            );
	}
	
	public function getAttributes($outputType = 'formatted') {
		
		$attr = parent::getAttributes($outputType);
		$attr['name']=$this->getName();
		
		return $attr;
	}


	
	/**
	 *
	 * @return String Full formatted name of the user
	 */
	protected function getName(){
		return GO_Base_Util_String::format_name($this->last_name, $this->first_name, $this->middle_name,'first_name');
	}

	protected function getCacheAttributes() {
		return array(
				'name' => $this->name
		);
	}
	
	protected function getLocalizedName() {
		return GO::t('contact', 'addressbook');
	}

	/**
	 * The files module will use this function.
	 */
	public function buildFilesPath() {
		
		$new_folder_name = GO_Base_Fs_Base::stripInvalidChars($this->name);
		$last_part = empty($this->last_name) ? '' : GO_Addressbook_Utils::getIndexChar($this->last_name);
		$new_path = $this->addressbook->buildFilesPath().'/contacts';
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
	
	
	
	/**
	 * Set the photo
	 * 
	 * @param String $tmpFile 
	 */
	protected function setPhoto($tmpFile){
				GO::debug("setPhoto($tmpFile)");
		$destination = GO::config()->file_storage_path.'contacts/contact_photos/'.$this->id.'.jpg';
		
		if(empty($tmpFile))
		{
			$file = new GO_Base_Fs_File($this->_getPhotoPath());
			return !$file->exists() || $file->delete();
		}else
		{		

			$f = new GO_Base_Fs_Folder(dirname($this->_getPhotoPath()));
			$f->create();


			$img = new GO_Base_Util_Image();
			if(!$img->load($tmpFile)){
				throw new Exception(GO::t('imageNotSupported','addressbook'));
			}

			$img->zoomcrop(90,120);
			if(!$img->save($destination, IMAGETYPE_JPEG))
				throw new Exception("Could not save photo at ".$destination." from ".$tmpFile);
		}
	}
	
	private function _getPhotoPath(){
		return GO::config()->file_storage_path.'contacts/contact_photos/'.$this->id.'.jpg';
	}
	
	protected function getPhoto(){
		if(file_exists($this->_getPhotoPath()))
			return $this->_getPhotoPath();
		else
			return '';
	}
	
	protected function getPhotoURL(){
		return GO::url('addressbook/contact/photo', 'id='.$this->id);
	}
	
	

}