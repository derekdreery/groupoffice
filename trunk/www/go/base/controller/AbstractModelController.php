<?php
/*
 * Copyright Intermesh BV
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * Extend this class for your models. It implements default actions for
 * 1. The grid
 * 2. The edit dialog
 * 3. The display panel
 * 
 * @package GO.base.controller
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl> 
 *
 */
class GO_Base_Controller_AbstractModelController extends GO_Base_Controller_AbstractController {

	/**
	 *
	 * @var GO_Base_Db_ActiveRecord 
	 */
	protected $model;
	
	
	/**
	 * An array of Id's
	 * 
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
			$model = GO::getModel($modelName)->findByPk($params['id']);
		else
			$model = new $modelName;

		$this->beforeSubmit($response, $model, $params);
		
		$model->setAttributes($params);
		
		$modifiedAttributes = $model->getModifiedAttributes();

		$response['success'] = $model->save();

		$response['id'] = $model->pk;

		//If the model has it's own ACL id then we return the newly created ACL id.
		//The model automatically creates it.
		if ($model->aclField() && !$model->aclFieldJoin) {
			$response[$model->aclField()] = $model->{$model->aclField()};
		}

		
		if (!empty($_POST['link'])) {
			
			//a link is sent like  GO_Notes_Model_Note:1
			//where 1 is the id of the model
			
			$linkProps = explode(':', $_POST['link']);			
			$linkModel = GO::getModel($linkProps[0])->findByPk($linkProps[1]);
			$model->link($linkModel);			
		}

		$this->afterSubmit($response, $model, $params, $modifiedAttributes);

		return $response;
	}

	/**
	 * Useful to override
	 * 
	 * @param array $response The response array
	 * @param mixed $model
	 */
	protected function beforeSubmit(&$response, &$model, &$params) {
		
	}

