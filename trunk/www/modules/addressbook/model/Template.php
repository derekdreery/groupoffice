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
		return 'ab_templates';
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
	
	public function replaceContactTags($content, GO_Addressbook_Model_Contact $contact, $leaveEmptyTags=false){
		
		$attributes = $this->_getDefaultTags();
		
		$attributes = array_merge($attributes, $this->_getModelAttributes($contact, 'contact:'));
		if($contact->company)
		{
			$attributes = array_merge($attributes, $this->_getModelAttributes($contact->company, 'company'));
		}
		
		$attributes = array_merge($attributes, $this->_getUserAttributes());
		
		$templateParser = new GO_Base_Util_TemplateParser();
		return $templateParser->parse($content, $attributes, $leaveEmptyTags);
	}
	
	
	
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
	
	public function replaceUserTags($content, $leaveEmptyTags=false){
		
		$attributes = $this->_getDefaultTags();
		
		$attributes = array_merge($attributes, $this->_getUserAttributes());
		
		$attributes['contact:salutation']=GO::t('default_salutation_unknown');
		
		//var_dump($attributes);
		
		$templateParser = new GO_Base_Util_TemplateParser();
		return $templateParser->parse($content, $attributes, $leaveEmptyTags);
	}
	
	
}