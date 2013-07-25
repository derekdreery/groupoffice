<?php
/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @package GO.modules.addressbook.model
 * @version $Id: example.php 7607 20120101Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
 
/**
 * The GO_Addressbook_Model_Template model
 *
 * @package GO.modules.addressbook.model
 * @property string $extension
 * @property string $content
 * @property int $acl_id
 * @property string $name
 * @property int $type
 * @property int $user_id
 * @property int $id
 * @property int $acl_write
 */

class GO_Addressbook_Model_Template extends GO_Base_Db_ActiveRecord{
	
	const TYPE_EMAIL=0;
	
	const TYPE_DOCUMENT=1;
	
	public $htmlSpecialChars=true;
	
	private $_defaultTags;
	private $_lineBreak;
	
		/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Addressbook_Model_Template 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	// TODO : move language from mailings module to addressbook module
	protected function getLocalizedName() {
		return GO::t('template', 'addressbook');
	}
	
	protected function init() {
		$this->columns['content']['required']=true;
		
//		$this->addDefaultTag('contact:salutation', GO::t('default_salutation_unknown'));
		$this->addDefaultTag('salutation', GO::t('default_salutation_unknown'));
		$this->addDefaultTag('date', GO_Base_Util_Date::get_timestamp(time(), false));
		
		return parent::init();
	}
	
	/**
	 * Add a default tag value.
	 * 
	 * @param string $key
	 * @param string $value 
	 */
	public function addDefaultTag($key, $value){
		$this->_defaultTags[$key]=$value;
	}
	
	public function setLineBreak($lb){
		$this->_lineBreak=$lb;
	}
	
	
	public function aclField(){
		return 'acl_id';	
	}
	
	public function tableName(){
		return 'ab_email_templates';
	}
	
	private function _addTagPrefixAndRemoveEmptyValues($attributes, $tagPrefix){
		if(!empty($tagPrefix)){
			foreach($attributes as $key=>$value){
				if(!empty($value))
					$newAttributes[$tagPrefix.$key]=$value;
			}
			$attributes=$newAttributes;
		}
		return $attributes;
	}
	
	private function _getModelAttributes($model, $tagPrefix=''){
		$attributes = $model->getAttributes('formatted');		
		
		if(method_exists($model, 'getFormattedAddress')){
			$attributes['formatted_address']=$model->getFormattedAddress();
		}
		
		if(method_exists($model, 'getFormattedPostAddress')){
			$attributes['formatted_post_address']=$model->getFormattedPostAddress();
		}
				
		if($model->customfieldsRecord){
			$attributes = array_merge($attributes, $model->customfieldsRecord->getAttributes('formatted'));
		}

		$attributes = $this->_addTagPrefixAndRemoveEmptyValues($attributes, $tagPrefix);
		
		return $attributes;
	}
	
	private function _getUserAttributes(){
		$attributes=array();
		
		if(GO::user() && GO::user()->contact){
			$attributes = array_merge($attributes, $this->_getModelAttributes(GO::user()->contact,'user:'));
			$attributes['user:sirmadam']=GO::user()->contact->sex=="M" ? GO::t('cmdSir','addressbook') : GO::t('cmdMadam', 'addressbook');
			if(GO::user()->contact->company){
				$attributes = array_merge($attributes, $this->_getModelAttributes(GO::user()->contact->company,'usercompany:'));
			}
			
			$attributes = array_merge($attributes, $this->_getModelAttributes(GO::user(),'user:'));			
		}
		return $attributes;
	}
	
