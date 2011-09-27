<?php

class GO_Base_Data_ColumnModel {
	
	private $_columns = array();
	
	public function __construct($model=false,$excludeColumns=array()) {
		if($model)
			$this->setColumnsFromModel($model,$excludeColumns);
	}

	

	public function setColumnsFromModel(GO_Base_Db_ActiveRecord $model, $excludeColumns=array()) {
		
		$attributes = $model->getAttributes();
		$columns = array_keys($attributes);

		if($model->customfieldsRecord) {
			$cfAttributes = array_keys($model->customfieldsRecord->columns);
			array_shift($cfAttributes); //remove model_id column
			
			$columns=array_merge($columns, $cfAttributes);
		}
		
		
		foreach($excludeColumns as $excl){
			$offset = array_search($excl, $columns);
			if($offset !== false)
				array_splice($columns, $offset, 1);
		}
		
		$this->_columns=array_merge($this->_columns, $columns);

	}
	
	public function formatColumn($column, $format, $extraVars=array(), $sortfield='') {
		$this->_columns[$column]['format'] = $format;
    $this->_columns[$column]['extraVars'] = $extraVars;
		
		if(!empty($sortfield)){
			$this->_sortFieldsAliases[$column]=$sortfield;
		}
	}
	
	public function getColumns() {
		return $this->_columns;
	}
	
	
}