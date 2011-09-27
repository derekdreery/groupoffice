<?php
class GO_Notes_Controller_Category extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Notes_Model_Category';
	
	
	protected function getGridMultiSelectProperties(){
		return array('requestParam'=>'notes_categories_filter');
	}
	
	protected function getGridMultiSelectDefault(){
		$category = GO_Notes_NotesModule::getDefaultNoteCategory(GO::user()->id);
		return $category->id;
	}	
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name','$model->user ? $model->user->name : 0');
		$columnModel->formatColumn('checked','in_array($model->id, $controller->multiselectIds)', array('controller'=>$this));
		return parent::formatColumns($columnModel);
	}

	
	/**
	 * List all fields that require a remote text to load for a remote combobox.
	 * eg. with a model you want to provide the category name so that that the
	 * category combo store does not need to be loaded to show it.
	 * 
	 * You would list that like this:
	 * 
	 * 'category_id'=>array('category','name')
	 * 
	 * The category name would be looked up in the model model ->category->name.
	 * A relation for this must be defined. See ActiveRecord->relations.
	 * 
	 * 
	 * @var array remote combo mappings 
	 */
	
	protected function remoteComboFields(){
		return array('user_id'=>'$model->user->name');
	}
}

