<?php 
class GO_Sites_Components_Language{
	private $_langIso='en';
	private $_lang;
	
	private $_templatePath;
	
	
	public function __construct($isoCode=false) {
		$this->_templatePath = GOS::site()->controller->getTemplatePath();
		$this->setLanguage($isoCode);
	}
	
	/**
	 * Set the language to translate into. Clears the cached language strings too.
	 * 
	 * @param string $isoCode Leave empty to set the default user language.
	 * @return string Old ISO code that was set.
	 */
	public function setLanguage($isoCode=false){
		$oldIso = $this->_langIso;
		
		if(!$isoCode){
			
			$goIso = GO::user() ? GO::user()->language : GO::config()->language;
			
			$this->_langIso=$this->hasLanguage($goIso) ? $goIso : 'en';
		}else
			$this->_langIso=$isoCode;
		
		if($oldIso!=$this->_langIso)
			$this->_lang=array();
				
		return $oldIso;
	}
	
	public function hasLanguage($iso){
		$file = $this->_templatePath.'language/'.$iso.'.php';
		return file_exists($file);
	}

	public function getTranslation($name){
		
		$file = $this->_find_file();
		if($file)
			require($file);
		else
			throw new GO_Base_Exception_NotFound('Language file not found');
		
		if(isset($l)){
			if(!empty($l[$name]))
				return $l[$name];
			else
				return $name;
		}
	}
	
	
	private function _find_file(){
		$file = $this->_templatePath.'language/'.$this->_langIso.'.php';
		if(file_exists($file))
			return $file;
		
		return false;
	}
	
	
	/**
	 * Get all supported languages.
	 * TODO: loop through file in language directory
	 * @return array array('en'=>'English');
	 */
	public function getLanguages(){
		require($this->_templatePath.'language/languages.php');
		asort($languages);
		return $languages;
	}
}