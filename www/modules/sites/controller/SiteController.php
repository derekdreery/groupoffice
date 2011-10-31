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
	
	
	protected function setSite($params){
		if(!empty($params['site_id']))
			$this->site=GO_Sites_Model_Site::model()->findByPk($params['site_id']);
		
		$this->site=GO_Sites_Model_Site::model()->find(GO_Base_Db_FindParams::newInstance()->single());
		
		$this->templateUrl = GO::config()->host.'modules/sites/templates/'.$this->site->template.'/';
		$this->templateFolder = new GO_Base_Fs_Folder(GO::modules()->sites->path.'templates/'.$this->site->template);
	}
	
	/**
	 * This default action should be overrriden
	 */
	public function actionIndex($params){		
		$this->setSite($params);
		$this->renderPage($params['p']);		
	}
	
	protected function renderPage($path, $params=array()){
		extract($params);
		
		$this->page = GO_Sites_Model_Page::model()->findSingleByAttributes(array('site_id'=>$this->site->id, 'path'=>$path));
		
		$template = empty($this->page->template) ? 'index.php' : $this->page->template.'.php';
		
		require(GO::modules()->sites->path.'templates/'.$this->site->template.'/'.$template);
	}
	
}
