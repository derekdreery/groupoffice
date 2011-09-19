<?php
class GO_Tasks_Controller_Category extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Tasks_Model_Category';

	
	protected function beforeSubmit(&$response, &$model, &$params) {
		// Checkbox "Use Global" is checked
		if(isset($params['global']))
			$model->user_id = 0;
		else
			$model->user_id = GO::user ()->id;
		
		return parent::beforeSubmit($response, $model, $params);
	}
	
	protected function prepareGrid($grid){
    $grid->formatColumn('user_name','$model->user ? $model->user->name : GO::t("globalCategory","tasks")');
    return $grid;
  }
	
	
}

