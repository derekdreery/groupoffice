<?php
/*
 * 
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 */

/**
 * An extended version of the PDOStatement PHP class that provides extra
 * functionality.
 * 
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl> 
 * @package GO.base.db
 */

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
   * Calculate the number of found rows when using a limit and calcFoundRows.
	 *
	 * Note: for simply counting the number of records in a statement use rowCount();
	 *  
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
	public function callOnEach($function, $verbose=false){
		//$models = $this->fetchAll();
		$i=0;
		while($m = $this->fetch()){		
			if(method_exists($m, $function))
				try{
					if($verbose){
						echo ($i++)." $function ".$m->className()."\n";
						flush();
					}
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
	
	/**
	 * Get the result as a key->value array.
	 * 
	 * You need to specify which column needs to be used as key column and which 
	 * culumn needs to be used as value column
	 * 
	 * @param string $keyColumn
	 * @param string $valueColumn
	 * @return array 
	 */
	public function fetchKeyValueArray($keyColumn, $valueColumn){
		$array = array();
		
		while($m = $this->fetch()){	
			$array[$m->$keyColumn] = $m->$valueColumn;
		}
		
		return $array;
	}
	

}