	/**
	 * Useful to override
	 *
	 * @param array $response The response array
	 * @param mixed $model
	 */
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		
	}

	/**
	 * Action to load a single record.
	 */
	public function actionLoad($params) {
		$modelName = $this->model;
		//$modelName::model() does not work on php 5.2!
		$model = GO::getModel($modelName)->findByPk($params['id']);
		
		$response = array();
		
		$response = $this->beforeLoad($response, $model, $params);

		$response['data'] = $model->getAttributes();
		
		$response['data']['permission_level']=$model->getPermissionLevel();
		$response['data']['write_permission']=$response['data']['permission_level']>GO_Base_Model_Acl::READ_PERMISSION;

		
		//todo custom fields should be in a subarray.
		if(GO::user()->getModulePermissionLevel('customfields') && $model->customfieldsRecord)
			$response['data'] = array_merge($response['data'], $model->customfieldsRecord->getAttributes());	
						
		$response['success'] = true;

		$response = $this->_loadComboTexts($response, $model);

		$response = $this->afterLoad($response, $model, $params);

		return $response;
	}

	protected function beforeLoad(&$response, &$model, &$params) {
		return $response;
	}
	
	
	protected function afterLoad(&$response, &$model, &$params) {
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

		$oldLevel = error_reporting(E_ERROR);	//suppress errors in the eval'd code
			
		foreach ($this->remoteComboFields() as $property => $map) {
			$value='';
			$eval = '$value = '.$map.';';
			eval($eval);
			
			$response['remoteComboTexts'][$property] = $value;
		}
		
		error_reporting($oldLevel);

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
	protected function getStoremultiSelectProperties(){
		return false;
	}
	
	/**
	 * If nothing is selected. Return a default id if necessary.
	 */
	protected function getStoreMultiSelectDefault(){
		return false;
	}

	/**
	 * Override this function to supply additional parameters to the 
	 * GO_Base_Db_ActiveRecord->find() function
	 * 
	 * @var array() $params The request parameters of actionStore
	 * 
	 * @return array parameters for the GO_Base_Db_ActiveRecord->find() function 
	 */
	protected function getStoreParams($params) {
		return array();
	}
	
	/**
	 * Override to pass an array of columns to exclude in the store.
	 * @return array 
	 */
	protected function getStoreExcludeColumns(){
		return array();
	}

	/**
	 * Override this function to format the grid record data.
	 * @TODO: THIS DESCRIPTION IS NOT OK
	 * @param array $record The grid record returned from the GO_Base_Db_ActiveRecord->getAttributes
	 * @param GO_Base_Db_ActiveRecord $model
	 * @return array The grid record data
	 */
	protected function getStoreColumnModel() {
		$cm =  new GO_Base_Data_ColumnModel();
		$cm->setColumnsFromModel(GO::getModel($this->model), $this->getStoreExcludeColumns());	
		return $cm;
	}
	
	/**
	 * Override this function to format the grid record data.
	 * @TODO: THIS DESCRIPTION IS NOT OK
	 * @param array $record The grid record returned from the GO_Base_Db_ActiveRecord->getAttributes
	 * @param GO_Base_Db_ActiveRecord $model
	 * @return array The grid record data
	 */
	protected function prepareStore($store) {
		
		return $store;
	}
	
  
  /**
   * Override this function to format columns if necessary.
   * You can also use formatColumn to add extra columns
   * 
   * @param GO_Base_Data_ColumnModel $columnModel
   * @return GO_Base_Data_ColumnModel 
   */
  protected function formatColumns(GO_Base_Data_ColumnModel $columnModel){
    return $columnModel;
  }
  
  /**
   * The default grid action for the current model.
   */
  public function actionStore($params){	
    $modelName = $this->model;  
		
		
    
    $store = new GO_Base_Data_Store($this->getStoreColumnModel());		    
		$store->processDeleteActions($params, $modelName);
		
		$response=array();
		
		$response = $this->beforeStore($response, $params, $store);
		
		
		if($multiSelectProperties =$this->getStoremultiSelectProperties()){
			
			
			
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
				$default = $this->getStoreMultiSelectDefault();
				if($default){
					$this->multiselectIds = array($category->id);
					GO::config()->save_setting($multiSelectProperties['requestParam'],implode(',', $this->multiselectIds), GO::user()->id);
				}
			}
			
			//Do a check if the permission model needs to be checked. If we don't ignore the acl and the model is the same as the model of this controller
			//it's not needed.
			if(isset($multiSelectProperties['permissionsModel']) && $multiSelectProperties['permissionsModel']!=$this->model && empty($storeParams['ignoreAcl'])){
				
		
				
				$titleArray = array();
				foreach($this->multiselectIds as $id){
					
					$model = GO::getModel($multiSelectProperties['permissionsModel'])->findByPk($id);
					
					if(!isset($response['buttonParams']) && $model->getPermissionLevel()>GO_Base_Model_Acl::READ_PERMISSION){

						//instruct the view for the add action.
						$response['buttonParams']=array('id'=>$model->id,'name'=>$model->name, 'permissionLevel'=>$model->getPermissionLevel());
					}
					
					if($model && !empty($multiSelectProperties['titleAttribute']))
						$titleArray[]=$model->{$multiSelectProperties['titleAttribute']};
				}		
				if(count($titleArray))
					$store->setTitle(implode(', ',$titleArray));
			}
		}


		$columnModel = $store->getColumnModel();
		$this->formatColumns($columnModel);
		
		$this->prepareStore($store);
		
		$storeParams = array_merge($store->getDefaultParams(),$this->getStoreParams($params));
		
			
		$store->setStatement(call_user_func(array($modelName,'model'))->find($storeParams));
		
		$response = array_merge($response, $store->getData());
		
		$response['success']=true;
		
    $response = $this->afterStore($response, $params, $store, $storeParams);		
		
		//this parameter is set when this request is the first request of the module.
		//We pass the response on to the output.
		if(isset($params['firstRun']) && is_array($params['firstRun'])){
			$response=array_merge($response, $params['firstRun']);
		}
		
		
		return $response;
  }	
	
	protected function afterStore(&$response, &$params, &$store, $storeParams){
		return $response;
	}
	
	protected function beforeStore(&$response, &$params, &$store){
		return $response;
	}

	/**
	 * The default action for displaying a model in a DisplayPanel.
	 */
	public function actionDisplay($params) {

		$response = array();
				
		$modelName = $this->model;
		$model = GO::getModel($modelName)->findByPk($params['id']);
		
		$response = $this->beforeDisplay($response, $model, $params);
		//todo build in new style. Now it's necessary for old library functions
		require_once(GO::config()->root_path.'Group-Office.php');

		$response['data'] = $model->getAttributes('html');
		$response['data']['model']=$model->className();
		$response['success'] = true;
		$response['data']['permission_level']=$model->getPermissionLevel();
		$response['data']['write_permission']=$response['data']['permission_level']>GO_Base_Model_Acl::READ_PERMISSION;

		$response['data']['customfields']=array();
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

			$categories=array();
			
			while($field = $stmt->fetch()){
				if(!isset($categories[$field->category_id])){
					$categories[$field->category->id]['id']=$field->category->id;
					$categories[$field->category->id]['name']=$field->category->name;
					$categories[$field->category->id]['fields']=array();
				}
				if(!empty($customAttributes[$field->columnName()])){
					$categories[$field->category->id]['fields'][]=array(
							'name'=>$field->name,
							'value'=>$customAttributes[$field->columnName()]
					);				
				}
			}
			
			
			foreach($categories as $category){
				if(count($category['fields']))
					$response['data']['customfields'][]=$category;
			}
			
		}

		$stmt = GO_Base_Model_SearchCacheRecord::model()->findLinks($model, array(
				'limit'=>15
		));
		
		$store = GO_Base_Data_Store::newInstance(GO_Base_Model_SearchCacheRecord::model());		
		$store->setStatement($stmt);
		
		$columnModel = $store->getColumnModel();		
		$columnModel->formatColumn('link_count','GO::getModel($model->model_name)->countLinks($model->model_id)');
		$columnModel->formatColumn('link_description','$model->link_description');
		
		$data = $store->getData();
		$response['data']['links']=$data['results'];
		

		if (GO::modules()->calendar){

			$startOfDay = GO_Base_Util_Date::clear_time(time());
			
			$stmt = GO_Calendar_Model_Event::model()->findLinks($model, array(
					//'limit'=>15
					'where'=>'start_time>=:start_time',
					'bindParams'=>array(':start_time'=>$startOfDay)
			));		

			$store = GO_Base_Data_Store::newInstance(GO_Calendar_Model_Event::model());
			$store->setStatement($stmt);
			
			$columnModel = $store->getColumnModel();			
			$columnModel->formatColumn('calendar_name','$model->calendar->name');
			$columnModel->formatColumn('link_count','$model->countLinks()');
			$columnModel->formatColumn('link_description','$model->link_description');
			
			$data = $store->getData();
			$response['data']['events']=$data['results'];
		}
		

	
