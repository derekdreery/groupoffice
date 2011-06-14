<?php
class GO_Base_Observable{
	
	public static function addListener($eventName,$listenerClass, $listenerFunction){
		$class = get_called_class();
		
		$dir = GO::config()->file_storage_path.'cache/listeners/';
		if(!is_dir($dir)){
			mkdir($dir, 0755,true);
			
			foreach(GO::modules()->modules as $module)
			{			
				/*$file = $module['class_path'].$module['id'].'.class.inc';

				if(!file_exists($file))
				{*/
					$file = $module['class_path'].$module['id'].'.class.inc.php';
				//}
				if(file_exists($file))
				{
					require_once($file);
					if(class_exists($module['id'], false))
					{				
						$class = new $module['id'];
						$method = '__on_load_listeners';
						if(method_exists($class, $method))
						{						
							$class->$method($this);						
						}
					}
				}
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
			require(GO::config()->file_storage_path.'cache/listeners/'.get_class($this).'.php');
			
			$this->_listeners=$listeners;
		}
	}
	
	
	private $_listeners;
}