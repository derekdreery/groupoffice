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
	
	
	public static function cacheListeners(){
		
		go_debug("GO_Base_Observable::cacheListeners");
		
		$dir = $GLOBALS['GO_CONFIG']->file_storage_path.'cache/listeners/';
		if($GLOBALS['GO_CONFIG']->debug){
			exec('rm -Rf '.$dir);
		}
		$dirExists = is_dir($dir);
		if(!$dirExists){
			mkdir($dir, 0755,true);
			
			$GLOBALS['GO_MODULES']->callModuleMethod('initListeners');
			
		}
	}
	
	public static function addListener($eventName,$listenerClass, $listenerFunction){
		$class = get_called_class();		
		
		$line = '$listeners["'.$eventName.'"][]=array("'.$listenerClass.'", "'.$listenerFunction.'");'."\n";
		
		$dir = $GLOBALS['GO_CONFIG']->file_storage_path.'cache/listeners/';
		$file = $dir.get_called_class().'.php';
		
		if(!file_exists($file))
			file_put_contents($file, "<?php\n", FILE_APPEND);	
		
		file_put_contents($file, $line, FILE_APPEND);	
		
	
	}	
	
	public static function removeListener($eventName,$listenerClass,$listenerFunction){
		
	}
	
	protected function fireEvent($eventName, $params){
		if(!isset($this->_listeners)){
			
			//listeners array will be loaded from a file. Because addListener is only called once when there is no cache.
			$listeners=array();
			
			$cacheFile = $GLOBALS['GO_CONFIG']->file_storage_path.'cache/listeners/'.get_class($this).'.php';
			if(file_exists($cacheFile))
				require($cacheFile);
			
			$this->_listeners=$listeners;
			
//			$cacheFile = $GLOBALS['GO_CONFIG']->file_storage_path.'cache/listeners/'.get_parent_class($this).'.php';
//			if(file_exists($cacheFile)){
//				require($cacheFile);
//				$this->_listeners=array_merge($this->_listeners,$listeners);
//			}
		}
		
		go_debug("fireEvent($eventName) class:".get_class($this));
		
		if(isset($this->_listeners[$eventName])){
			foreach($this->_listeners[$eventName] as $listener)
			{
				go_debug('Firing listener: '.$listener[0].'::'.$listener[1]);

				$method = !empty($listener[0]) ? array($listener[0], $listener[1]) : $listener[1];
				call_user_func_array($method, $params);
			}
		}
		
		//recurse up.
		//parent::fireEvent($eventName, $params);
	}
	
	
	private $_listeners;
}