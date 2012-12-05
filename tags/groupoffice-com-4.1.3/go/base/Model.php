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
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base
 */

/**
 * Base model class
 * 
 * All data models extend this class.
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base
 * @abstract
 */
abstract class GO_Base_Model extends GO_Base_Observable{
	
	protected $_validationErrors = array();
	
	private static $_models=array();			// class name => model
	
	/**
	 * Returns the static model of the specified AR class.
	 * Every child of this class must override it.
	 * 
	 * @return GO_Base_Model the static model class
	 */
	public static function model($className=__CLASS__)
	{		
		if(isset(self::$_models[$className])){
			$model = self::$_models[$className];			
		}else
		{
			$model=self::$_models[$className]=new $className(false, true);			
		}
		return $model;
	}
	
	/**
	 * Clears the model cache. Useful when upgrading. 
	 */
	public static function clearCache(){
		self::$_models=array();
	}
	
	/**
	 * Get the name of the model in short
	 * eg. GO_Base_Model_User will return 'User'
	 * @return string Model name
	 */
	public function getModelName()
	{
		$classParts = explode('_',get_class($this));
		return array_pop($classParts);
	}
	
	/**
	 * You can override this function to implement validation in your model.
	 * 
	 * @return boolean
	 */
	public function validate()
	{			
		return !$this->hasValidationErrors();
	}
	
	
	/**
	 * Return all validation errors of this model
	 * 
	 * @return array 
	 */
	public function getValidationErrors(){
		return $this->_validationErrors;
	}
	
	/**
	 * Get the validationError for the given attribute
	 * If the attribute has no error then fals will be returned
	 * 
	 * @param string $attribute
	 * @return mixed 
	 */
	public function getValidationError($attribute){
		if(!empty($this->_validationErrors[$attribute]))
			return $this->_validationErrors[$attribute];
		else
			return false;
	}
	
	/**
	 * Set a validation error for the given field.
	 * 
	 * @param string $attribute Set to 'form' for a general form error.
	 * @param string $message 
	 */
	protected function setValidationError($attribute, $message) {
		$this->_validationErrors[$attribute] = $message;
	}
	
	/**
		* Returns a value indicating whether there is any validation error.
		* @param string $attribute attribute name. Use null to check all attributes.
		* @return boolean whether there is any error.
		*/
	public function hasValidationErrors($attribute=null)
	{
		if ($attribute === null)
			return $this->_validationErrors !== array();
		else
			return isset($this->_validationErrors[$attribute]);
	}
	
}