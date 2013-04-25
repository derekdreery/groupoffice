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
 * Base object class
 * 
 * All objects extend this class
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base
 * @abstract
 */
abstract class GO_Base_Object extends GO_Base_Observable{

	/**
	 * Returns the name of this object
	 * 
	 * @return string Name
	 */
	public function className(){
		return get_class($this);
	}
	
	/**
	 * Magic getter that calls get<NAME> functions in objects
	 
	 * @param string $name property name
	 * @return mixed property value
	 * @throws Exception If the property setter does not exist
	 */
	public function __get($name)
	{			
		$getter = 'get'.$name;

		if(method_exists($this,$getter)){
			return $this->$getter();
		}else
		{
			throw new Exception("Can't get not existing property '$name' in '".$this->className()."'");
		}
	}		
	
	public function __isset($name) {
		$var = $this->__get($name);
		return isset($var);
	}
	
	/**
	 * Magic setter that calls set<NAME> functions in objects
	 * 
	 * @param string $name property name
	 * @param mixed $value property value
	 * @throws Exception If the property getter does not exist
	 */
	public function __set($name,$value)
	{
		$setter = 'set'.$name;
			
		if(method_exists($this,$setter)){
			$this->$setter($value);
		}else
		{				
			throw new Exception("Can't set not existing property '$name' in '".$this->className()."'");
		}
	}
	
}