<?php
class GO_EVENTS
{
	var $listeners;
	
	public function __construct(){
		global $GO_CONFIG;
		
		if(!defined('NO_EVENTS') && (!isset($_SESSION['GO_SESSION']['event_listeners']) || $GO_CONFIG->debug))
		{
			$this->load_listeners();
		}else
		{
			$this->listeners=isset($_SESSION['GO_SESSION']['event_listeners']) ? $_SESSION['GO_SESSION']['event_listeners'] : array();
		}
	}	
	
	public function load_listeners(){
		global $GO_MODULES;
		
		$this->listeners = array();
					
		foreach($GO_MODULES->modules as $module)
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
		$_SESSION['GO_SESSION']['event_listeners']=$this->listeners;
	}
		
	public function add_listener($event, $file, $class, $method){
		if(!isset($this->listeners[$event]))
		{
			$this->listeners[$event]=array();
		}
		$this->listeners[$event][]=array(
			'file'=>$file,
			'class'=>$class,
			'method'=>$method
		);
		
		//go_debug('Adding listener: '.$class.'::'.$method);
	}
	
	public function fire_event($event, $args=array())
	{		
		//didn't work with references :(
		//$args = func_get_args();
		//array_shift($args);
		
		if(isset($this->listeners[$event]))
		{
			foreach($this->listeners[$event] as $listener)
			{
				require_once($listener['file']);
				go_debug('Firing listener: '.$listener['class'].'::'.$listener['method']);
				call_user_func_array(array($listener['class'], $listener['method']),$args);
			}		
		}		
	}
}