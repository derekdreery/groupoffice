<?php
class GO_Site_Components_Config{

	private $_configOptions = array();
	
	public function __construct(GO_Site_Model_Site $siteModel) {

		$file = new GO_Base_Fs_File($siteModel->getSiteModule()->moduleManager->path().'siteconfig.php');
		if($file->exists()){
			require ($file->path());
		}	
		if(isset($siteconfig))
			$this->_configOptions = $siteconfig;
	}
	
	public function __get($name) {
		
		if(array_key_exists($name, $this->_configOptions))
			return $this->_configOptions[$name];
		else
			return null;
	}
	
	public function getDefaultTemplate(){
		if($this->defaultTemplate)
			return $this->defaultTemplate;
		
		if($this->templates){
			
			$templates = array_keys($this->templates);
			return array_shift($templates);
		}
		
		return false;
	}
}