<?php
abstract class GO_Customfields_Customfieldtype_Abstract{
	
	/**
	 * The field model that this datatype will be used for.
	 * 
	 * @var GO_Customfields_Model_Field 
	 */
	protected $field;

	public function __construct($field=false){
		if($field)
			$this->field=$field;
	}
	
	/**
	 * The SQL to create the database field.
	 * 
	 * @return MySQL field 
	 */
	public function fieldSql(){
		return "VARCHAR(255) NOT NULL default ''";
	}

	/**
	 * This function is used to format the database value for the interface edit
	 * form.
	 * 
	 * @param string $key Database column 'col_x'
	 * @param array $attributes Customfield model attributes
	 * @return Mixed 
	 */
	public function formatFormOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model){	
		return $attributes[$key];
	}
	
	/**
	 * This function is used to format the value that comes from the interface for
	 * the database.
	 * 
	 * @param string $key Database column 'col_x'
	 * @param array $attributes Customfield model attributes
	 * @return Mixed 
	 */
	
	public function formatFormInput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model){
		return $attributes[$key];
	}
	
	/**
	 * This function is used to format the database value for the interface display
	 * panel (HTML).
	 * 
	 * @param string $key Database column 'col_x'
	 * @param array $attributes Customfield model attributes
	 * @return Mixed 
	 */
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model){
		return GO_Base_Util_String::text_to_html($attributes[$key]);
	}
	
	/**
	 * Returns the name of this custom field type localized.
	 * 
	 * @return String
	 */
	abstract public function name();
	
	
	/**
	 * Validate the input
	 * 
	 * @param mixed $value The value of the customfield that needs to be validated
	 * @return boolean Is the value valid?
	 */
	public function validate($value){
		return true;
	}
	
	/**
	 * Get the validation error message
	 * 
	 * @return string The errormessage for this validator 
	 */
	public function getValidationError(){
		return GO::t('defaultValidationError','customfields');
	}
	
	
	protected function getId($cf) {
		$pos = strpos($cf,':');
		return substr($cf,0,$pos);
	}

	protected function getName($cf) {
		$pos = strpos($cf,':');
		return substr($cf,$pos+1);
	}
	
	/**
	 * Include this column in quick search actions in grids
	 */
	public function includeInSearches(){
		return false;
	}
	
	public function selectForGrid(){
		return true;
	}
	
}