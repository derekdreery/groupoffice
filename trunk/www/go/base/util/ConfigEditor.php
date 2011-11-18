<?php

class GO_Base_Util_ConfigEditor {
	
	private $_config;
	
	public function __construct(){

		$values = get_object_vars(GO::config());

		foreach($values as $key=>$value)
		{
			if($key == 'version')
			break;

			if(!is_object($value))
			{
				$this->_config[$key]=$value;
			}
		}
		
	}
	
	public function __set($key, $value){
		$this->_config[$key]=$value;
	}
	
	public function __get($key){
		return $this->_config[$key];
	}

	

}