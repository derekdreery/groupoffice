<?php


namespace GO\Site\Controller;


class SiteController extends \GO\Base\Controller\AbstractJsonController {
	
	/**
	 * Redirect to the homepage
	 * 
	 * @param array $params
	 */
	protected function actionRedirectToFront($params){
		
		$site = \GO\Site\Model\Site::model()->findByPk($params['id']);
		
		header("Location: ".$site->getBaseUrl());
		exit();
	}
	
	protected function actionLoad($params) {
		$model = \GO\Site\Model\Site::model()->createOrFindByParams($params);
		
		echo $this->renderForm($model);
	}
	
	protected function actionSubmit($params) {
		$model = \GO\Site\Model\Site::model()->createOrFindByParams($params);
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
		
		$extractedNode = \GO\Site\SiteModule::extractTreeNode($params['node']);
		
		// 1_menuitem_6 = array('siteId' => '1','type' =>'menuitem','modelId' => '6');
		// 1_root = array('siteId' => '1','type' =>'root','modelId' => false);
		// 1_content = array('siteId' => '1','type' =>'content','modelId' => false);
		// 1_menu = array('siteId' => '1','type' =>'menu','modelId' => false);
		// 1_menu_1 = array('siteId' => '1','type' =>'menu','modelId' => '1');
				
		switch($extractedNode['type']){
			case 'root':
				$response = \GO\Site\Model\Site::getTreeNodes();
				break;
			case 'content':
				if(empty($extractedNode['modelId'])){
					$response = \GO\Site\Model\Site::getTreeNodes();
				} else {
					$content = \GO\Site\Model\Content::model()->findByPk($extractedNode['modelId']);
					if($content)
						$response = $content->getChildrenTree();
				}
				break;
			case 'menu':
				$menu = \GO\Site\Model\Menu::model()->findByPk($extractedNode['modelId']);
					if($menu)
						$response = $menu->getChildrenTree();
				break;
			case 'menuitem':
				$menuitem = \GO\Site\Model\MenuItem::model()->findByPk($extractedNode['modelId']);
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
			$extractedParentNode = \GO\Site\SiteModule::extractTreeNode($parent);
			
			switch($extractedParentNode['type']){
				case 'content':
					$allowedTypes = array('content');
					return \GO\Site\Model\Content::setTreeSort($extractedParentNode, $sortOrder, $allowedTypes);
					break;
//				case 'site':
//					$allowedTypes = array('content');
//					return \GO\Site\Model\Site::setTreeSort($extractedParentNode, $sortOrder, $allowedTypes);
//					break;
				case 'menu':
					$allowedTypes = array('menuitem');
					return \GO\Site\Model\Menu::setTreeSort($extractedParentNode, $sortOrder, $allowedTypes);
					break;
				case 'menuitem':
					$allowedTypes = array('menuitem');
					return \GO\Site\Model\MenuItem::setTreeSort($extractedParentNode, $sortOrder, $allowedTypes);
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
		$response['success'] = \GO::config()->save_setting("site_tree_state", $params['expandedNodes'], \GO::user()->id);
		return $response;
	}	
}