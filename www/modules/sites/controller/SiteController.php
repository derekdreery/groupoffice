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
 * @version $Id: GO_Tasks_Controller_Category.php 7607 2011-09-20 10:07:50Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
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
	
	protected $rootTemplatePath;
	protected $rootTemplateUrl;
	protected $templateUrl;
	protected $templateFolder;
		
//	protected $notification;
	
	public function __construct($site, $page) {	
		
		$this->site=$site;
		$this->page=$page;
		
		parent::__construct();
	}
	
	public function getSite(){
		return $this->site;
	}
	
	public function getPage(){
		return $this->page;
	}
	
	protected function allowGuests() {
		return array('index');
	}
	
	protected function init() {
		
		$this->_checkSessionVars();
		
		$this->rootTemplatePath = GO::config()->root_path.'modules/sites/templates/'.$this->site->template.'/';
		$this->rootTemplateUrl = GO::config()->host.'modules/sites/templates/'.$this->site->template.'/';
		$this->templateUrl = $this->_getTemplateFolderUrl();
		$this->templateFolder = new GO_Base_Fs_Folder($this->_getTemplateFolderPath());
		
		$this->_checkAuth();
		
		return parent::init();
	}
	
	public function getRootTemplatePath(){
		return $this->rootTemplatePath;
	}
	
	public function getRootTemplateUrl(){
		return $this->rootTemplateUrl;
	}
		
	private function _getTemplateFolderPath(){
		$path = '';
		$moduleName = $this->getModule()->id;
		
		$path .= GO::config()->root_path.'modules/'.$moduleName;
		if($moduleName != 'sites')
			$path .= '/sites';
		$path .= '/templates/'.$this->site->template.'/';
		return $path;
	}
	
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
	 * This default action should be overrriden
	 */
	public function actionIndex($params){		
		$this->renderPage($params);		
	}
	
	protected function beforeRenderPage($params){
		
	}

	protected function renderPage($params=array()){
		
		$this->beforeRenderPage($params);
		extract($params);

		$template = empty($this->page->template) ? 'index.php' : $this->page->template.'.php';

		require($this->getRootTemplatePath().'header.php');
		require($this->templateFolder->path().'/'.$template);
		require($this->getRootTemplatePath().'footer.php');
		
	}

	private function _checkSessionVars(){
		
		if(!isset(GO::session()->values['sites']))
			GO::session()->values['sites'] = array();
		
		if(isset(GO::session()->values['sites']['lastPath']))
			$this->site->setLastPath(GO::session()->values['sites']['lastPath']);

		if($this->page->path == $this->site->getLoginPath()){
			if($this->site->getLastPath() == $this->site->getLoginPath())
				$this->site->setLastPath($this->site->getHomePagePath());
		} else {
			GO::session()->values['sites']['lastPath'] = $this->page->path;
		}
	}
	
	private function _checkAuth() {
		if($this->page->login_required && !GO::user()){
			$this->pageRedirect($this->site->getLoginPath());
		}
	}
	
//	private function _handleForm($params){
//		if(isset($params['formRoute'])){
//			$params['r']=$params['formRoute'];
//			if(GO_Base_Html_Input::checkRequired()){
//				$router = new GO_Base_Router();
//				$router->runController($params);
//			}
//		}
//	}
	
	protected function notFound(){
		echo '<h1>Not found</h1>';
	}
	
//	/**
//	 * Generate a URL to a page.
//	 * 
//	 * @param string $path
//	 * @param array $params
//	 * @param boolean $relative
//	 * @return string 
//	 */
//	public static function pageUrl($path, $params=array(), $relative=true){
//		$params['p']=$path;	
//		
//		$page = GO_Sites_Model_Page::model()->findSingleByAttribute('path', $path);
//		
//		if(!$page)
//			throw new Exception('NOT FOUND');
//		return $page->getUrl($params, $relative);
//	}
	
	
	/**
	 * Generate a controller URL.
	 * 
	 * @param string $path To controller. eg. addressbook/contact/submit
	 * @param array $params eg. array('id'=>1,'someVar'=>'someValue')
	 * @param boolean $relative Defaults to true. Set to false to return an absolute URL.
	 * @param boolean $htmlspecialchars Set to true to escape special html characters. eg. & becomes &amp.
	 * @return string 
	 */
	public static function pageUrl($path='', $params=array(), $relative=true, $htmlspecialchars=false){
		$url = $relative ? GO::config()->host : GO::config()->full_url;
		
		$url .= 'modules/sites/index.php';
		
		if(empty($path) && empty($params)){
			return $url;
		}
		
		if(empty($path)){
			$amp = '?';
		}else
		{
			$url .= '?path='.$path;
			
			$amp = $htmlspecialchars ? '&amp;' : '&';
		}
		
		if(!empty($params)){			
			if(is_array($params)){				
				foreach($params as $name=>$value){
					$url .= $amp.$name.'='.urlencode($value);
					
					$amp = $htmlspecialchars ? '&amp;' : '&';
				}
			}else
			{
				$url .= $amp.$params;			
			}			
		}
		
		return $url;
	}
	
	
	/**
	 * Redirect to a page.
	 * 
	 * @param string $path 
	 */
	protected function pageRedirect($path = '', $params=array()) {
		header('Location: ' .self::pageUrl($path, $params));
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
