<?php
class GO_Notes_Controller_Note extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Notes_Model_Note';
	
	public function actionTest(){
		return GO::user()->username;
	}
	
	
	
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
	
	protected function remoteComboFields(){
		return array('category_id'=>'$model->category->name');
	}
	
	

}

