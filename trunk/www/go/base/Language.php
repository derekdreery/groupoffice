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
 * Translates variables into localized strings
 */
class GO_Base_Language{
	
	private $_lang;
	
	/**
	 * Translates a language variable name into the local language
	 * 
	 * @param String $name Name of the translation variable
	 * @param String $module Name of the module to find the translation
	 * @param String $basesection Only applies if module is set to 'base'
	 */
	public function getTranslation($name, $module='base',$basesection='common'){
		
		$this->_loadSection($module, $basesection);		
		
		if($module=='base'){
			return isset($this->_lang[$module][$basesection][$name]) ? $this->_lang[$module][$basesection][$name] : $name;
		}else
		{
			return isset($this->_lang[$module][$name]) ? $this->_lang[$module][$name] : $name;
		}
	}
	
	private function _loadSection($module='base',$basesection='common'){
		if(!isset($this->_lang[$module]) || ($module=='base' && !isset($this->_lang[$module][$basesection]))){
			
			$file = $this->_find_file('en', $module, $basesection);
			if($file)
				require($file);
			
			$langcode = GO::user() ? GO::user()->language : GO::config()->language;
			if($langcode!='en')
			{
				$file = $this->_find_file($langcode, $module, $basesection);
				if($file)
					require($file);
			}		
			
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
	
	
	public function getAllLanguage(){
		$folder = new GO_Base_Fs_Folder(GO::config()->root_path.'language');
		$items = $folder->ls();
		foreach($items as $folder){
			if($folder instanceof GO_Base_Fs_Folder){
				$this->_loadSection('base', $folder->name());
			}
		}
		
		$stmt = GO::modules()->getAll();
		while($module = $stmt->fetch()){
			$this->_loadSection($module->id);
		}
		
		return $this->_lang;
	}
	
}