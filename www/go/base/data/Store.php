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
 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.provider
 */
class GO_Base_Data_Store {

  /**
   * Holds the columns from model. See GO_Base_Db_ActiveRecord::$columns for more info.
   * This array may be extended with a format function.
   * 
   * @var array 
   */
  private $_columns;
  
  /**
   *
   * @var GO_Base_Db_ActiveStatement 
   */
  private $_stmt;
  
  /**
   *
   * @var array the relation of the given model.  
   */
  private $_relation;
	
	private $_response;
	
	private $_sortFieldsAliases=array();
	
	private $_cm=false;
	
	private $_modelFormatType='formatted';

	private $_defaultSortOrder='';
	private $_defaultSortDirection='ASC';
	
  /**
   * See function formatColumn for a detailed description about how to use the format parameter.
   *
   * @param array $columns eg. array('username', 'date'=>array('format'=>'date("Ymd", $date)'))
   */
  public function __construct($columnModel=false) {        
		if($columnModel)
			$this->_cm = $columnModel;
		else
			$this->_cm = new GO_Base_Data_ColumnModel();
  }
	
	/**
	 * Create a new grid with column model and query result
	 * 
	 * @param GO_Base_Db_ActiveRecord $model
	 * @param array $excludeColumns
	 * @param array $findParams
	 * @return GO_Base_Data_Store 
	 */
	public static function newInstance($model, $excludeColumns=array(), $findParams=false)
	{
		$cm = new GO_Base_Data_ColumnModel($model, $excludeColumns);		
		$store = new self($cm);
		if($findParams)
			$store->setStatement ($model->find($findParams));
		
		return $store;
		
	}
	
