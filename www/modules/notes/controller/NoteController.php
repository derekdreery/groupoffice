<?php
class GO_Notes_Controller_Note extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Notes_Model_Note';
	
	protected function getStoreMultiSelectProperties(){
		return array(
				'requestParam'=>'no-multiselect',
				'permissionsModel'=>'GO_Notes_Model_Category',
				'titleAttribute'=>'name'
				);
	}	
	
	protected function getStoreParams($params){
		
		$findParams = GO_Base_Db_FindParams::newInstance()						
						->joinCustomFields()
						->debugSql();
		
		if(count($this->multiselectIds)){
			$findParams->ignoreAcl();
			$findParams->criteria(GO_Base_Db_FindCriteria::newInstance()->addInCondition('category_id', $this->multiselectIds));
		}
						
		return $findParams;
	}

	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name','$model->user->name');
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

