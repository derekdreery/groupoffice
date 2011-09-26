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
 * The Grid provider is useful to generate response for a grid store in a 
 * controller.
 * 
 * @version $Id: Group.php 7607 2011-08-04 13:41:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.provider
 */
class GO_Base_Provider_Grid extends GO_Base_Provider_Abstract {

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
   *
   * @param GO_Base_Db_ActiveRecord $model
   * @return array formatted grid row key value array
   */
  public function formatModelForGrid($model){
		
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
		//$models = $this->_stmt->fetchAll();
		
		//when using this:
		//while ($model = $this->_stmt->fetch()) {
		//I got this error on php 5.2
		//SQLSTATE[HY000]: General error: 2014 Cannot execute queries while other unbuffered queries are active. Consider using PDOStatement::fetchAll(). Alternatively, if your code is only ever going to run against mysql, you may enable query buffering by setting the PDO::MYSQL_ATTR_USE_BUFFERED_QUERY attribute.
		
		while ($model = $this->_stmt->fetch()) {
			$this->_response['results'][] = $this->formatModelForGrid($model);
		}
		$this->_response['total']=$this->_stmt->foundRows;


    return $this->_response;
  }
}