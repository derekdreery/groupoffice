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
	 * @var string 
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
		if (!empty($params['id'])){
			$model = GO::getModel($modelName)->findByPk($params['id']);
		}else
		{
			$model = new $modelName;
			$model->user_id=GO::user()->id;
		}

		$this->beforeSubmit($response, $model, $params);
		
		$model->setAttributes($params);
		
		$modifiedAttributes = $model->getModifiedAttributes();
		try{
			$response['success'] = $model->save();

			$response['id'] = $model->pk;

			//If the model has it's own ACL id then we return the newly created ACL id.
			//The model automatically creates it.
			if ($model->aclField() && !$model->aclFieldJoin) {
				$response[$model->aclField()] = $model->{$model->aclField()};
			}


			if (!empty($params['link'])) {

				//a link is sent like  GO_Notes_Model_Note:1
				//where 1 is the id of the model

				$linkProps = explode(':', $params['link']);			
				$linkModel = GO::getModel($linkProps[0])->findByPk($linkProps[1]);
				$model->link($linkModel);			
			}

			if(!empty($_FILES['importFiles'])){

				$attachments = $_FILES['importFiles'];
				$count = count($attachments['name']);

				$params['enclosure'] = $params['importEnclosure'];
				$params['delimiter'] = $params['importDelimiter'];

				for($i=0;$i<$count;$i++){
					if(is_uploaded_file($attachments['tmp_name'][$i])) {
						$params['file']= $attachments['tmp_name'][$i];
						//$params['model'] = $params['importModel'];

						$controller = new $params['importController'];

						$controller->actionImport($params);
					}
				}
			}


			$this->afterSubmit($response, $model, $params, $modifiedAttributes);
		}catch(Exception $e){
			$response['success']=false;
			$response['feedback']=nl2br($e->getMessage());
			$response['validationErrors']=$model->getValidationErrors();
		}

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
		if(!empty($params['id'])){
			$model = GO::getModel($modelName)->findByPk($params['id']);
		}else{
			$model = new $modelName;
			$model->setAttributes($params);
		}
		
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
				//'requestParam'=>'notes_categories_filter', //The name of the request parameter sent by the view. 
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
	 * @return GO_Base_Db_FindParams parameters for the GO_Base_Db_ActiveRecord->find() function 
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
	
	public function formatStoreRecord($record, $model, $store){
		return $record;
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
	protected function prepareStore(GO_Base_Data_Store $store) {
		
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
	
	protected function processStoreDelete($store, &$params){
		$store->processDeleteActions($params, $this->model);
	}
  
  /**
   * The default grid action for the current model. This action also handles:
	 * 
	 * 1. Multiselection of related BELONGS_TO models (See getMultiSelectProperties).
	 * 2. Advanced queries. See _handleAdvancedQuery, the contacts advanced search
	 * use case in Group-Office, and
	 * GO_Addressbook_Controller_Contact::beforeIntegrateRegularSql.
	 * 3. Deleting models
   */
  public function actionStore($params){	
    $modelName = $this->model;  

    $store = new GO_Base_Data_Store($this->getStoreColumnModel());	
		$store->getColumnModel()->setFormatRecordFunction(array($this, 'formatStoreRecord'));		
		
		
		$response=array();
		
		$response = $this->beforeStore($response, $params, $store);
		
		$this->processStoreDelete($store, $params);

		if(($multiSelectProperties =$this->getStoremultiSelectProperties()) && empty($params['noMultiSelectFilter'])){

			if(isset($params[$multiSelectProperties['requestParam']])){
				$this->multiselectIds=json_decode($params[$multiSelectProperties['requestParam']], true);
				GO::config()->save_setting('ms_'.$multiSelectProperties['requestParam'], implode(',',$this->multiselectIds), GO::session()->values['user_id']);
			}else
			{			
				$this->multiselectIds = GO::config()->get_setting('ms_'.$multiSelectProperties['requestParam'], GO::session()->values['user_id']);
				$this->multiselectIds  = $this->multiselectIds ? explode(',',$this->multiselectIds) : array();
//				$this->multiselectIds=array();
			}
		
			
			if(empty($this->multiselectIds))
			{
				$default = $this->getStoreMultiSelectDefault();
				if($default){
					$this->multiselectIds = array($default);
					GO::config()->save_setting('ms_'.$multiSelectProperties['requestParam'],implode(',', $this->multiselectIds), GO::user()->id);
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
		
		if($multiSelectProperties)
			$columnModel->formatColumn('checked','in_array($model->id, $controller->multiselectIds)', array('controller'=>$this));
		
		$this->prepareStore($store);
		
		$storeParams = $store->getDefaultParams($params)->mergeWith($this->getStoreParams($params));
		
		if (!empty($params['advancedQueryData'])) {		
			$this->_handleAdvancedQuery($params['advancedQueryData'],$storeParams);
		}
			
		$store->setStatement(GO::getModel($modelName)->find($storeParams));
		
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
		//require_once(GO::config()->root_path.'Group-Office.php');

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

		if($model->hasLinks()){
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
		}

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

		if (/* !in_array('files', $hidden_sections) && */!isset($response['data']['files'])) {
			if (isset(GO::modules()->files) && $model->hasFiles() && $response['data']['files_folder_id']>0) {
				
				$fc = new GO_Files_Controller_Folder();
				$listResponse = $fc->actionList(array('folder_id'=>$response['data']['files_folder_id']));
				$response['data']['files'] = $listResponse['results'];

				//$response['data']['files'] = $files->get_content_json($response['data']['files_folder_id']);
			} else {
				$response['data']['files'] = array();
			}
		}

		if (GO::modules()->comments){

			$stmt = GO_Comments_Model_Comment::model()->find(GO_Base_Db_FindParams::newInstance()
							->limit(5)
							->select('t.*')
							->order('id','DESC')
							->criteria(GO_Base_Db_FindCriteria::newInstance()
							        ->addModel(GO_Comments_Model_Comment::model())
											->addCondition('model_id', $model->id)
											->addCondition('model_type_id',$model->modelTypeId())
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
		
		$model = GO::getModel($this->model)->findByPk($params['id']);
		
		$response=array();
		
		$response = $this->beforeDelete($response, $model, $params);

		$response['success'] = $model->delete();

		$response = $this->afterDelete($response, $model, $params);

		return $response;
	}
	
	/**
	 * Useful to override
	 * 
	 * @param array $response The response array
	 * @param mixed $model
	 */
	protected function beforeDelete(&$response, &$model, &$params) {
		return $response;
	}
	
	/**
	 * Useful to override
	 * 
	 * @param array $response The response array
	 * @param mixed $model
	 */
	protected function afterDelete(&$response, &$model, &$params) {
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
	
		$showHeader = false;
		$orientation = false;
		
		if(!empty($params['exportOrientation']) && ($params['exportOrientation']=="Horizontaal"))
			$orientation = 'L'; // Set the orientation to Landscape
		else
			$orientation = 'P'; // Set the orientation to Portrait

		if(!empty($params['documentTitle']))
			$title = $params['documentTitle'];
		else
			$title = GO::session()->values[$params['name']]['name'];
	
		if(!empty($params['includeHeaders']))
			$showHeader = true;
		
		$findParams = GO::session()->values[$params['name']]['findParams'];
		$findParams['limit']=0; // Let the export handle all found records without a limit
		$model = GO::getModel(GO::session()->values[$params['name']]['model']);
		
		$store = new GO_Base_Data_Store();

		$stmt = $model->find($findParams);
		$store->setStatement($stmt);

		$columnModel = $store->getColumnModel();

		if(!empty($params['columns'])) {
			$includeColumns = explode(',',$params['columns']);
			$columnModel->setColumnsFromModel($model, array(), $includeColumns);
		} else {
			$columnModel->setColumnsFromModel($model);
		}

		if(!empty($params['type']))
			$export = new $params['type']($store, $columnModel,$showHeader, $title, $orientation);
		else
			$export = new GO_Base_Export_ExportCSV($store, $columnModel,$showHeader, $title, $orientation); // The default Export is the CSV outputter.

		$export->output();
	}
	
	/**
	 *
	 * Defaults to a CSV import.
	 * 
	 * Custom fields can be specified in the header with cf\$categoryName\$fieldName
	 * 
	 * eg. name,attribute,cf\Test\Textfield
	 * 
	 * @param array $params 
	 */
	public function actionImport($params) {

		$importFile = new GO_Base_Fs_CsvFile($params['file']);
		
		if(!empty($params['delimiter']))
			$importFile->delimiter = $params['delimiter'];
		
		if(!empty($params['enclosure']))
			$importFile->enclosure = $params['enclosure'];
			
		$importFile->convertToUtf8();

		$headers = $importFile->getRecord();
		
		//Map the field headers to the index in the record.
		//eg. name=>2,user_id=>4, etc.
		$attributeIndexMap = array();		
		for ($i = 0, $m = count($headers); $i < $m; $i++) {
			if(substr($headers[$i],0,3)=='cf\\'){				
				$cf = $this->_resolveCustomField($headers[$i]);
				if($cf)
					$attributeIndexMap[$i] = $cf;
			}else
			{
				$attributeIndexMap[$i] = $headers[$i];
			}
		}

		while ($record = $importFile->getRecord()) {
			$attributes = array();
			foreach($attributeIndexMap as $index=>$attributeName){
				$attributes[$attributeName]=$record[$index];
			}

			$model = new $this->model;
			
			if($this->beforeImport($model, $attributes, $record)){			
				
				$columns = $model->getColumns();
				foreach($columns as $col=>$attr){
					if(isset($attributes[$col]) && ($attr['gotype']=='unixtimestamp' || $attr['gotype']=='unixdate')){
						$attributes[$col]=strtotime($attributes[$col]);
					}
				}
				// True is set because import needs to be checked by the model.
				$model->setAttributes($attributes, true);
				
				// If there are given baseparams to the importer
				if(isset($params['importBaseParams'])) {
					$baseParams = json_decode($params['importBaseParams'],true);
					foreach($baseParams as $attr=>$val){
						$model->setAttribute($attr,$val);
					}
				}
				
				$this->_parseImportDates($model);
				
				$model->save();			
			}
			
			$this->afterImport($model, $attributes, $record);
		}
	}
	
	protected function beforeImport(&$model, &$attributes, $record){
		return true;
	}
	protected function afterImport(&$model, &$attributes, $record){
		return true;
	}
	
	private function _resolveCustomField($header){
		$parts = explode('\\', $header);
		
		if(count($parts)<3)
			return false;
		
		$categoryName = $parts[1];
		$fieldName = $parts[2];
		
		$category = GO_Customfields_Model_Category::model()->findSingleByAttributes(array('extends_model'=>$this->model, 'name'=>$categoryName));
		
		if(!$category){
			$category = new GO_Customfields_Model_Category();
			$category->extends_model=$this->model;
			$category->name=$categoryName;
			$category->save();
		}	
		$field = GO_Customfields_Model_Field::model()->findSingleByAttributes(array('category_id'=>$category->id,'name'=>$fieldName));
		if(!$field){
			$field = new GO_Customfields_Model_Field();
			$field->category_id=$category->id;
			$field->name=$fieldName;
			$field->save();
		}
		
		return $field->columnName();
	}
	
	
	public function actionAttributes($params){
		
		if(!isset($params['exclude']))
			$params['exclude']=array();
		else
			$params['exclude']=explode(',', $params['exclude']);
		
		array_push($params['exclude'], 'id','acl_id','files_folder_id');
		
		$response['results']=array();
		
		$model = GO::getModel($this->model);
		$labels = $model->attributeLabels();
		
		$attributes = array();
		
		$columns = $model->getColumns();
		foreach($columns as $name=>$attr){
			if(!in_array($name, $params['exclude']))
				$attributes['t.'.$name]=array('name'=>'t.'.$name,'label'=>$model->getAttributeLabel($name),'gotype'=>$attr['gotype']);				
		}
		
		asort($attributes);
		
		if($model->customfieldsRecord){
			$customAttributes = array();
			$columns = $model->customfieldsRecord->getColumns();
			foreach($columns as $name=>$attr){
				if($name != 'model_id' && !in_array($name, $params['exclude'])){					
					$customAttributes['cf.'.$name]=array('name'=>'cf.'.$name, 'label'=>$model->customfieldsRecord->getAttributeLabel($name),'gotype'=>'customfield');					
				}
			}
			asort($attributes);
			
			$attributes=array_merge($attributes, $customAttributes);
		}
		
		$this->afterAttributes($attributes, $response, $params, $model);
		
		
		
		foreach($attributes as $field=>$attr)
			$response['results'][]=$attr;
		
		return $response;		
	}
	
	/**
	 * Customizations to the attributes in the store for the view can be done
	 * here. This function should be used in your controller in conjunction with
	 * beforeIntegrateRegularSql(). See for an example: the advanced search use
	 * case in Group-Office, GO_Addressbook_Controller_Contact::afterAttributes
	 * and GO_Addressbook_Controller_Contact::beforeIntegrateRegularSql().
	 * @param Array $attributes Array of attributes. Keys of the array are how the
	 * attributes will be known as search record field names after the
	 * view passes an advanced search record to the controller. Values of the
	 * array are how they will be named in the view's advanced search dialog's
	 * select box.
	 * @param Array $response The response to be passed to the client.
	 * @param type $params The request parameters from the client.
	 * @param GO_Base_Db_ActiveRecord $model 
	 */
	protected function afterAttributes(&$attributes, &$response, &$params, GO_Base_Db_ActiveRecord $model)
	{
	//unset($attributes['t.company_id']);
	//$attributes['companies.name']=GO::t('company','addressbook');
	//return parent::afterAttributes($attributes, $response, $params, $model);
	}
	
	/**
	 * Adds advanced query request parameters to a findCriteria object. 
	 * The advanced query panel view can be found in GO.query.QueryPanel
	 * 
	 * @param String-or-array $advancedQueryData 
	 * @param GO_Base_Db_FindParams $storeParams
	 */
	private function _handleAdvancedQuery($advancedQueryData, &$storeParams){
		$advancedQueryData = is_string($advancedQueryData) ? json_decode($advancedQueryData, true) : $advancedQueryData;
		$findCriteria = $storeParams->getCriteria();
		
		$criteriaGroup = GO_Base_Db_FindCriteria::newInstance();
		$criteriaGroupAnd=true;
		for($i=0,$count=count($advancedQueryData);$i<$count;$i++){
			
			$advQueryRecord=$advancedQueryData[$i];
			
			if($i==0 || $advQueryRecord['start_group']){
				$findCriteria->mergeWith($criteriaGroup,$criteriaGroupAnd);
				$criteriaGroupAnd=$advQueryRecord['andor']=='AND';
				$criteriaGroup = GO_Base_Db_FindCriteria::newInstance();
			}
			
			if(!empty($advQueryRecord['field'])){	
				// Give the record a unique id, to enable the programmers to
				// discriminate between advanced search query records of the same field
				// type.
				$advQueryRecord['id'] = $i;
				// Check if current adv. search record should be handled in the standard
				// manner.
				if($this->beforeHandleAdvancedQuery($advQueryRecord, $criteriaGroup ,$storeParams)){
					
					$fieldParts = explode('.',$advQueryRecord['field']);
				
					if(count($fieldParts)==2){
						$field = $fieldParts[1];
						$tableAlias=$fieldParts[0];
					}else
					{
						$field = $fieldParts[0];
						$tableAlias=false;
					}

					if($tableAlias=='t'){
						$advQueryRecord['value']=GO::getModel($this->model)->formatInput($field, $advQueryRecord['value']);
					}
					$criteriaGroup->addCondition($field, $advQueryRecord['value'], $advQueryRecord['comparator'],$tableAlias,$advQueryRecord['andor']=='AND');
					
				}
			}
		}
			
		$findCriteria->mergeWith($criteriaGroup,$criteriaGroupAnd);
		
		$storeParams->debugSql();
	}
	
	/**
	 * If this function is not overridden in your controller, advanced search will
	 * be only possible for model fields that correspond directly to fields in the
	 * model's database table.
	 * You can catch advanced search query records that have to be handled
	 * differently by overriding this function. For example, if the purpose is to
	 * search through fields of models related to the current model, such as
	 * 'company name' for 'contacts', you can handle it here and return false. The
	 * resulting overridden function should be a switch.
	 * In your controller, this should be used in conjunction with
	 * afterAttributes(). See for an example: the advanced search use case in
	 * Group-Office, GO_Addressbook_Controller_Contact::afterAttributes and
	 * GO_Addressbook_Controller_Contact::beforeIntegrateRegularSql().
	 * @param Array $advQueryRecord
	 * @param GO_Base_Db_FindCriteria $findCriteria
	 * @param GO_Base_Db_FindParams $storeParams
	 * @return boolean Return true if the current $advQueryRecord must be handled
	 * in the regular way, return false after it has been handled differently.
	 */
	protected function beforeHandleAdvancedQuery($advQueryRecord, GO_Base_Db_FindCriteria &$findCriteria, GO_Base_Db_FindParams &$storeParams){
		return true;
	}
	
	/**
	 * Checks if query data $advancedQueryData contains a field with name $fieldName,
	 * and returns the record with that name, if any.
	 * @param String $fieldName
	 * @param Array $advancedQueryData
	 * @return Array The advanced query record, or false if not found. 
	 */
//	protected function getAdvancedQueryRecord($fieldName, $advancedQueryData) {
//		$advancedQueryData = json_decode($advancedQueryData, true);
//		foreach ($advancedQueryData as $record) {
//			if ($record['field']==$fieldName)
//				return $record;
//		}
//		return false;
//	}
	
		/**
	 * Removes record with name $fieldName from $advancedQueryData contains.
	 * @param String $fieldName
	 * @param Array $advancedQueryData
	 */
//	protected function removeAdvancedQueryRecord($fieldName, &$advancedQueryData) {
//		$advancedQueryData = json_decode($advancedQueryData, true);
//		foreach ($advancedQueryData as $k=>$record) {
//			if ($record['field']==$fieldName)
//				unset($advancedQueryData[$k]);
//		}
//		$advancedQueryData = json_encode($advancedQueryData);
//	}
	
	
	/**
	 * Checks for dates in the import model and performs an strtotime on it.
	 * 
	 * @param GO_Base_Db_ActiveRecord $model 
	 */
	private function _parseImportDates(&$model){
		
		$columns = $model->getColumns();
		
		foreach($columns as $attributeName => $column){
			if(!empty($column['gotype']) && $column['gotype'] == 'date'){
				$model->$attributeName = date('Y-m-d',strtotime($model->$attributeName));
			}
		}		
	}
	
}