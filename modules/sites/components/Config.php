<?php



class GO_Sites_Components_Config{

	private $_configOptions = array();
	
	public function __construct($siteconfig=array()) {
		$this->_configOptions = $siteconfig;
	}
	
	public function __get($name) {
		
		if(array_key_exists($name, $this->_configOptions))
			return $this->_configOptions[$name];
		else
			return null;
	}
}