<?php
class GO_Notes_Controller_Note extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Notes_Model_Note';
	
	protected function getGridMultiSelectProperties(){
		return array(
				'requestParam'=>'notes_categories_filter',
				'permissionsModel'=>'GO_Notes_Model_Category',
				'titleAttribute'=>'name'
				);
	}	
	
	protected function getGridParams($params){
		return array(
				'ignoreAcl'=>true,
				'joinCustomFields'=>true,
				'by'=>array(array('category_id', $this->multiselectIds, 'IN'))
		);
	}
  
  protected function prepareGrid($grid){		
    $grid->formatColumn('user_name','$model->user->name');
    return $grid;
  }
	
	protected function remoteComboFields(){
		return array('category_id'=>'$model->category->name');
	}
}

