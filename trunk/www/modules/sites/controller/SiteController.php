<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The GO_Tasks_Controller_Category controller
 *
 * @package GO.modules.Tasks
 * @version $Id: GO_Tasks_Controller_Category.php 7607 2011-09-20 10:07:50Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 */

class GO_Sites_Controller_Site extends GO_Base_Controller_AbstractController{
	
	/**
	 * The current site model
	 * 
	 * @var GO_Sites_Model_Site 
	 */
	private $site;
	
	/**
	 * The current page model
	 * 
	 * @var GO_Sites_Model_Page 
	 */
	private $page;
	
	/**
	 * The path to the template folder in the sites module.
	 *
	 * @var string 
	 */
	protected $rootTemplatePath;
	
	/**
	 * The url to the template folder in the sites module.
	 * 
	 * @var string 
	 */
	protected $rootTemplateUrl;
	
	/**
	 * The url to the template folder of the current module
	 * 
	 * @var string 
	 */
	protected $templateUrl;
	
	/**
	 * The path of the template folder in the current module
	 * 
	 * @var string 
	 */
	protected $templateFolder;
		
//	protected $notification;
	
	/**
	 * Construct this object.
	 * Sets the private variables site and page.
	 * 
	 * @param GO_Sites_Model_Site $site
	 * @param GO_Sites_Model_Page $page 
	 */
	public function __construct($site, $page) {	
		
		$this->site=$site;
		$this->page=$page;
		
		parent::__construct();
	}
	
	/**
	 * Returns the current site object
	 * 
	 * @return GO_Sites_Model_Site The current site
	 */
	public function getSite(){
		return $this->site;
	}
	
	/**
	 * Returns the current page object
	 * 
	 * @return GO_Sites_Model_Page The current page 
	 */
	public function getPage(){
		return $this->page;
	}
	
	/**
	 * Sets the access permissions for guests
	 * Defaults to '*' which means that all functions can be accessed by guests.
	 * 
	 * @return array List of all functions that can be accessed by guests 
	 */
	protected function allowGuests() {
		return array('*');
	}
	
	/**
	 * Initialize this object and sets the protected variables
	 * 
	 * @return mixed 
	 */
	protected function init() {
		
		$this->_checkSessionVars();
		
		$this->rootTemplatePath = GO::config()->root_path.'modules/sites/templates/'.$this->site->template.'/';
		$this->rootTemplateUrl = GO::config()->host.'modules/sites/templates/'.$this->site->template.'/';
		$this->templateUrl = $this->_getTemplateFolderUrl();
		$this->templateFolder = new GO_Base_Fs_Folder($this->_getTemplateFolderPath());
		
		$this->_checkAuth();
		
		return parent::init();
	}
	
	/**
	 * Get the path of the template folder in the sites module.
	 * 
	 * @return string The path to the template folder in the sites module. 
	 */
	public function getRootTemplatePath(){
		return $this->rootTemplatePath;
	}
	
	/**
	 * Get the url of the template folder in the sites module.
	 * 
	 * @return string The url to the template folder in the sites module.
	 */
	public function getRootTemplateUrl(){
		return $this->rootTemplateUrl;
	}
		
	/**
	 * Private function to get the template folder path of the current module.
	 * 
	 * @return string The path to the template folder in the current module. 
	 */
	private function _getTemplateFolderPath(){
		$path = '';
		$moduleName = $this->getModule()->id;
		
		$path .= GO::config()->root_path.'modules/'.$moduleName;
		if($moduleName != 'sites')
			$path .= '/sites';
		$path .= '/templates/'.$this->site->template.'/';
		return $path;
	}
	
	/**
	 * Private function to get the template folder url of the current module.
	 * 
	 * @return string The url to the template folder in the current module. 
	 */
	private function _getTemplateFolderUrl(){
		$url = '';
		$moduleName = $this->getModule()->id;
		
		$url .= GO::config()->host.'modules/'.$moduleName;
		if($moduleName != 'sites')
			$url .= '/sites';
		$url .= '/templates/'.$this->site->template.'/';
		return $url;
	}
	
