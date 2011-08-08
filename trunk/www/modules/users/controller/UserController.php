<?php
class GO_Users_Controller_User extends GO_Base_Controller_AbstractModelController{
	protected $model = 'GO_Base_Model_User';
	
	protected function prepareGrid($grid){
    $grid->formatColumn('name','$model->name');
    return $grid;
  }
}