//		require_once(GO::config()->class_path . '/base/search.class.inc.php');
//		$search = new search();
//
//		if (/* !in_array('links', $hidden_sections) && */!isset($response['data']['links'])) {
//			$links_json = $search->get_latest_links_json(GO::session()->values['user_id'], $response['data']['id'], $model->linkModelId());
//			$response['data']['links'] = $links_json['results'];
//		}
//
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
			if (isset(GO::modules()->files) && $model->hasFiles()) {
				require_once($GLOBALS['GO_MODULES']->modules['files']['class_path']. 'files.class.inc.php');
				$files = new files();

				$response['data']['files'] = $files->get_content_json($response['data']['files_folder_id']);
			} else {
				$response['data']['files'] = array();
			}
		}


		if (isset(GO::modules()->comments)){
			$stmt = GO_Comments_Model_Comment::model()->find(array(
				'where'=>'model_id=:model_id AND model_type_id=:model_type_id',
				'bindParams'=>array('model_id'=>$model->id,'model_type_id'=>$model->modelTypeId()),
				'limit'=>5
			));

			
			$store = GO_Base_Data_Store::newInstance(GO_Comments_Model_Comment::model());			
			$store->setStatement($stmt);
			
			$columnModel = $store->getColumnModel();			
			$columnModel->formatColumn('user_name','$model->user->name');
			
			$data = $store->getData();
			$response['data']['comments']=$data['results'];
		}		

		$response = $this->afterDisplay($response, $model, $params);

		return $response;
	}

	
	/**
	 * Useful to override
	 * 
	 * @param array $response The response array
	 * @param mixed $model
	 */
	protected function beforeDisplay(&$response, &$model, &$params) {
		return $response;
	}
	
	/**
	 * Useful to override
	 * 
	 * @param array $response The response array
	 * @param mixed $model
	 */
	protected function afterDisplay(&$response, &$model, &$params) {
		return $response;
	}

	/**
	 * Deletes a specific record.
	 * @param type $params The POST parameters 
	 */
	protected function actionDelete($params) {
	    $modelName = $this->model;
	    $model = GO::getModel($modelName)->findByPk($params['id']);
	    $response['success'] = $model->delete();
			return $response;
	}
	
	/**
	 * This function can export the current data to a given format.
	 * 
	 * The $params array has a couple of keys wich you maybe want to set:
	 * 
	 * * title	: The title of the file that will be created. (Without extention)
	 * * type		: Which class needs to be used to export. (Eg. GO_Base_Export_ExportCSV)
	 * * showHeader : Do you want to show the column headers in the file? (True or False)
	 * 
	 * @param Array $params 
	 */
	public function actionExport($params) {
		
		if(!empty($params['documentTitle']))
			$title = $params['documentTitle'];
		else
			$title = GO::session()->values[$params['name']]['name'];
		
		if(!empty($params['type']))
			$export = new $params['type']($title,false);
		else
			$export = new GO_Base_Export_ExportCSV($title,false); // The default Export is the CSV outputter.
		
		$filter = GO::session()->values[$params['name']]['findParams'];
		$model = GO::getModel(GO::session()->values[$params['name']]['model']);
		
		$columnModel = new GO_Base_Data_ColumnModel();
		$columnModel->setColumnsFromModel($model);
		$columnModel = $this->formatColumns($columnModel);
		
		
		$stmt = $model->find($filter);
		
		while($obj = $stmt->fetch()) {
			if(!empty($params['showHeader'])) {
				$attr = $obj->getAttributes('formatted');
				$header = array();				
				foreach($attr as $attribute=>$value)
					$header[] = $model->getAttributeLabel($attribute);
				$export->write($header);
				$params['showHeader']=false;
			}
			$export->write($obj->getAttributes());
		}
		
		$export->endFlush();
	}
}

