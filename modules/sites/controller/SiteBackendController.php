<?php

class GO_Sites_Controller_SiteBackend extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Sites_Model_Site';
	
	
	protected function actionRedirectToFront($params){
		
		$site = GO_Sites_Model_Site::model()->findByPk($params['id']);
		
		header("Location: ".$site->getBaseUrl());
		exit();
	}
	
	protected function actionSiteTree($params) {

		$response=array();
		
		$findParams = GO_Base_Db_FindParams::newInstance();
		//->getCriteria()->addCondition('user_id',GO::user()->id);


		$sites = GO_Sites_Model_Site::model()->find($findParams);
		while ($site = $sites->fetch()) {

			$children = array(array(
					'id' => 'content_' . $site->id,  
					'site_id'=>$site->id, 
					'iconCls' => 'go-model-icon-GO_Sites_Model_Content', 
					'text' => 'Content', 
					'expanded' => true, 
					'children' => array()
			)
//					,array(
//					'id' => 'menus_' . $site->id,  
//					'site_id'=>$site->id, 
//					'iconCls' => 'go-model-icon-GO_Sites_Model_Menu', 
//					'text' => 'Menus', 
//					'expanded' => true, 
//					'children' => array()
//			)
					);

			$siteNode = array(
					'id' => 'site_' . $site->id,  
					'site_id'=>$site->id, 
					'iconCls' => 'go-model-icon-GO_Sites_Model_Site', 
					'text' => $site->name, 
					'expanded' => true, 
					'children' => $children);

			$response[] = $siteNode;
		}


		return $response;
	}

//	private function _buildPagesTree($site_id, $parent_id) {
//		$response = array();
//
//		$attr = array('parent_id' => $parent_id);
//		
//		if($site_id>0)
//			$attr['site_id']=$site_id;
//		
//		$stmt = GO_Sites_Model_Content::model()->findByAttributes($attr);
//
//		while ($page = $stmt->fetch()) {
//			// Check the leaf parameter (Needed to show no [+] before th node if this page has no children
//			
//			$pageNode = array('id'=>$page->id, 'iconCls' => 'go-model-icon-GO_Sites_Model_Content', 'text' => $page->name, 'expanded' => false, 'leaf'=>true);
//
//			$response[] = $pageNode;
//		}
//
//		return $response;
//	}
//	
	protected function afterLoad(&$response, &$model, &$params)
	{
		if(GO::modules()->isInstalled('webshop'))
		{
			$webshop = GO_Webshop_Model_Webshop::model()->findSingleByAttribute('site_id', $model->id);
			if($webshop != null)
				$response['data']['webshop'] = $webshop->getAttributes();
		}
		
		return parent::afterLoad($response, $model, $params);
	}

}