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
		
		$args = explode('_', $params['node']);
		
		$siteId = $args[0];
		
		if(!isset($args[1]))
			$type = 'root';
		else
			$type = $args[1];
		
		if(isset($args[2]))
			$parentId = $args[2];
		else
			$parentId = null;
		
		switch($type){
			case 'root':
				$response = GO_Site_Model_Site::getTreeNodes();
				break;
			case 'content':
				
				if($parentId === null){
					$response = GO_Site_Model_Content::getTreeNodes($siteId);
				} else {
					$parentNode = GO_Site_Model_Content::model()->findByPk($parentId);
					if($parentNode)
						$response = $parentNode->getChildrenTree();
				}
				break;
//			case 'news':
//				$response = GO_Site_Model_News::getTreeNodes($site);
//				break;
		}
		
		echo $this->renderJson($response);
	}
	
	
	/**
	 * Rearrange the tree based on the given sorting
	 * 
	 * @param array $params
	 * @return array
	 */
	protected function actionTreeSort($params){
		$sortOrder = json_decode($params['sort_order'], true);
		$parentId = $params['parent_id'];
			
		$order = 0;
		foreach($sortOrder as $contentId){
			$content = GO_Site_Model_Content::model()->findByPk($contentId);

			if($content){
				$content->parent_id = empty($parentId)?null:$parentId;
				$content->sort_order = $order;
				if($content->save()){
					$order++;
				}
			}
		}

		return array("success"=>true);
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