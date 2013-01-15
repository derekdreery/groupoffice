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
 * @version $Id: AbstractSettingsCollection.php 7962 2011-08-24 14:48:45Z wsmits $
 * @author Wesley Smits <wsmits@intermesh.nl>
 * @package GO.base.model
 */

/**
 * 
 * @package GO.base.model
 */
abstract class GO_Base_Model_AbstractSettingsCollection extends GO_Base_Model {

	/**
	 * The id of the user you want the settings for.
	 * @var int
	 */
	private $_userId = 0;
	
	/**
	 * Load the settings to this object.
	 * If a userId is given then it loads the settings for the given user
	 * 
	 * @param int $userId
	 */
	public function __construct($userId=0) {
		
		if(!empty($userId))
			$this->_userId = $userId;
		
		$this->load();
	}
	
	/**
	 * Load function to load the setting values to the properties
	 */
	public function load(){
		$refClass = $this->_getReflectionClass();
		$properties = $this->get_parent_properties_diff($refClass);
		
		foreach($properties as $property){
				$key = $property->name;
				$this->{$key} = GO::config()->get_setting($key,$this->_userId);
		}
	}
	
	/**
	 * Determine which properties are of the childs class. 
	 * Return them as an array.
	 * 
	 * @param ReflectionClass $obj
	 * @return array
	 */
	public function get_parent_properties_diff( $obj) {
    $parent = $obj->getParentClass();
    return array_diff($obj->getProperties(),$parent->getProperties());
}
	
	
	public function save(){
		
		if(!$this->validate())
			return false;
		
		$refClass = $this->_getReflectionClass();
		$properties = $this->get_parent_properties_diff($refClass);
		$success = true;
		foreach($properties as $property){
			$key = $property->name;
			$value = $this->{$key};

			$success = $success && GO::config()->save_setting($key, $value, $this->_userId);			
		}
		return $success;
	}
	
	/**
	 * A validate function that can be used in the child class.
	 */
	public function validate(){
		return true;
	}
	
	/**
	 * Function to save an array of properties at once. ($key=>$value array)
	 * (Usually the $params array that is send by the browser)
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public function saveFromArray($data){
		
		$refClass = $this->_getReflectionClass();
		$properties = $this->get_parent_properties_diff($refClass);
		
		foreach($properties as $property){
			$key = $property->name;
			if(key_exists($key, $data))
				$this->{$key} = $data[$key];
		}
			
		return $this->save();	
	}
	
	/**
	 * Private function to return the Reflection class of the current object
	 * 
	 * @return \ReflectionClass
	 */
	private function _getReflectionClass(){
		return new ReflectionClass($this);
	}
	
	/**
	 * Return the settings as a $key=>$value array
	 * 
	 * @return array
	 */
	public function getArray(){
		
		$data = array();
		$refClass = $this->_getReflectionClass();
		$properties = $this->get_parent_properties_diff($refClass);
		
		foreach($properties as $property){
			$key = $property->name;	  
			$data[$key] = $this->{$key};
		}
		
		return $data;
	}
	
}