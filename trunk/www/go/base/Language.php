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
 * Translates variables into localized strings
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base
 */
 
class GO_Base_Language{
	
	private $_langIso='en';
	private $_lang;
	
	
	public function __construct() {
		$this->setLanguage();
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
			$this->_langIso=GO::user() ? GO::user()->language : GO::config()->language;
		}else
		{
			$this->_langIso=$isoCode;
		}
		
		if($oldIso!=$this->_langIso)
			$this->_lang=array();
		
		return $oldIso;
	}
	
	/**
	 * Translates a language variable name into the local language.
	 * 
	 * Note: You can use GO::t() instead. It's a shorter alias.
	 * 
	 * @param String $name Name of the translation variable
	 * @param String $module Name of the module to find the translation
	 * @param String $basesection Only applies if module is set to 'base'
	 * @param boolean $found Pass by reference to determine if the language variable was found in the language file.
	 */
	public function getTranslation($name, $module='base',$basesection='common', &$found=false){
		
		$this->_loadSection($module, $basesection);		
		
		if($module=='base'){
			if(isset($this->_lang[$module][$basesection][$name])){
				$found=true;
				return $this->_lang[$module][$basesection][$name];
			}else
			{
				$found = false;
				return $name;
			}
		}else
		{
			if(isset($this->_lang[$module][$name])){
				$found=true;
				return $this->_lang[$module][$name];
			}else
			{
				$found = false;
				return $name;
			}
		}
	}
	
	private function _loadSection($module='base',$basesection='common'){
		if(!isset($this->_lang[$module]) || ($module=='base' && !isset($this->_lang[$module][$basesection]))){
			
			$file = $this->_find_file('en', $module, $basesection);
			if($file)
				require($file);
			
			//$langcode = GO::user() ? GO::user()->language : GO::config()->language;
			if($this->_langIso!='en')
			{
				$file = $this->_find_file($this->_langIso, $module, $basesection);
				if($file)
					require($file);
			}		
			
			$file = $this->_find_override_file($this->_langIso, $module, $basesection);
			if($file)
				require($file);
			
			if(isset($l)){
				if($module=='base'){
					$this->_lang[$module][$basesection]=$l;
				}else
				{
					$this->_lang[$module]=$l;
				}
			}
			
//			if(isset($l)){
//				$this->_lang[$key]=$l;
//			}elseif(isset($lang[$module])){
//				$this->_lang[$key]=$lang[$module];
//			}else	if(isset($lang[$basesection])){
//				$this->_lang[$key]=$lang[$basesection];
//			}
		}
	}
	
	private function _find_file($lang, $module, $basesection){
		if($module=='base')
			$dir=GO::config()->root_path.'language/'.$basesection.'/';
		else
			$dir=GO::config()->root_path.'modules/'.$module.'/language/';
				
		$file = $dir.$lang.'.php';
		
		if(file_exists($file))
			return $file;
		else
			return false;
	}
	
	private function _find_override_file($lang, $module, $basesection){
		
		$dir=GO::config()->file_storage_path.'users/admin/lang/'.$lang.'/';		
		$filename = $module=='base' ? 'base_'.$basesection.'.php' : $module.'.php';
						
		$file = $dir.$filename;
		
		if(file_exists($file))
			return $file;
		

		$dir=GO::config()->file_storage_path.'users/admin/lang/';		

		$file = $dir.$filename;

		if(file_exists($file))
			return $file;			
		
		
		return false;
	}
	
	
	public function getAllLanguage(){
		$folder = new GO_Base_Fs_Folder(GO::config()->root_path.'language');
		$items = $folder->ls();
		foreach($items as $folder){
			if($folder instanceof GO_Base_Fs_Folder){
				$this->_loadSection('base', $folder->name());
			}
		}
		
		//always load users lang for settings panels
		$this->_loadSection('users');
		
		$stmt = GO::modules()->getAll();
		while($module = $stmt->fetch()){
			$this->_loadSection($module->id);
		}
		
		return $this->_lang;
	}
	
	/**
	 * Get all supported languages.
	 * 
	 * @return array array('en'=>'English');
	 */
	public function getLanguages(){
		require(GO::config()->root_path.'language/languages.inc.php');
		asort($languages);
		return $languages;
	}
	
	/**
	 * Get all countries
	 * 
	 * @return array array('nl'=>'The Netherlands');
	 */
	public function getCountries(){
		$this->_loadSection('base','countries');
		asort($this->_lang['base']['countries']);
		return $this->_lang['base']['countries'];
	}
	
}