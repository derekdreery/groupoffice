<?php

class GO_Email_Controller_Filter extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Email_Model_Filter';

	protected function beforeStore(&$response, &$params, &$store) {

		$store->setDefaultSortOrder('priority');

		return parent::beforeStore($response, $params, $store);
	}

	protected function getStoreParams($params) {
		
	
		$findParams = GO_Base_Db_FindParams::newInstance();
		$findParams->getCriteria()
						->addCondition("account_id", $params['account_id']);
	
		return $findParams;
	}
	protected function actionSaveSort($params){		
		$fields = json_decode($params['filters'], true);

		foreach ($fields as $id=>$sort) {
			$model = GO_Email_Model_Filter::model()->findByPk($id);
			$model->priority=$sort;
			$model->save();
		}		
		
		return array('success'=>true);
	}	
	

}
