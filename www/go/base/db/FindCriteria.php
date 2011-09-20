<?php
class GO_Base_Db_FindCriteria {
	
	private $_condition='';
	
	private static $_paramCount = 0;
	
	private $_paramPrefix = ':go';
	
	private $_params;
	
	public function addCondition($field, $value, $comparator='=', $useAnd=true) {
		
		if($this->_condition!='')
			$this->_condition .= $useAnd ? ' AND' : ' OR';
		
		$paramTag = $this->_getParamTag();
		
		$this->_condition .= '`'.$field.'` '.$comparator.' '.$paramTag.'';
		$this->_params[$paramTag]=$value;
		
		return $this;
	}
	
	// TODO: Create function
	public function addInCondition($field, $value, $useAnd=true) {
		
	}
	
	// TODO: Create function
	public function searchCondition($field, $value, $useAnd=true) {
		
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