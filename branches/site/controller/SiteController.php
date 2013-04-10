<?php

class GO_Site_Controller_Site extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Site_Model_Site';
	

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
		
		return $response;
	}	
}