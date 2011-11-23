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
 * The Store provider is useful to generate response for a grid store in a 
 * controller.
 * @TODO: RENAME THIS STORE TO DBSTORE so it will be GO_Base_Data_DBStore. NEEDS TO BE FIXED IN THE WHOLE PROJECT THEN
 * 

 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.data
 */
class GO_Base_Data_Store extends GO_Base_Data_AbstractStore {
	
  /**
   *
   * @var GO_Base_Db_ActiveStatement 
   */
  private $_stmt;
	
	
	protected $_limit;
	protected $_defaultSortOrder='';
	protected $_defaultSortDirection='ASC';
  
  /**
   *
   * @var array the relation of the given model.  
   */
//  private $_relation;
	
	private $_response;
	

	
	/**
	 * Create a new grid with column model and query result
	 * 
	 * @param GO_Base_Db_ActiveRecord $model
	 * @param array $excludeColumns Exlude columns if you autoload all columns
	 * @param array $excludeColumns Set the columns to load from the model. If ommitted it will load all columns.
	 * @param array $findParams
	 * @return GO_Base_Data_Store 
	 */
	public static function newInstance($model, $excludeColumns=array(), $includeColumns=array())
	{
		$cm = new GO_Base_Data_ColumnModel($model, $excludeColumns, $includeColumns=array());		
		$store = new self($cm);
		return $store;
		
	}
	
	/**
	 * Set the default column to sort on.
	 * @param String / Array $order 
	 */
	public function setDefaultSortOrder($order, $direction){
		$this->_defaultSortOrder=$order;
		$this->_defaultSortDirection=$direction;
	}
	
	
	/**
	 * Set a title response
	 * 
	 * @param String $title 
	 */
	public function setTitle($title){
		$this->_response['title'] = $title;
	}
	

	/**
	 * Set the statement that contains the models for the grid data.
	 * Run the statement after you construct this grid. Otherwise the delete
	 * actions will be ran later and they will still be in the result set.
	 * 
	 * @param GO_Base_Db_ActiveStatement $stmt 
	 */
	public function setStatement(GO_Base_Db_ActiveStatement $stmt){
		$this->_stmt = $stmt;
		
//		if(!$this->_columnModelProvided)
//			$this->_columns = array_merge(array_keys($stmt->model->columns), $this->_columns);
//		
//		if($stmt->model->customfieldsRecord){
//			
//			$cfColumns = array_keys($stmt->model->customfieldsRecord->columns);
//			array_shift($cfColumns); //remove link_id column
//			
//			$this->_columns=array_merge($this->_columns, $cfColumns);
//		}
		
//    if (isset($stmt->relation))
//      $this->_relation = $stmt->relation;
	}
	
  /**
   * Handle a delete request when a grid loads.
   * 
	 * @param array $params The action request params
   * @param type $deleteModelName Name of the model to delete
   * @param array $extraPkValue If your model has more then one pk. Then you can supply the other keys in an array eg. array('group_id'=>1)
   */
	public function processDeleteActions($params, $deleteModelName, $extraPkValue=false){
		
		if(isset($this->_stmt))
			throw new Exception("processDeleteActions should be called before setStatement. If you run the statement before the deletes then the deleted items will still be in the result.");
		
		if (isset($params['delete_keys'])) {
      try {
        $deleteIds = json_decode($params['delete_keys']);
        foreach ($deleteIds as $modelPk) {

//          $deleteModelName = $this->_stmt->model->className();
//
//          //If this is a MANY_MANY relational query. For example when you're displaying the users in a 
//          // group in a grid then you don't want to delete the GO_BAse_Model_User but the linking table record GO_Base_MOdel_UserGroup
//          if (!empty($this->_stmt->relation)) {
//            $relations = $this->stmt->model->relations();
//            if (isset($relations[$this->stmt->relation]['linksModel']))
//              $deleteModelName = $relations[$this->stmt->relation]['linksModel'];
//          }
          $staticModel = call_user_func(array($deleteModelName,'model'));
          if($extraPkValue){           
            
            //get the primary key names of the delete model in an array
            $primaryKeyNames = $staticModel->primaryKey();
            
            $newPk=array();
            foreach($primaryKeyNames as $name){
              
              if(isset($extraPkValue[$name]))
              {
                //pk is supplied in the extra values
                $newPk[$name]=$extraPkValue[$name];
              }else
              {
                //it's not set in the extra values so it must be the key passed in the request
                $newPk[$name]=$modelPk;
              }
            }
            
            $modelPk=$newPk;
          }

          $model = $staticModel->findByPk($modelPk);
          $model->delete();
        }
        $this->_response['deleteSuccess'] = true;
      } catch (Exception $e) {
        $this->_response['deleteSuccess'] = false;
        $this->_response['deleteFeedback'] = $e->getMessage();
      }
    }
	}
	

	public function nextRecord() {
		
		$model = $this->_stmt->fetch();
		
		return $model ? $this->getColumnModel()->formatModel($model) : false;
	}
	
	public function getTotal() {
		return isset($this->_stmt->foundRows) ? $this->_stmt->foundRows : $this->_stmt->rowCount();
	}

  /**
   * Returns the data for the grid.
   * Also deletes the given delete_keys.
   *
   * @return array $this->_response 
   */
  public function getData() {
		
		if(!isset($this->_stmt))
			throw new Exception('You must provide a statement with setStatement()');

		$columns = $this->_columnModel->getColumns();
    if (empty($columns))
      throw new Exception('No columns given for this grid.');   

    
    $this->_response['results'] = array();		
		//$models = $this->_stmt->fetchAll();
		
		//when using this:
		//while ($model = $this->_stmt->fetch()) {
		//I got this error on php 5.2
		//SQLSTATE[HY000]: General error: 2014 Cannot execute queries while other unbuffered queries are active. Consider using PDOStatement::fetchAll(). Alternatively, if your code is only ever going to run against mysql, you may enable query buffering by setting the PDO::MYSQL_ATTR_USE_BUFFERED_QUERY attribute.
		
		while ($record = $this->nextRecord()) {
			$this->_response['results'][] = $record;
		}
		$this->_response['total']=$this->getTotal();


    return $this->_response;
  }
  
  

  /**
   * Returns a set of default parameters for use with a grid.
	 * 
   * @var array $requestParams The request parameters passed to the controller. (Similar to $_REQUEST)
   * @var GO_Base_Db_FindParams $extraFindParams Supply parameters to add to or override the default ones
   * @return GO_Base_Db_FindParams defaultParams 
   */
  public function getDefaultParams($requestParams, $extraFindParams=false) {
		
		$sort = !empty($requestParams['sort']) ? $requestParams['sort'] : $this->_defaultSortOrder;
		
		$sort = $this->getColumnModel()->getSortColumn($sort);
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->calcFoundRows()
						->joinCustomFields()
						->order($sort, !empty($requestParams['dir']) ? $requestParams['dir'] : $this->_defaultSortDirection);
		if(!empty($requestParams['query']))
			$findParams->searchQuery ('%'.preg_replace ('/[\s]+/','%', $requestParams['query']).'%');
		
		if(!empty($requestParams['limit']))
			$findParams->limit ($requestParams['limit']);
		else
			$findParams->limit (GO::user()->max_rows_list);
		
		if(!empty($requestParams['start']))
			$findParams->start ($requestParams['start']);
		
		if(isset($requestParams['permissionLevel']))
			$findParams->permissionLevel ($requestParams['permissionLevel']);
		
		if($extraFindParams)
			$findParams->mergeWith($extraFindParams);
		
		return $findParams;
		
    
  }
}

