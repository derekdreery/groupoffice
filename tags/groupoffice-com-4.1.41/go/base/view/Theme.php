<?php

class GO_Base_View_Theme{
	/**
	 * Get the name of the theme that is selected by the user.
	 * 
	 * @return string
	 */
	public function getName(){
		$theme = GO::user() ? GO::user()->theme : GO::config()->theme;
		
		if(!file_exists(GO::config()->root_path.'views/'.GO::view().'/themes/'.$theme)){
			return 'Default';
		}  else {
			return $theme;
		}
	}
	
	/**
	 * Get the full path to the main theme folder with trailing slash.
	 * 
	 * @return string
	 */
	public function getPath(){
		return GO::config()->root_path.'views/'.GO::view().'/themes/'.$this->getName().'/';
	}
	
	/**
	 * Get the full path to the main theme folder with trailing slash.
	 * 
	 * @return string
	 */
	public function getUrl(){
		return GO::config()->host.'views/'.GO::view().'/themes/'.$this->getName().'/';
	}
	
	
}