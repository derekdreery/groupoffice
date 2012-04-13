<?php
class GO_Bookmarks_Controller_Category extends GO_Base_Controller_AbstractModelController{

	protected $model ='GO_Bookmarks_Model_Category';
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name', '$model->user->name');
		return parent::formatColumns($columnModel);
	}
	protected function getStoreParams($params) {
		return array(
				'order' => 'name',
				'orderDirection' => 'ASC'
		);
	}

}