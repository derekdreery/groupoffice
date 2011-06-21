<?php
class GO_Notes_Controller_Note extends GO_Base_Controller_FormController{
	
	protected $model = 'GO_Notes_Model_Note';
	
	public function actionGrid(){
		$note = new GO_Notes_Model_Note();
		
		
		if(isset($_POST['categories']))
		{
			$categories = json_decode($_POST['categories'], true);
			GO::config()->save_setting('notes_categories_filter',implode(',', $categories), GO::security()->user_id);
		}else
		{
			$categories = GO::config()->get_setting('notes_categories_filter', GO::security()->user_id);
			$categories = $categories ? explode(',',$categories) : array();
		}
		
		
		$stmt = $note->find(array(
			'by'=>array(array('category_id', $categories, 'IN')),
			'limit'=>isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 0,
			'start'=>isset($_REQUEST['start']) ? $_REQUEST['start'] : 0,
		), $response['total']);
		
		$response['results']=array();
		
		while($note = $stmt->fetch()){
			$response['results'][]=$note->getAttributes();
		}		
		
		$this->output($response);
		
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

