<?php

class GO_Site_Controller_Site extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Site_Model_Site';
	
	protected function actionRedirectToFront($params){
		
		$site = GO_Site_Model_Site::model()->findByPk($params['id']);
		
		header("Location: ".$site->getBaseUrl());
		exit();
	}
	
	
	protected function actionSiteTree($params) {
		$response=array();
		
		$findParams = GO_Base_Db_FindParams::newInstance();
		
		if(isset($params['parent_id'])){ // content_18   // agenda_67
			$findParams->criteria (GO_Base_Db_FindCriteria::newInstance()->addCondition('parent_id', $params['parent_id']));
		}
		
		$sites = GO_Site_Model_Site::model()->find($findParams);
		
		foreach($sites as $site){
			
			$children = array();

			
			
			
			// Site node
			$siteNode = array(
				'id' => 'site_' . $site->id,
				'site_id'=>$site->id, 
				'iconCls' => 'go-model-icon-GO_Site_Model_Site', 
				'text' => $site->name, 
				'expanded' => true,
				'children' => $children
			);

			$response[] = $siteNode;
		}
		
		return $response;
	}
}