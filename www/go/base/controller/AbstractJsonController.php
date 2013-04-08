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
 * This controller can be extended for most of the groupoffice controllers
 * It has support for rendering JSON data from a loaded model or render failure output
 * 
 * @package GO.base.controller
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Michael de Hart <mdhart@intermesh.nl>
 *
 */
abstract class GO_Base_Controller_AbstractJsonController extends GO_Base_Controller_AbstractController {

	/**
	 * Start rendering the json output for extJS
	 * @param array $data the data that need to be rendered as json
	 */
	public function renderJson($data) {
		$this->setHeaders();
		echo json_encode($data);
	}

	/**
	 * Render JSON response for forms
	 * @param GO_Base_Db_ActiveRecord $model the AWR to renerated the JSON form data for
	 * @param array $remoteComboField List all fields that require a remote text to load for a remote combobox.
	 * eg. with a model you want to provide the category name so that that the
	 * category combo store does not need to be loaded to show it.
	 * 
	 * You would list that like this:
	 * 
	 * 'category_id'=>array('category','name')
	 * 
	 * The category name would be looked up in the model model ->category->name.
	 * A relation for this must be defined. See ActiveRecord->relations.
	 * @see GO_Base_Controller_AbstractModelController::remoteComboFields()
	 * @param array $extraFields the extra fields that should be attached to the data array as key => value
	 * @param boolean $return defaults to false, if true the JSON data is returned as string
	 * @return string JSON render when $return=true
	 * @throws GO_Base_Exception_AccessDenied
	 */
	public function renderForm($model, $remoteComboFields = array(), $extraFields = array(), $return = false) {

		$response = array('data' => array(), 'success' => true);

		//TODO: check if this can be moved. This methode renders JSON and should not check permissions.
		if (!$model->checkPermissionLevel($model->isNew ? GO_Base_Model_Acl::CREATE_PERMISSION : GO_Base_Model_Acl::WRITE_PERMISSION))
			throw new GO_Base_Exception_AccessDenied();

		//Init data array
		$response['data'] = array_merge($extraFields, $model->getAttributes());
		$response['data']['permission_level'] = $model->getPermissionLevel();
		$response['data']['write_permission'] = true;

		//Add the customerfields to the data array
		if (GO::user()->getModulePermissionLevel('customfields') && $model->customfieldsRecord)
			$response['data'] = array_merge($response['data'], $model->customfieldsRecord->getAttributes());

		if (!empty($remoteComboFields))
			$response = $this->_loadComboTexts($model, $remoteComboFields, $response);

		if ($return)
			return $response;
		$this->renderJson($response);
	}

	/**
	 * Can be used in actionDisplay like actions
	 * @param GO_Base_Db_ActiveRecord $model the model to render display data for
	 * @param array $extraFields the extra fields that should be attached to the data array as key => value
	 * @param array $return if the response data gets returned else it will be echoed
	 * @return array response data if $return = true
	 */
	public function renderDisplay($model, $extraFields = array(), $return = false) {
		$response = array('data' => array(), 'success' => true);
		$response['data'] = array_merge_recursive($extraFields, $model->getAttributes('html'));
		//$response['data'] = $model->getAttributes('html');
		//$response['data']['model'] = $model->className();
		$response['data']['permission_level'] = $model->getPermissionLevel();
		$response['data']['write_permission'] = GO_Base_Model_Acl::hasPermission($response['data']['permission_level'], GO_Base_Model_Acl::WRITE_PERMISSION);


		$response['data']['customfields'] = array();

		if (!isset($response['data']['workflow']) && GO::modules()->workflow)
			$response = $this->_processWorkflowDisplay($model, $response);

		if ($model->customfieldsRecord)
			$response = $this->_processCustomFieldsDisplay($model, $response);

		if ($model->hasLinks()) {
			$response = $this->_processLinksDisplay($model, $response);

			if (!isset($response['data']['events']) && GO::modules()->calendar)
				$response = $this->_processEventsDisplay($model, $response);

			if (!isset($response['data']['tasks']) && GO::modules()->tasks)
				$response = $this->_processTasksDisplay($model, $response);
		}

		if (!isset($response['data']['files']))
			$response = $this->_processFilesDisplay($model, $response);

		if (GO::modules()->comments)
			$response = $this->_processCommentsDisplay($model, $response);

		if ($return)
			return $response;
		$this->renderJson($response);
	}

