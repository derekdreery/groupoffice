<?php

/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * This store provide will generate a JSON response to be used in the Ext GridPanel
 * It can be used in the actionStore() if most controllers to generated data from
 * a query.
 * 
 * <pre>
 * $columnModel =  new GO_Base_Data_ColumnModel(GO_Notes_Model_Note::model());
 * $columnModel->formatColumn('user_name', '$model->user->name', array(), 'user_id');
 * 
 * $store=new GO_Base_Data_Store('GO_Notes_Model_Note', $columnModel, $params);
 * </pre>
 * 
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Michael de Hart <mdhart@intermesh.nl>
 * @package GO.base.data
 */
class GO_Base_Data_DbStore extends GO_Base_Data_AbstractStore {

  // --- Attributes ---
  
  /**
   * Will be used internaly to save the statement
   * @var GO_Base_Db_ActiveStatement 
   */
  protected $_stmt;

  /**
   * The column name to sort the resulting record set on
   * @var string
   */
  public $sort;
  public $defaultSort = '';

  /**
   * The sort direction, ASC or DESC
   * @var string 
   */
  public $direction;
  public $defaultDirection = 'ASC';

  /**
   * The amount of records to load at ones (per page)
   * @var integer amount of records per page
   */
  public $limit;
  
  /**
   * offset in limit part of query @see GO_Base_DB_findParams::start()
   * @var integer  
   */
  public $start = 0;
  
  /**
   * Find only record that contain this word
   * Is used by the quick search bar on top of a grid
   * @var string word to search for
   */
  public $query = '';
  
  /**
   * This indicateds if the data was loaded from database
   * Will be set back to false of statement changes
   * @var boolean 
   */
  protected $_loaded = false;
  
  /**
   * The name of the model this db store contains record from
   * @var string name of model (eg. GO_Base_User)
   */
  protected $_modelClass;
  
  /**
   * Extra find params the be merged with the storeparams 
   * @var GO_Base_Db_FindParams
   */
  protected $_extraFindParams;
  
  /**
   * Will be set for multi select stores @see multiSelect()
   * @var array  attache to response if set
   */
  private  $_buttonParams;
  
  /**
   * the primary key of the record that should be delete just before loading the store data
   * @var array model PKs 
   */
  protected $_deleteRecords = array();
  
  // --- Methods ---
  
  /**
   * Create a new store
   * @param string $modelClass the classname of the model to execute the find() method on
   * @param GO_Base_Data_ColumnModel $columnModel
   * @param array $storeParams the $_POST params to set to this store @see setStoreParams()
   * @param GO_Base_Db_FindParams $findParams extra findParams to be added to the store
   */
  public function __construct($modelClass, $columnModel, $requestParams, $findParams = null) {

	$this->_modelClass = $modelClass;
	$this->_columnModel = $columnModel;
	$this->setStoreParams($requestParams);
	if($findParams instanceof GO_Base_Db_FindParams)
	  $this->_extraFindParams = $findParams;
	else
	  $this->_extraFindParams = GO_Base_Db_FindParams::newInstance();
  }

  /**
   * Walk through the params and set the data that the store can use
   * Valid config options: 
   * 'sort:string'
   * 'dir:string'
   * 'limit:integer'
   * 'query:string'
   * 'delete_keys:array'
   * 'advancedQueryData:array'
   * 'forEditing:boolean'
   * @param array $params the data to set
   */
  public function setStoreParams($params) {
	if (isset($params['sort']))
	  $this->sort = $params['sort'];
	
	if (isset($params['dir']))
	  $this->direction =  $params['dir'];
	
	if(isset($params['limit']))
	  $this->limit = $params['limit'];
	
	if(isset($params['start']))
	  $this->start = $params['start'];
	
	if(isset($params['query']))
	  $this->query = $params['query'];
	
	if (isset($params['delete_keys'])) // will be deleted just before loading.
	  $this->_deleteRecords = json_decode($params['delete_keys']);
	
	if (!empty($params['advancedQueryData']))
	  $this->_handleAdvancedQuery($params['advancedQueryData'], $storeParams);

	if (!empty($params["forEditing"]))
	  $this->_columnModel->setModelFormatType("formatted");
  }

