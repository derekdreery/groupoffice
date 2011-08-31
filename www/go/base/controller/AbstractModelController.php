<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * Extend this class for your models. It implements default actions for
 * 1. The grid
 * 2. The edit dialog
 * 3. The display panel
 */
class GO_Base_Controller_AbstractModelController extends GO_Base_Controller_AbstractController {

	/**
	 *
	 * @var GO_Base_Db_ActiveRecord 
	 */
	protected $model;
	
	
	/**
	 * It's often convenient to select multiple addressbooks, calendars etc. for
	 * display in a grid. By overriding multiSelectProperties and multiSelectDefault
	 * this array will be filled with ids that are send by the request parameter.
	 * They will be saved to the database too.
	 * 
	 * @var array Ids that are selected 
	 */
	public $multiselectIds=array();

	/**
	 * The default action when the form in an edit dialog is submitted.
	 */
	public function actionSubmit($params) {

		$modelName = $this->model;
		if (!empty($params['id']))
			$model = call_user_func(array($modelName,'model'))->findByPk($params['id']);
		else
			$model = new $modelName;

		$this->beforeSubmit($response, $model, $params);
		
		$model->setAttributes($params);

		$response['success'] = $model->save();

		$response['id'] = $model->pk;

		//If the model has it's own ACL id then we return the newly created ACL id.
		//The model automatically creates it.
		if ($model->aclField && !$model->aclFieldJoin) {
			$response[$model->aclField] = $model->{$model->aclField};
		}


		if (!empty($_POST['link'])) {
			require_once(GO::config()->class_path . 'base/links.class.inc.php');
			$GO_LINKS = new GO_LINKS();

			//todo link type should be handled better.
			//Nicer would be $model->linkTo($othermodel);
			$link_props = explode(':', $_POST['link']);
			$GO_LINKS->add_link(
							($link_props[1]), ($link_props[0]), $model->pk, $model->linkModelId());
		}

		$this->afterSubmit($response, $model, $params);

		return $response;
	}

	/**
	 * Useful to override
	 * 
	 * @param array $response The response array
	 * @param mixed $model
	 */
	protected function beforeSubmit(&$response, &$model, $params) {
		
	}

	/**
	 * Useful to override
	 *
	 * @param array $response The response array
	 * @param mixed $model
	 */
	protected function afterSubmit(&$response, &$model, $params) {
		
	}

	/**
	 * Action to load a single record.
	 */
	public function actionLoad($params) {
		$modelName = $this->model;
		//$modelName::model() does not work on php 5.2!
		$model = call_user_func(array($modelName,'model'))->findByPk($params['id']);

		$response['data'] = $model->getAttributes();
		
		//todo custom fields should be in a subarray.
		if(GO::user()->getModulePermissionLevel('customfields') && $model->customfieldsRecord)
			$response['data'] = array_merge($response['data'], $model->customfieldsRecord->getAttributes());	
						
		$response['success'] = true;

		$response = $this->_loadComboTexts($response, $model);

		$response = $this->afterLoad($response, $model, $params);

		return $response;
	}

	protected function afterLoad($response, $model, $params) {
		return $response;
	}

	/**
	 * List all fields that require a remote text to load for a remote combobox.
	 * eg. with a model you want to provide the category name so that that the
	 * category combo store does not need to be loaded to show it.
	 *
	 * You would list that like this:
	 *
	 * 'category_id'=>'$model->category->name'
	 *
	 * The category name would be looked up in the model model ->category->name.
	 * A relation for this must be defined. See ActiveRecord->relations.
	 *
	 *
	 * @return array remote combo mappings
	 */
	protected function remoteComboFields(){
		return array();
	}

	private function _loadComboTexts($response, $model) {

		$response['remoteComboTexts'] = array();

		foreach ($this->remoteComboFields() as $property => $map) {
			
			$eval = '$value = '.$map.';';
			//GO::debug($eval);
			eval($eval);			
			$response['remoteComboTexts'][$property] = $value;
		}

		return $response;
	}
	
