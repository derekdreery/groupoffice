<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * Objects that extend this class can fire events and modules can add listeners 
 * to these objects.
 */
class GO_Base_Observable{
	
	/**
	 * Will check if the event listeners have been cached and will 
	 * cache them when necessary.
	 * 
	 * At the moment this function is called in index.php In the future this
	 * should be called at the new entry point of the application.
	 */
	public static function cacheListeners(){
		
		GO::debug("GO_Base_Observable::cacheListeners");
		
		$dir = GO::config()->file_storage_path.'cache/listeners/';
		if(GO::config()->debug){
			exec('rm -Rf '.$dir);
		}
		$dirExists = is_dir($dir);
		if(!$dirExists){
			mkdir($dir, 0755,true);
			
			GO::modules()->callModuleMethod('initListeners');
			
		}
	}
	/**
	 * Add a listener function to this object
	 * 
	 * @param String $eventName
	 * @param String $listenerClass Object class name where the static listener function is in.
	 * @param type $listenerFunction Static listener function name.
	 */
	public function addListener($eventName,$listenerClass, $listenerFunction){
		
		$line = '$listeners["'.$eventName.'"][]=array("'.$listenerClass.'", "'.$listenerFunction.'");'."\n";
		
		$dir = GO::config()->file_storage_path.'cache/listeners/';
		$file = $dir.$this->className().'.php';
		
		if(!file_exists($file))
			file_put_contents($file, "<?php\n", FILE_APPEND);	
		
		file_put_contents($file, $line, FILE_APPEND);	
		
	
	}	
	
	/**
	 * Remove a listener function to this object
	 * 
	 * @todo
	 * @param String $eventName
	 * @param String $listenerClass Object class name where the static listener function is in.
	 * @param type $listenerFunction Static listener function name.
	 */
	public static function removeListener($eventName,$listenerClass,$listenerFunction){
		return false;
	}
	
	/**
	 * Fire an event so that listener functions will be called.
	 * 
	 * @param String $eventName Name fo the event
	 * @param Array $params Paramters for the listener function
	 */
	protected function fireEvent($eventName, $params){
		if(!isset($this->_listeners)){
			
			//listeners array will be loaded from a file. Because addListener is only called once when there is no cache.
			$listeners=array();
			
			$cacheFile = GO::config()->file_storage_path.'cache/listeners/'.get_class($this).'.php';
			if(file_exists($cacheFile))
				require($cacheFile);
			
			$this->_listeners=$listeners;
			
//			$cacheFile = GO::config()->file_storage_path.'cache/listeners/'.get_parent_class($this).'.php';
//			if(file_exists($cacheFile)){
//				require($cacheFile);
//				$this->_listeners=array_merge($this->_listeners,$listeners);
//			}
		}
		
		GO::debug("fireEvent($eventName) class:".get_class($this));
		
		if(isset($this->_listeners[$eventName])){
			foreach($this->_listeners[$eventName] as $listener)
			{
				GO::debug('Firing listener: '.$listener[0].'::'.$listener[1]);

				$method = !empty($listener[0]) ? array($listener[0], $listener[1]) : $listener[1];
				call_user_func_array($method, $params);
			}
		}
		
		//recurse up.
		//parent::fireEvent($eventName, $params);
	}
	
	
	private $_listeners;
}