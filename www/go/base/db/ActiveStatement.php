<?php

class GO_Base_Db_ActiveStatement extends PDOStatement {

  /**
   * The model type this statement result returns.
   * 
   * @var GO_Base_Db_ActiveRecord 
   */
  public $model;
  
  /**
   * Parameters  that were passed to GO_BaseDb_activeRecord::find()
   * 
   * @var array 
   */
  public $findParams;
  
  /**
   * If the statement was returned by a relational query eg. $model->relationName() then this
   * is set to the relation name.
   * 
   * @var String 
   */
  public $relation;
  
  /**
   * The total number of found rows. Even when specifying a limit it will return 
	 * the number of rows as if you wouldn't have specified a limit.
	 * 
	 * It is only set when calcFoundRows was passed to the GO_Base_Db_ActiveRecord::find() function parameters.
   * 
   * @var int 
   */
  public $foundRows;

  protected function __construct() {
    
  }
	
	/**
	 * Calls the specified function on each model that's in the result set of 
	 * the statement object.
	 * 
	 * @param String $function 
	 */
	public function callOnEach($function){
		//$models = $this->fetchAll();
		while($m = $this->fetch()){		
			if(method_exists($m, $function))
				try{
					$m->$function();
				}catch(Exception $e){
					echo $e->getMessage();
				}
			}
	}
	
//	public function foundRows(){
//		//Total numbers are cached in session when browsing through pages.
//		
//		$queryUid = $this->queryString;
//		if(empty($this->findParams['start'])){
//			
//			$distinct = stripos($this->queryString, 'distinct');
//			$fromPos = stripos($this->queryString, 'from');
//			
//			$sql = "SELECT ";
//			if($distinct)
//				$sql .= 'DISTINCT ';
//			
//			$sql .= 'count(*) as found ';
//			$sql .= substr($this->queryString, $fromPos);
//			
//			$sql = preg_replace('/^LIMIT .*$/mi','', $sql);
//			$sql = preg_replace('/^ORDER BY .*$/mi','', $sql);
//			$sql = preg_replace('/^LEFT JOIN .*$/mi','', $sql);			
//			GO::debug($sql);
//		
//			$r = GO::getDbConnection()->query($sql);
//			$foundRows = GO::session()->values[$queryUid]=intval($r->fetchColumn(0));	
//		}else
//		{
//			$foundRows=GO::session()->values[$queryUid];
//		}
//		
//		return $foundRows;
//	}

}