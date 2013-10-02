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
 * Observable base class
 * 
 * Objects that extend this class can fire events and modules can add listeners 
 * to these objects.
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id: config.class.inc.php 7687 2011-06-23 12:00:34Z mschering $
 * @copyright Copyright Intermesh BV.
 * @package GO.base 
 */
class GO_Base_Observable{
	
	public static $listeners;
	
	/**
	 * Will check if the event listeners have been cached and will 
	 * cache them when necessary.
	 * 
	 * At the moment this function is called in index.php In the future this
	 * should be called at the new entry point of the application.
	 */
	public static function cacheListeners(){
		
		GO::debug("GO_Base_Observable::cacheListeners");
		
		$cacheFolder = GO::config()->getCacheFolder();
		$folder = $cacheFolder->createChild('listeners',false);
		
		if(GO::config()->debug){
			$folder->delete();			
		}
		
		if(!$folder->exists()){
			$folder->create();
			
			GO::modules()->callModuleMethod('initListeners');
		}
	}
	/**
	 * Add a listener function to this object
	 * 
	 * @param string $eventName
	 * @param string $listenerClass Object class name where the static listener function is in.
	 * @param string $staticListenerFunction Static listener function name.
	 */
	public function addListener($eventName,$listenerClass, $staticListenerFunction){
		
		GO::debug("addListener($eventName,$listenerClass, $staticListenerFunction)");
		
		$line = '$listeners["'.$eventName.'"][]=array("'.$listenerClass.'", "'.$staticListenerFunction.'");'."\n";
		
		$dir = GO::config()->orig_tmpdir.'cache/listeners/';
		$file = $dir.get_class($this).'.php';
		
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
	public function removeListener($eventName,$listenerClass,$listenerFunction){
		return false;
	}
	
	/**
	 * Fire an event so that listener functions will be called.
	 * 
	 * @param String $eventName Name fo the event
	 * @param Array $params Paramters for the listener function
	 */
	protected function fireEvent($eventName, $params=array()){
		
		$className = get_class($this);		
		
		if(!isset(self::$listeners[$className])){
			
			//listeners array will be loaded from a file. Because addListener is only called once when there is no cache.
			$listeners=array();
			
			$cacheFile = GO::config()->orig_tmpdir.'cache/listeners/'.get_class($this).'.php';
			if(file_exists($cacheFile))
				require($cacheFile);
			
			self::$listeners[$className]=$listeners;			
		}
		
		if(isset(self::$listeners[$className][$eventName])){
			foreach(self::$listeners[$className][$eventName] as $listener)
			{
				GO::debug('Firing listener for class '.$className.' event '.$eventName.': '.$listener[0].'::'.$listener[1]);

				$method = !empty($listener[0]) ? array($listener[0], $listener[1]) : $listener[1];
				$return = call_user_func_array($method, $params);
				if($return===false){
					GO::debug("Event '$eventName' cancelled by ".$listener[0].'::'.$listener[1]);
					return false;
				}
			}
		}
		
		return true;
	}

}