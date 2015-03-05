<?php
/**
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @package GO.modules.Site
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 */
 
/**
 * The GO_Site_Controller_Menu controller object
 *
 * @package GO.modules.Site
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 * 
 */
class GO_Site_Controller_Menu extends GO_Base_Controller_AbstractJsonController {
		
	/**
	 * Loads a store of content items of the current website
	 * 
	 * @param int $id
	 * @param int $site_id
	 */
	public function actionContentStore($menu_id){
		
		$menu = GO_Site_Model_Menu::model()->findByPk($menu_id);
		
		$findCriteria = GO_Base_Db_FindCriteria::newInstance()->addCondition('site_id', $menu->site_id);
		$findParams = GO_Base_Db_FindParams::newInstance()->criteria($findCriteria);
		
		$store = new GO_Base_Data_DbStore('GO_Site_Model_Content', new GO_Base_Data_ColumnModel('GO_Site_Model_Content'), $_REQUEST,$findParams);
		
		echo $this->renderStore($store);
	}
	
	
	/**
	 * Loads a menu item
	 * 
	 * @param int $id
	 * @param int $site_id
	 */
	public function actionLoad($id = false,$site_id){
		$remoteComboFields = array();
		
		$model = $this->_loadModel($site_id,$id);
		
		if(!empty($model->content_id))
			$remoteComboFields['content_id']=$model->content->title;
		
		echo $this->renderForm($model,$remoteComboFields);
	}
	
	/**
	 * Create a new menu item
	 * 
	 * @param int $site_id
	 */
	public function actionCreate($site_id){
		$remoteComboFields = array();
		
		$model = $this->_loadModel($site_id);
		
		if(GO_Base_Util_Http::isPostRequest()){
			$model->setAttributes($_POST);
			$model->save();
		}
		
		echo $this->renderForm($model,$remoteComboFields);
	}
	
	/**
	 * Update a new menu item
	 * 
	 * @param int $id
	 * @param int $site_id
	 */
	public function actionUpdate($id,$site_id){
		$remoteComboFields = array();
		
		$model = $this->_loadModel($site_id,$id);
		
		if(!empty($model->content_id))
			$remoteComboFields['content_id']=$model->content->title;
		
		if(GO_Base_Util_Http::isPostRequest()){
			$model->setAttributes($_POST);
			$model->save();
		}
		
		echo $this->renderForm($model,$remoteComboFields);
	}
	
	/**
	 * Delete a new menu item
	 * 
	 * @param int $id
	 * @param int $site_id
	 */
	public function actionDelete($id,$site_id){
		
		$model = $this->_loadModel($site_id,$id);
		$model->delete();
		
		echo $this->renderForm($model);
	}
	
	/**
	 * Load the model menu object
	 * 
	 * @param int $siteId
	 * @param int $id
	 * @return \GO_Site_Model_Menu
	 * @throws Exception
	 */
	private function _loadModel($siteId, $id = false){
		
		if(!empty($id)){
			$model = GO_Site_Model_Menu::model()->findByPk($id);
		}else{
			$model = new GO_Site_Model_Menu();
			$model->site_id = $siteId;
		}
		
		if(!$model)
			Throw new Exception('Model with id: '.$id.' not found.');
		
		return $model;
	}
	
}
	