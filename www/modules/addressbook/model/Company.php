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
 * @property int $files_folder_id
 * @property boolean $email_allowed
 * @property int $mtime
 * @property int $muser_id
 * @property int $ctime
 * @property string $crn Company registration number
 * @property string $iban
 * @property string $vat_no
 * @property string $bank_no
 * @property string $bank_bic
 * @property string $comment
 * @property string $homepage
 * @property string $email
 * @property string $fax
 * @property string $phone
 * @property string $post_zip
 * @property string $post_country
 * @property string $post_state
 * @property string $post_city
 * @property string $post_address_no
 * @property string $post_address
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $zip
 * @property string $address
 * @property string $address_no
 * @property string $name2
 * @property string $name
 * @property int $addressbook_id
 * @property int $user_id
 * @property int $id
 * @property int $link_id
 * @property string $invoice_email
 */

class GO_Addressbook_Model_Company extends GO_Base_Db_ActiveRecord {
	
	/**
	 * Check the VAT number with the VIES service.
	 * 
	 * @var boolean
	 */
	public $checkVatNumber=false;
	
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
		
		return "GO_Addressbook_Customfields_Model_Company";
	}
	
	public function defaultAttributes() {
		return array(
				'country'=>GO::config()->default_country,
				'post_country'=>GO::config()->default_country
		);
	}
	
	public function attributeLabels() {
		$attr = parent::attributeLabels();
		
		$attr['postAddressIsEqual']="Post address is equal to visit address";
		
		return $attr;
	}
	
	public function validate() {
		if(!empty($this->vat_no) && GO_Base_Util_Validate::isEuCountry($this->post_country)){
			
			if(substr($this->vat_no,0,2)!=$this->post_country)			
				$this->vat_no = $this->post_country.' '.$this->vat_no;
			
			if($this->checkVatNumber && ($this->isModified('vat_no') || $this->isModified('post_country')) && !GO_Base_Util_Validate::checkVat($this->post_country, $this->vat_no))
				$this->setValidationError('vat_no', 'European VAT (Country:'.$this->post_country.', No.:'.$this->vat_no.') number is invalid according to VIES. Please click <a target="_blank" href="http://ec.europa.eu/taxation_customs/vies/" target="_blank">here</a> to check it on their website.');
		}
		
		return parent::validate();
	}
	
	protected function init() {
		$this->columns['addressbook_id']['required']=true;
		$this->columns['email']['regex']=GO_Base_Util_String::get_email_validation_regex();
		
		return parent::init();
	}

	public function relations(){
		return array(
			'addressbook' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Addressbook_Model_Addressbook', 'field'=>'addressbook_id'),
			'contacts' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Contact', 'field'=>'company_id', 'delete'=>false),
			'addresslists' => array('type'=>self::MANY_MANY, 'model'=>'GO_Addressbook_Model_Addresslist', 'field'=>'company_id', 'linkModel' => 'GO_Addressbook_Model_AddresslistCompany'),
		);
	}


	protected function getCacheAttributes() {
		
		$name =$this->name;
		
		if($this->addressbook)
			$name .= ' ('.$this->addressbook->name.')';
		
		return array(
				'name' => $name
		);
	}
	/**
	 * The files module will use this function.
	 */
	public function buildFilesPath() {
		
		$new_folder_name = GO_Base_Fs_Base::stripInvalidChars($this->name).' ('.$this->id.')';
		$new_path = $this->addressbook->buildFilesPath().'/companies';
		

		$char = GO_Addressbook_Utils::getIndexChar($new_folder_name);
			
		$new_path .= '/'.$char.'/'.$new_folder_name;
		return $new_path;
	}
	
	protected function afterSave($wasNew) {
		
		if(!$wasNew && $this->isModified('addressbook_id')){
			
			//make sure contacts and companies are in the same addressbook.
			$whereCriteria = GO_Base_Db_FindCriteria::newInstance()
							->addCondition('company_id', $this->id)
							->addCondition('addressbook_id', $this->addressbook_id,'!=');
			
			$findParams = GO_Base_Db_FindParams::newInstance()
							->ignoreAcl()
							->criteria($whereCriteria);			
			
			$stmt = GO_Addressbook_Model_Contact::model()->find($findParams);			
			while($contact = $stmt->fetch()){
				$contact->addressbook_id=$this->addressbook_id;
				$contact->save();
			}
		}		
		return parent::afterSave($wasNew);
	}
	
	protected function afterMergeWith(GO_Base_Db_ActiveRecord $model) {
		
		//move company employees to this model too.
		if($model->className()==$this->className()){
			$stmt = $model->contacts();
			while($contact = $stmt->fetch())
			{
				$contact->company_id=$this->id;
				$contact->save();
			}
		}
		
		return parent::afterMergeWith($model);
	}
	
	protected function beforeSave() {
		if(!empty($this->homepage))
			$this->homepage = GO_Base_Util_Http::checkUrlForHttp($this->homepage);
		
		return parent::beforeSave();
	}
	
	/**
	 * Function to let this model copy the visit address to the post address.
	 * After this function is called, you need to call the save() function to 
	 * actually save this model. 
	 * 
	 */
	public function setPostAddressFromVisitAddress(){
		$this->post_address=$this->address;
		$this->post_address_no=$this->address_no;
		$this->post_zip=$this->zip;
		$this->post_city=$this->city;
		$this->post_country=$this->country;
		$this->post_state=$this->state;
	}
	
	
	/**
	 * Used on website registration form
	 * @return boolean
	 */
	protected function getPostAddressIsEqual(){
		return ($this->post_address==$this->address &&
		$this->post_address_no==$this->address_no &&
		$this->post_zip==$this->zip &&
		$this->post_city==$this->city &&
		$this->post_country==$this->country &&
		$this->post_state==$this->state);
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
	
	/**
	 * Get the full post address formatted according to the country standards.
	 * 
	 * @return string
	 */
	public function getFormattedPostAddress()
	{
		return GO_Base_Util_Common::formatAddress(
						$this->post_country, 
						$this->post_address, 
						$this->post_address_no,
						$this->post_zip, 
						$this->post_city, 
						$this->post_state
						);
	}

}