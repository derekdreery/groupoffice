<?php

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
	 * Handles string translation for sites 
	 */
	public static function t($key)
	{
		return self::language()->getTranslation($key);
	}
	
	/**
	 * Get the site model.
	 * 
	 * @return GO_Site_Model_Site
	 */
	public static function model(){
		return self::$_site;
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
	 * Get the url manager for this site
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
		if(isset($_GET['site_id']))
			GO::session()->values['sites']['site_id']=$_GET['site_id'];
		
		if(!empty(GO::session()->values['sites']['site_id']))
			self::$_site = GO_Site_Model_Site::model()->findByPk(GO::session()->values['sites']['site_id']);
		else
			self::$_site = GO_Site_Model_Site::model()->findSingleByAttribute('domain', $_SERVER["SERVER_NAME"]); // Find the website model from its domainname
		
		if(!self::$_site)
			throw new GO_Base_Exception_NotFound('Website for domain '.$_SERVER["SERVER_NAME"].' not found in database');
				
		self::router()->runController();
	}
	
	/**
	 * 
	 * @return GO_Site_Components_Language
	 */
	public static function language() {
		if (self::$_language == null)
			self::$_language = new GO_Site_Components_Language(Site::model()->language);
		return self::$_language;
	}
	
	/**
	 * 
	 * @return GO_Site_Components_Request
	 */
	public static function request() {
		if (self::$_request == null)
			self::$_request = new GO_Site_Components_Request();
		return self::$_request;
	}
	
	
	/**
	 * 
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
	 * Get URL to a public template file that is accessible with the browser.
	 * 
	 * @param string $relativePath
	 * @return string
	 */
	public static function file($relativePath){

		$referenceString = 'site/'.Site::model()->id.'/';
	
		$check = substr_count($relativePath,$referenceString);
		
		if($check)
			return str_replace($referenceString,'', $relativePath);
		else
			return self::template()->getUrl().$relativePath;
	}
}