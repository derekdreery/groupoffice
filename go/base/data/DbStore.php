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
	 * Contains the loaded records from the database or empty if not loaded
	 * @var array 
	 */
	protected $_record = false;

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
	 * Taken from old store to add a value to the primary key to search for
	 * @var array keys and value to attach to the pk to look for when deleting 
	 */
	public $extraDeletePk = null;

	/**
	 * Set this property to files if deleting records is not allowed for the store object
	 * @var boolean
	 */
	public $allow_delete = true;

	/**
	 * the primary key of the record that should be delete just before loading the store data
	 * @var array model PKs 
	 */
	protected $_deleteRecords = array();

	/**
	 * The request params passed by the controller
	 * @var array 
	 */
	protected $_requestParams = array();
	
	
	private $_multiSel;

	// --- Methods ---

	/**
	 * Create a new store
	 * @param string $modelClass the classname of the model to execute the find() method on
	 * @param GO_Base_Data_ColumnModel $columnModel the column model object for formatting this store's columns
	 * @param array $storeParams the $_POST params to set to this store @see setStoreParams()
	 * @param GO_Base_Db_FindParams $findParams extra findParams to be added to the store
	 */
	public function __construct($modelClass, $columnModel, $requestParams, $findParams = null) {

		$this->_modelClass = $modelClass;
		$this->_columnModel = $columnModel;
		$this->_requestParams = $requestParams;
		//$this->setStoreParams($requestParams);
		if ($findParams instanceof GO_Base_Db_FindParams)
			$this->_extraFindParams = $findParams;
		else
			$this->_extraFindParams = GO_Base_Db_FindParams::newInstance();
	}

	/**
	 * Read all parameters that are usable by the store from the actions $params array
	 * The following parametes are accepted:
	 * 'sort:string'
	 * 'dir:string'
	 * 'limit:integer'
	 * 'query:string'
	 * 'delete_keys:array'
	 * 'advancedQueryData:array'
	 * 'forEditing:boolean'
	 */
	private function _readRequestParams() {
		if (isset($this->_requestParams['sort']))
			$this->sort = $this->_requestParams['sort'];

		if (isset($this->_requestParams['dir']))
			$this->direction = $this->_requestParams['dir'];

		if (isset($this->_requestParams['limit']))
			$this->limit = $this->_requestParams['limit'];

		if (isset($this->_requestParams['start']))
			$this->start = $this->_requestParams['start'];

		if (isset($this->_requestParams['query']))
			$this->query = $this->_requestParams['query'];

		if (isset($this->_requestParams['delete_keys']) && $this->allow_delete) { // will be deleted just before loading.
			$this->_deleteRecords = json_decode($this->_requestParams['delete_keys'], true);
			foreach ($this->_deleteRecords as $i => $modelPk) {
				if (is_array($modelPk)) {
					foreach ($modelPk as $col => $val) //format input columnvalues to database
						$modelPk[$col] = GO::getModel($this->_modelClass)->formatInput($col, $val);
					$this->_deleteRecords[$i] = $modelPk;
				}
			}
		}

		if (!empty($this->_requestParams['advancedQueryData']))
			$this->_handleAdvancedQuery($this->_requestParams['advancedQueryData']);

		if (!empty($this->_requestParams["forEditing"]))
			$this->_columnModel->setModelFormatType("formatted");
	}

	/**
	 * FIXME: this method was copied from ModelController and never tested
	 * @param array $advancedQueryData the query data to be set to the store
	 * @param array $storeParams store params to be modied by advancedQuery
	 */
	private function _handleAdvancedQuery($advancedQueryData) {
		$advancedQueryData = is_string($advancedQueryData) ? json_decode($advancedQueryData, true) : $advancedQueryData;
		$findCriteria = $this->_extraFindParams->getCriteria();

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
	protected function createStatement() {
		$params = $this->createFindParams();
		$modelFinder = GO::getModel($this->_modelClass);
		return $modelFinder->find($params);
	}

	/**
	 * Create FindParams object to be passen the this models find() function
	 * If there are extraFind params supplied these well be merged in the end
	 * @return GO_Base_Db_FindParams the created find params to be passen to AR's find() function
	 */
	protected function createFindParams() {

		$sort = !empty($this->_requestParams['sort']) ? $this->_requestParams['sort'] : $this->defaultSort;
		$dir = !empty($this->_requestParams['dir']) ? $this->_requestParams['dir'] : $this->defaultDirection;

		if (!is_array($sort))
			$sort = empty($sort) ? array() : array($sort);

		if (isset($this->_requestParams['groupBy']))
			array_unshift($sort, $this->_requestParams['groupBy']);

		if (!is_array($dir))
			$dir = count($sort) ? array($dir) : array();

		if (isset($this->_requestParams['groupDir']))
			array_unshift($dir, $this->_requestParams['groupDir']);

		$sort = $this->getColumnModel()->getSortColumns($sort);

		$sortCount = count($sort);
		$dirCount = count($dir);
		for ($i = 0; $i < $sortCount - $dirCount; $i++)
			$dir[] = $dir[0];


		$findParams = GO_Base_Db_FindParams::newInstance()
						->joinCustomFields()
						->order($sort, $dir);

		if (empty($this->_requestParams['dont_calculate_total'])) {
			$findParams->calcFoundRows();
		}

		//do not prefix search query with a wildcard by default. 
		//When you start a query with a wildcard mysql can't use indexes.
		//Correction: users can't live without the wildcard at the start.

		if (!empty($this->query))
			$findParams->searchQuery('%' . preg_replace('/[\s*]+/', '%', $this->query) . '%');

		if (isset($this->limit))
			$findParams->limit($this->limit);
		else
			$findParams->limit(GO::user()->max_rows_list);

		if (!empty($this->start))
			$findParams->start($this->start);

		//TODO: check if this is still used by any actionStore()
		if (isset($this->_requestParams['permissionLevel']))
			$findParams->permissionLevel($this->_requestParams['permissionLevel']);

		if (isset($this->_extraFindParams))
			$findParams->mergeWith($this->_extraFindParams);

		return $findParams;
	}

//	public function createDefaultParams() {
//		$sort = !empty($requestParams['sort']) ? $requestParams['sort'] : $this->_defaultSortOrder;
//		$dir = !empty($requestParams['dir']) ? $requestParams['dir'] : $this->_defaultSortDirection;
//
//		if (!is_array($sort))
//			$sort = empty($sort) ? array() : array($sort);
//
//		if (isset($requestParams['groupBy']))
//			array_unshift($sort, $requestParams['groupBy']);
//
//		if (!is_array($dir))
//			$dir = count($sort) ? array($dir) : array();
//
//		if (isset($requestParams['groupDir']))
//			array_unshift($dir, $requestParams['groupDir']);
//
//		$sort = $this->getColumnModel()->getSortColumns($sort);
//
//		$sortCount = count($sort);
//		$dirCount = count($dir);
//		for ($i = 0; $i < $sortCount - $dirCount; $i++) {
//			$dir[] = $dir[0];
//		}
//
//		$sort = array_merge($sort, $this->_extraSortColumnNames);
//		$dir = array_merge($dir, $this->_extraSortDirections);
//
////		for($i=0;$i<count($sort);$i++){
////			$sort[$i] = $this->getColumnModel()->getSortColumn($sort[$i]);
////		}
//
//		$findParams = GO_Base_Db_FindParams::newInstance()
//						->joinCustomFields()
//						->order($sort, $dir);
//
//		if (empty($requestParams['dont_calculate_total'])) {
//			$findParams->calcFoundRows();
//		}
//
//		//do not prefix search query with a wildcard by default. 
//		//When you start a query with a wildcard mysql can't use indexes.
//		//Correction: users can't live without the wildcard at the start.
//
//		if (!empty($requestParams['query']))
//			$findParams->searchQuery('%' . preg_replace('/[\s*]+/', '%', $requestParams['query']) . '%');
//
//		if (isset($requestParams['limit']))
//			$findParams->limit($requestParams['limit']);
//		else
//			$findParams->limit = 0; //(GO::user()->max_rows_list);
//
//		if (!empty($requestParams['start']))
//			$findParams->start($requestParams['start']);
//
//		if (isset($requestParams['permissionLevel']))
//			$findParams->permissionLevel($requestParams['permissionLevel']);
//
//		if ($extraFindParams)
//			$findParams->mergeWith($extraFindParams);
//
//		return $findParams;
//	}

	/**
	 * This method will be called internally before getData().
	 * It will delete all record that has the pk in $_deleteprimaryKey array
	 * @see: GO_Base_Db_Store::processDeleteActions()
	 * @return boolean $success true if all went well
	 */
	protected function processDeleteActions() {
		if (isset($this->_records))
			throw new Exception("deleteRecord should be called before loading data. If you run the statement before the deletes then the deleted items will still be in the result.");

		$errors = array();
		foreach ($this->_deleteRecords as $modelPk) {
			if ($this->extraDeletePk !== null) {
				$primaryKeyNames = GO::getModel($this->_modelClass)->primaryKey(); //get the primary key names of the delete model in an array
				$newPk = array();
				foreach ($primaryKeyNames as $name) {
					if (isset($this->extraDeletePk[$name])) //pk is supplied in the extra values
						$newPk[$name] = $this->extraDeletePk[$name];
					else //it's not set in the extra values so it must be the key passed in the request
						$newPk[$name] = $modelPk;
				}
				$modelPk = $newPk;
			}
			$model = GO::getModel($this->_modelClass)->findByPk($modelPk);
			if (!empty($model)){
				try {
					$key = is_array($model->pk) ? implode('-', $model->pk) : $model->pk;
					if(!$model->delete())
						$errors[$key] = $model->getValidationErrors();
				} catch (GO_Base_Exception_AccessDenied $e) {
					$errors[$key] = array('access_denied'=>$e->getMessage());
				}
			}
		}
		
		if (empty($errors))
			$this->_deleteRecords = array();
		else {
			$error_string = '';
			foreach($errors as $error)
				$error_string .= implode("<br>", $error)."<br>";
			$this->response['feedback'] = str_replace("{count}", count($errors), GO::t('deleteErrors')) . "<br><br>" . $error_string;
		}
		return empty($errors);
	}

	/**
	 * Fetch the next record from the PDO statement.
	 * Format it using the _columnModel's formatMode() function
	 * Or return false if there are no more records
	 * @return GO_Base_Db_ActiveRecord
	 */
	public function nextRecord() {
		if (!isset($this->_stmt))
			$this->_stmt = $this->createStatement();
		
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

		$this->_readRequestParams();

		if (!empty($this->_deleteRecords))
			$this->response['deleteSuccess'] = $this->processDeleteActions();

		if (!isset($this->_stmt))
			$this->_stmt = $this->createStatement();

		$this->_loaded = true;

		$columns = $this->_columnModel->getColumns();
		if (empty($columns))
			throw new Exception('No columns given for this store');

		$this->_records = array();
		while ($record = $this->nextRecord())
			$this->_records[] = $record;

		$this->response['success'] = true;
		$this->response['total'] = $this->getTotal();
		$this->response['results'] = $this->_records;
		return $this->response;
	}

	public function getDeleteSuccess() {
		return isset($this->response['deleteSuccess']) ? $this->response['deleteSuccess'] : null;
	}
	
	public function getFeedBack() {
		return isset($this->response['feedback']) ? $this->response['feedback'] : null;
	}

	/**
	 * Returns an array with the stores records
	 * @return array records
	 */
	public function getRecords() {
		$response = $this->getData();
		return $response['results'];
	}
	
	public function getModels() {
		return $this->_stmt->fetchAll();
	}

	/**
	 * Select Items that belong to one of the selected Models
	 * Call this in the grids that get filterable by other selectable stores
	 * @param string $requestParamName That key that will hold the seleted item in go_setting table
	 * @param string $selectClassName Name of the related model (eg. GO_Notes_Model_Category)
	 * @param string $foreignKey column name to match the related models PK (eg. category_id)
	 * @param boolean $checkPermissions check Permission for item defaults to true
	 */
	public function multiSelect($requestParamName, $selectClassName, $foreignKey, $checkPermissions = true) {
		$this->_multiSel = new GO_Base_Component_MultiSelectGrid(
										$requestParamName,
										$selectClassName,
										$this,
										$this->_requestParams,
										$checkPermissions
		);
		$this->_multiSel->addSelectedToFindCriteria($this->_extraFindParams, $foreignKey);
		$this->_multiSel->setStoreTitle();


	}

	/**
	 * Call this in the selectable stores that effect other grids by selecting values
	 * @param string $requestParamName
	 */
	public function multiSelectable($requestParamName) {
		$this->_multiSel = new GO_Base_Component_MultiSelectGrid($requestParamName, $this->_modelClass, $this, $this->_requestParams);
		$this->_multiSel->setFindParamsForDefaultSelection($this->_extraFindParams);
		$this->_multiSel->formatCheckedColumn();
	}

	/**
	 * The buttons params to be attached to the response
	 * @return array button params
	 */
	public function getButtonParams() {
		$buttonParams = array();
		$this->_multiSel->setButtonParams($buttonParams);
		if (isset($buttonParams['buttonParams']))
			return $buttonParams['buttonParams'];
		else
			return false;
	}

}