	/**
	 * The default index action
	 * This default action should be overrriden
	 * 
	 * @param array $params The parameters that need to be passed through to 
	 * the page.
	 */
	protected function actionIndex($params){		
		$this->renderPage($params);		
	}
	
	/**
	 * Function that you can override.
	 * This function will be called right before the renderpage function.
	 * 
	 * @param array $params The parameters that need to be passed through to 
	 * the page.
	 */
	protected function beforeRenderPage($params){
		
	}

	/**
	 * Render the page 
	 * 
	 * @todo Make the header and footer dynamic
	 * 
	 * @param array $params The parameters that need to be passed through to 
	 * the page.
	 */
	protected function renderPage($params=array()){
		
		$this->beforeRenderPage($params);
		extract($params);

		$template = empty($this->page->template) ? 'index.php' : $this->page->template.'.php';

		require($this->getRootTemplatePath().'header.php');
		require($this->templateFolder->path().'/'.$template);
		require($this->getRootTemplatePath().'footer.php');
		
	}

	/**
	 * Checks if the last path needs to be changed and sets the right last path 
	 * in the session. 
	 */
	private function _checkSessionVars(){
		
		if(!isset(GO::session()->values['sites']))
			GO::session()->values['sites'] = array();
		
		if(isset(GO::session()->values['sites']['lastPath']))
			$this->site->setLastPath(GO::session()->values['sites']['lastPath']);

		if($this->page->path == $this->site->getLoginPath()){
			if($this->site->getLastPath() == $this->site->getLoginPath())
				$this->site->setLastPath($this->site->getHomePagePath());
		} else if($this->page->path == $this->site->getRegisterPath()){
			if($this->site->getLastPath() == $this->site->getRegisterPath())
				$this->site->setLastPath($this->site->getHomePagePath());
		} else if($this->page->path == $this->site->getPasswordResetPath()){
			if($this->site->getLastPath() == $this->site->getPasswordResetPath())
				$this->site->setLastPath($this->site->getHomePagePath());
		}else {
			GO::session()->values['sites']['lastPath'] = $this->page->path;
		}
	}
	
	/**
	 * Check if the page needs that a user is logged in and checks if the user is 
	 * logged in also. 
	 */
	private function _checkAuth() {
		if($this->page->login_required && !GO::user()){
			$this->pageRedirect($this->site->getLoginPath());
		}
	}

	/**
	 * Will be displayed when a page is not found 
	 */
	protected function notFound(){
		echo '<h1>Not found</h1>';
	}
	
	/**
	 * Generate a controller URL.
	 * 
	 * @param string $path To controller. eg. addressbook/contact/submit
	 * @param array $params eg. array('id'=>1,'someVar'=>'someValue')
	 * @param boolean $relative Defaults to true. Set to false to return an absolute URL.
	 * @param boolean $htmlspecialchars Set to true to escape special html characters. eg. & becomes &amp.
	 * @return string 
	 */
	public function pageUrl($path='', $params=array(), $relative=true, $htmlspecialchars=false){
		return $this->site->pageUrl($path, $params, $relative);
	}
	
	
	/**
	 * Redirect to the given page.
	 * 
	 * @param string $path The path of the redirect page
	 * @param array $params The parameters that need to be passed through the 
	 * redirect page
	 */
	protected function pageRedirect($path = '', $params=array()) {
		header('Location: ' .$this->site->pageUrl($path, $params, true, false));
		exit();
	}
//	
//	protected function setNotification($type, $string){
//		
//	}
//	
//	protected function getNotification(){
//		
//	}
	
	/**
	 * Get the webshop for this website if it has one.
	 * 
	 * @return GO_Webshop_Model_Webshop 
	 */
	protected function getWebshop(){
		return GO_Webshop_Model_Webshop::model()->findSingleByAttribute('site_id',$this->site->id);
	}
	
}
