<?php

class GO_Site_Controller_Site extends GO_Base_Controller_AbstractJsonController {
	
	/**
	 * Redirect to the homepage
	 * 
	 * @param array $params
	 */
	protected function actionRedirectToFront($params){
		
		$site = GO_Site_Model_Site::model()->findByPk($params['id']);
		
		header("Location: ".$site->getBaseUrl());
		exit();
	}
	
	protected function actionLoad($params) {
		$model = GO_Site_Model_Site::model()->createOrFindByParams($params);
		
		echo $this->renderForm($model);
	}
	
	protected function actionSubmit($params) {
		$model = GO_Site_Model_Site::model()->createOrFindByParams($params);
		$model->setAttributes($params);
		$model->save();
		echo $this->renderSubmit($model);
	}
	
		
	/**
	 * Build the tree for the backend
	 * 
	 * @param array $params
	 * @return array
	 */
	protected function actionTree($params){
		$response=array();
	
		if(!isset($params['node']))
			return $response;
		
		$extractedNode = GO_Site_SiteModule::extractTreeNode($params['node']);
		
		// 1_menuitem_6 = array('siteId' => '1','type' =>'menuitem','modelId' => '6');
		// 1_root = array('siteId' => '1','type' =>'root','modelId' => false);
		// 1_content = array('siteId' => '1','type' =>'content','modelId' => false);
		// 1_menu = array('siteId' => '1','type' =>'menu','modelId' => false);
		// 1_menu_1 = array('siteId' => '1','type' =>'menu','modelId' => '1');
				
		switch($extractedNode['type']){
			case 'root':
				$response = GO_Site_Model_Site::getTreeNodes();
				break;
			case 'content':
				if(empty($extractedNode['modelId'])){
					$response = GO_Site_Model_Site::getTreeNodes();
				} else {
					$content = GO_Site_Model_Content::model()->findByPk($extractedNode['modelId']);
					if($content)
						$response = $content->getChildrenTree();
				}
				break;
			case 'menu':
				$menu = GO_Site_Model_Menu::model()->findByPk($extractedNode['modelId']);
					if($menu)
						$response = $menu->getChildrenTree();
				break;
			case 'menuitem':
				$menuitem = GO_Site_Model_MenuItem::model()->findByPk($extractedNode['modelId']);
					if($menuitem)
						$response = $menuitem->getChildrenTree();
				break;
		}
		
		echo $this->renderJson($response);
	}
	
	
	/**
	 * Rearrange the tree based on the given sorting
	 * 
	 * @param array $params
	 * @return array
	 */
	protected function actionTreeSort($sortOrder, $parent){
//		EXAMPLE INPUT
//		parent:1_menu_11
//		sortOrder:["1_menuitem_30","1_menuitem_31","1_menuitem_33","1_menuitem_8"]
			$sortOrder = json_decode($sortOrder, true);
			$extractedParentNode = GO_Site_SiteModule::extractTreeNode($parent);
			
			switch($extractedParentNode['type']){
				case 'content':
					$allowedTypes = array('content');
					return GO_Site_Model_Content::setTreeSort($extractedParentNode, $sortOrder, $allowedTypes);
					break;
//				case 'site':
//					$allowedTypes = array('content');
//					return GO_Site_Model_Site::setTreeSort($extractedParentNode, $sortOrder, $allowedTypes);
//					break;
				case 'menu':
					$allowedTypes = array('menuitem');
					return GO_Site_Model_Menu::setTreeSort($extractedParentNode, $sortOrder, $allowedTypes);
					break;
				case 'menuitem':
					$allowedTypes = array('menuitem');
					return GO_Site_Model_MenuItem::setTreeSort($extractedParentNode, $sortOrder, $allowedTypes);
					break;
			}
	}
	
	/**
	 * Save the state of the tree
	 * 
	 * @param array $params
	 * @return array
	 */
	protected function actionSaveTreeState($params) {
		$response['success'] = GO::config()->save_setting("site_tree_state", $params['expandedNodes'], GO::user()->id);
		return $response;
	}	
}