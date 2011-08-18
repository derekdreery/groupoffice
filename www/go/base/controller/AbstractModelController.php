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
							($link_props[1]), ($link_props[0]), $model->pk, $model->linkType());
		}

		$this->afterSubmit($response, $model);

		return $response;
	}

	/**
	 * Useful to override
	 * 
	 * @param array $response The response array
	 * @param mixed $model
	 */
	protected function beforeSubmit(&$response, &$model) {
		
	}

	/**
	 * Useful to override
	 *
	 * @param array $response The response array
	 * @param mixed $model
	 */
	protected function afterSubmit(&$response, &$model) {
		
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

		$response = $this->afterLoad($response, $model);

		return $response;
	}

	protected function afterLoad($response, $model) {
		return $response;
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
	protected $remoteComboFields = array(
					//'category_id'=>array('category','name')
	);

	private function _loadComboTexts($response, $model) {

		$response['remoteComboTexts'] = array();

		foreach ($this->remoteComboFields as $property => $map) {
			$response['remoteComboTexts'][$property] = $model->{$map[0]}->{$map[1]};
		}

		return $response;
	}

	/**
	 * Override this function to supply additional parameters to the 
	 * GO_Base_Db_ActiveRecord->find() function
	 * 
	 * @return array parameters for the GO_Base_Db_ActiveRecord->find() function 
	 */
	protected function getGridParams() {
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

		$gridParams = array_merge(GO_Base_Provider_Grid::getDefaultParams(),$this->getGridParams());

		$grid->setStatement(call_user_func(array($modelName,'model'))->find($gridParams));
		$this->prepareGrid($grid);
    return $grid->getData();
  }	

	/**
	 * The default action for displaying a model in a DisplayPanel.
	 */
	public function actionDisplay($params) {
		$modelName = $this->model;
		$model = call_user_func(array($modelName,'model'))->findByPk($params['id']);
		
		//necessary for old library functions
		require_once(GO::config()->root_path.'Group-Office.php');

		$response['data'] = $model->getAttributes();
		$response['data']['model']=$model->className();
		$response['success'] = true;
		$response['data']['permission_level']=$model->getPermissionLevel();
		$response['data']['write_permission']=$response['data']['permission_level']>GO_Base_Model_Acl::READ_PERMISSION;


	
		require_once(GO::config()->class_path . '/base/search.class.inc.php');
		$search = new search();

		if (/* !in_array('links', $hidden_sections) && */!isset($response['data']['links'])) {
			$links_json = $search->get_latest_links_json(GO::session()->values['user_id'], $response['data']['id'], $model->linkType());
			$response['data']['links'] = $links_json['results'];
		}

		if (/* isset(GO::modules()->modules['tasks']) && !in_array('tasks', $hidden_sections) && */!isset($response['data']['tasks'])) {
			require_once(GO::modules()->tasks->class_path . 'tasks.class.inc.php');
			$tasks = new tasks();

			$response['data']['tasks'] = $tasks->get_linked_tasks_json($response['data']['id'], $model->linkType());
		}

		if (isset(GO::modules()->calendar)/* && !in_array('events', $hidden_sections) */) {
			require_once(GO::modules()->calendar->class_path . 'calendar.class.inc.php');
			$cal = new calendar();

			$response['data']['events'] = $cal->get_linked_events_json($response['data']['id'], $model->linkType());
		}

		if (/* !in_array('files', $hidden_sections) && */!isset($response['data']['files'])) {
			if (isset(GO::modules()->files)) {
				require_once(GO::modules()->files->class_path . 'files.class.inc.php');
				$files = new files();

				$response['data']['files'] = $files->get_content_json($response['data']['files_folder_id']);
			} else {
				$response['data']['files'] = array();
			}
		}


		if (/* !in_array('comments', $hidden_sections) && */isset(GO::modules()->comments) && !isset($response['data']['comments'])) {
			require_once (GO::modules()->comments->class_path.'comments.class.inc.php');
			$comments = new comments();

			$response['data']['comments'] = $comments->get_comments_json($response['data']['id'], $model->linkType());
		}

		if (GO::modules()->customfields && $model->customFieldsModel() && !isset($response['data']['customfields'])) {
			require_once(GO::modules()->customfields->class_path.'customfields.class.inc.php');
			$cf = new customfields();		
			
			$response['data']['customfields'] = $cf->get_all_fields_with_values(GO::session()->values['user_id'], $model->linkType(), $response['data']['id']);
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

