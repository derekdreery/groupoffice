<?php
class GO_Base_Db_FindCriteria {
	
	public $joinCustomFields=false;
	
	/**
	 * Set to true 
	 * 
	 * @var boolean 
	 */
	public $ignoreAcl=false;
	public $userId;
	public $permissionLevel;
	
	public $joins=array();
	
	/**
	 * Add a search query on all text fields.
	 * 
	 * @var String 
	 */
	public $searchQuery="";
	
	/**
	 * Set tot true to return the number of foundRows in the statement (See class GO_Base_Db_ActiveStatement 
	 * 
	 * @var boolean 
	 */
	public $calcFoundRows=false;
	
	
	public $limit=0;
	public $start=0;
	
	/**
	 * The model of a linking table passed in case of a MANY_MANY relation query
	 * @var String 
	 */
	public $linkModel;
	
	/**
	 * The name of the key to link the linking table too in a many_many relational query.
	 * @var type 
	 */
	public $linkModelLocalField;
	public $linkModelLocalPk;
	public $relation;
	
	public $orderField;
	public $orderDirection;
	
	/**
	 *
	 * @param type $params 
	 * 
	 * array(
	 * type=>'LEFT'
	 * table1=>'go_users_groups'
	 * table2=>'go_acl'
	 * key1=>'acl_id',
	 * key2=>'acl_id'
	 * )
	 * 
	 */
	public function addJoin($params){
		$this->joins[]=$params;
	}
	
	/**
	 *
	 * @param $params $params 
	 * 
	 * array(array('field','value','=')),
	 *  "byOperator"=>[AND / OR]
	 */
	public function addBy($params, $byOperator='AND'){
		$this->by[]=array($params, $byOperator);
	}
	
	
	public static function newInstance($params=array()){
		
		$f = new GO_Base_Db_FindCriteria();
		
		if(isset($params['by']))
		{
			$f->addBy($params['by']);
			unset($params['by']);
		}
		
		foreach($params as $key=>$value)
		{
			$f->$key=$value;
		}
		
		return $f;
		
	}
	
}