<?php
class GO_Notes_Controller_Category extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Notes_Model_Category';
	
	private $selectedCategories;
	
	protected function getGridParams(){
		
		$this->selectedCategories = GO::config()->get_setting('notes_categories_filter', GO::security()->user_id);
		$this->selectedCategories  = $this->selectedCategories ? explode(',',$this->selectedCategories) : array();

		if(!count($this->selectedCategories))
		{			
//			$notes->get_category();
//			$default_category_id = $notes->f('id');
//
//			$categories[] = $default_category_id;
//			GO::config()->save_setting('notes_categories_filter',$default_category_id, GO::security()->user_id);
		}
		
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

