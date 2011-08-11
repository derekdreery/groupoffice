<?php

class GO_Base_Provider_Grid {

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
	
	private $_formatVariables=array();

  /**
   * See function formatColumn for a detailed description about how to use the format parameter.
   *
   * @param array $columns eg. array('username', 'date'=>array('format'=>'date("Ymd", $date)'))
   */
  public function __construct($columns=array()) {        
    $this->_columns = $columns;	
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
		
		if(!count($this->_columns))
			$this->_columns = array_keys($stmt->model->columns);
		
		if($stmt->model->customfieldsRecord){
			
			$cfColumns = array_keys($stmt->model->customfieldsRecord->getAttributes());
			array_shift($cfColumns); //remove link_id column
			
			$this->_columns=array_merge($this->_columns, $cfColumns);
		}
		
    if (isset($stmt->relation))
      $this->_relation = $stmt->relation;
	}
	
  /**
   * Handle a delete request when a grid loads.
   * 
   * @param type $deleteModelName Name of the model to delete
   * @param array $extraPkValue If your model has more then one pk. Then you can supply the other keys in an array eg. array('group_id'=>1)
   */
	public function processDeleteActions($deleteModelName, $extraPkValue=false){
		
		if(isset($this->_stmt))
			throw new Exception("processDeleteActions should be called before setStatement. If you run the statement before the deletes then the deleted items will still be in the result.");
		
		if (isset($_POST['delete_keys'])) {
      try {
        $deleteIds = json_decode($_POST['delete_keys']);
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
          $staticModel = $deleteModelName::model();
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
   */
  public function formatColumn($column, $format, $extraVars=array()) {

    $this->_columns[$column]['format'] = $format;
    $this->_columns[$column]['extraVars'] = $extraVars;
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

    if (empty($this->_columns))
      throw new Exception('No columns given for this grid.');   

    
    $this->_response['results'] = array();
    $this->_response['total']=$this->_stmt->foundRows;

		while ($model = $this->_stmt->fetch()) {
			$this->_response['results'][] = $this->formatModelForGrid($model);
		}


    return $this->_response;
  }
  
  /**
   *
   * @param GO_Base_Db_ActiveRecord $model
   * @return array formatted grid row key value array
   */
  public function formatModelForGrid($model){
    
    $array = $model->getAttributes();
    
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
    foreach($this->_columns as $colName=>$attributes)
    {     
      if(!is_array($attributes)){
        $colName=$attributes;
        $attributes=array();
      }
      
      if(isset($attributes['extraVars'])){
        extract($attributes['extraVars']);
      }     
      
      if(isset($attributes['format'])){
        eval('$result='.$attributes['format'].';');
        $formattedRecord[$colName]=$result;
      }elseif(isset($array[$colName]))
        $formattedRecord[$colName]=$array[$colName];
    }
    
    return $formattedRecord;
  }

  /**
   * Returns a set of default parameters for use with a grid.
   * 
   * @var array $params Supply parameters to add to or override the default ones
   * @return array defaultParams 
   */
  public static function getDefaultParams($params=array()) {
    return array_merge(array(
        'searchQuery' => !empty($_REQUEST['query']) ? '%' . $_REQUEST['query'] . '%' : '',
        'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 0,
        'start' => isset($_REQUEST['start']) ? $_REQUEST['start'] : 0,
        'orderField' => isset($_REQUEST['orderField']) ? $_REQUEST['orderField'] : '',
        'orderDirection' => isset($_REQUEST['orderDirection']) ? $_REQUEST['orderDirection'] : '',
				'joinCustomFields'=>true,
        'calcFoundRows'=>true
    ), $params);
  }

}