	/**
	 * Replaces all contact, company and user tags in a string.
	 * 
	 * Tags look like this:
	 * 
	 * {contact:modelAttributeName}
	 * 
	 * {company:modelAttributeName}
	 * 
	 * {user:modelAttributeName}
	 * 
	 * @param string $content Containing the tags
	 * @param GO_Addressbook_Model_Contact $contact
	 * @param boolean $leaveEmptyTags Set to true if you don't want unreplaced tags to be cleaned up.
	 * @return string 
	 */
	public function replaceContactTags($content, GO_Addressbook_Model_Contact $contact, $leaveEmptyTags=false){
		
		if(GO::modules()->customfields)
			GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport=true;
		
		$attributes = $leaveEmptyTags ? array() : $this->_defaultTags;
		
		if(!empty($contact->salutation))
			$attributes['salutation']=$contact->salutation;
		
		$attributes['contact:sirmadam']=$contact->sex=="M" ? GO::t('sir') : GO::t('madam');
		
		$attributes = array_merge($attributes, $this->_getModelAttributes($contact, 'contact:'));
		if($contact->company)
		{
			$attributes = array_merge($attributes, $this->_getModelAttributes($contact->company, 'company:'));
		}
		
		$attributes = array_merge($attributes, $this->_getUserAttributes());
				
		if(GO::modules()->customfields)
			GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport=false;
		
		return $this->_parse($content, $attributes, $leaveEmptyTags);
	}
	
	
	/**
	 * Replaces all tags of a model.
	 * 
	 * Tags look like this:
	 * 
	 * {$tagPrefix:modelAttributeName}
	 * 
	 * @param string $content Containing the tags
	 * @param GO_Base_Db_ActiveRecord $model
	 * @param string $tagPrefix
	 * @param boolean $leaveEmptyTags Set to true if you don't want unreplaced tags to be cleaned up.
	 * @return string 
	 */
	public function replaceModelTags($content, $model, $tagPrefix='', $leaveEmptyTags=false){
		
		if(GO::modules()->customfields)
			GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport=true;
		
		$attributes = $leaveEmptyTags ? array() : $this->_defaultTags;
		
		$attributes = array_merge($attributes, $this->_getModelAttributes($model, $tagPrefix));
		
		$attributes = array_merge($attributes, $this->_getUserAttributes());
		
		$content = $this->_replaceRelations($content, $model, $tagPrefix, $leaveEmptyTags);
		
		if(GO::modules()->customfields)
			GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport=false;
	
		return $this->_parse($content, $attributes, $leaveEmptyTags);		
	}
	
	/**
	 * 
	 * Replaces relations if found in the template.
	 * eg. {project:responsibleUser:name}
	 * 
	 * @param type $content
	 * @param type $model
	 * @param type $tagPrefix
	 * @param type $leaveEmptyTags 
	 */
	private function _replaceRelations($content, $model, $tagPrefix='', $leaveEmptyTags=false){
		
		$relations = $model->relations();
		$pattern = '/'.preg_quote($tagPrefix,'/').'([^:]+):[^\}]+\}/';
		if(preg_match_all($pattern,$content, $matches)){
			foreach($matches[1] as $relation){
				if(isset($relations[$relation])){
					$relatedModel = $model->$relation;	

					if($relatedModel){

						$content = $this->replaceModelTags($content, $relatedModel, $tagPrefix.$relation.':', $leaveEmptyTags);
					}
				}
			}
		}
		return $content;
	}
	
	private function _parse($content, $attributes, $leaveEmptyTags){
		
		$attributes = array_merge($this->_defaultTags, $attributes);
		
		if($this->htmlSpecialChars){
			foreach($attributes as $key=>$value)
				$attributes[$key]=htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
		}
		
		if(isset($this->_lineBreak)){
			foreach($attributes as $key=>$value)
				$attributes[$key]=str_replace("\n", $this->_lineBreak, $attributes[$key]);
		}
		
		$templateParser = new GO_Base_Util_TemplateParser();
		return $templateParser->parse($content, $attributes, $leaveEmptyTags);
	}
	
	/**
	 * Replaces all tags of the current user.
	 * 
	 * Tags look like this:
	 * 
	 * {user:modelAttributeName}
	 * 
	 * @param string $content Containing the tags
	 * @param boolean $leaveEmptyTags Set to true if you don't want unreplaced tags to be cleaned up.
	 * @return string 
	 */
	public function replaceUserTags($content, $leaveEmptyTags=false){
		if(GO::modules()->customfields)
			GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport=true;
		
		$attributes = $leaveEmptyTags ? array() : $this->_defaultTags;
		
		$attributes = array_merge($attributes, $this->_getUserAttributes());
		
		//$attributes['contact:salutation']=GO::t('default_salutation_unknown');
		
		if(GO::modules()->customfields)
			GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport=false;
		
		return $this->_parse($content, $attributes, $leaveEmptyTags);
	}
	
	/**
	 * Replaces customtags
	 * 
	 * Tags look like this:
	 * 
	 * {$key}
	 * 
	 * @param string $content Containing the tags
	 * @param array $attributes
	 * @param boolean $leaveEmptyTags Set to true if you don't want unreplaced tags to be cleaned up.
	 * @return string 
	 */
	public function replaceCustomTags($content, $attributes, $leaveEmptyTags=false){
		return $this->_parse($content, $attributes, $leaveEmptyTags);
	}

//	/**
//	 * @return GO_Email_Model_SavedMessage
//	 */
//	private function _getMessage(){
//		if(!isset($this->_message)){
//			
//			//todo getFromMimeData
//			$this->_message = GO_Email_Model_SavedMessage::model()->createFromMimeData($this->content);
//
//		}
//		return $this->_message;
//	}
//	protected function getBody(){
//		return $this->_getMessage()->getHtmlBody();
//	}
	
}