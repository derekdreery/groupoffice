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
	
	/**
	 * Import a task from a VObject 
	 * 
	 * @param Sabre_VObject_Component $vobject
	 * @param array $attributes Extra attributes to apply to the event
	 * @return GO_Tasks_Model_Task 
	 */
//	public function importVObject(Sabre_VObject_Component $vobject, $attributes=array()){
//		//$event = new GO_Calendar_Model_Event();
//		
//		$this->uuid = (string) $vobject->uid;
//		$this->name = (string) $vobject->summary;
//		$this->description = (string) $vobject->description;
//		if(!empty($vobject->dtstart))
//			$this->start_time = $vobject->dtstart->getDateTime()->format('U');
//		
//		if(!empty($vobject->dtend))
//			$this->due_time = $vobject->dtend->getDateTime()->format('U');
//		
//		if(!empty($vobject->due))
//			$this->due_time = $vobject->due->getDateTime()->format('U');
//				
//		if($vobject->dtstamp)
//			$this->mtime=$vobject->dtstamp->getDateTime()->format('U');
//		
//		if(empty($this->due_time))
//			$this->due_time=time();
//		
//		if(empty($this->start_time))
//			$this->start_time=$this->due_time;
//		
//		if($vobject->rrule){			
//			$rrule = new GO_Base_Util_Icalendar_Rrule();
//			$rrule->readIcalendarRruleString($this->start_time, (string) $vobject->rrule);			
//			$this->rrule = $rrule->createRrule();
//			$this->repeat_end_time = $rrule->until;
//		}		
//		
//		//var_dump($vobject->status);
//		if($vobject->status)
//			$this->status=(string) $vobject->status;
//		
//		if($vobject->duration){
//			$duration = GO_Base_VObject_Reader::parseDuration($vobject->duration);
//			$this->end_time = $this->start_time+$duration;
//		}
//		
//		if(!empty($vobject->priority))
//		{			
//			if((string) $vobject->priority>5)
//			{
//				$this->priority=self::PRIORITY_LOW;
//			}elseif((string) $vobject->priority==5)
//			{
//				$this->priority=self::PRIORITY_NORMAL;
//			}else
//			{
//				$this->priority=self::PRIORITY_HIGH;
//			}
//		}
//		
//		if(!empty($vobject->completed)){
//			$this->completion_time=$vobject->completed->getDateTime()->format('U');
//			$this->status='COMPLETED';
//		}else
//		{
//			$this->completion_time=0;
//		}
//		
//		if($this->status=='COMPLETED' && empty($this->completion_time))
//			$this->completion_time=time();
//		
//		if($vobject->valarm){
//			
//		}else
//		{
//			$this->reminder=0;
//		}		
//		
//		$this->setAttributes($attributes);
//		
//		$this->save();
//		
//		////////////////////////////////////////////////////////////////////////////
//		
//		ini_set('max_execution_time', 360);
//
//	    $addressbook_id = isset($_REQUEST['addressbook_id']) ? ($_REQUEST['addressbook_id']) : 0;
//	    $separator	= isset($_REQUEST['separator']) ? ($_REQUEST['separator']) : ',';
//	    $quote	= isset($_REQUEST['quote']) ? ($_REQUEST['quote']) : '"';
//	    $import_type = isset($_REQUEST['import_type']) ? ($_REQUEST['import_type']) : '';
//	    $import_filetype = isset($_REQUEST['import_filetype']) ? ($_REQUEST['import_filetype']) : '';
//
//	    $addressbook = $ab->get_addressbook($addressbook_id);
//					ini_set('memory_limit', '256M');
//
//	    $result['success'] = true;
//	    $result['feedback'] = $feedback;
//
//	    switch($import_filetype) {
//		case 'vcf':
//
//		    break;
//		case 'csv':
//
//		    if(isset($GLOBALS['GO_MODULES']->modules['customfields']) && $GLOBALS['GO_MODULES']->modules['customfields']['read_permission']) {
//			require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
//			$cf = new customfields();
//			$company_customfields = $cf->get_authorized_fields($GLOBALS['GO_SECURITY']->user_id, 3);
//			$contact_customfields = $cf->get_authorized_fields($GLOBALS['GO_SECURITY']->user_id, 2);
//		    }
//
//		    $fp = fopen($_SESSION['GO_SESSION']['addressbook']['import_file'], "r");
//
//		    if (!$fp || !$addressbook = $ab->get_addressbook($addressbook_id)) {
//			unlink($_SESSION['GO_SESSION']['addressbook']['import_file']);
//			throw new Exception($lang['comon']['selectError']);
//		    }
//
//		    fgets($fp, 4096);
//		    while (!feof($fp)) {
//			$record = fgetcsv($fp, 4096, $separator, $quote);
//
//			$new_id=0;
//
//			if ($import_type == 'contacts') {
//			    if ((isset ($record[$_POST['first_name']]) && $record[$_POST['first_name']] != "") || (isset ($record[$_POST['last_name']]) && $record[$_POST['last_name']] != '')) {
//				$contact=array();
//				$contact['email_allowed']='1';
//				
//				$this->title = $vobject->title;
//				$this->first_name = $vobject->first_name;
//				$this->middle_name = $vobject->middle_name;
//				$this->last_name = $vobject->last_name;
//				$this->initials = $vobject->initials;
//				$this->sex = $vobject->sex;
//				$this->birthday = $vobject->birthday;
//				$this->email = $vobject->email;
//				$this->email2 = $vobject->email2;
//				$this->email3 = $vobject->email3;
//				$this->work_phone = $vobject->work_phone;
//				$this->home_phone = $vobject->home_phone;
//				$this->fax = $vobject->fax;
//				$this->work_fax = $vobject->work_fax;
//				$this->cellular = $vobject->cellular;
//				$this->country = $vobject->country;
//				$this->state = $vobject->state;
//				$this->city = $vobject->city;
//				$this->zip = $vobject->zip;
//				$this->address = $vobject->address;
//				$this->address_no = $vobject->address_no;
//				$this->department = $vobject->department;
//				$this->function = $vobject->function;
//				$this->salutation = $vobject->salutation;
//				$this->comment = $vobject->comment;
//
//				if (isset($vobject->company_name) || isset($vobject->company_name2)) {
//					$company = GO_Addressbook_Model_Company::model()->findByAttributes(
//							array(
//								'addressbook_id' => $attributes['addressbook_id'],
//								'company_name' =>
//							)
//						);
//					if (isset($vobject->company_name))
//						$company->setAttribute('company_name',$vobject->company_name);
//					if (isset($vobject->company_name2))
//						$company->setAttribute('company_name2',$vobject->company_name2);
//				}
//				
//				$company_name = isset ($record[$_POST['company_name']]) ? trim($record[$_POST['company_name']]) : '';
//				$company_name2 = isset ($record[$_POST['company_name2']]) ? trim($record[$_POST['company_name2']]) : '';
//				if ($company_name != '') {
//				    $contact['company_id'] = $ab->get_company_id_by_name($company_name, $addressbook_id);
//				    if(!$contact['company_id']) {
//							$company=array();
//							$company['addressbook_id']=$addressbook_id;
//							$company['name']=$company_name;
//							$company['name2']=$company_name2;
//							$contact['company_id']=$ab->add_company($company);
//				    }
//				}else {
//				    $contact['company_id']=0;
//				}
//
//				$contact['addressbook_id'] = $addressbook_id;
//				$new_id=$ab->add_contact($contact, $addressbook);
//				$new_type=2;
//			    }
//			} else {
//			    if (isset ($record[$_POST['name']]) && $record[$_POST['name']] != '') {
//				$company=array();
//				$company['name'] = trim($record[$_POST['name']]);
//				$company['name2'] = trim($record[$_POST['name2']]);
//
//				//if (!$ab->get_company_by_name($_POST['addressbook_id'], $company['name']))
//				{
//
//				    $company['email_allowed']='1';
//				    $company['email'] = isset ($record[$_POST['email']]) ? String::get_email_from_string($record[$_POST['email']]) : '';
//				    $company['phone'] = isset ($record[$_POST['phone']]) ? trim($record[$_POST['phone']]) : '';
//				    $company['fax'] = isset ($record[$_POST['fax']]) ? trim($record[$_POST['fax']]) : '';
//				    $company['country'] = isset ($record[$_POST['country']]) ? trim($record[$_POST['country']]) : '';
//				    $company['state'] = isset ($record[$_POST['state']]) ? trim($record[$_POST['state']]) : '';
//				    $company['city'] = isset ($record[$_POST['city']]) ? trim($record[$_POST['city']]) : '';
//				    $company['zip'] = isset ($record[$_POST['zip']]) ? trim($record[$_POST['zip']]) : '';
//				    $company['address'] = isset ($record[$_POST['address']]) ? trim($record[$_POST['address']]) : '';
//				    $company['address_no'] = isset ($record[$_POST['address_no']]) ? trim($record[$_POST['address_no']]) : '';
//				    $company['post_country'] = isset ($record[$_POST['post_country']]) ? trim($record[$_POST['post_country']]) : '';
//				    $company['post_state'] = isset ($record[$_POST['post_state']]) ? trim($record[$_POST['post_state']]) : '';
//				    $company['post_city'] = isset ($record[$_POST['post_city']]) ? trim($record[$_POST['post_city']]) : '';
//				    $company['post_zip'] = isset ($record[$_POST['post_zip']]) ? trim($record[$_POST['post_zip']]) : '';
//				    $company['post_address'] = isset ($record[$_POST['post_address']]) ? trim($record[$_POST['post_address']]) : '';
//				    $company['post_address_no'] = isset ($record[$_POST['post_address_no']]) ? trim($record[$_POST['post_address_no']]) : '';
//				    $company['homepage'] = isset ($record[$_POST['homepage']]) ? trim($record[$_POST['homepage']]) : '';
//				    $company['bank_no'] = isset ($record[$_POST['bank_no']]) ? trim($record[$_POST['bank_no']]) : '';
//				    $company['vat_no'] = isset ($record[$_POST['vat_no']]) ? trim($record[$_POST['vat_no']]) : '';
//				    $company['addressbook_id']  = $_POST['addressbook_id'];
//
//				    $new_id=$ab->add_company($company, $addressbook);
//				    $new_type=3;
//				}
//			    }
//			}
//
//			if($new_id>0) {
//			    if(isset($cf)) {
//				$customfields = $new_type==2 ? $contact_customfields : $company_customfields;
//				$cf_record=array('link_id'=>$new_id);
//				foreach($customfields as $field) {
//				    if(isset($_POST[$field['dataname']]) && isset($record[$_POST[$field['dataname']]]))
//					$cf_record[$field['dataname']]=$record[$_POST[$field['dataname']]];
//				}
//				$cf->insert_row('cf_'.$new_type,$cf_record);
//			    }
//			}
//		    }
//		    break;
//	    }
//		
//		return $this;
//	}

}