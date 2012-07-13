<?php

class GO_Sites_Controller_SiteBackend extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Sites_Model_Site';
	
	protected function actionSiteTree($params) {

		$response=array();
		
		$findParams = GO_Base_Db_FindParams::newInstance();
		//->getCriteria()->addCondition('user_id',GO::user()->id);

		if ($params['node'] == 'root') {
			$sites = GO_Sites_Model_Site::model()->find($findParams);
			while ($site = $sites->fetch()) {

				$children = $this->_buildPagesTree($site->id, 0);

				$siteNode = array('id' => 'site_' . $site->id, 'iconCls' => 'go-model-icon-GO_Sites_Model_Site', 'text' => $site->name, 'expanded' => true, 'children' => $children);

				$response[] = $siteNode;
			}
		}else
		{
			$response = $this->_buildPagesTree(0, $params['node']);
		}

		return $response;
	}

	private function _buildPagesTree($site_id, $parent_id) {
		$response = array();

		$attr = array('parent_id' => $parent_id);
		
		if($site_id>0)
			$attr['site_id']=$site_id;
		
		$stmt = GO_Sites_Model_Content::model()->findByAttributes($attr);

		while ($page = $stmt->fetch()) {
			// Check the leaf parameter (Needed to show no [+] before th node if this page has no children
			
			$pageNode = array('id'=>$page->id, 'iconCls' => 'go-model-icon-GO_Sites_Model_Content', 'text' => $page->name, 'expanded' => false, 'leaf'=>true);

			$response[] = $pageNode;
		}

		return $response;
	}
	
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		
//		if(empty($params['id'])){
//			$controller = new GO_Sites_Controller_SiteModule();
//			$controller->run('createdefaultpages',array('site_id'=>$model->id),false);
//		}
		
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
}