	/**
	 * Render the JSON outbut for a submit action to be used by ExtJS Form submit
	 * @param GO_Base_Db_ActiveRecord $model
	 * @param boolean $return true if the output should be returned as an array
	 * @return array The JSON out as php array if $return parameter is true
	 */
	public function renderSubmit($model, $return = false) {

		$response = array('feedback' => '', 'success' => true);
		//$ret = $this->beforeSubmit($response, $model, $params);
		//$modifiedAttributes = $model->getModifiedAttributes();
		if (!$model->hasValidationErrors() && !$model->isNew) { //model was saved
			$response['id'] = $model->pk;

			//If the model has it's own ACL id then we return the newly created ACL id.
			//The model automatically creates it.
			if ($model->aclField() && !$model->joinAclField)
				$response[$model->aclField()] = $model->{$model->aclField()};

			//TODO: move the link saving to the model someday
			if (!empty($params['link']) && $model->hasLinks()) {
				//a link is sent like  GO_Notes_Model_Note:1
				//where 1 is the id of the model
				$linkProps = explode(':', $params['link']);
				$linkModel = GO::getModel($linkProps[0])->findByPk($linkProps[1]);
				$model->link($linkModel);
			}
			//TODO: move the file saving to the model someday
			if (!empty($_FILES['importFiles'])) {

				$attachments = $_FILES['importFiles'];
				$count = count($attachments['name']);

				$params['enclosure'] = $params['importEnclosure'];
				$params['delimiter'] = $params['importDelimiter'];

				for ($i = 0; $i < $count; $i++) {
					if (is_uploaded_file($attachments['tmp_name'][$i])) {
						$params['file'] = $attachments['tmp_name'][$i];
						//$params['model'] = $params['importModel'];

						$controller = new $params['importController'];

						$controller->run("import", $params, false);
					}
				}
			}
		} else { // model was not saved
			$response['success'] = false;
			//can't use <br /> tags in response because this goes wrong with the extjs fileupload hack with an iframe.
			$response['feedback'] = sprintf(GO::t('validationErrorsFound'), strtolower($model->localizedName)) . "\n\n" . implode("\n", $model->getValidationErrors()) . "\n";
			if (GO_Base_Util_Http::isAjaxRequest(false)) {
				$response['feedback'] = nl2br($response['feedback']);
			}
			$response['validationErrors'] = $model->getValidationErrors();
		}

		if ($return)
			return $response;
		$this->renderJson($response);
	}

	/**
	 * 
	 * @param GO_Base_Date_JsonStore $store I JsonStore object to get JSON from
	 * @param boolean $return fi true the JSON response will be returned
	 * @return string generated JSON if $return=true
	 */
	public function renderStore(GO_Base_Data_AbstractStore $store, $return = false) {

		$response = array(
				"success" => true,
				"results" => $store->getRecords(),
				'total' => $store->getTotal()
		);

		$title = $store->getTitle();
		if (!empty($title))
			$response['title'] = $title;

		if ($store instanceof GO_Base_Data_DbStore) {
			if ($store->getDeleteSuccess() !== null)
				$response['deleteSuccess'] = $store->getDeleteSuccess();
			$buttonParams = $store->getButtonParams();
			if (!empty($buttonParams))
				$response['buttonParams'] = $buttonParams;
		}
		if ($return)
			return $response;
		$this->renderJson($response);
	}

