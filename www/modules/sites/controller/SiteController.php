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
	
	protected $site;
	protected $page;
	
	protected $rootTemplatePath;
	protected $rootTemplateUrl;
	
	protected $templateUrl;
	protected $templateFolder;
	
	protected $lastPath;
	
	protected $notification;
	
	
	protected function allowGuests() {
		return array('index');
	}
	
	protected function init() {

		if(!isset(GO::session()->values['sites']))
						GO::session()->values['sites'] = array();
					
		$this->setSite($_REQUEST);
		
		if(isset(GO::session()->values['sites']['lastPath']))
			$this->lastPath=GO::session()->values['sites']['lastPath'];
		
		
		$this->rootTemplatePath = GO::config()->root_path.'modules/sites/templates/'.$this->site->template.'/';
		$this->rootTemplateUrl = GO::config()->host.'modules/sites/templates/'.$this->site->template.'/';
		
		return parent::init();
	}
	
	public function getRootTemplatePath(){
		return $this->rootTemplatePath;
	}
	
	public function getRootTemplateUrl(){
		return $this->rootTemplateUrl;
	}
	
	
	protected function setSite($params){
		if(!empty($params['site_id']))
			$this->site=GO_Sites_Model_Site::model()->findByPk($params['site_id']);		
		
		$this->site=GO_Sites_Model_Site::model()->find(GO_Base_Db_FindParams::newInstance()->single());

		$this->templateUrl = $this->_getTemplateFolderUrl();
		$this->templateFolder = new GO_Base_Fs_Folder($this->_getTemplateFolderPath());
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
		$this->renderPage($params['p'],$params);		
	}
	protected function beforeRenderPage($path, $params){
		
	}
	
	protected function renderPage($path, $params=array()){
		
		$this->beforeRenderPage($path, $params);
		extract($params);
		
		//echo $path.':'.$this->site->id;
		
		if($path == $this->site->getLoginPath()){
			if(!empty(GO::session()->values['sites']['lastPath']) && GO::session()->values['sites']['lastPath'] == $this->site->getLoginPath())
					GO::session()->values['sites']['beforeLoginPath']='products'; // TODO: This path needs to be the path to the homepage when that is working
				else
					GO::session()->values['sites']['beforeLoginPath']=GO::session()->values['sites']['lastPath'];
		}
		
		GO::session()->values['sites']['lastPath']=$path;
		
		$this->page = GO_Sites_Model_Page::model()->findSingleByAttributes(array('site_id'=>$this->site->id, 'path'=>$path));
			
		if(!$this->page){
			$this->notFound();
		}else{
			
			$this->_checkAuth();
		
			$this->_handleForm($params);
		
			//var_dump($this->page);

			$template = empty($this->page->template) ? 'index.php' : $this->page->template.'.php';

			require($this->templateFolder->path().'/'.$template);
		}
	}
	
	private function _checkAuth() {
		if($this->page->login_required && !GO::user()){
			$this->pageRedirect($this->site->getLoginPath());
		}
	}
	
	private function _handleForm($params){
		if(isset($params['formRoute'])){
			$params['r']=$params['formRoute'];
			if(GO_Base_Html_Input::checkRequired()){
				$router = new GO_Base_Router();
				$router->runController($params);
			}
		}
	}
	
	protected function notFound(){
		echo '<h1>Not found</h1>';
	}
	
	/**
	 * Generate a URL to a page.
	 * 
	 * @param string $path
	 * @param array $params
	 * @param boolean $relative
	 * @return string 
	 */
	public static function pageUrl($path, $params=array(), $relative=true){
		$params['p']=$path;	
		
		$page = GO_Sites_Model_Page::model()->findSingleByAttribute('path', $path);
		
		if(!$page)
			throw new Exception('NOT FOUND');
		return $page->getUrl($params, $relative);
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
	
	protected function setNotification($type, $string){
		
	}
	
	protected function getNotification(){
		
	}
	
	/**
	 * Get the webshop for this website if it has one.
	 * 
	 * @return GO_Webshop_Model_Webshop 
	 */
	protected function getWebshop(){
		return GO_Webshop_Model_Webshop::model()->findSingleByAttribute('site_id',$this->site->id);
	}
	
}
