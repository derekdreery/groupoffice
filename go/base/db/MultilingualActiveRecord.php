<?php

abstract class GO_Base_Db_MultilingualActiveRecord extends GO_Base_Db_ActiveRecord {
	
	/**
	 * An array with all the attributes that contain multilangual values
	 * 
	 * @var array 
	 */
	protected $multilingualAttributes = array();
	
	/**
	 * The name of the relation with the language.
	 * 
	 * @var string 
	 */
	protected $languageRelationName = 'languages';
	/**
	 * The column name of the language_id field in the language table.
	 * 
	 * @var string 
	 */
	protected $languageIdColumnName = 'language_id';
	
	/**
	 * The column name of the value field in the language table.
	 * 
	 * @var string 
	 */
	protected $languageValueColumnName = 'name';
	
	/**
	 * Are the attributes loaded already?
	 * 
	 * @var boolean 
	 */
	private $_multiLingualAttributesLoaded;	
	
	/**
	 * Get an activestatement with the available languages
	 * 
	 * @return mixed false or an GO_Base_Db_ActiveStatement 
	 */
	public function activeLanguages() {
		return false;
	}

	/**
	 * Get the value of the attribute in the default language 
	 * (First language in the table)
	 * 
	 * @param string $attr
	 * @return string The attribute value 
	 */
	public function getDefaultLanguageAttribute($attr) {
		$relationName = $this->languageRelationName;
		$langRelation = $this->$relationName;

		$value = $langRelation->fetch(); // Return the first available record
		if (empty($value))
			return false;

		return $value->$attr;
	}

	/**
	 * Load the language attributes to the current model.
	 * (So in the current model the language variables are available like: name_1, description_1, name_2, description_2)
	 *  
	 */
	private function _loadMultilingualAttributes() {
		if (!isset($this->_multiLingualAttributesLoaded)) {

			$lColName = $this->languageIdColumnName;
			$lColValue = $this->languageValueColumnName;

			$relation = $this->getRelation($this->languageRelationName);

			$lmodel = GO::getModel($relation['model']);

			$activeLanguages = $this->activeLanguages();

			while ($lang = $activeLanguages->fetch()) {
				foreach ($this->multilingualAttributes as $mLAttribute) {
					$requestAttribute = $mLAttribute . '_' . $lang->id;
					$langModel = $lmodel->findByPk(array($relation['field'] => $this->id, $lColName => $lang->id));

					$this->_attributes[$requestAttribute] = $langModel->$lColValue;
				}
			}
		}
		$this->_multiLingualAttributesLoaded = true;
	}
	
	/**
	 * Extracts the attribute to attribute name and language id
	 * 
	 * @param string $name The attribute to extract (like: name_1 or description_1)
	 * @return array $attr The array with the extracted attributes 
	 */
	private function _extractMultilingualAttribute($name){
		$attr = explode('_', $name);
		
		return $attr;
	}

	/**
	 * Load the given language attributes to the current model.
	 * (So in the current model the language variables are available like: name_1, description_1, name_2, description_2)
	 *  
	 */
	private function _loadMultilingualAttribute($name) {

		if(!isset($this->_attributes[$name])){
			$lColName = $this->languageIdColumnName;
			$relation = $this->getRelation($this->languageRelationName);
			$lmodel = GO::getModel($relation['model']);

			$extractedName = $this->_extractMultilingualAttribute($name);

			$langModel = $lmodel->findByPk(array($relation['field'] => $this->id, $lColName => $extractedName[1]));

			if($langModel)
				$this->_attributes[$name] = $langModel->$extractedName[0];
		}
	}

	/**
	 * Check if an attribute is a multilangualattribute or not.
	 * 
	 * @param string $name
	 * @return boolean 
	 */
	private function _isMultilingualAttribute($name){
		
		$attr = explode('_', $name);
		
		return is_array($attr) && count($attr) > 1;
	}
	
	/**
	 * PHP getter magic method.
	 * This method is overridden so that AR attributes can be accessed like properties.
	 * @param string $name property name
	 * @return mixed property value
	 * @see getAttribute
	 */
	public function __get($name) {

		if ($this->_isMultilingualAttribute($name) && !$this->isNew) {
			$this->_loadMultilingualAttribute($name);
		}

		return parent::__get($name);
	}
	
	/**
	 * Sets the named attribute value.
	 * You may also use $this->AttributeName to set the attribute value.
	 * @param string $name the attribute name
	 * @param mixed $value the attribute value.
	 * @return boolean whether the attribute exists and the assignment is conducted successfully
	 * @see hasAttribute
	 */
	public function setAttribute($name, $value, $format = false) {
		
		//hack to force save
		$this->forceSave();
						
		return parent::setAttribute($name, $value, $format);
	}

	/**
	 * Returns all column attribute values.
	 * Note, related objects are not returned.
	 * @param string $outputType Can be 
	 * 
	 * raw: return values as they are stored in the db
	 * formatted: return the values formatted for an input form
	 * html: Return the values formatted for HTML display
	 * 
	 * @return array attribute values indexed by attribute names.
	 */
	public function getAttributes($outputType = 'formatted') {
		if(!$this->isNew)
			$this->_loadMultilingualAttributes();
		return parent::getAttributes($outputType);
	}

	/**
	 * Get the relation that is defined in the model as an array
	 *
	 * @return array The relation array data 
	 */
	private function _getRelationArray(){
		return $this->getRelation($this->languageRelationName);
	}
	
	/**
	 * Get an instance of the model of the relation
	 * 
	 * @return GO_Base_Db_ActiveRecord 
	 */
	private function _getRelationModelInstance(){
		
		$relationModelInstance = false;
		
		$languageRelationArray = $this->_getRelationArray();
		
		if($languageRelationArray)
			$relationModelInstance = GO::getModel($languageRelationArray['model']);
		
		return $relationModelInstance;
	}
	
	/**
	 * Save the languages after save of this model.
	 * 
	 * @var bool $wasNew True if the model was new before saving
	 * @return boolean 
	 */
	protected function afterSave($wasNew) {

		$lColName = $this->languageIdColumnName;
		$lColValue = $this->languageValueColumnName;
		$relationModelInstance = $this->_getRelationModelInstance();
		$languageRelationArray = $this->_getRelationArray();
		$activeLanguages = $this->activeLanguages();

		while ($lang = $activeLanguages->fetch()) {
			foreach ($this->multilingualAttributes as $mLAttribute) {

				$completeAttributeName = $mLAttribute . '_' . $lang->id;
				if (!empty($this->$completeAttributeName)) {

						$l = $relationModelInstance->findByPk(array($languageRelationArray['field'] => $this->id, $lColName => $lang->id));

						if (!$l) {
							$l = new $relationModelInstance();
							$l->$languageRelationArray['field'] = $this->id;
							$l->$lColName = $lang->id;
						}

						$l->$lColValue = $this->$completeAttributeName;
						$l->save();
				}
			}
		}
		return parent::afterSave($wasNew);
	}

}