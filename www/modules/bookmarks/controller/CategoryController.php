<?php
class GO_Bookmarks_Controller_Category extends \GO\Base\Controller\AbstractModelController{

	protected $model ='GO_Bookmarks_Model_Category';
	
	protected function formatColumns(\GO\Base\Data\ColumnModel $columnModel) {
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