	/**
	 * Render the headers of the generated response
	 * If headers are not set already. Set them to application/json
	 */
	protected function setHeaders() {
		if (headers_sent())
			return;

		header('Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0'); //prevent caching
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); //resolves problem with IE GET requests
		header('Content-type: application/json; charset=UTF-8'); //tell the browser we are returning json
	}

	public function run($action = '', $params = array(), $render = true, $checkPermissions = true) {
		if (empty($action))
			$action = $this->defaultAction;

		$this->fireEvent($action, array(
				&$this,
				&$params
		));

		$response = parent::run($action, $params, $render, $checkPermissions);

		if (isset($params['firstRun']) && is_array($params['firstRun'])) {
			$response = array_merge($response, $params['firstRun']);
		}

		return $response;
	}

	/**
	 * Adds remoteComboTexts array to response
	 * Will be called in renderLoad()
	 * @param array $response the response data
	 * @return string modified response data
	 * @throws Exception if no valid key defined
	 */
	private function _loadComboTexts($model, $combofields, $response) {

		$response['remoteComboTexts'] = array();

		$oldLevel = error_reporting(E_ERROR); //suppress errors in the eval'd code

		foreach ($combofields as $property => $map) {
			if (is_numeric($property))
				throw new Exception("remoteComboFields() must return a key=>value array.");

			$value = '';
			$eval = '$value = ' . $map . ';';
			eval($eval);

			$response['remoteComboTexts'][$property] = $value;

			//hack for comboboxes displaying 0 instead of the emptyText in extjs
			if (isset($response['data'][$property]) && $response['data'][$property] === 0)
				$response['data'][$property] = "";
		}

		error_reporting($oldLevel);

		return $response;
	}

	/**
	 * 
	 * Below follow all process display functions
	 * 
	 */
	private function _processWorkflowDisplay($model, $response) {

		$response['data']['workflow'] = array();

		$workflowModelstmnt = GO_Workflow_Model_Model::model()->findByAttributes(array("model_id" => $model->id, "model_type_id" => $model->modelTypeId()));

		while ($workflowModel = $workflowModelstmnt->fetch()) {

			$currentStep = $workflowModel->step;

			$workflowResponse = $workflowModel->getAttributes('html');

//			$workflowResponse['id'] = $workflowModel->id;
			$workflowResponse['process_name'] = $workflowModel->process->name;
//			$workflowResponse['due_time'] = $workflowModel->due_time;
//			$workflowResponse['shift_due_time'] = $workflowModel->shift_due_time;			

			$workflowResponse['user'] = !empty($workflowModel->user_id) ? $workflowModel->user->name : '';

			$workflowResponse['approvers'] = array();
			$workflowResponse['approver_groups'] = array();
			$workflowResponse['step_id'] = $workflowModel->step_id;

			if ($workflowModel->step_id == '-1') {
				$workflowResponse['step_progress'] = '';
				$workflowResponse['step_name'] = GO::t('complete', 'workflow');
				$workflowResponse['is_approver'] = false;
				$workflowResponse['step_all_must_approve'] = false;
			} else {
				$workflowResponse['step_progress'] = $workflowModel->getStepProgress();
				$workflowResponse['step_name'] = $currentStep->name;
				$workflowResponse['step_all_must_approve'] = $currentStep->all_must_approve;

				$is_approver = GO_Workflow_Model_RequiredApprover::model()->findByPk(array("user_id" => GO::user()->id, "process_model_id" => $workflowModel->id, "approved" => false));

				if ($is_approver)
					$workflowResponse['is_approver'] = true;
				else
					$workflowResponse['is_approver'] = false;

				// Add the approvers of the current step to the response
				$approversStmnt = $workflowModel->requiredApprovers;

				while ($approver = $approversStmnt->fetch()) {
					$approver_hasapproved = $currentStep->hasApproved($workflowModel->id, $approver->id);
					$workflowResponse['approvers'][] = array('name' => $approver->name, 'approved' => $approver_hasapproved, 'last' => '0');
				}
				// Set the last flag for the latest approver in the list
				$i = count($workflowResponse['approvers']) - 1;

				if ($i >= 0)
					$workflowResponse['approvers'][$i]['last'] = "1";

				// Add the approver groups of the current step to the response
				$approverGroupsStmnt = $currentStep->approverGroups;
				while ($approverGroup = $approverGroupsStmnt->fetch()) {
					$workflowResponse['approver_groups'][] = array('name' => $approverGroup->name);
				}
			}

			$workflowResponse['history'] = array();
			$historiesStmnt = GO_Workflow_Model_StepHistory::model()->findByAttribute('process_model_id', $workflowModel->id, GO_Base_Db_FindParams::newInstance()->select('t.*')->order('ctime', 'DESC'));
			while ($history = $historiesStmnt->fetch()) {
				GO_Base_Db_ActiveRecord::$attributeOutputMode = 'html';


				if ($history->step_id == '-1')
					$step_name = GO::t('complete', 'workflow');
				else
					$step_name = $history->step->name;

				$workflowResponse['history'][] = array(
						'history_id' => $history->id,
						'step_name' => $step_name,
						'approver' => $history->user->name,
						'ctime' => $history->ctime,
						'comment' => $history->comment,
						'status' => $history->status ? "1" : "0",
						'status_name' => $history->status ? GO::t('approved', 'workflow') : GO::t('declined', 'workflow')
				);

				GO_Base_Db_ActiveRecord::$attributeOutputMode = 'raw';
			}

			$response['data']['workflow'][] = $workflowResponse;
		}

		return $response;
	}

	private function _processCustomFieldsDisplay($model, $response) {
		$customAttributes = $model->customfieldsRecord->getAttributes('html');

		//Get all field models and build an array of categories with their
		//fields for display.

		$findParams = GO_Base_Db_FindParams::newInstance()
						->order(array('category.sort_index', 't.sort_index'), array('ASC', 'ASC'));
		$findParams->getCriteria()
						->addCondition('extends_model', $model->customfieldsRecord->extendsModel(), '=', 'category');

		$stmt = GO_Customfields_Model_Field::model()->find($findParams);

		$categories = array();

		while ($field = $stmt->fetch()) {
			if (!isset($categories[$field->category_id])) {
				$categories[$field->category->id]['id'] = $field->category->id;
				$categories[$field->category->id]['name'] = $field->category->name;
				$categories[$field->category->id]['fields'] = array();
			}
			if (!empty($customAttributes[$field->columnName()])) {
				if ($field->datatype == "GO_Customfields_Customfieldtype_Heading") {
					$header = array('name' => $field->name, 'value' => $customAttributes[$field->columnName()]);
				}
				if (!empty($header)) {
					$categories[$field->category->id]['fields'][] = $header;
					$header = null;
				}
				$categories[$field->category->id]['fields'][] = array(
						'name' => $field->name,
						'value' => $customAttributes[$field->columnName()]
				);
			}
		}

		foreach ($categories as $category) {
			if (count($category['fields']))
				$response['data']['customfields'][] = $category;
		}

		return $response;
	}

	private function _processFilesDisplay($model, $response) {
		if (isset(GO::modules()->files) && $model->hasFiles() && $response['data']['files_folder_id'] > 0) {

			$fc = new GO_Files_Controller_Folder();
			$listResponse = $fc->run("list", array('folder_id' => $response['data']['files_folder_id'], "limit" => 20, "sort" => 'mtime', "dir" => 'DESC'), false);
			$response['data']['files'] = $listResponse['results'];
		} else {
			$response['data']['files'] = array();
		}
		return $response;
	}

	private function _processLinksDisplay($model, $response) {
		$findParams = GO_Base_Db_FindParams::newInstance()
						->limit(15);

		$ignoreModelTypes = array();
		if (GO::modules()->calendar)
			$ignoreModelTypes[] = GO_Calendar_Model_Event::model()->modelTypeId();
		if (GO::modules()->tasks)
			$ignoreModelTypes[] = GO_Tasks_Model_Task::model()->modelTypeId();

		$findParams->getCriteria()->addInCondition('model_type_id', $ignoreModelTypes, 't', true, true);

		$stmt = GO_Base_Model_SearchCacheRecord::model()->findLinks($model, $findParams);

		$store = GO_Base_Data_Store::newInstance(GO_Base_Model_SearchCacheRecord::model());
		$store->setStatement($stmt);

		$columnModel = $store->getColumnModel();
		$columnModel->formatColumn('link_count', 'GO::getModel($model->model_name)->countLinks($model->model_id)');
		$columnModel->formatColumn('link_description', '$model->link_description');

		$data = $store->getData();
		$response['data']['links'] = $data['results'];

		return $response;
	}

	private function _processEventsDisplay($model, $response) {
		$startOfDay = GO_Base_Util_Date::clear_time(time());

		$findParams = GO_Base_Db_FindParams::newInstance()->order('start_time', 'DESC');
		$findParams->getCriteria()->addCondition('start_time', $startOfDay, '>=');

		$stmt = GO_Calendar_Model_Event::model()->findLinks($model, $findParams);

		$store = GO_Base_Data_Store::newInstance(GO_Calendar_Model_Event::model());
		$store->setStatement($stmt);

		$columnModel = $store->getColumnModel();
		$columnModel->formatColumn('calendar_name', '$model->calendar->name');
		$columnModel->formatColumn('link_count', '$model->countLinks()');
		$columnModel->formatColumn('link_description', '$model->link_description');

		$data = $store->getData();
		$response['data']['events'] = $data['results'];

		return $response;
	}

	private function _processCommentsDisplay($model, $response) {
		$stmt = GO_Comments_Model_Comment::model()->find(GO_Base_Db_FindParams::newInstance()
										->limit(5)
										->select('t.*,cat.name AS categoryName')
										->order('id', 'DESC')
										->joinModel(array(
												'model' => 'GO_Comments_Model_Category',
												'localTableAlias' => 't',
												'localField' => 'category_id',
												'foreignField' => 'id',
												'tableAlias' => 'cat',
												'type' => 'LEFT'
										))
										->criteria(GO_Base_Db_FindCriteria::newInstance()
														->addModel(GO_Comments_Model_Comment::model())
														->addCondition('model_id', $model->id)
														->addCondition('model_type_id', $model->modelTypeId())
										));

		$store = GO_Base_Data_Store::newInstance(GO_Comments_Model_Comment::model());
		$store->setStatement($stmt);

		$columnModel = $store->getColumnModel();
		$columnModel->formatColumn('user_name', '$model->user->name');

		$data = $store->getData();
		foreach ($data['results'] as $k => $v) {
			$data['results'][$k]['categoryName'] = !empty($v['categoryName']) ? $v['categoryName'] : GO::t('noCategory', 'comments');
		}
		$response['data']['comments'] = $data['results'];

		return $response;
	}

	private function _processTasksDisplay($model, $response) {
		//$startOfDay = GO_Base_Util_Date::clear_time(time());

		$findParams = GO_Base_Db_FindParams::newInstance()->order('due_time', 'DESC');
		//$findParams->getCriteria()->addCondition('start_time', $startOfDay, '<=')->addCondition('status', GO_Tasks_Model_Task::STATUS_COMPLETED, '!=');						

		$stmt = GO_Tasks_Model_Task::model()->findLinks($model, $findParams);

		$store = GO_Base_Data_Store::newInstance(GO_Tasks_Model_Task::model());
		$store->setStatement($stmt);

		$store->getColumnModel()
						->setFormatRecordFunction(array($this, 'formatTaskLinkRecord'))
						->formatColumn('late', '$model->due_time<time() ? 1 : 0;')
						->formatColumn('tasklist_name', '$model->tasklist->name')
						->formatColumn('link_count', '$model->countLinks()')
						->formatColumn('link_description', '$model->link_description');

		$data = $store->getData();
		$response['data']['tasks'] = $data['results'];

		return $response;
	}

	public function formatTaskLinkRecord($record, $model, $cm) {

		$statuses = GO::t('statuses', 'tasks');

		$record['status'] = $statuses[$model->status];

		if ($model->percentage_complete > 0 && $model->status != 'COMPLETED')
			$record['status'].= ' (' . $model->percentage_complete . '%)';

		return $record;
	}
	
	
	protected function renderExport(GO_Base_Data_DbStore $store, $params){
		//define('EXPORTING', true);
		//used by custom fields to format diffently
		if(GO::modules()->customfields)
			GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport=true;
		
		$checkboxSettings = array(
			'export_include_headers'=>!empty($params['includeHeaders']),
			'export_human_headers'=>empty($params['humanHeaders']),
			'export_include_hidden'=>!empty($params['includeHidden'])
		);
		
		$settings =  GO_Base_Export_Settings::load();
		$settings->saveFromArray($checkboxSettings);
		
		if(!empty($params['exportOrientation']) && ($params['exportOrientation']=="H"))
			$orientation = 'L'; // Set the orientation to Landscape
		else
			$orientation = 'P'; // Set the orientation to Portrait
		
		
		if(!empty($params['columns'])) {
			$columnModel = $store->getColumnModel();
			$includeColumns = explode(',',$params['columns']);
			foreach($includeColumns as $incColumn){
				if(!$columnModel->getColumn($incColumn))
					$columnModel->addColumn (new GO_Base_Data_Column($incColumn,$incColumn));
			}
				
			$columnModel->sort($includeColumns);
			
			foreach($columnModel->getColumns() as $c){
				if(!in_array($c->getDataIndex(), $includeColumns))
					$columnModel->removeColumn($c->getDataIndex());
			}
		}
		
		if(!empty($params['type'])){
			//temporary fix for compatibility with AbsractModelController
			$params['type']=str_replace('GO_Base_Export', 'GO_Base_Storeexport', $params['type']);
			$export = new $params['type']($store, $settings->export_include_headers, $settings->export_human_headers, $params['documentTitle'], $orientation);
		}else
			$export = new GO_Base_Storeexport_ExportCSV($store, $settings->export_include_headers, $settings->export_human_headers, $params['documentTitle'], $orientation); // The default Export is the CSV outputter.

		$export->output();
	}
}