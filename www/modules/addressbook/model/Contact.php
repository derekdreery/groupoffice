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
	
	
	public $photoFile;
	
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
	
	public function defaultAttributes() {
		return array(
				'country'=>GO::config()->default_country
		);
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
		
		return "GO_Addressbook_Customfields_Model_Contact";
	}

	public function relations(){
            return array(
								'goUser' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Base_Model_User', 'field'=>'go_user_id'),
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
		
		if($this->goUser()>0)			
			throw new Exception("This contact belongs to a user account. Please delete this account first.");
		
		return parent::beforeDelete();
	}
	
	protected function afterDelete() {
		if($this->photo)
			unlink($this->photo);
		
		return parent::afterDelete();
	}
	
	protected function afterSave($wasNew) {
		
		if(isset($this->photoFile)){
			$this->setPhoto($this->photoFile->path());
			unset($this->photoFile);
		}
		
		if(!$wasNew && $this->isModified('addressbook_id') && ($company=$this->company())){
			//make sure company is in the same addressbook.
			$company->addressbook_id=$this->addressbook_id;
			$company->save();
		}
		
		return parent::afterSave($wasNew);
	}
	
	/**
	 * Set the photo
	 * 
	 * @param String $tmpFile 
	 */
	protected function setPhoto($tmpFile){

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
		return $this->photo ? GO::url('addressbook/contact/photo', 'id='.$this->id) : '';
	}
	
	/**
	 * Import a contact (with or without company) from a VObject 
	 * 
	 * @param Sabre_VObject_Component $vobject
	 * @param array $attributes Extra attributes to apply to the contact. Raw values should be past. No input formatting is applied.
	 * @return GO_Addressbook_Model_Contact
	 */
	public function importVObject(Sabre_VObject_Component $vobject, $attributes=array()) {
		//$event = new GO_Calendar_Model_Event();
		$companyAttributes = array();
		if (!empty($attributes['addressbook_id'])) {
			$companyAttributes['addressbook_id'] = $attributes['addressbook_id'];
		} 
		
		foreach ($vobject->children as $vobjProp) {
			switch ($vobjProp->name) {
				case 'PHOTO':					
					if(!empty($vobjProp->value)){
						$file = GO_Base_Fs_File::tempFile('','jpg');
						$file->putContents(base64_decode($vobjProp->value));
						$this->photoFile=$file;
					}
					break;
				case 'N':
					$nameArr = explode(';',$vobjProp->value);
					$attributes['last_name'] = $nameArr[0];
					$attributes['first_name'] = $nameArr[1];
					$attributes['middle_name'] = !empty($nameArr[2]) ? $nameArr[2] : '' ;
					$attributes['suffix'] = !empty($nameArr[4]) ? $nameArr[4] : '' ;
					break;
				case 'ORG':
					$companyAttributes['name'] =  null;
					if (!empty($vobjProp->value)) {
						$compNameArr = explode(';',$vobjProp->value);
						if (!empty($compNameArr[0]))
							$companyAttributes['name'] = $compNameArr[0];
						if (!empty($compNameArr[1]))
							$companyAttributes['department'] = $compNameArr[1];
						if (!empty($compNameArr[2]))
							$companyAttributes['name2'] = $compNameArr[2];
					}
					break;
				case 'TITLE':
					$attributes['title'] = !empty($vobjProp->value) ? $vobjProp->value : null;
					break;
				case 'TEL':
					foreach ($vobjProp->parameters as $param) {
						if ($param->name=='TYPE')
							$types = explode(',',strtolower($param->value));
						else
							$types = array();
					}
					if(in_array('work',$types) && ( in_array('voice',$types) || count($types)==1 ) ) {
						$attributes['work_phone'] = $vobjProp->value;
						$companyAttributes['phone'] = $vobjProp->value;
					}
					if(in_array('cell',$types) && ( in_array('voice',$types) || count($types)==1 ) )
						$attributes['cellular'] = $vobjProp->value;
					if(in_array('fax',$types) && in_array('home',$types))
						$attributes['fax'] = $vobjProp->value;
					if(in_array('fax',$types) && in_array('work',$types)) {
						$companyAttributes['fax'] = $vobjProp->value;
						$attributes['work_fax'] = $vobjProp->value;
					}
					if(in_array('home',$types) && ( in_array('voice',$types) || count($types)==1 ) )
						$attributes['home_phone'] = $vobjProp->value;
					
//					foreach ($vobjProp->parameters as $param) {
//						if ($param['name']=='TYPE') {
//							switch (susbstr($param['value'],0,4)) {
//								case 'work':
//									$attributes['work_phone'] = $vobjProp->value;
//									break;
//								default:
//									$attributes['home_phone'] = $vobjProp->value;
//									break;
//							}
//						}
//					}
					break;
//				case 'LABEL':
				case 'ADR':
					foreach ($vobjProp->parameters as $param) {
						if ($param->name=='TYPE')
							$types = explode(',',strtolower($param->value));
						else
							$types = array();
					}
					if(in_array('work',$types)) {
						$addrArr = explode(';',$vobjProp->value);
						$companyAttributes['address'] = $addrArr[2];
						$companyAttributes['city'] = $addrArr[3];
						$companyAttributes['state'] = $addrArr[4];
						$companyAttributes['zip'] = $addrArr[5];
						$companyAttributes['country'] = $addrArr[6];
					}
					if(in_array('home',$types)) {
						$addrArr = explode(';',$vobjProp->value);
						$attributes['address'] = $addrArr[2];
						$attributes['city'] = $addrArr[3];
						$attributes['state'] = $addrArr[4];
						$attributes['zip'] = $addrArr[5];
						$attributes['country'] = $addrArr[6];
					}
					break;
				case 'EMAIL':
					foreach ($vobjProp->parameters as $param) {
						if ($param->name=='TYPE')
							$types = explode(',',strtolower($param->value));
						else
							$types = array();
					}
					if(in_array('pref',$types)) {
						$attributes['email'] = $vobjProp->value;
					} elseif(in_array('home',$types)) {
						$attributes['email2'] = $vobjProp->value;
					} elseif(in_array('work',$types)) {
						$attributes['email3'] = $vobjProp->value;
					} else {
						$attributes['email'] = $vobjProp->value;
					}
					break;
				case 'ROLE':
					$attributes['function'] = $vobjProp->value;
					break;
				case 'BDAY':
					$attributes['birthday'] = !empty($vobjProp->value) ? $vobjProp->value : null;
					break;				
				case 'NOTE':
					$attributes['comment'] = $vobjProp->value;
					break;
				
				default:
					break;
			}
		}

		$this->setAttributes($attributes, false);		
		
		if (isset($companyAttributes['name'])) {
			$stmt = GO_Addressbook_Model_Company::model()->findByAttribute('name', $companyAttributes['name']);
			$company = $stmt->fetch();
			if (empty($company)) {
				$company = new GO_Addressbook_Model_Company();
				$company->setAttributes($companyAttributes,false);
			} else {
				$company->setAttribute('addressbook_id', $companyAttributes['addressbook_id']);
			}
			$company->save();
			$this->setAttribute('company_id',$company->id);			
		}
		$this->save();
		
		return $this;
	}

		/**
	 * Get this task as a VObject. This can be turned into a vcard file data.
	 * 
	 * @return Sabre_VObject_Component 
	 */
	public function toVObject(){
		//require vendor lib SabreDav vobject
		require_once(GO::config()->root_path.'go/vendor/SabreDAV/lib/Sabre/VObject/includes.php');

		$e=new Sabre_VObject_Component('vcard');
		
		$e->add('VERSION','3.0');
		$e->add('N',$this->last_name.";".$this->first_name.";".$this->middle_name.';;'.$this->suffix);
		$e->add('FN',$this->name);
		
		if (!empty($this->email)) {
			$p = new Sabre_VObject_Property('EMAIL',$this->email);
			$p->add(new GO_Base_VObject_Parameter('TYPE','PREF,INTERNET'));
			$e->add($p);
		}
		if (!empty($this->email2)) {
			$p = new Sabre_VObject_Property('EMAIL',$this->email2);
			$p->add(new GO_Base_VObject_Parameter('TYPE','HOME,INTERNET'));
			$e->add($p);
		}
		if (!empty($this->email3)) {
			$p = new Sabre_VObject_Property('EMAIL',$this->email3);
			$p->add(new GO_Base_VObject_Parameter('TYPE','WORK,INTERNET'));
			$e->add($p);
		}
		
		if (!empty($this->function)) {
			$e->add('ROLE',$this->function);
		}
		
		if (!empty($this->title))
			$e->add('TITLE',$this->title);
		if (!empty($this->home_phone)) {
			$p = new Sabre_VObject_Property('TEL',$this->home_phone);
			$p->add(new GO_Base_VObject_Parameter('TYPE','HOME,VOICE'));
			$e->add($p);	
		}
		if (!empty($this->work_phone)) {
			$p = new Sabre_VObject_Property('TEL',$this->work_phone);
			$p->add(new GO_Base_VObject_Parameter('TYPE','WORK,VOICE'));
			$e->add($p);	
		}
		if (!empty($this->fax)) {
			$p = new Sabre_VObject_Property('TEL',$this->fax);
			$p->add(new GO_Base_VObject_Parameter('TYPE','HOME,FAX'));
			$e->add($p);	
		}
		if (!empty($this->work_fax)) {
			$p = new Sabre_VObject_Property('TEL',$this->work_fax);
			$p->add(new GO_Base_VObject_Parameter('TYPE','WORK,FAX'));
			$e->add($p);	
		}
		if (!empty($this->cellular)) {
			$p = new Sabre_VObject_Property('TEL',$this->cellular);
			$p->add(new GO_Base_VObject_Parameter('TYPE','CELL,VOICE'));
			$e->add($p);	
		}
		if (!empty($this->birthday)) {
			$e->add('BDAY',$this->birthday);
		}
		
		if (!empty($this->company)) {
			$e->add('ORG',$this->company->name.';'.$this->department.';'.$this->company->name2);
			$p = new Sabre_VObject_Property('ADR',';;'.$this->company->address.' '.$this->company->address_no.';'.
				$this->company->city.';'.$this->company->state.';'.$this->company->zip.';'.$this->company->country);
			$p->add('TYPE','WORK');
			$e->add($p);
//			$p = new Sabre_VObject_Property('LABEL',GO_Base_Util_Common::formatAddress(
//				$this->company->country, $this->company->address, $this->company->address_no,
//				$this->company->zip, $this->company->city, $this->company->state));
//			$p->add('TYPE','WORK');
//			$e->add($p);
		}
		
		if ($this->address) {
			$p = new Sabre_VObject_Property('ADR',';;'.$this->address.' '.$this->address_no.';'.
				$this->city.';'.$this->state.';'.$this->zip.';'.$this->country);
			$p->add('TYPE','HOME');
			$e->add($p);
//			$p = new Sabre_VObject_Property('LABEL',GO_Base_Util_Common::formatAddress(
//				$this->country, $this->address, $this->address_no,
//				$this->zip, $this->city, $this->state));
//			$p->add('TYPE','HOME');
//			$e->add($p);
		}
		
		if(!empty($this->comment)){
			$e->note=$this->comment;
		}
		
		$mtimeDateTime = new DateTime('@'.$this->mtime);
		$rev = new Sabre_VObject_Element_DateTime('LAST-MODIFIED');
		$rev->setDateTime($mtimeDateTime, Sabre_VObject_Element_DateTime::UTC);		
		$e->add($rev);
		
		
		if($this->photo){
			$p = new Sabre_VObject_Property('photo', base64_encode(file_get_contents($this->photo)));
			$p->add('type','jpeg');
			$p->add('encoding','b');
			$e->add($p);	
		}
		
		return $e;
	}
	
}