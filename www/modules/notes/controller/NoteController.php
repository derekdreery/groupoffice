<?php
class GO_Notes_Controller_Note extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Notes_Model_Note';
	
	
	protected function getGridParams(){		
		
		if(isset($_POST['categories']))
		{
			$categories = json_decode($_POST['categories'], true);
			GO::config()->save_setting('notes_categories_filter',implode(',', $categories), GO::user()->id);
		}else
		{
			$categories = GO::config()->get_setting('notes_categories_filter', GO::user()->id);
			$categories = $categories ? explode(',',$categories) : array();
		}
		
		//todo category acl's should be checked for read permission here.
   

		return array(
				'ignoreAcl'=>true,
				'joinCustomFields'=>true,
				'by'=>array(array('category_id', $categories, 'IN'))
		);
	}
	
	protected function beforeActionGrid(){
		if(isset($_POST['categories']))
		{
			$categories = json_decode($_POST['categories'], true);
			GO::config()->save_setting('notes_categories_filter',implode(',', $categories), GO::user()->id);
		}else
		{
			$categories = $GO_CONFIG->get_setting('notes_categories_filter', $GO_SECURITY->user_id);
			$categories = ($categories) ? explode(',',$categories) : array();
		}
	}
  
  protected function prepareGrid($grid){
    $grid->formatColumn('user_name','$model->user->name');
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
	
	protected $remoteComboFields=array(
			'category_id'=>array('category','name')
	);	
	

}

