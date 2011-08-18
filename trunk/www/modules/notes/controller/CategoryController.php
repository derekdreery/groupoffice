<?php
class GO_Notes_Controller_Category extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Notes_Model_Category';
	
	public $selectedCategories;
	
	protected function getGridParams(){
		
		$this->selectedCategories = GO::config()->get_setting('notes_categories_filter', GO::session()->values['user_id']);
		$this->selectedCategories  = $this->selectedCategories ? explode(',',$this->selectedCategories) : array();

		return array();
	}
	
	protected function prepareGrid($grid){
    $grid->formatColumn('user_name','$model->user ? $model->user->name : 0');
		$grid->formatColumn('checked','in_array($model->id, $controller->selectedCategories)', array('controller'=>$this));
    return $grid;
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

