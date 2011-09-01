<?php
class GO_Tasks_Controller_Tasklist extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Tasks_Model_Tasklist';
	
	protected function getGridMultiSelectProperties(){
		return array(
				'requestParam'=>'notes_tasklist_filter',
				'permissionsModel'=>'GO_Tasks_Model_Tasklist',
				'titleAttribute'=>'name'
				);
	}	
	
//	protected function getGridParams($params){
//		return array(
//				'ignoreAcl'=>true,
//				'joinCustomFields'=>true,
//				'by'=>array(array('category_id', $this->multiselectIds, 'IN'))
//		);
//	}
  
//  protected function prepareGrid($grid){		
//    $grid->formatColumn('user_name','$model->user->name');
//    return $grid;
//  }
//	
//	protected function remoteComboFields(){
//		return array('category_id'=>'$model->category->name');
//	}

}