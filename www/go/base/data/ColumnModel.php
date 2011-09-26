<?php

class GO_Base_Data_ColumnModel {
	
	private $_columns = array();

	public function setColumnsFromModel(GO_Base_Db_ActiveRecord $model) {
		
		$attributes = $model->getAttributes();

		$this->_columns=array_merge($this->_columns, array_keys($attributes));

		if($model->customfieldsRecord) {
			$cfAttributes = array_keys($model->customfieldsRecord->columns);
			array_shift($cfAttributes); //remove link_id column
			
			$this->_columns=array_merge($this->_columns, $cfAttributes);
		}
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