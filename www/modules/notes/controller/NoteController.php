<?php
class GO_Notes_Controller_Note extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Notes_Model_Note';
	
	protected function getStoreMultiSelectProperties(){
		return array(
				'requestParam'=>'notes_categories_filter',
				'permissionsModel'=>'GO_Notes_Model_Category',
				'titleAttribute'=>'name'
				);
	}	
	
	protected function getStoreParams($params){
		
		return GO_Base_Db_FindParams::newInstance()
						->ignoreAcl()
						->joinCustomFields()
						->criteria(GO_Base_Db_FindCriteria::newInstance()->addInCondition('category_id', $this->multiselectIds));
	}

	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name','$model->user->name');
		return parent::formatColumns($columnModel);
	}

	protected function remoteComboFields(){
		return array('category_id'=>'$model->category->name');
	}
}

