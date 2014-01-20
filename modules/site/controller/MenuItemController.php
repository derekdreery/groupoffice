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
 * The GO_Site_Controller_MenuItem controller object
 *
 * @package GO.modules.Site
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits wsmits@intermesh.nl
 * 
 */
class GO_Site_Controller_MenuItem extends GO_Base_Controller_AbstractJsonController {
	
	/**
	 * Loads a store of menu items that can be a parent of the given menu item.
	 * 
	 * @param int $id
	 * @param int $site_id
	 */
	public function actionParentStore($id = false, $menu_id){
		
		$findCriteria = GO_Base_Db_FindCriteria::newInstance()->addCondition('menu_id', $menu_id);
		$findCriteria->addCondition('id', $id,'<>');
		$findParams = GO_Base_Db_FindParams::newInstance()->criteria($findCriteria);
		
		$store = new GO_Base_Data_DbStore('GO_Site_Model_MenuItem', new GO_Base_Data_ColumnModel('GO_Site_Model_MenuItem'), $_REQUEST,$findParams);
		
		echo $this->renderStore($store);
	}
	
	/**
	 * Loads a menu item
	 * 
	 * @param int $id
	 * @param int $site_id
	 */
	public function actionLoad($id = false,$menu_id){
		$remoteComboFields = array();
		
		$model = $this->_loadModel($menu_id,$id);
		
		if(!empty($model->content_id))
			$remoteComboFields['content_id']=$model->content->title;
		
		echo $this->renderForm($model,$remoteComboFields);
	}
	
	/**
	 * Create a new menu item
	 * 
	 * @param int $site_id
	 */
	public function actionCreate($menu_id){
		$remoteComboFields = array();
		
		$model = $this->_loadModel($menu_id);
		
		if(GO_Base_Util_Http::isPostRequest()){
			$model->setAttributes($_POST);
			$model->save();
		}
		
		echo $this->renderForm($model,$remoteComboFields);
	}
	
	/**
	 * Create a new menu item
	 * 
	 * @param int $site_id
	 */
	public function actionCreateFromContent($target,$content){
		
		if(GO_Base_Util_Http::isPostRequest()){
			
			$target = json_decode($target);
			$content = json_decode($content);
			
			$targetModelName = GO_Site_SiteModule::getModelNameFromTreeNodeType($target->type);
			$contentModelName = GO_Site_SiteModule::getModelNameFromTreeNodeType($content->type);
			
			$targetModel = $targetModelName::model()->findByPk($target->modelId);
			$contentModel = $contentModelName::model()->findByPk($content->modelId);
			if($targetModel instanceof GO_Site_Model_MenuItem){
				$menuId = $targetModel->menu_id;
			}else{
				$menuId = $targetModel->id;
			}

			$model = $this->_loadModel($menuId);
			$model->parent_id = $targetModel->id;
			$model->content_id = $contentModel->id;
			$model->label = $contentModel->title;
			
	
// * @property int $menu_id The menu_id of this menu
// * @property int $id The id of this menu item
// * @property int $parent_id Optional: The parent menuItem of this current item.
// * @property int $content_id Optional: The content_id where this menu items links to
// * @property String $label The label of the menu item
// * @property String $url Optional: url field to create menu items that link to a page that you can fill in manually
// * @property Boolean $display_children Only usable when content_id is set. When true this will load the child items of the current content_id.
// * @property int $sort_order The sort order for the menu items at the same level
// * @property String $target The target for the url in this menu item
//				
//			$model->setAttributes($_POST);
			$model->save();
		}
		
		echo $this->renderJson(array('success'=>true));
	}
	
	/**
	 * Update a new menu item
	 * 
	 * @param int $id
	 * @param int $site_id
	 */
	public function actionUpdate($id,$menu_id){
		$remoteComboFields = array();
		
		$model = $this->_loadModel($menu_id,$id);
		
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
	public function actionDelete($id,$menu_id){
		
		$model = $this->_loadModel($menu_id,$id);
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
	private function _loadModel($menu_id, $id = false){
		
		if(!empty($id)){
			$model = GO_Site_Model_MenuItem::model()->findByPk($id);
		}else{
			$model = new GO_Site_Model_MenuItem();
			$model->menu_id = $menu_id;
		}
		
		if(!$model)
			Throw new Exception('Model with id: '.$id.' not found.');
		
		return $model;
	}
	
}
	