  /**
   * FIXME: this method was copied from ModelController and never tested
   * @param array $advancedQueryData the query data to be set to the store
   * @param array $storeParams store params to be modied by advancedQuery
   */
  private function _handleAdvancedQuery($advancedQueryData, &$storeParams) {
	$advancedQueryData = is_string($advancedQueryData) ? json_decode($advancedQueryData, true) : $advancedQueryData;
	$findCriteria = $storeParams->getCriteria();

	$criteriaGroup = GO_Base_Db_FindCriteria::newInstance();
	$criteriaGroupAnd = true;
	for ($i = 0, $count = count($advancedQueryData); $i < $count; $i++) {

	  $advQueryRecord = $advancedQueryData[$i];

	  //change * into % wildcard
	  $advQueryRecord['value'] = isset($advQueryRecord['value']) ? str_replace('*', '%', $advQueryRecord['value']) : '';

	  if ($i == 0 || $advQueryRecord['start_group']) {
		$findCriteria->mergeWith($criteriaGroup, $criteriaGroupAnd);
		$criteriaGroupAnd = $advQueryRecord['andor'] == 'AND';
		$criteriaGroup = GO_Base_Db_FindCriteria::newInstance();
	  }

	  if (!empty($advQueryRecord['field'])) {
		// Give the record a unique id, to enable the programmers to
		// discriminate between advanced search query records of the same field
		// type.
		$advQueryRecord['id'] = $i;
		// Check if current adv. search record should be handled in the standard
		// manner.

		$fieldParts = explode('.', $advQueryRecord['field']);

		if (count($fieldParts) == 2) {
		  $field = $fieldParts[1];
		  $tableAlias = $fieldParts[0];
		} else {
		  $field = $fieldParts[0];
		  $tableAlias = false;
		}

		if ($tableAlias == 't')
		  $advQueryRecord['value'] = GO::getModel($this->_modelClass)->formatInput($field, $advQueryRecord['value']);
		elseif ($tableAlias == 'cf') {
		  $advQueryRecord['value'] = GO::getModel(GO::getModel($this->_modelClass)->customfieldsModel())->formatInput($field, $advQueryRecord['value']);
		}

		$criteriaGroup->addCondition($field, $advQueryRecord['value'], $advQueryRecord['comparator'], $tableAlias, $advQueryRecord['andor'] == 'AND');
	  }
	}

	$findCriteria->mergeWith($criteriaGroup, $criteriaGroupAnd);
  }
  
  /**
   * Create the PDO statment that will query the results
   * @return GO_Base_Db_ActiveStatement the PDO statement
   */
  protected function createStatement()
  {
	$params = $this->createFindParams();
	$modelFinder = GO::getModel($this->_modelClass);
	return $modelFinder->find($params);
  }
  
  /**
   * Create FindParams object to be passen the this models find() function
   * If there are extraFind params supplied these well be merged in the end
   * @return GO_Base_Db_FindParams the created find params to be passen to AR's find() function
   */
  protected function createFindParams()
  {
	if(empty($this->sort))
	  $this->sort = $this->defaultSort;
	
	if(empty($this->direction))
	  $this->direction = $this->defaultDirection;
	
	$findParams = GO_Base_Db_FindParams::newInstance()						
	  ->joinCustomFields()
	  ->order($this->sort, $this->direction);
	
	//FIXME: save in model of rows should be calculated
	if(empty($requestParams['dont_calculate_total'])){
		$findParams->calcFoundRows();
	}
		
	//do not prefix search query with a wildcard by default. 
	//When you start a query with a wildcard mysql can't use indexes.
	//Correction: users can't live without the wildcard at the start.

	if(!empty($this->query))
		$findParams->searchQuery ('%'.preg_replace ('/[\s*]+/','%', $this->query).'%');

	if(isset($this->limit))
		$findParams->limit ($this->limit);
	else
		$findParams->limit =0;//(GO::user()->max_rows_list);

	if(!empty($this->start))
		$findParams->start ($this->start);

	//TODO: check if this is still used by any actionStore()
	if(isset($requestParams['permissionLevel']))
		$findParams->permissionLevel ($requestParams['permissionLevel']);
		
	if(isset($this->_extraFindParams))
			$findParams->mergeWith($this->_extraFindParams);
	
	return $findParams;
  }