	/**
	 *
	 * It's often convenient to select multiple addressbooks, calendars etc. for
	 * display in a grid. By overriding multiSelectProperties and multiSelectDefault
	 * this array will be filled with ids that are send by the request parameter.
	 * They will be saved to the database too.
	 * 
	 *
	 * @return array The name of the request parameter sent by the view. 
	 * 
	 * array(
				'requestParam'=>'notes_categories_filter', //The name of the request parameter sent by the view. 
				'permissionsModel'=>'GO_Notes_Model_Category', //The model to check permissions. 
				'titleAttribute'=>'name' //Only set this if your grid needs the names of the permissionsmodel in the title.
				);
	 */
	protected function getGridmultiSelectProperties(){
		return false;
	}
	
	/**
	 * If nothing is selected. Return a default id if necessary.
	 */
	protected function getGridMultiSelectDefault(){
		return false;
	}

	/**
	 * Override this function to supply additional parameters to the 
	 * GO_Base_Db_ActiveRecord->find() function
	 * 
	 * @var array() $params The request parameters of actionGrid
	 * 
	 * @return array parameters for the GO_Base_Db_ActiveRecord->find() function 
	 */
	protected function getGridParams($params) {
		return array();
	}

	/**
	 * Override this function to format the grid record data.
	 * 
	 * @param array $record The grid record returned from the GO_Base_Db_ActiveRecord->getAttributes
	 * @param GO_Base_Db_ActiveRecord $model
	 * @return array The grid record data
	 */
	protected function getGridColumnModel() {
		return array();
	}
  
  /**
   * Override this function to format columns if necessary.
   * You can also use formatColumn to add extra columns
   * 
   * @param GO_Base_Provider_Grid $grid
   * @return GO_Base_Provider_Grid 
   */
  protected function prepareGrid($grid){
    return $grid;
  }
  
  /**
   * The default grid action for the current model.
   */
  public function actionGrid($params){	
    $modelName = $this->model;  
    
    $grid = new GO_Base_Provider_Grid($this->getGridColumnModel());		    
		$grid->processDeleteActions($modelName);
		
		if($multiSelectProperties =$this->getGridmultiSelectProperties()){
			
			if(isset($params[$multiSelectProperties['requestParam']])){
				$this->multiselectIds=json_decode($params[$multiSelectProperties['requestParam']], true);
				GO::config()->save_setting($multiSelectProperties['requestParam'], implode(',',$this->multiselectIds), GO::session()->values['user_id']);
			}else
			{
				$this->multiselectIds = GO::config()->get_setting($multiSelectProperties['requestParam'], GO::session()->values['user_id']);
				$this->multiselectIds  = $this->multiselectIds ? explode(',',$this->multiselectIds) : array();
			}
		
			
			if(empty($this->multiselectIds))
			{
				$default = $this->getGridMultiSelectDefault();
				if($default){
					$this->multiselectIds = array($category->id);
					GO::config()->save_setting($multiSelectProperties['requestParam'],implode(',', $this->multiselectIds), GO::user()->id);
				}
			}
			
			//Do a check if the permission model needs to be checked. If we don't ignore the acl and the model is the same as the model of this controller
			//it's not needed.
			if(isset($multiSelectProperties['permissionsModel']) && $multiSelectProperties['permissionsModel']!=$this->model && empty($gridParams['ignoreAcl'])){
				$titleArray = array();
				foreach($this->multiselectIds as $id){
					$model = call_user_func(array($multiSelectProperties['permissionsModel'],'model'))->findByPk($id);
					if($model && !empty($multiSelectProperties['titleAttribute']))
						$titleArray[]=$model->{$multiSelectProperties['titleAttribute']};
				}		
				if(count($titleArray))
					$grid->setTitle(implode(', ',$titleArray));
			}
		}

		$this->prepareGrid($grid);
		$gridParams = array_merge($grid->getDefaultParams(),$this->getGridParams($params));
		
			
		$grid->setStatement(call_user_func(array($modelName,'model'))->find($gridParams));
		
    $response = $this->afterActionGrid($grid->getData(), $params, $grid, $gridParams);		
		
		//this parameter is set when this request is the first request of the module.
		//We pass the response on to the output.
		if(isset($params['firstRun']) && is_array($params['firstRun'])){
			$response=array_merge($response, $params['firstRun']);
		}
		
		return $response;
  }	
	
	protected function afterActionGrid($response, $params, $grid, $gridParams){
		return $response;
	}

