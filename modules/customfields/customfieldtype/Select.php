<?php
class GO_Customfields_Customfieldtype_Select extends GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'Select';
	}
	
	
	public function fieldSql(){
		//needs to be text for multiselect field
		if($this->field->multiselect)
			return "TEXT NULL";		
		else
			return parent::fieldSql ();
	}
	
	/**
	 * This function is used to format the database value for the interface edit
	 * form.
	 * 
	 * @param string $key Database column 'col_x'
	 * @param array $attributes Customfield model attributes
	 * @return Mixed 
	 */
	public function formatFormInput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model){
		
		$value = $attributes[$key];
		
		//implode array values with pipes for multiselect fields
		if(is_array($value))
				$value=implode('|',$value);
				
		
		return $value;
	}
	
//	public function formatFormOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {		
//		
//		if(!empty($this->field->multiselect) && isset($attributes[$key]))
//			$attributes[$key.'[]'] = $attributes[$key];
//		
//		return parent::formatFormOutput($key, $attributes, $model);
//	}
//	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {		
		
		if(!empty($this->field->multiselect) && isset($attributes[$key]))
			$attributes[$key] = str_replace('|', ', ', $attributes[$key]);
		
		return parent::formatDisplay($key, $attributes, $model);
	}
	
	public function includeInSearches() {
		return true;
	}
}