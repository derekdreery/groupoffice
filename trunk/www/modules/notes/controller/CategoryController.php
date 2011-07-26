<?php
class GO_Notes_Controller_Category extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Notes_Model_Category';
	
	private $selectedCategories;
	
	protected function getGridParams(){
		
		$this->selectedCategories = $GLOBALS['GO_CONFIG']->get_setting('notes_categories_filter', $GLOBALS['GO_SECURITY']->user_id);
		$this->selectedCategories  = $this->selectedCategories ? explode(',',$this->selectedCategories) : array();
		
		return array();
	}
	
	protected function formatModelForGrid($record, $model) {
		
		$record['checked'] = in_array($record['id'], $this->selectedCategories);
		$record['user_name']=$model->user->name;
		return $record;
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
	
	protected $remoteComboFields=array(
			'user_id'=>array('user','name')
	);
}

