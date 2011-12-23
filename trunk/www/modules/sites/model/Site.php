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
 * The GO_Sites_Model_Page model
 *
 * @package GO.modules.Tasks
 * @version $Id: GO_Tasks_Model_Tasklist.php 7607 2011-09-20 10:07:07Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 *
 */

class GO_Sites_Model_Site extends GO_Base_Db_ActiveRecord {

	private $_lastPath;
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Sites_Model_Site 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function tableName() {
		return 'si_sites';
	}
	public function relations() {
		return array(
				'pages' => array('type' => self::HAS_MANY, 'model' => 'GO_Sites_Model_Page', 'field' => 'site_id', 'findParams'=>  GO_Base_Db_FindParams::newInstance()->order('sort')->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('parent_id', 0)->addCondition('hidden',0)), 'delete' => true),
				);
	}	
	
	public function setLastPath($path){
		$this->_lastPath = $path;
	}
	
	public function getLastPath(){
		if(empty($this->_lastPath))
			$this->_lastPath = $this->getHomePagePath();
		
		return $this->_lastPath;
	}
		
	
	
	public function getLoginPath(){
		return $this->login_page;
	}
	
	public function getLogoutPath(){
		return 'logout'; // TODO: get the right path name here
	}
	
	public function getHomePagePath(){
		return 'products'; // TODO: get the right path name here
	}
	
}