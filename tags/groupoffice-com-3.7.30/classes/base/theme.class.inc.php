<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package go.basic
 */

/**
 * This class is used to retrieve information about the currently selected 
 * theme.
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id$
 * @copyright Copyright Intermesh
 * @package go.basic
 * @since Group-Office 1.0
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


	var $stylesheets;


	/**
	* Constructor. Initialises user's theme
	*
	* @access public
	* @return void
	*/
	function GO_THEME()
	{
		$this->set_theme();
	}


	function set_theme($theme=false){

		global $GO_CONFIG;

		if($theme)
			$_SESSION['GO_SESSION']['theme'] = $theme;

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

		$this->theme_path = $GO_CONFIG->theme_path.$this->theme.'/';
		$this->theme_url = $GO_CONFIG->theme_url.$this->theme.'/';
		$this->image_url = $this->theme_url.'images/';
		$this->image_path = $GO_CONFIG->theme_path.'images/';
	}



	function replace_url($css, $baseurl){
		return preg_replace('/url[\s]*\(([^\)]*)\)/ieU', "GO_THEME::replace_url_callback('$1', \$baseurl)", $css);
	}

	function replace_url_callback($url, $baseurl){
		return 'url('.$baseurl.trim(stripslashes($url),'\'" ').')';
	}

	function add_stylesheet($path){

		//echo '<!-- '.$path.' -->'."\n";

		go_debug('Adding stylesheet: '.$path);

		$this->stylesheets[]=$path;
	}

	function load_module_stylesheets($derrived_theme=false){
		global $GO_MODULES;

		foreach($GO_MODULES->modules as $module)
		{
			if(file_exists($module['path'].'themes/Default/style.css')){
				$this->add_stylesheet($module['path'].'themes/Default/style.css');
			}

			if($this->theme!='Default'){
				if($derrived_theme && file_exists($module['path'].'themes/'.$derrived_theme.'/style.css')){
					$this->add_stylesheet($module['path'].'themes/'.$derrived_theme.'/style.css');
				}
				if(file_exists($module['path'].'themes/'.$this->theme.'/style.css')){
					$this->add_stylesheet($module['path'].'themes/'.$this->theme.'/style.css');
				}
			}
		}
	}

	function get_cached_css(){
		global $GO_CONFIG, $GO_SECURITY, $GO_MODULES;

		$mods='';
		foreach($GO_MODULES->modules as $module) {
			$mods.=$module['id'];
		}

		$hash = md5($GO_CONFIG->file_storage_path.$GO_CONFIG->host.$GO_CONFIG->mtime.$mods);

		$relpath= 'cache/'.$hash.'-'.$this->theme.'-style.css';
		$cssfile = $GO_CONFIG->file_storage_path.$relpath;

		if(!file_exists($cssfile) || $GO_CONFIG->debug){

			File::mkdir($GO_CONFIG->file_storage_path.'cache');

			$fp = fopen($cssfile, 'w+');
			foreach($this->stylesheets as $s){

				$baseurl = str_replace($GO_CONFIG->root_path, $GO_CONFIG->host, dirname($s)).'/';

				fputs($fp, $this->replace_url(file_get_contents($s),$baseurl));
			}
			fclose($fp);
		}

		$cssurl = $GO_CONFIG->host.'compress.php?file='.basename($relpath);
		echo '<link href="'.$cssurl.'" type="text/css" rel="stylesheet" />';
	}
	
	/**
	 * Get the stylesheet of a module
	 *
	 * @param String $module_id
	 * @return String URL to stylesheet
	 */

	function get_stylesheet($module_id, $theme=null)
	{
		global $GO_MODULES;
		
		if(!isset($theme))
			$theme = $this->theme;

		$file = $GO_MODULES->modules[$module_id]['path'].'themes/'.$theme.'/style.css';
		$url = $GO_MODULES->modules[$module_id]['url'].'themes/'.$theme.'/style.css';
		if(!file_exists($file))
		{			
			if($theme == 'Default')
			{
				return '';
			}else
			{
				return $this->get_stylesheet($module_id, 'Default');
			}
		}else
		{
			return '<link href="'.$url.'" type="text/css" rel="stylesheet" />'."\n";
		}
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
			file_exists($GO_CONFIG->theme_path.$file.'/layout.inc.php'))
			{
				$themes[] = $file;
			}
		}
		closedir($theme_dir);
		return $themes;
	}
}
