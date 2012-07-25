<?php 
class GO_Sites_Language{
	private $_langIso='en';
	private $_lang;
	
	private $_templatePath;
	
	
	public function __construct($templatePath,$isoCode=false) {
		$this->_templatePath = $templatePath;
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
		
		if(!$isoCode)
			$this->_langIso=GO::user() ? GO::user()->language : GO::config()->language;
		else
			$this->_langIso=$isoCode;
		
		if($oldIso!=$this->_langIso)
			$this->_lang=array();
		
		return $oldIso;
	}

	public function getTranslation($name){
		
		$file = $this->_find_file();
		if($file)
			require($file);
		
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
		else
			return false;
	}
	
	
	/**
	 * Get all supported languages.
	 * 
	 * @return array array('en'=>'English');
	 */
	public function getLanguages(){
		require($this->_templatePath.'language/languages.php');
		asort($languages);
		return $languages;
	}
}