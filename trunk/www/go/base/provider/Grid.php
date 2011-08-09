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
  private $_relation = NULL;

  /**
   * See function formatColumn for a detailed description about how to use the format parameter.
   * 
   * @param GO_Base_Db_ActiveStatement $stmt
   * @param array $columns eg. array('username', 'date'=>array('format'=>'date("Ymd", $date)'))
   */
  public function __construct(GO_Base_Db_ActiveStatement $stmt, $columns=array()) {

    $this->_stmt = $stmt;
    
    $this->_columns = count($columns) ? $columns :  array_keys($stmt->model->columns);
		
		if($stmt->model->customfieldsRecord){
			
			$cfColumns = array_keys($stmt->model->customfieldsRecord->getAttributes());
			array_shift($cfColumns); //remove link_id column
			
			$this->_columns=array_merge($this->_columns, $cfColumns);
		}
		
    if (isset($stmt->relation))
      $this->_relation = $stmt->relation;
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
   * @param type $column
   * @param string $format 
   */
  public function formatColumn($column, $format) {
//    if (!isset($this->_columns[$column]))
//      throw new Exception('Column ' . $column . ' does not exist in ' . $this->stmt);

    $this->_columns[$column]['format'] = $format;
  }

  
  /**
   * Returns the data for the grid.
   * Also deletes the given delete_keys.
   *
   * @return array $response 
   */
  public function getData() {

    if (empty($this->_columns))
      throw new Exception('No columns given for this grid.');


    if (isset($_POST['delete_keys'])) {
      try {
        $deleteIds = json_decode($_POST['delete_keys']);
        foreach ($deleteIds as $modelPk) {

          $deleteModelName = $this->_stmt->model->className();

          //If this is a MANY_MANY relational query. For example when you're displaying the users in a 
          // group in a grid then you don't want to delete the GO_BAse_Model_User but the linking table record GO_Base_MOdel_UserGroup
          if (!empty($this->_stmt->relation)) {
            $relations = $this->stmt->model->relations();
            if (isset($relations[$this->stmt->relation]['linksModel']))
              $deleteModelName = $relations[$this->stmt->relation]['linksModel'];
          }

          $model = $deleteModelName::model()->findByPk($modelPk);
          $model->delete();
        }
        $response['deleteSuccess'] = true;
      } catch (Exception $e) {
        $response['deleteSuccess'] = false;
        $response['deleteFeedback'] = $e->getMessage();
      }
    }

    
    $response['results'] = array();
    $response['total']=$this->_stmt->foundRows;

		while ($model = $this->_stmt->fetch()) {
			$response['results'][] = $this->formatModelForGrid($model);
		}


    return $response;
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
      if(isset($attributes['format'])){
        eval('$result='.$attributes['format'].';');
        $formattedRecord[$colName]=$result;
      }else
        $formattedRecord[$colName]=$array[$colName];
    }
    
    return $formattedRecord;
  }

  /**
   *  Returns a set of default parameters for use with a grid.
   * 
   * @return array defaultParams 
   */
  public static function getDefaultParams() {
    return array(
        'searchQuery' => !empty($_REQUEST['query']) ? '%' . $_REQUEST['query'] . '%' : '',
        'limit' => isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 0,
        'start' => isset($_REQUEST['start']) ? $_REQUEST['start'] : 0,
        'orderField' => isset($_REQUEST['orderField']) ? $_REQUEST['orderField'] : '',
        'orderDirection' => isset($_REQUEST['orderDirection']) ? $_REQUEST['orderDirection'] : '',
				'joinCustomFields'=>true,
        'calcFoundRows'=>true
    );
  }

}

