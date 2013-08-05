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
 * @property int $go_user_id
 * @property int $files_folder_id
 * @property boolean $email_allowed
 * @property string $salutation
 * @property int $mtime
 * @property int $muser_id
 * @property int $ctime
 * @property string $comment
 * @property string $address_no
 * @property string $zip
 * @property string $city
 * @property string $state
 * @property string $country
 * @property string $cellular
 * @property string $cellular2
 * @property string $work_fax
 * @property string $fax
 * @property string $work_phone
 * @property string $home_phone
 * @property string $function
 * @property string $department
 * @property int $company_id
 * @property string $email3
 * @property string $email2
 * @property string $email
 * @property string $birthday
 * @property string $sex
 * @property string $suffix
 * @property string $title
 * @property string $initials
 * @property string $last_name
 * @property string $middle_name
 * @property string $first_name
 * @property int $addressbook_id
 * @property int $user_id
 * @property int $id
 * @property int $age;
 * 
 * @property string $firstEmail Automatically returns the first filled in e-mail address.
 * @property GO_Addressbook_Model_Addressbook $addressbook
 * @property GO_Addressbook_Model_Company $company
 * @property string $homepage
 * @property string $uuid
 */
class GO_Addressbook_Model_Contact extends GO_Base_Db_ActiveRecord {
		
	/**
	 * if user typed in a new company name manually we set this attribute so a new company will be autocreated.
	 * 
	 * @var string 
	 */
	public $company_name;
	
	
	public $skip_user_update=false;
	
	
	private $_photoFile;
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
		
		$this->columns['addressbook_id']['required']=true;
		$this->columns['email']['regex']=GO_Base_Util_String::get_email_validation_regex();
		$this->columns['email2']['regex']=GO_Base_Util_String::get_email_validation_regex();
		$this->columns['email3']['regex']=GO_Base_Util_String::get_email_validation_regex();
		
