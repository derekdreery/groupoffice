<?php
class GO_Base_Observable{
	
	public static function addListener($eventName,$listenerClass, $listenerFunction){
		$class = get_called_class();
		
		$dir = GO::config()->file_storage_path.'cache/listeners/';
		if(!is_dir($dir)){
			mkdir($dir, 0755,true);
			
			foreach(GO::modules()->getAll() as $module)
			{	
				//todo load listeners
			}
		}
		
		$line = '$listeners[]=array("'.$eventName.'", "'.$listenerClass.'", "'.$listenerClass.'");';
		
		file_put_contents($dir.get_called_class().'.php', $line, FILE_APPEND);	
	}	
	
	public static function removeListener($eventName,$listenerClass,$listenerFunction){
		
	}
	
	protected function fireEvent($eventName, $params){
		if(!isset($this->_listeners)){
			
			//listeners array will be loaded from a file. Because addListener is only called once when there is no cache.
			$listeners=array();
			
			$cacheFile = GO::config()->file_storage_path.'cache/listeners/'.get_class($this).'.php';
			if(file_exists($cacheFile))
				require($cacheFile);
			
			$this->_listeners=$listeners;
		}
	}
	
	
	private $_listeners;
}