	/**
	 * The default action for displaying a model in a DisplayPanel.
	 */
	public function actionDisplay($params) {
		$modelName = $this->model;
		$model = call_user_func(array($modelName,'model'))->findByPk($params['id']);
		
		//todo build in new style. Now it's necessary for old library functions
		require_once(GO::config()->root_path.'Group-Office.php');

		$response['data'] = $model->getAttributes('html');
		$response['data']['model']=$model->className();
		$response['success'] = true;
		$response['data']['permission_level']=$model->getPermissionLevel();
		$response['data']['write_permission']=$response['data']['permission_level']>GO_Base_Model_Acl::READ_PERMISSION;

		
		if($model->customfieldsRecord){
			$customAttributes = $model->customfieldsRecord->getAttributes('html');
			
			//Get all field models and build an array of categories with their
			//fields for display.
			$stmt = GO_Customfields_Model_Field::model()->find(array(
					'where'=>'category.extends_model=:extends_model',
					'bindParams'=>array('extends_model'=>$model->customfieldsRecord->extendsModel()),
					'order'=>array('category.sort_index','t.sort_index'),
					'orderDirection'=>array('ASC','ASC')
			));			
			
			while($field = $stmt->fetch()){
				if(!isset($categories[$field->category_id])){
					$categories[$field->category->id]['id']=$field->category->id;
					$categories[$field->category->id]['name']=$field->category->name;
					$categories[$field->category->id]['fields']=array();
				}
				$categories[$field->category->id]['fields'][]=array(
						'name'=>$field->name,
						'value'=>$customAttributes[$field->columnName()]
				);				
			}
			
			
			$response['data']['customfields']=array_values($categories);
			
		}else
		{
			$response['data']['customfields']=array();
		}

	
//		require_once(GO::config()->class_path . '/base/search.class.inc.php');
//		$search = new search();
//
//		if (/* !in_array('links', $hidden_sections) && */!isset($response['data']['links'])) {
//			$links_json = $search->get_latest_links_json(GO::session()->values['user_id'], $response['data']['id'], $model->linkModelId());
//			$response['data']['links'] = $links_json['results'];
//		}

//		if (/* isset(GO::modules()->modules['tasks']) && !in_array('tasks', $hidden_sections) && */!isset($response['data']['tasks'])) {
//			require_once($GLOBALS['GO_MODULES']->modules['tasks']['class_path'] . 'tasks.class.inc.php');
//			$tasks = new tasks();
//
//			$response['data']['tasks'] = $tasks->get_linked_tasks_json($response['data']['id'], $model->linkModelId());
//		}
//
//		if (isset(GO::modules()->calendar)/* && !in_array('events', $hidden_sections) */) {
//			require_once($GLOBALS['GO_MODULES']->modules['calendar']['class_path'] . 'calendar.class.inc.php');
//			$cal = new calendar();
//
//			$response['data']['events'] = $cal->get_linked_events_json($response['data']['id'], $model->linkModelId());
//		}

		if (/* !in_array('files', $hidden_sections) && */!isset($response['data']['files'])) {
			if (isset(GO::modules()->files)) {
				require_once($GLOBALS['GO_MODULES']->modules['files']['class_path']. 'files.class.inc.php');
				$files = new files();

				$response['data']['files'] = $files->get_content_json($response['data']['files_folder_id']);
			} else {
				$response['data']['files'] = array();
			}
		}


		if (/* !in_array('comments', $hidden_sections) && */isset(GO::modules()->comments) && !isset($response['data']['comments'])) {
			require_once ($GLOBALS['GO_MODULES']->modules['comments']['class_path'].'comments.class.inc.php');
			$comments = new comments();

			$response['data']['comments'] = $comments->get_comments_json($response['data']['id'], $model->linkModelId());
		}

		$response = $this->afterDisplay($response, $model);

		return $response;
	}

	protected function afterDisplay($response, $model) {
		return $response;
	}

	/**
	 * Deletes a specific record.
	 * @param type $params The POST parameters 
	 */
	protected function actionDelete($params) {
	    $modelName = $this->model;
	    $model = $modelName::model()->findByPk($params['id']);
	    $response['success'] = $model->delete();
	    return $response;
	}
}

