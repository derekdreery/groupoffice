<?php
class GO_Bookmarks_Controller_Category extends GO_Base_Controller_AbstractModelController{

	protected $model ='GO_Bookmarks_Model_Category';
	
	protected function actionTest($params)
	{
		echo 'test';
	}
	
	protected function getStoreParams($params) {
		return array(
				'order' => 'name',
				'orderDirection' => 'ASC'
		);
	}

}