		return parent::init();
	}
	
	public function getFindSearchQueryParamFields($prefixTable = 't', $withCustomFields = true) {
		$fields = parent::getFindSearchQueryParamFields($prefixTable, $withCustomFields);
		$fields[]="CONCAT(t.first_name,' ',t.middle_name,' ',t.last_name)";	
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
			'vcardProperties' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_ContactVcardProperty', 'field'=>'contact_id', 'delete'=> true)
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
	public function getName($sort_name=false){
		
		if(!$sort_name){
			if(GO::user()){
				$sort_name = GO::user()->sort_name;
			}else
			{
				$sort_name = 'first_name';
			}
		}
		
		return GO_Base_Util_String::format_name($this->last_name, $this->first_name, $this->middle_name,$sort_name);
	}
	
	/**
	 * Get the full address formatted according to the country standards.
	 * 
	 * @return string
	 */
	public function getFormattedAddress()
	{
		return GO_Base_Util_Common::formatAddress(
						$this->country, 
						$this->address, 
						$this->address_no,
						$this->zip, 
						$this->city, 
						$this->state
						);
	}

	protected function getCacheAttributes() {
		
		$name = $this->name;
		if($this->company)
			$name .= ' ('.$this->company->name.')';

		if($this->addressbook)
			$name .= ' ('.$this->addressbook->name.')';
			
		return array(
				'name' => $name
		);
	}
	
	protected function getLocalizedName() {
		return GO::t('contact', 'addressbook');
	}

	/**
	 * The files module will use this function.
	 */
	public function buildFilesPath() {
		
		if(!$this->addressbook)
			return false;
		
		$new_folder_name = GO_Base_Fs_Base::stripInvalidChars($this->name).' ('.$this->id.')';
		$last_part = empty($this->last_name) ? '' : GO_Addressbook_Utils::getIndexChar($this->last_name);
		$new_path = $this->addressbook->buildFilesPath().'/contacts';
		if(!empty($last_part)) {
			$new_path .= '/'.$last_part;
		}else {
			$new_path .= '/0 no last name';
		}
					
		$new_path .= '/'.$new_folder_name;
		return $new_path;
	}
	
	public function beforeDelete() {
		
		if($this->goUser())			
			throw new Exception("This contact belongs to a user account. Please delete this account first.");
		
		return parent::beforeDelete();
	}
	
	protected function afterDelete() {
		if($this->getPhotoFile()->exists())
			$this->getPhotoFile()->delete();
		
		return parent::afterDelete();
	}
	
	protected function beforeSave() {
		
		$this->_autoSalutation();
		
		if (strtolower($this->sex)==strtolower(GO::t('female','addressbook')))
			$this->sex = 'F';
		$this->sex = $this->sex=='M' || $this->sex=='F' ? $this->sex : 'M';
		
		//Auto create company if company_id is a String and can't be found.
		if(!empty($this->company_name)){			
			$company = GO_Addressbook_Model_Company::model()->findSingleByAttributes(array(
				'addressbook_id'=>$this->addressbook_id,
				'name'=>$this->company_name
			));
			
			if(!$company)
			{
				$company = new GO_Addressbook_Model_Company();
				$company->name=$this->company_name;
				$company->addressbook_id=$this->addressbook_id;			
				$company->save();
			}			
			
			$this->company_id=$company->id;			
		}
				
		return parent::beforeSave();
	}
	
	protected function afterDbInsert() {
		if(empty($this->uuid)){
			$this->uuid = GO_Base_Util_UUID::create('contact', $this->id);
			return true;
		}else
		{
			return false;
		}
	}
	
	private function _autoSalutation(){
		if(empty($this->salutation)){
			$tpl = $this->addressbook->default_salutation;
			$a = $this->getAttributes();
			foreach($a as $key=>$value){
				if(is_string($value))
					$tpl = str_replace('{'.$key.'}', $value, $tpl);
			}			
			$tpl = preg_replace('/[ ]+/',' ',$tpl);
			
			preg_match('/\[([^\/]+)\/([^\]]+)]/',$tpl, $matches);
			
			if(isset($matches[0])){
				$index = $this->sex=='M' ? 1 : 2;			
				$replaceText = isset($matches[$index]) ? $matches[$index] : "";
				
				$tpl = str_replace($matches[0], $replaceText, $tpl);
			}
			
			$this->salutation=$tpl;
			
			$this->cutAttributeLength('salutation');
		}
	}
	
	protected function afterSave($wasNew) {
	
		if(!$wasNew && $this->isModified('addressbook_id') && ($company=$this->company())){
			//make sure company is in the same addressbook.
			$company->addressbook_id=$this->addressbook_id;
			$company->save();
		}
		
		if(!$this->skip_user_update &&  $this->isModified(array('first_name','middle_name','last_name','email')) && $this->goUser){
			$this->goUser->first_name = $this->first_name;
			$this->goUser->middle_name = $this->middle_name;
			$this->goUser->last_name = $this->last_name;
			$this->goUser->email = $this->email;
			$this->goUser->skip_contact_update=true;
			if($this->goUser->isModified())
				$this->goUser->save(true);
		}
		
		return parent::afterSave($wasNew);
	}
	
//	/**
//	 * Set the photo
//	 * 
//	 * @param String $srcFileName The source image file name.
//	 */
//	public function setPhoto($srcFileName){
//		
//		if(!$this->id)
//			throw new Exception("Contact must be saved before you can set a photo");
//
//		$destination = GO::config()->file_storage_path.'contacts/contact_photos/'.$this->id.'.jpg';
//		
//		if(empty($srcFileName))
//		{
//			$file = new GO_Base_Fs_File($this->_getPhotoPath());
//			return !$file->exists() || $file->delete();
//		}else
//		{		
//
//			$f = new GO_Base_Fs_Folder(dirname($this->_getPhotoPath()));
//			$f->create();
//
//
//			$img = new GO_Base_Util_Image();
//			if(!$img->load($srcFileName)){
//				throw new Exception(GO::t('imageNotSupported','addressbook'));
//			}
//
//			$img->zoomcrop(90,120);
//			if(!$img->save($destination, IMAGETYPE_JPEG))
//				throw new Exception("Could not save photo at ".$destination." from ".$srcFileName);
//		}
//	}
	
//	private function _getPhotoPath(){
//		return GO::config()->file_storage_path.'contacts/contact_photos/'.$this->id.'.jpg';
//	}
//	
//	protected function getPhoto(){
//		if(file_exists($this->_getPhotoPath()))
//			return $this->_getPhotoPath();
//		else
//			return '';
//	}
	
	/**
	 * Get the photo file object. It always returns a file even though it doesn't
	 * exist. Use $contact->photoFile->exists() to detect that.
	 * 
	 * @return \GO_Base_Fs_File
	 */
	public function getPhotoFile(){
		if(!isset($_photoFile)){
			if(empty($this->photo))
				$this->photo=$this->id.'.jpg';
		
			$this->_photoFile = new GO_Base_Fs_File(GO::config()->file_storage_path.$this->photo);
		}
		
		return $this->_photoFile;
	}
	
	/**
	 * Get the URL to the original photo.
	 * 
	 * @return string
	 */
	public function getPhotoURL(){
		return $this->photoFile->exists() 
						? GO::url('addressbook/contact/photo', array('id'=>$this->id,'mtime'=>$this->photoFile->mtime())) 
						: GO::config()->host.'modules/addressbook/themes/Default/images/unknown-person.png';
	}
	
	public function getPhotoThumbURL($urlParams=array("w"=>90, "h"=>120, "zc"=>1)) {
		
		if($this->getPhotoFile()->exists()){
			$urlParams['filemtime']=$this->getPhotoFile()->mtime();
			$urlParams['src']=$this->getPhotoFile()->stripFileStoragePath();
			return GO::url('core/thumb', $urlParams);	
		}else
		{
			return GO::config()->host.'modules/addressbook/themes/Default/images/unknown-person.png';
		}
		
		
	}
	
	
	/**
	 * Set new photo file. The file will be converted into JPEG and resized to fit
	 * a 480x640 pixel box
	 * 
	 * @param GO_Base_Fs_File $file
	 */
	public function setPhoto(GO_Base_Fs_File $file){
		
		if($this->isNew)
			Throw new Exception("Cannot save a photo on a new contact that is not yet saved.");
		
		$this->getPhotoFile()->delete();
				
		$photoPath = new GO_Base_Fs_Folder(GO::config()->file_storage_path.'addressbook/photos/'.$this->addressbook_id.'/');
		$photoPath->create();		
		
		
//		if(strtolower($file->extension())!='jpg'){
		$filename = $photoPath->path().'/'.$this->id.'.jpg';
		$img = new GO_Base_Util_Image();
		if(!$img->load($file->path())){
			throw new Exception(GO::t('imageNotSupported','addressbook'));
		}
		
		//resize it to small image so we don't get in trouble with sync clients
		$img->fitBox(240,320);
		
		if(!$img->save($filename, IMAGETYPE_JPEG)){
			throw new Exception("Could not save photo!");
		}
		$file = new GO_Base_Fs_File($filename);
//		}else
//		{		
//			$file->move($photoPath, $this->id.'.'.strtolower($file->extension()));
//		}
	
		
		$this->photo=$file->stripFileStoragePath();
	}
	
	
	public function removePhoto(){
		$this->getPhotoFile()->delete();
		$this->photo="";
	}
	
	/**
	 * Import a contact (with or without company) from a VObject 
	 * 
	 * @param Sabre_VObject_Component $vobject
	 * @param array $attributes Extra attributes to apply to the contact. Raw values should be past. No input formatting is applied.
	 * @return GO_Addressbook_Model_Contact
	 */
	public function importVObject(Sabre\VObject\Component $vobject, $attributes=array(),$saveToDb=true,$ignoreInvalidProperties=true) {
		//$event = new GO_Calendar_Model_Event();
		$companyAttributes = array();
//		if (!empty($attributes['addressbook_id'])) {
//			$companyAttributes['addressbook_id'] = $attributes['addressbook_id'];
//		} 
		
		$uid = (string) $vobject->uid;
		if(!empty($uid) && empty($attributes['uuid']))
			$attributes['uuid'] = $uid;
		
		$emails = array();
//		$remainingVcardProps = array(); // format: $remainingVcardProps[$integer] = array('name'=>$vobjName, 'parameters'=>$vobjParams, 'value'=>$vobjValue)
//		$deletedPropertiesPrefixes_nonGO = array(); // This is to keep track of the prefixes occurring in the current VCard.
																	// Every time a new prefix is encountered during the current sync,
																	// all of this contact's properties starting with this prefix will
																	// be removed to make place for the ones in the imported VCard.

		// Remove this contact's non-GO VCard properties.
		// (We assume they will be updated by the client during the current sync process).
//		if (!empty($this->id)) {
//			$nonGO_PropModels_toDelete = GO_Addressbook_Model_ContactVcardProperty::model()
//				->find(
//					GO_Base_Db_FindParams::newInstance()
//						->criteria(
//							GO_Base_Db_FindCriteria::newInstance()
//								->addCondition('contact_id',$this->id)
//								->addCondition('name','X-%','NOT LIKE')
//						)
//				);
//			while ($contactVcardProp = $nonGO_PropModels_toDelete->fetch())
//				$contactVcardProp->delete();
//		}
		
		foreach ($vobject->children as $vobjProp) {
			switch ($vobjProp->name) {
				case 'PHOTO':					
					if(!empty($vobjProp->value)){
						$photoFile = GO_Base_Fs_File::tempFile('','jpg');
						$photoFile->putContents(base64_decode($vobjProp->value));
					}
					break;
				case 'N':
					$nameArr = explode(';',$vobjProp->value);
					if(isset($nameArr[0]))
						$attributes['last_name'] = $nameArr[0];
					if(isset($nameArr[1]))
						$attributes['first_name'] = $nameArr[1];
					
					
					
					$attributes['middle_name'] = !empty($nameArr[2]) ? $nameArr[2] : '' ;
					$attributes['suffix'] = !empty($nameArr[4]) ? $nameArr[4] : '' ;
					$attributes['title'] = !empty($nameArr[3]) ? $nameArr[3] : '' ;
					break;
				case 'ORG':
					$companyAttributes['name'] =  null;
					if (!empty($vobjProp->value)) {
						$compNameArr = explode(';',$vobjProp->value);
						if (!empty($compNameArr[0]))
							$companyAttributes['name'] = $compNameArr[0];
						if (!empty($compNameArr[1]))
							$attributes['department'] = $compNameArr[1];
						if (!empty($compNameArr[2]))
							$companyAttributes['name2'] = $compNameArr[2];
					}
					break;
//				case 'TITLE':
//					$attributes['title'] = !empty($vobjProp->value) ? $vobjProp->value : null;
//					break;
				case 'TEL':
					if(!empty($vobjProp->value)){
						$types = array();
						foreach ($vobjProp->parameters as $param) {
							if ($param->name=='TYPE')
								$types = explode(',',strtolower($param->value));							
						}
						if(in_array('work',$types) && ( in_array('voice',$types) || count($types)==1 ) ) {
							$attributes['work_phone'] = $vobjProp->value;
							$companyAttributes['phone'] = $vobjProp->value;
						}
						if(in_array('cell',$types) && ( in_array('voice',$types) || count($types)==1 ) ) {
							if (empty($attributes['cellular']))
								$attributes['cellular'] = $vobjProp->value;
							elseif (empty($attributes['cellular2']))
								$attributes['cellular2'] = $vobjProp->value;
						}
						if(in_array('fax',$types) && in_array('home',$types))
							$attributes['fax'] = $vobjProp->value;
						if(in_array('fax',$types) && in_array('work',$types)) {
							$companyAttributes['fax'] = $vobjProp->value;
							$attributes['work_fax'] = $vobjProp->value;
						}
						if(in_array('home',$types) && ( in_array('voice',$types) || count($types)==1 ) )
							$attributes['home_phone'] = $vobjProp->value;
					}
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
					$types = array();
					
					
					
					
					foreach ($vobjProp->parameters as $param) {
						if ($param->name=='TYPE')
							$types = explode(',',strtolower($param->value));						
					}
					
					
					if(in_array('work',$types)) {
						$addrArr = explode(';',$vobjProp->value);
						if(isset($addrArr[2]))
							$companyAttributes['address'] = $addrArr[2];
						if(isset($addrArr[3]))
							$companyAttributes['city'] = $addrArr[3];
						if(isset($addrArr[4]))
							$companyAttributes['state'] = $addrArr[4];
						if(isset($addrArr[5]))
							$companyAttributes['zip'] = $addrArr[5];						
						if(isset($addrArr[6]))
							$companyAttributes['country'] = $addrArr[6];
					}
					if(in_array('home',$types)) {
						$addrArr = explode(';',$vobjProp->value);
						if(isset($addrArr[2]))
							$attributes['address'] = $addrArr[2];
						if(isset($addrArr[3]))
							$attributes['city'] = $addrArr[3];
						if(isset($addrArr[4]))
							$attributes['state'] = $addrArr[4];
						if(isset($addrArr[5]))
							$attributes['zip'] = $addrArr[5];
						if(isset($addrArr[6]))
							$attributes['country'] = $addrArr[6];
					}
					
					
					
					if(empty($types)){
						$addrArr = explode(';',$vobjProp->value);
						if(isset($addrArr[2]))
							$companyAttributes['post_address'] = $addrArr[2];
						if(isset($addrArr[3]))
							$companyAttributes['post_city'] = $addrArr[3];
						if(isset($addrArr[4]))
							$companyAttributes['post_state'] = $addrArr[4];
						if(isset($addrArr[5]))
							$companyAttributes['post_zip'] = $addrArr[5];						
						if(isset($addrArr[6]))
							$companyAttributes['post_country'] = $addrArr[6];
					}
					break;
				case 'EMAIL':
//					foreach ($vobjProp->parameters as $param) {
//						if ($param->name=='TYPE')
//							$types = explode(',',strtolower($param->value));
//						else
//							$types = array();
//					}
//					if(in_array('pref',$types)) {
//						$attributes['email'] = $vobjProp->value;
//					} elseif(in_array('home',$types)) {
//						$attributes['email2'] = $vobjProp->value;
//					} elseif(in_array('work',$types)) {
//						$attributes['email3'] = $vobjProp->value;
//					} else {
//						$attributes['email'] = $vobjProp->value;
//					}
					if(!empty($vobjProp->value))
						$emails[]=$vobjProp->value;
					break;
				case 'TITLE':
					$attributes['function'] = $vobjProp->value;
					break;
				case 'BDAY':
					if(!empty($vobjProp->value))
						$attributes['birthday'] = substr($vobjProp->value,0,4).'-'.substr($vobjProp->value,5,2).'-'.substr($vobjProp->value,8,2);
					break;				
				case 'NOTE':
					$attributes['comment'] = $vobjProp->value;
					break;
				case 'VERSION':
				case 'LAST-MODIFIED':
					break;
				default:
					$paramsArr = array();
					foreach ($vobjProp->parameters as $param) {
						$paramsArr[] = $param->serialize();
					}
//					$remainingVcardProps[] = array('name' => $vobjProp->name, 'parameters'=>implode(';',$paramsArr), 'value'=>$vobjProp->value);					
					break;
			}
		}
		
		foreach($emails as $email){
			if(!isset($attributes['email']))
				$attributes['email']=$email;
			elseif(!isset($attributes['email2']))
				$attributes['email2']=$email;
			elseif(!isset($attributes['email3']))
				$attributes['email3']=$email;
		}
		
		//some attributes can be specified with multiple values like tel and email.
		//We don't know which value is going to map to which exact GO attribute because every client handles this differently.
		//Clear the values if they haven't been found at all.
		$attributesMultiple=array('home_phone','work_phone','fax', 'work_fax','cellular','email','email2','email3');
		foreach($attributesMultiple as $attributeName){
			if(!isset($attributes[$attributeName]))
				$attributes[$attributeName]="";
		}
		
		$attributes=array_map('trim',$attributes);
		
		$attributes = $this->_splitAddress($attributes);
		
		if(empty($attributes['last_name']) && empty($attributes['first_name']))
			$attributes['first_name']='unnamed';

		$this->setAttributes($attributes, false);		
				
		if (isset($companyAttributes['name'])) {
			$company = GO_Addressbook_Model_Company::model()->findSingleByAttributes(array('name' => $companyAttributes['name'], 'addressbook_id' => $this->addressbook_id));
			if (!$company) {
				$company = new GO_Addressbook_Model_Company();
				$company->setAttributes($companyAttributes, false);
				$company->addressbook_id = $this->addressbook_id;
			}

			if (!empty($saveToDb))
				$company->save();

			$this->setAttribute('company_id', $company->id);
		}
		
		$this->cutAttributeLengths();
		
		if($ignoreInvalidProperties){
			$this->ignoreInvalidProperties();
		}
		
		if (!empty($saveToDb))
			$this->save();
		
		if (!empty($photoFile))
			$this->setPhoto($photoFile);
		
//		foreach ($remainingVcardProps as $prop) {
//			if (!empty($this->id) && substr($prop['name'],0,2)=='X-') {
//				// Process encounters a custom property name in the VCard.
//				$arr = explode('-',$prop['name']);
//				$currentPropName = 'X-'.$arr[1];
//				if (!in_array($currentPropName,$deletedPropertiesPrefixes_nonGO)) {
//					// Process encounters a new custom property prefix in the VCard.
//					// Now deleting all properties with this contact that have this prefix.
//					// Because of $deletedPropertiesPrefixes_nonGO, this is only done once
//					// per sync per VCard.
//					$deletablePropertiesStmt = GO_Addressbook_Model_ContactVcardProperty::model()->find(
//						GO_Base_Db_FindParams::newInstance()->criteria(
//							GO_Base_Db_FindCriteria::newInstance()
//								->addCondition('contact_id',$this->id)
//								->addCondition('name',$currentPropName.'-%','LIKE')
//						)
//					);
//
//					while ($delPropModel = $deletablePropertiesStmt->fetch())
//						$delPropModel->delete();
//
//					$deletedPropertiesPrefixes_nonGO[] = $currentPropName; // Keep track of prefixes for which we have deleted the properties.
//				}
//			}
//			
//			$propModel = GO_Addressbook_Model_ContactVcardProperty::model()->find(
//				GO_Base_Db_FindParams::newInstance()
//					->single()
//					->criteria(
//						GO_Base_Db_FindCriteria::newInstance()
//							->addCondition('contact_id',$this->id)
//							->addCondition('name',$prop['name'])
//							->addCondition('parameters',$prop['parameters'])
//					)
//				);
//			if (empty($propModel))
//				$propModel = new GO_Addressbook_Model_ContactVcardProperty();
//			$propModel->contact_id = $this->id;
//			$propModel->name = $prop['name'];
//			$propModel->parameters = $prop['parameters'];
//			$propModel->value = $prop['value'];
//			$propModel->cutAttributeLengths();
//			$propModel->save();
//		}
		
		return $this;
	}
	
	private function _splitAddress($attributes){
		if(isset($attributes['address'])){
			$attributes['address_no']='';
			$attributes['address']=  GO_Base_Util_String::normalizeCrlf($attributes['address'], "\n");
			$lines = explode("\n", $attributes['address']);
			if(count($lines)>1){
				$attributes['address']=$lines[0];
				$attributes['address_no']=$lines[1];
			}else
			{
				$attributes['address']=$this->_getAddress($lines[0]);
				$attributes['address_no']=$this->_getAddressNo($lines[0]);
			}
		}
		
		return $attributes;
	}
	
	/**
	* Gets the street name from address.
	*
	* @param  string	$address Contains the address (street-name and house-number)
	* @access private
	* @return string
	*/
	function _getAddress($address) {
		if (!$address = substr($address, 0, strrpos($address, " "))) {
			return '';
		}

		return trim($address);
	}

	/**
	* Gets the house-number from address.
	*
	* @param  string	$address Contains the address (street-name and house-number)
	* @access private
	* @return string
	*/
	function _getAddressNo($address) {
		if (!$address_no = strrchr($address, " ")) {
			return '';
		}

		return trim($address_no);
	}

		/**
	 * Get this task as a VObject. This can be turned into a vcard file data.
	 * 
	 * @return Sabre_VObject_Component 
	 */
	public function toVObject(){
		$e=new Sabre\VObject\Component('vcard');
					
		$e->add('VERSION','3.0');
		$e->prodid='-//Intermesh//NONSGML Group-Office '.GO::config()->version.'//EN';		
		
		if(empty($this->uuid)){
			$this->uuid=GO_Base_Util_UUID::create('contact', $this->id);
			$this->save(true);
		}
		
		$e->uid=$this->uuid;
		$e->add('N',$this->last_name.";".$this->first_name.";".$this->middle_name.';'.$this->title.';'.$this->suffix);
		$e->add('FN',$this->name);
		
		if (!empty($this->email)) {
			$p = new Sabre\VObject\Property('EMAIL',$this->email);
			$p->add(new GO_Base_VObject_Parameter('TYPE','INTERNET'));
			$e->add($p);
		}
		if (!empty($this->email2)) {
			$p = new Sabre\VObject\Property('EMAIL',$this->email2);
			$p->add(new GO_Base_VObject_Parameter('TYPE','HOME,INTERNET'));
			$e->add($p);
		}
		if (!empty($this->email3)) {
			$p = new Sabre\VObject\Property('EMAIL',$this->email3);
			$p->add(new GO_Base_VObject_Parameter('TYPE','WORK,INTERNET'));
			$e->add($p);
		}
		
		if (!empty($this->function))
			$e->add('TITLE',$this->function);
		
		if (!empty($this->home_phone)) {
			$p = new Sabre\VObject\Property('TEL',$this->home_phone);
			$p->add(new GO_Base_VObject_Parameter('TYPE','HOME,VOICE'));
			$e->add($p);	
		}
		if (!empty($this->work_phone)) {
			$p = new Sabre\VObject\Property('TEL',$this->work_phone);
			$p->add(new GO_Base_VObject_Parameter('TYPE','WORK,VOICE'));
			$e->add($p);	
		}
		if (!empty($this->work_fax)) {
			$p = new Sabre\VObject\Property('TEL',$this->work_fax);
			$p->add(new GO_Base_VObject_Parameter('TYPE','WORK,FAX'));
			$e->add($p);	
		}
		if (!empty($this->fax)) {
			$p = new Sabre\VObject\Property('TEL',$this->fax);
			$p->add(new GO_Base_VObject_Parameter('TYPE','HOME,FAX'));
			$e->add($p);	
		}
		if (!empty($this->cellular)) {
			$p = new Sabre\VObject\Property('TEL',$this->cellular);
			$p->add(new GO_Base_VObject_Parameter('TYPE','CELL,VOICE'));
			$e->add($p);	
		}
		if (!empty($this->cellular2)) {
			$p = new Sabre\VObject\Property('TEL',$this->cellular2);
			$p->add(new GO_Base_VObject_Parameter('TYPE','CELL,VOICE'));
			$e->add($p);	
		}
		if (!empty($this->birthday)) {
			$e->add('BDAY',$this->birthday);
		}
		
		if (!empty($this->company)) {
			$e->add('ORG',$this->company->name.';'.$this->department.';'.$this->company->name2);
			$p = new Sabre\VObject\Property('ADR',';;'.$this->company->address.' '.$this->company->address_no.';'.
				$this->company->city.';'.$this->company->state.';'.$this->company->zip.';'.$this->company->country);
			$p->add('TYPE','WORK');
			$e->add($p);
			
			
			$p = new Sabre\VObject\Property('ADR',';;'.$this->company->post_address.' '.$this->company->post_address_no.';'.
				$this->company->post_city.';'.$this->company->post_state.';'.$this->company->post_zip.';'.$this->company->post_country);
			$e->add($p);
		}
		
		$p = new Sabre\VObject\Property('ADR',';;'.$this->address.' '.$this->address_no.';'.
			$this->city.';'.$this->state.';'.$this->zip.';'.$this->country);
		$p->add('TYPE','HOME');
		$e->add($p);
		
		if(!empty($this->comment)){
			$e->note=$this->comment;
		}
		
//		$mtimeDateTime = new DateTime('@'.$this->mtime);
//		$rev = new Sabre_VObject_Element_DateTime('LAST-MODIFIED');
//		$rev->setDateTime($mtimeDateTime, Sabre_VObject_Element_DateTime::UTC);		
//		$e->add($rev);
		
		$e->rev=gmdate("Y-m-d\TH:m:s\Z", $this->mtime);
		
		
		if($this->getPhotoFile()->exists()){
			$p = new Sabre\VObject\Property('photo', base64_encode($this->getPhotoFile()->getContents()));
			$p->add('type','jpeg');
			$p->add('encoding','b');
			$e->add($p);	
		}
		
//		$propModels = $this->vcardProperties->fetchAll(PDO::FETCH_ASSOC);
//		
//		foreach ($propModels as $propModel) {
//			$p = new Sabre\VObject\Property($propModel['name'],$propModel['value']);
//			if(!empty($propModel['parameters'])){
//				$paramStrings = explode(';',$propModel['parameters']);
//				foreach ($paramStrings as $paramString) {
//					if(!empty($paramString)){
//						$paramStringArr = explode('=',$paramString);
//
//						$param = new GO_Base_VObject_Parameter($paramStringArr[0]);
//						if (!empty($paramStringArr[1]))
//							$param->value = $paramStringArr[1];
//						$p->add($param);
//					}
//				}
//			}
//			$e->add($p);
//		}
		
		return $e;
	}
	
	/**
	 * Find contacts by e-mail address
	 * 
	 * @param string $email
	 * @param GO_Base_Db_FindParams $findParams Optional
	 * @return GO_Base_Db_ActiveStatement 
	 */
	public function findByEmail($email, $findParams = false){
		
		if(!$findParams)
			$findParams = GO_Base_Db_FindParams::newInstance();
		
		$findParams->getCriteria()->mergeWith(GO_Base_Db_FindCriteria::newInstance()
										->addCondition('email', $email)
										->addCondition('email2', $email, '=', 't', false)
										->addCondition('email3', $email, '=', 't', false)
		);

		return GO_Addressbook_Model_Contact::model()->find($findParams);		
	}
	
	/**
	 * Find contacts by e-mail address
	 * 
	 * @param string $email
	 * @return GO_Base_Db_ActiveStatement 
	 */
	public function findSingleByEmail($email){
		
		$criteria = GO_Base_Db_FindCriteria::newInstance()
			->addCondition('email',$email)
			->addCondition('email2', $email,'=','t',false)
			->addCondition('email3', $email,'=','t',false);
			
		$fp = GO_Base_Db_FindParams::newInstance()->criteria($criteria)->limit(1);
		$stmt = GO_Addressbook_Model_Contact::model()->find($fp);
		return $stmt->fetch();
	}
	
	protected function afterMergeWith(GO_Base_Db_ActiveRecord $model) {
		
		//this contact becomes the new user contact
		if($this->go_user_id>0)
			$model->go_user_id=0;
		
		if(!$this->photo && $model->photo){
			rename($model->photo, $this->_getPhotoPath());
		}
		
		
		return parent::afterMergeWith($model);
	}
	
	
	protected function getFirstEmail(){
		if(!empty($this->email)){
			return $this->email;
		}elseif(!empty($this->email2)){
			return $this->email2;
		}elseif(!empty($this->email3)){
			return $this->email3;
		}else{
			return false;
		}
	}
	
	
	protected function getAge(){
		if(empty($this->birthday))
			return "";
		
		$date = new DateTime($this->birthday);
		$diff = $date->diff(new DateTime());
		
		return $diff->y;
	}
	
	/**
	 * Get all user contacts that a given user is authorized to see
	 * 
	 * @param int $user_id
	 * @param GO_Base_Db_FindParams $findParams
	 * @return GO_Addressbook_Model_Contact Statement
	 */
	public function findUsers($user_id, GO_Base_Db_FindParams $findParams=null){
		$aclJoinCriteria = GO_Base_Db_FindCriteria::newInstance()
						->addRawCondition('a.acl_id', 'goUser.acl_id', '=', false);

		$aclWhereCriteria = GO_Base_Db_FindCriteria::newInstance()
				->addCondition('user_id', $user_id, '=', 'a', false)
				->addInCondition("group_id", GO_Base_Model_User::getGroupIds($user_id), "a", false);

		$fp = GO_Base_Db_FindParams::newInstance()				
				->group('t.id')
				->ignoreAcl()
				->joinRelation('goUser')							
				->join(GO_Base_Model_AclUsersGroups::model()->tableName(), $aclJoinCriteria, 'a', 'INNER');

		$fp->getCriteria()
						->mergeWith($aclWhereCriteria);
		
		
		if(isset($findParams))
			$fp->mergeWith ($findParams);
		
		return GO_Addressbook_Model_Contact::model()->find($fp);
	}
	
}