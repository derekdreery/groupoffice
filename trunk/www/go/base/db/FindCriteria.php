<?php
class GO_Base_Db_FindCriteria {
	
	private $_condition='';
	
	private static $_paramCount = 0;
	
	private $_paramPrefix = ':go';
	
	private $_params=array();
	
	private $_columns;
		
	/**
	 * 
	 * @return GO_Base_Db_FindCriteria 
	 */
	public static function newInstance(){
		return new self;
	}
	
	/**
	 * Add a model to the criteria object so it can determine of which PDO type a column is.
	 * You can also give an alias with it. If not then the alias defaults to "t".
	 * 
	 * @param GO_Base_Db_ActiveRecord $model
	 * @param String $tableAlias 
	 */
	public function addModel($model, $tableAlias='t'){
		$this->_columns[$tableAlias]=$model->getColumns();
		return $this;
	}
	
	
	/**
	 * A
	 * 
	 * @param Boolean $useAnd 
	 */
	private function _appendOperator($useAnd){
		if($this->_condition!='')
			$this->_condition .= $useAnd ? ' AND' : ' OR';
		
	}
	
	private function _getPdoType($tableAlias, $field){
		if(isset($this->_columns[$tableAlias][$field]['type']))
			$type = $this->_columns[$tableAlias][$field]['type'];
		else{
			$type= PDO::PARAM_STR;
			GO::debug("WARNING: Could not find column type for $tableAlias. $field in GO_Base_Db_FindCriteria. Using PDO::PARAM_STR. Do you need to use addModel?");
		}
		return $type;
	}
	
	private function _appendConditionString($tableAlias, $field, $value, $comparator){
		$paramTag = $this->_getParamTag();
		
		$this->_params[$paramTag]=array($value, $this->_getPdoType($tableAlias, $field));
		$this->_condition .= ' `'.$tableAlias.'`.`'.$field.'` '.$comparator.' '.$paramTag;
	}
	
	public function addCondition($field, $value, $comparator='=',$tableAlias='t', $useAnd=true) {
		
		$this->_appendOperator($useAnd);
		$this->_appendConditionString($tableAlias, $field, $value, $comparator);		
		return $this;
	}
	
	
	public function addInCondition($field, $value, $tableAlias='t', $useAnd=true, $useNot=false) {	
				
		if(!is_array($value))
			throw new Exception("ERROR: Value for addInCondition must be an array");
		
		$this->_appendOperator($useAnd);
		$comparator = $useNot ? 'NOT IN' : 'IN';
		
		$paramTags=array();
		foreach($value as $val){
			$paramTag = $this->_getParamTag();
			$paramTags[]=$paramTag;
			$this->_params[$paramTag]=array($val, $this->_getPdoType($tableAlias, $field));
		}
		
		$this->_condition .= ' `'.$tableAlias.'`.`'.$field.'` '.$comparator.' ('.implode(',',$paramTags).')';
		
		return $this;
		
	}
	
	
	public function addSearchCondition($field, $value, $useAnd=true, $useNot=false, $useExact=false) {
		
		$this->_appendOperator($useAnd);
		
		$comparator = $useNot ? 'NOT LIKE' : 'LIKE';
		$exact = $useExact ? '%' : '';
		$value = $exact.$value.$exact;
		
		$this->_appendConditionString($tableAlias, $field, $value, $comparator);	
	
		return $this;
	}

	private function _getParamTag() {
		self::$_paramCount++;
		return $this->_paramPrefix.self::$_paramCount;
	}
	
	public function getCondition() {
		return $this->_condition;
	}
	
	public function getParams() {
		return $this->_params;
	}
	
	
	public function mergeWith(GO_Base_Db_FindCriteria $criteria, $useAnd=true) {
		$operator = $useAnd ? 'AND' : 'OR';
		$this->_condition = '('.$this->_condition.') '.$operator.' ('.$criteria->getCondition().')';
		$this->_params = array_merge($this->_params, $criteria->getParams());
		return $this;
	}
}