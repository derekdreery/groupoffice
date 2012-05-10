<?php
class GO_Notes_Controller_Note extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Notes_Model_Note';
	
	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store, GO_Base_Db_FindParams $storeParams) {
		
		$multiSel = new GO_Base_Component_MultiSelectGrid(
						'no-multiselect', 
						"GO_Notes_Model_Category",$store, $params);		
		$multiSel->addSelectedToFindCriteria($storeParams->getCriteria(), 'category_id');
		$multiSel->setButtonParams($response);
		$multiSel->setStoreTitle();
		
		return parent::beforeStoreStatement($response, $params, $store, $storeParams);
	}

	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name','$model->user->name',array(),'user_id');
		return parent::formatColumns($columnModel);
	}

	protected function remoteComboFields(){
		return array('category_id'=>'$model->category->name');
	}
	
	
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {		
		 if(GO::modules()->files){
			 $f = new GO_Files_Controller_Folder();
			 $f->processAttachments($response, $model, $params);
		 }		
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
}

