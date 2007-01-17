<?php
/**
* @copyright Intermesh 2003
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.11 $ $Date: 2006/11/24 16:05:23 $
*
*  This program is free software; you can redistribute it and/or modify it
*  under the terms of the GNU General Public License as published by the
*  Free Software Foundation; either version 2 of the License, or (at your
*  option) any later version.
*/

/**
* This class is used to manage the modules
*
* @package Framework
* @author   Merijn Schering <mschering@intermesh.nl>
* @since    Group-Office 1.0
*/

class GO_THEME
{
	/**
	* The name of the active theme
	*
	* @var     string
	* @access  public
	*/
	var $theme;

	/**
	* The URL to the images of a theme
	*
	* @var     string
	* @access  public
	*/
	var $image_url;

	/**
	* The full filesystem path to a theme
	*
	* @var     string
	* @access  public
	*/
	var $theme_path;

	/**
	* The relative URL to a theme
	*
	* @var     string
	* @access  public
	*/
	var $theme_url;

	/**
	* The images of a theme
	*
	* @var     array
	* @access  public
	*/

	var $images = array();
	/**
	* The sound files of a theme
	*
	* @var     array
	* @access  public
	*/
	var $sounds = array();

	/**
	* The filetype images of a theme
	*
	* @var     array
	* @access  public
	*/
	var $filetypes = array();

	/**
	* Constructor. Initialises user's theme
	*
	* @access public
	* @return void
	*/
	function GO_THEME()
	{
		global $GO_CONFIG;

		$_SESSION['GO_SESSION']['theme'] =
		isset($_SESSION['GO_SESSION']['theme']) ?
		$_SESSION['GO_SESSION']['theme'] : $GO_CONFIG->theme;

		if ($_SESSION['GO_SESSION']['theme'] != '' && file_exists($GO_CONFIG->theme_path.$_SESSION['GO_SESSION']['theme']))
		{
			$this->theme = $_SESSION['GO_SESSION']['theme'];
		}else
		{
			$_SESSION['GO_SESSION']['theme'] = $GO_CONFIG->theme;
			$this->theme = $GO_CONFIG->theme;
		}

		$this->theme_path = $GO_CONFIG->theme_path.$this->theme.$GO_CONFIG->slash;
		$this->theme_url = $GO_CONFIG->theme_url.$this->theme.'/';
		$this->image_url = $this->theme_url.'images/';


		require($this->theme_path.'images.inc');

		foreach($images as $key => $value)
		{
			$this->images[$key] = $this->image_url.$value;
		}
		require($this->theme_path.'filetypes.inc');
		foreach($filetypes as $key => $value)
		{
			$this->filetypes[$key] = $this->image_url.$value;
		}
		require($this->theme_path.'sounds.inc');
		foreach($sounds as $key => $value)
		{
			$this->sounds[$key] = $this->theme_url.'sounds/'.$value;
		}
	}

	function load_module_theme($module_id)
	{
		global $GO_MODULES;

		$file = $GO_MODULES->modules[$module_id]['path'].'themes/'.$this->theme.'/images.inc';
		$image_url =  $GO_MODULES->modules[$module_id]['url'].'themes/'.$this->theme.'/images/';
		if(!file_exists($file))
		{
			$file = $GO_MODULES->modules[$module_id]['path'].'themes/Default/images.inc';
			$image_url =  $GO_MODULES->modules[$module_id]['url'].'themes/Default/images/';
		}

		if(file_exists($file))
		{
			require($file);
			foreach($images as $key => $value)
			{
				$this->images[$key] = $image_url.$value;
			}
		}
	}

	function get_stylesheet($module_id)
	{
		global $GO_MODULES;
		
		$file = $GO_MODULES->modules[$module_id]['path'].'themes/'.$this->theme.'/style.css';	
		$url = $GO_MODULES->modules[$module_id]['url'].'themes/'.$this->theme.'/style.css';	
		if(!file_exists($file))
		{
			$file = $GO_MODULES->modules[$module_id]['path'].'themes/Default/style.css';	
			$url = $GO_MODULES->modules[$module_id]['url'].'themes/Default/style.css';	
			if(!file_exists($file))
			{
				$file = $this->theme_path.'css/'.$module_id.'.css';	
				$url = $this->theme_url.'css/'.$module_id.'.css';	
				
				if(!file_exists($file))
				{
					return '';
				}
			}
		}
		
		return '<link href="'.$url.'" type="text/css" rel="stylesheet" />';
	}

	/**
	*	Gets all theme names
	*
	* @access public
	* @return array Theme names
	*/
	function get_themes()
	{
		global $GO_CONFIG;

		$theme_dir=opendir($GO_CONFIG->theme_path);
		while ($file=readdir($theme_dir))
		{
			if (is_dir($GO_CONFIG->theme_path.$file) &&
			file_exists($GO_CONFIG->theme_path.$file.'/images.inc') &&
			file_exists($GO_CONFIG->theme_path.$file.'/filetypes.inc') &&
			file_exists($GO_CONFIG->theme_path.$file.'/sounds.inc'))
			{
				$themes[] = $file;
			}
		}
		closedir($theme_dir);
		return $themes;
	}

	/**
	*	Gets the login screens
	*
	* @access public
	* @return array Login screen names
	*/
	function get_login_screens()
	{
		global $GO_CONFIG;

		$path_to_login_screens = $GO_CONFIG->root_path.'login_screens/';
		$dir=opendir($path_to_login_screens);
		while ($file=readdir($dir))
		{
			if (is_dir($path_to_login_screens.$file) &&
			file_exists($path_to_login_screens.$file.'/login_header.inc') &&
			file_exists($path_to_login_screens.$file.'/login.inc') &&
			file_exists($path_to_login_screens.$file.'/login_footer.inc'))
			{
				$login_screens[] = $file;
			}
		}
		closedir($dir);
		return $login_screens;
	}
}