	/**
	 * Returns the column model
	 * 
	 * @return GO_Base_Data_ColumnModel 
	 */
	public function getColumnModel(){
		return $this->_cm;
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
	 * Set the default column to sort on.
	 * @param String / Array $order 
	 */
	public function setDefaultSortOrder($order, $direction){
		$this->_defaultSortOrder=$order;
		$this->_defaultSortDirection=$direction;
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
		
    if (isset($stmt->relation))
      $this->_relation = $stmt->relation;
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
	
	/**
	 * Set the format type used in the GO_Base_Db_ActiveRecord
	 * @param string $type @see GO_Base_Db_ActiveRecord::getAttributes()
	 */
	public function setModelFormatType($type){
		$this->_modelFormatType=$type;
	}

  /**
   * Add columns to the grid and give the format in how to parse the value of this column.
   * You can also use this function to set the format of an existing column.
   * 
   * The format can be parsed as normal php. (For example: formatColumn('read_date','date("d-m-Y")');)
	 * 
	 * You can use any model attribute name as a variable and you also have the $model variable available.
	 * 
	 * Example formatColumn('Special name','$model->getSpecialName()');
   * 
   * @param type $column   * 
   * @param string $format 
   * @param array $extraVars 
   * 
   * Add extra variables like this for example array('controller'=>$this) in a controller.
   * 
   * Then you can use '$controller->aControllerProperty' in the column format.
	 * 
	 * @param $sortfield
	 * 
	 * Set a sort field. Sometimes you need construct a column from multiple columns
	 * Like user->name is a concatenation of first,middle and last.
	 * In that case you can set sortfield to: array('first_name','last_name')
   * 
   */
//  public function formatColumn($column, $format, $extraVars=array(), $sortfield='') {
//
//    $this->_columns[$column]['format'] = $format;
//    $this->_columns[$column]['extraVars'] = $extraVars;
//		
//		if(!empty($sortfield)){
//			$this->_sortFieldsAliases[$column]=$sortfield;
//		}
//  }
//	  
  /**
   * Returns the data for the grid.
   * Also deletes the given delete_keys.
   *
   * @return array $this->_response 
   */
  public function getData() {
		
		if(!isset($this->_stmt))
			throw new Exception('You must provide a statement with setStatement()');

		$columns = $this->_cm->getColumns();
    if (empty($columns))
      throw new Exception('No columns given for this grid.');   

    
    $this->_response['results'] = array();		
		//$models = $this->_stmt->fetchAll();
		
		//when using this:
		//while ($model = $this->_stmt->fetch()) {
		//I got this error on php 5.2
		//SQLSTATE[HY000]: General error: 2014 Cannot execute queries while other unbuffered queries are active. Consider using PDOStatement::fetchAll(). Alternatively, if your code is only ever going to run against mysql, you may enable query buffering by setting the PDO::MYSQL_ATTR_USE_BUFFERED_QUERY attribute.
		
		while ($model = $this->_stmt->fetch()) {
			$this->_response['results'][] = $this->formatModelForStore($model);
		}
		$this->_response['total']=$this->_stmt->foundRows;


    return $this->_response;
  }
  
  /**
   *
   * @param GO_Base_Db_ActiveRecord $model
   * @return array formatted grid row key value array
   */
  public function formatModelForStore($model){
		
		$oldLevel = error_reporting(E_ERROR);	//suppress errors in the eval'd code
    
    $array = $model->getAttributes($this->_modelFormatType);
    
    /**
     * The extract function makes the array keys available as variables in the current function scope.
     * we need this for the eval functoion.
     * 
     * example $array = array('name'=>'Pete'); becomes $name='Pete';
     * 
     * In the column definition we can supply a format like this:
     * 
     * 'format'=>'$name'
     */
    extract($array);
		
    
    $formattedRecord = array();
		$columns = $this->_cm->getColumns();
		
    foreach($columns as $colName=>$attributes)
    {     
      if(!is_array($attributes)){
        $colName=$attributes;
        $attributes=array();
      }
      
      if(isset($attributes['extraVars'])){
        extract($attributes['extraVars']);
      }     
      
      if(isset($attributes['format'])){
				$result = '';
        eval('$result='.$attributes['format'].';');
        $formattedRecord[$colName]=$result;
      }elseif(isset($array[$colName]))
        $formattedRecord[$colName]=$array[$colName];
    }
		
		error_reporting($oldLevel);
		
		if(isset($this->_formatRecordFunction)){
			$formattedRecord=call_user_func($this->_formatRecordFunction, $formattedRecord, $model, $this);
		}
    
    return $formattedRecord;
  }
	
	/**
	 * Set a function that will be called with call_user_func to format a record.
	 * The function will be called with parameters:
	 * 
	 * Array $formattedRecord, GO_Base_Db_ActiveRecord $model, GO_Base_Data_Store $store
	 * 
	 * @param mixed $func Function name string or array($object, $functionName)
	 */
	public function setFormatRecordFunction($func){
		$this->_formatRecordFunction=$func;
	}

  /**
   * Returns a set of default parameters for use with a grid.
   * 
   * @var array $params Supply parameters to add to or override the default ones
   * @return array defaultParams 
   */
  public function getDefaultParams($params=array()) {
		
		$sort = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : $this->_defaultSortOrder;
		
		if(isset($this->_sortFieldsAliases[$sort]))
			$sort=$this->_sortFieldsAliases[$sort];
		
    return array_merge(array(
        'searchQuery' => !empty($_REQUEST['query']) ? '%' . $_REQUEST['query'] . '%' : '',
        'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : GO::user()->max_rows_list,
        'start' => isset($_REQUEST['start']) ? $_REQUEST['start'] : 0,
        'order' => $sort,
        'orderDirection' => !empty($_REQUEST['dir']) ? $_REQUEST['dir'] : $this->_defaultSortDirection,
				'joinCustomFields'=>true,
        'calcFoundRows'=>true,
				'permissionLevel'=> isset($_REQUEST['permissionLevel']) ? $_REQUEST['permissionLevel'] : GO_Base_Model_Acl::READ_PERMISSION
    ), $params);
  }

}