  /**
   * This method will be called internally before getData().
   * It will delete all record that has the pk in $_deleteprimaryKey array
   * @param array $primaryKeys array of value to be passen to findByPk()
   * @see: GO_Base_Db_Store::processDeleteActions()
   */
  protected function processDeleteActions() {
	if ($this->_loaded)
	  throw new Exception("deleteRecord should be called before loading data. If you run the statement before the deletes then the deleted items will still be in the result.");
	if(empty($this->_deleteRecords))
	  return true;
	
	$success = true;
	$stmt = GO::getModel($this->_modelClass)->findByPk($this->_deleteRecords);
	foreach ($stmt as $model)
	  $success = $success && $model->delete();

	if ($success)
	  $this->_deleteRecords = array();

	return $success;
  }

  /**
   * Fetch the next record from the PDO statement.
   * Format it using the _columnModel's formatMode() function
   * Or return false if there are no more records
   * @return GO_Base_Db_ActiveRecord
   */
  public function nextRecord() {
	$model = $this->_stmt->fetch();
	return $model ? $this->_columnModel->formatModel($model) : false;
  }

  /**
   * Return total amount of record for the statement (without limit)
   * @return integer Number of total Records
   */
  public function getTotal() {
	if (!isset($this->_stmt))
	  $this->_stmt = $this->createStatement();
	return isset($this->_stmt->foundRows) ? $this->_stmt->foundRows : $this->_stmt->rowCount();
  }

  /**
   * Returns the formatted data for an ExtJS grid.
   * Also deletes the given delete_keys.
   * @return array $this->response 
   */
  public function getData() {

	$this->processDeleteActions();
	
	if (!isset($this->_stmt))
	  $this->_stmt = $this->createStatement();

	$this->_loaded = true;

	$columns = $this->_columnModel->getColumns();
	if (empty($columns))
	  throw new Exception('No columns given for this store');
	
	$results = array();
	while ($record = $this->nextRecord())
	  $results[] = $record;

	return $results;
  }
  
  /**
   * Select Items that belong to one of the selected Models
   * @param string $requestParamName That key that will hold the seleted item in go_setting table
   * @param string $selectClassName Name of the related model (eg. GO_Notes_Model_Category)
   * @param string $foreignKey column name to match the realed models PK (eg. category_id)
   * @param boolean $checkPermissions check Permission for item defaults to true
   */
  public function multiSelect($requestParamName, $selectClassName, $foreignKey, $checkPermissions=true) {
	$multiSel = new GO_Base_Component_MultiSelectGrid(
		$requestParamName,
		$selectClassName,
		$this,
		$_POST, //quickfix
		$checkPermissions
	);
	$multiSel->addSelectedToFindCriteria($this->_extraFindParams, $foreignKey);
	$multiSel->setStoreTitle();
	
	$buttonParams=array();
	$multiSel->setButtonParams($buttonParams);
	if(isset($buttonParams['buttonParams']))
	  $this->_buttonParams = $buttonParams['buttonParams'];
  }
  
  /**
   * Call this in the selectable stores that effect other grid by selecting values
   * @param string $requestParamName 
   * @param string $selectClassName
   */
  public function multiSelectable($requestParamName, $selectClassName) {
	$multiSel = new GO_Base_Component_MultiSelectGrid($requestParamName, $selectClassName, $this, $_POST);
	$multiSel->setFindParamsForDefaultSelection($this->_extraFindParams);
	$multiSel->formatCheckedColumn();
  }
  
  /**
   * The buttons params to be attached to the response
   * @return array button params
   */
  public function getButtonParams(){
	return $this->_buttonParams;
  }

}