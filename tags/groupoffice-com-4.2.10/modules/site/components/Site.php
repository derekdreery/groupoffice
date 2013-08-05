<?php
/*
 * Copyright Intermesh BV
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The Site Launcher Object.
 * The static methods inside this object can be used in all the
 * Models, Views and Controllers that are runned by the site's index.php
 *
 * @package GO.modules.site.components
 * @copyright Copyright Intermesh
 * @version $Id$ 
 * @author Wesley Smits <wsmits@intermesh.nl> 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 */
class Site {
	
	/**
	 *
	 * @var GO_Site_Model_Site 
	 */
	private static $_site;
	
	/**
	 *
	 * @var GO_Site_Model_Router 
	 */
	private static $_router;
	
	/**
	 *
	 * @var GO_Site_Components_UrlManager 
	 */
	private static $_urlManager;
	
	/**
	 *
	 * @var GO_Site_Components_Language
	 */
	private static $_language;
	
	/**
	 *
	 * @var GO_Site_Components_Notifier
	 */
	private static $_notifier;
	
	/**
	 *
	 * @var GO_Site_Components_Request
	 */
	private static $_request;
	
	/**
	 *
	 * @var GO_Site_Components_Scripts 
	 */
	private static $_scripts;
	
	/**
	 *
	 * @var GO_Site_Components_Template 
	 */
	private static $_template;
	
	/**
	 *
	 * @var GO_Site_Components_AssetManager
	 */
	private static $_assetManager;

	/**
	 *
	 * @var GO_Site_Components_Config
	 */
	private static $_config;
	
	/**
	 * Handles string translation for sites 
	 */
	public static function t($key)
	{
		return self::language()->getTranslation($key);
	}
	
	/**
	 * Get the site model fro the database.
	 * 
	 * @return GO_Site_Model_Site
	 */
	public static function model(){
		return self::$_site;
	}
	
	public static function controller() {
		return self::$_router->getController();
	}
	
	/**
	 * Return's the router that routes an incomming request to a controller
	 * 
	 * @return GO_Site_Components_Router
	 */
	public static function router(){
		if(!isset(self::$_router))
			self::$_router = new GO_Site_Components_Router ();
		
		return self::$_router;
	}
	
	/**
	 * Return the config component with all parameter as defined in siteconfig.php
	 * @return GO_Site_Components_Config
	 */
	public static function config() {
		if(!isset(self::$_config))
			self::$_config = new GO_Site_Components_Config(self::model());
		return self::$_config;
	}
	
	/**
	 * Get the url manager for this site for createUrl()
	 * 
	 * @return GO_Site_Components_UrlManager
	 */
	public static function urlManager() {
		if (self::$_urlManager == null) {
			
			self::$_urlManager = new GO_Site_Components_UrlManager();
			
			$urls = Site::model()->getConfig()->urls;

			if(!empty($urls))
				self::$_urlManager->rules = $urls;
			else
				self::$_urlManager->rules = array();
			
			self::$_urlManager->init();
		}
		return self::$_urlManager;
	}

	/**
	 * Find's the site model by server name or GET param site_id and runs the site.
	 * 
	 * @throws GO_Base_Exception_NotFound
	 */
	public static function launch() {
		
		if(empty(GO::session()->values['sites']['site']) || GO::config()->debug){
			if(!empty(GO::session()->values['sites']['site_id']))
				GO::session()->values['sites']['site'] = GO_Site_Model_Site::model()->findByPk(GO::session()->values['sites']['site_id']);
			else
				GO::session()->values['sites']['site'] = GO_Site_Model_Site::model()->findSingleByAttribute('domain', $_SERVER["SERVER_NAME"]); // Find the website model from its domainname

			if(!GO::session()->values['sites']['site']){
				GO::session()->values['sites']['site'] = GO_Site_Model_Site::model()->findSingleByAttribute('domain', '*'); // Find the website model from its domainname
			}

			if(!GO::session()->values['sites']['site'])
				throw new GO_Base_Exception_NotFound('Website for domain '.$_SERVER["SERVER_NAME"].' not found in database');
		}
		
		self::$_site=GO::session()->values['sites']['site'];
		
		if(!empty(self::model()->language))
			GO::language()->setLanguage(self::model()->language);

		self::router()->runController();
	}
	
	/**
	 * 
	 * @return GO_Site_Components_Language
	 */
	public static function language() {
		if (self::$_language == null){
			self::$_language = new GO_Site_Components_Language(Site::model()->language);
		}
		return self::$_language;
	}
	
	/**
	 * Adds notification messages to the rendered page.
	 * The message is deleted from the session after it is displayed for the first time
	 * In most cases you want ti use it inside if(Notifier::hasMessage($key))
	 * @return GO_Site_Components_Notifier
	 */
	public static function notifier() {
		if (self::$_notifier == null)
			self::$_notifier = new GO_Site_Components_Notifier();
		return self::$_notifier;
	}
	
	/**
	 * Request object for finding requestUri, basePath, HostIno
	 * @return GO_Site_Components_Request
	 */
	public static function request() {
		if (self::$_request == null)
			self::$_request = new GO_Site_Components_Request();
		return self::$_request;
	}
	
	
	/**
	 * Component for adding scripts css en meta tags to the head of the rendered result.
	 * use the POS_ constants to define where the scripts should be added
	 * @return GO_Site_Components_Scripts
	 */
	public static function scripts() {
		if (self::$_scripts == null)
			self::$_scripts = new GO_Site_Components_Scripts();
		return self::$_scripts;
	}
	
	/**
	 * 
	 * @return GO_Site_Components_Template
	 */
	public static function template(){
		if (self::$_template == null)
			self::$_template = new GO_Site_Components_Template();
		return self::$_template;
	}
	
	/**
	 * 
	 * @return GO_Site_Components_AssetManager
	 */
	public static function assetManager(){
		if (self::$_assetManager == null)
			self::$_assetManager = new GO_Site_Components_AssetManager();
		return self::$_assetManager;
	}
	
	/**
	 * Get URL to a public template file that is accessible with the browser.
	 * 
	 * @param string $relativePath
	 * @return string
	 */
	public static function file($relativePath, $template=true){

		if(!$template){			
			$folder = new GO_Base_Fs_Folder(Site::model()->getPublicPath());
			
			$relativePath=str_replace($folder->stripFileStoragePath().'/files/', '', $relativePath);
			return Site::model()->getPublicUrl().'files/'.$relativePath;	
		}else{
			return self::template()->getUrl().$relativePath;
		}
	}
	
	
	/**
	 * Get Path to a public template file that is accessible with the browser.
	 * 
	 * @param string $relativePath
	 * @return string
	 */
	public static function filePath($relativePath, $template=true){
		if(!$template){
			return Site::model()->getPublicPath().'/files/'.$relativePath;
		}else
		{
			return self::template()->getPath().$relativePath;
		}
	}
	
	
	/**
	 * Get a thumbnail URL for user uploaded files. This does not work for template
	 * images.
	 * 
	 * @param string $relativePath
	 * @param array $thumbParams
	 * @return string URL to thumbnail
	 */
	public static function thumb($relativePath, $thumbParams=array("lw"=>100, "ph"=>100, "zc"=>1)) {
		
		$file = new GO_Base_Fs_File(GO::config()->file_storage_path.$relativePath);
		
		$thumbParams['filemtime']=$file->mtime();
		$thumbParams['src']=$relativePath;
	
		return Site::urlManager()->createUrl('site/front/thumb', $thumbParams);
	}
	
}