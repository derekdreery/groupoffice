<?php
class GO_Notes_Controller_Note extends GO_Base_Controller_FormController{
	
	protected $model = 'GO_Notes_Model_Note';
	
	public function actionTest(){
		
				
		
	}
	
	/**
	 * List all fields that require a remote text to load for a remote combobox.
	 * eg. with a note you want to provide the category name so that that the
	 * category combo store does not need to be loaded to show it.
	 * 
	 * You would list that like this:
	 * 
	 * 'category_id'=>array('category','name')
	 * 
	 * The category name would be looked up in the note model ->category->name.
	 * A relation for this must be defined. See ActiveRecord->relations.
	 * 
	 * 
	 * @var array remote combo mappings 
	 */
	
	protected $remoteComboFields=array(
			'category_id'=>array('category','name')
	);	
	

}

