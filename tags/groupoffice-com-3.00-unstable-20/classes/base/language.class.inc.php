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
 * This class is used to include language files according to the user's preference
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id$
 * @copyright Copyright Intermesh
 * @package go.basic
 * @since Group-Office 1.0
 */

class GO_LANGUAGE {
	/**
	 * The current language setting
	 *
	 * @var     string
	 * @access  private
	 */
	var $language;

	/**
	 * The path to the common language files
	 *
	 * @var     string
	 * @access  private
	 */
	var $language_path;

	/**
		* The default language
		*
		* @var     string
		* @access  private
		*/
	var $default_language;

	/**
	 * Constructor. Initialises language setting and checks in following order:
	 * User preference (Session), Browser language setting, default language
	 *
	 * @access public
	 * @return string 	language code (See developer guidelines for codes)
	 */
	function __construct() {
		global $GO_CONFIG;

		$this->language_path = $GO_CONFIG->root_path.$GO_CONFIG->language_path.'/';
		$this->default_language = $GO_CONFIG->language;

		if (!empty($_SESSION['GO_SESSION']['language'])) {
			$this->language = $_SESSION['GO_SESSION']['language'];
		}elseif(isset($_COOKIE['GO_LANGUAGE']) && $this->set_language($_COOKIE['GO_LANGUAGE']))
		{
			return $this->language;
				
		}elseif (isset ($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			if($this->set_language($browser_languages[0]))
			{
				return $this->language;					
			}else
			{
				$this->set_language($this->default_language);
				return $this->language;
			}
		}
	}

	/**
	 *	Set the language for this browser session
	 *
	 *	@param  $language	The language code (See developer guidelines for codes)
	 * @access public
	 * @return string	language code
	 */
	function set_language($language) {

		if(file_exists($this->language_path.'common/'.$language.'.inc.php'))
		{
			$this->language=$_SESSION['GO_SESSION']['language']=$language;

			if(!isset($_COOKIE['GO_LANGUAGE']) || $_COOKIE['GO_LANGUAGE']!=$this->language)
			{
				$_COOKIE['GO_LANGUAGE']=$this->language;
				SetCookie("GO_LANGUAGE",$language,time()+3600*24*30,"/",'',0);
			}
				
			return true;
		}else
		{
			return false;
		}

	}

	/**
	 *	Get's a language file from the framework (Not a module)
	 *
	 *	@param  $section	The section to fetch language for. (See dirs in 'language')
	 * @access public
	 * @return string	Full path to the language file
	 */
	function get_base_language_file($section, $language=null) {
		global $GO_CONFIG;
		
		if(!isset($language))
			$language = $this->language;
		
		$file = $this->language_path.$section.'/'.$language.'.inc.php';
		if (file_exists($file)) {
			return $file;
		} else {
			return $this->get_fallback_base_language_file($section);
		}
	}

	/**
	 *	Get's the default language file from the framework (Not a module).
	 * This is always included before the prefered language file.
	 * If the prefered language file misses some strings they will be
	 * defined by the default language.
	 *
	 *	@param  $section	The section to fetch language for. (See dirs in 'language')
	 * @access public
	 * @return string	Full path to the fallback language file
	 */
	function get_fallback_base_language_file($section) {
		global $GO_CONFIG;

		$file = $this->language_path.$section."/en.inc.php";
		if (file_exists($file)) {
			return $file;
		} else {
			return false;
		}
	}

	/**
	 *	Get's a language file from a module
	 *
	 *	@param  $module_id	The module to fetch language for.
	 * @access public
	 * @return string	Full path to the language file
	 */
	function get_language_file($module_id, $language=null) {
		global $GO_CONFIG;

		/*
		 The new language file location is inside the language folder in the
		 modules folder. So we create the absolute path to the file and check
		 if this file exists.
		 */
		if(!isset($language))
			$language = $this->language;

		$module_path = $GO_CONFIG->module_path.$module_id;

		$file = $module_path.'/language/'.$language.'.inc.php';

		if (file_exists($file)) {
			return $file;
		} else {
			return $this->get_fallback_language_file($module_id);
		}
	}

	/**
	 *	Get's the prefered language file.
	 *
	 *	@param  $section	The section to fetch language for. (See dirs in 'language')
	 * @access public
	 * @return string	Full path to the language file
	 */
	function get_fallback_language_file($module_id) {
		global $GO_CONFIG;

		$module_path = $GO_CONFIG->module_path.'/'.$module_id;
		if(!file_exists($module_path))
		{
			$module_path = $GO_CONFIG->root_path.'/legacy/modules/'.$module_id;
		}

		$file = $module_path.'/language/en.inc.php';

		if (file_exists($file)) {
			return $file;
		} else {
			return false;
		}
	}
}
