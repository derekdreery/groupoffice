<?php
class GO_Addressbook_Model_Template extends GO_Base_Db_ActiveRecord{
	
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
	
	
	public function aclField(){
		return 'acl_id';	
	}
	
	public function tableName(){
		return 'ab_email_templates';
	}
	
	private function _addTagPrefix($attributes, $tagPrefix){
		if(!empty($tagPrefix)){
			foreach($attributes as $key=>$value){
				$newAttributes[$tagPrefix.$key]=$value;
			}
			$attributes=$newAttributes;
		}
		return $attributes;
	}
	
	private function _getModelAttributes($model, $tagPrefix=''){
		$attributes = $model->getAttributes('formatted');		
		$attributes = $this->_addTagPrefix($attributes, $tagPrefix);
		
		if($model->customfieldsRecord){
			$attributes = array_merge($attributes, $model->customfieldsRecord->getAttributes('formatted'));
		}
		
		return $attributes;
	}
	
	private function _getUserAttributes(){
		$attributes=array();
		
		if(GO::user() && GO::user()->contact){
			$attributes = array_merge($attributes, $this->_getModelAttributes(GO::user()->contact,'user:'));
			if(GO::user()->contact->company){
				$attributes = array_merge($attributes, $this->_getModelAttributes(GO::user()->contact->company,'usercompany:'));
			}
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
		
		$attributes = $this->_getDefaultTags();
		
		$attributes = array_merge($attributes, $this->_getModelAttributes($contact, 'contact:'));
		if($contact->company)
		{
			$attributes = array_merge($attributes, $this->_getModelAttributes($contact->company, 'company:'));
		}
		
		$attributes = array_merge($attributes, $this->_getUserAttributes());
		
		$templateParser = new GO_Base_Util_TemplateParser();
		return $templateParser->parse($content, $attributes, $leaveEmptyTags);
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
		$attributes = $this->_getDefaultTags();
		
		$attributes = array_merge($attributes, $this->_getModelAttributes($model, $tagPrefix));
		
		$attributes = array_merge($attributes, $this->_getUserAttributes());
		
		$templateParser = new GO_Base_Util_TemplateParser();
		return $templateParser->parse($content, $attributes, $leaveEmptyTags);
	}
	
	private function _getDefaultTags(){
		$attributes['contact:salutation']=GO::t('default_salutation_unknown');
		$attributes['date']=GO_Base_Util_Date::get_timestamp(time(), false);
		
		return $attributes;
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
		
		$attributes = $this->_getDefaultTags();
		
		$attributes = array_merge($attributes, $this->_getUserAttributes());
		
		$attributes['contact:salutation']=GO::t('default_salutation_unknown');
		
		//var_dump($attributes);
		
		$templateParser = new GO_Base_Util_TemplateParser();
		return $templateParser->parse($content, $attributes, $leaveEmptyTags);
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