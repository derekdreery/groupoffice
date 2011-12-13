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
	
	protected $templateUrl;
	protected $templateFolder;
	
	protected $lastPath;
	
	protected $notification;
	
	
	protected function allowGuests() {
		return array('index');
	}
	
	protected function init() {

		$this->setSite($_REQUEST);
		
		if(isset(GO::session()->values['sites']['lastPath']))
			$this->lastPath=GO::session()->values['sites']['lastPath'];
		
		return parent::init();
	}
	
	protected function setSite($params){
		if(!empty($params['site_id']))
			$this->site=GO_Sites_Model_Site::model()->findByPk($params['site_id']);		
		
		$this->site=GO_Sites_Model_Site::model()->find(GO_Base_Db_FindParams::newInstance()->single());
		

		$this->templateUrl = GO::config()->host.'modules/sites/templates/'.$this->site->template.'/';
		$this->templateFolder = new GO_Base_Fs_Folder(GO::config()->root_path.'modules/sites/templates/'.$this->site->template);
	}
	
	/**
	 * This default action should be overrriden
	 */
	public function actionIndex($params){		
		$this->renderPage($params['p'],$params);		
	}
	
	protected function renderPage($path, $params=array()){
		extract($params);
		
		//echo $path.':'.$this->site->id;
		
		if($path == $this->site->getLoginPath()){
			if(GO::session()->values['sites']['lastPath'] == $this->site->getLoginPath())
					GO::session()->values['sites']['beforeLoginPath']='products'; // TODO: This path needs to be the path to the homepage when that is working
				else
					GO::session()->values['sites']['beforeLoginPath']=GO::session()->values['sites']['lastPath'];
		}
		
		GO::session()->values['sites']['lastPath']=$path;
		
		$this->page = GO_Sites_Model_Page::model()->findSingleByAttributes(array('site_id'=>$this->site->id, 'path'=>$path));
		
		$this->_checkAuth();
		
		$this->_handleForm($params);
		
		if(!$this->page){
			$this->notFound();
		}else{
		
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
		return GO::url('sites/site/index', $params, $relative);
	}
	
	/**
	 * Redirect to a page.
	 * 
	 * @param string $path 
	 */
	protected function pageRedirect($path = '') {
		header('Location: ' .self::pageUrl($path));
		exit();
	}
	
	protected function setNotification($type, $string){
		
	}
	
	protected function getNotification(){
		
	}
	
}
