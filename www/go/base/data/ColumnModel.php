<?php

/*
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * The ColumnModel is useful to generate a columnListing that can be used in Stores
 * 
 * @version $Id: ColumnModel.php 7607 2011-08-04 13:41:42Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits <wsmits@intermesh.nl>
 * @package GO.base.data
 */
class GO_Base_Data_ColumnModel {

	/**
	 * The columns that are defined in this column model
	 *
	 * @var Array 
	 */
	private $_columns;
	
	private $_columnSort;
	
	//private $_sortFieldsAliases=array();

	private $_modelFormatType='html';
	
	private $_model;
	
	/**
	 * Constructor of the ColumnModel class.
	 * 
	 * Use this to constructor a new ColumnModel. You can give two parameters.
	 * If you give the $model param then the columns of that model are set automatically in this columnModel.
	 * The public parameters and the customfield parameters are also set.
	 * The $excludeColumns are meant to give up the column names that need to be excluded in the columnModel.
	 * 
	 * @param string $modelName The models where to get the columns from.
	 * @param Array $excludeColumns 
	 */
	public function __construct($modelName=false, $excludeColumns=array(), $includeColumns=array()) {
		if ($modelName){
			
			if(is_string($modelName)){
				$modelName = GO::getModel($modelName);
			}
			
			$this->setColumnsFromModel($modelName, $excludeColumns, $includeColumns);
			$this->_model=$modelName;
		}
	}

	/**
	 * Add a model to the ColumnModel class.
	 * 
	 * Give this ColumnModel class a model where to get the columns from.
	 * The public parameters and the customfield parameters are also set.
	 * The $excludeColumns are meant to give up the column names that need to be excluded in the columnModel.
	 * 
	 * @TODO: The text parameters need to be excluded.
	 * 
	 * @param GO_Base_Db_ActiveRecord $model
	 * @param Array $excludeColumns 
	 */
	public function setColumnsFromModel(GO_Base_Db_ActiveRecord $model, $excludeColumns=array(), $includeColumns=array()) {

			$attributes = $model->getColumns();

			foreach (array_keys($attributes) as $colName) {					
				if(!in_array($colName, $excludeColumns)){					
					$sortIndex = empty($includeColumns) ? 0 : array_search($colName, $includeColumns);				
					if($sortIndex!==false){
						$column = new GO_Base_Data_Column($colName, $model->getAttributeLabel($colName),$sortIndex);					
						$this->addColumn($column);
					}
				}
			}

			if ($model->customfieldsRecord) {
				$cfAttributes = array_keys($model->customfieldsRecord->columns);
				array_shift($cfAttributes); //remove model_id column

				foreach ($cfAttributes as $colName) {
					if(!in_array($colName, $excludeColumns)){
					
						$sortIndex = empty($includeColumns) ? 0 : array_search($colName, $includeColumns);				
						if($sortIndex!==false){
							$column = new GO_Base_Data_Column($colName, $model->customfieldsRecord->getAttributeLabel($colName), $sortIndex);
							$this->addColumn($column);
						}
					}
				}
			}
	}

	/**
	 * Set a new displayformat for the given column.
	 * 
	 * You need to give the column name of where the displayformat needs to be changed.
	 * Then you need to give the new displayFormat. This is a string with the format in it.
	 * 
	 * Eg. '$model->user->name' or '$model->task->name'
	 * The user and task are related models of the given $model.
	 *
	 *
	 * @param String $column
	 * @param String $format
	 * @param Array $extraVars Optional and can include extra params that are needed for the $format.
	 * @param String $sortAlias A string or array of columns to sort on if this column is sorted on.
	 * @param string $label Label to use on exports.
	 * 
	 * @return GO_Base_Data_ColumnModel
	 */
	public function formatColumn($column, $format, $extraVars=array(), $sortAlias='', $label='') {		
		
		
		if(empty($label) && $existingColumn = $this->getColumn($column)){			
			$label = $existingColumn->getLabel();
		}
		
		
		$column = new GO_Base_Data_Column($column, $label);
		$column->setFormat($format, $extraVars);
		if(!empty($sortAlias))
			$column->setSortAlias($sortAlias);
		
		$this->addColumn($column);
//		$this->_columns[$column]['format'] = $format;
//		$this->_columns[$column]['extraVars'] = $extraVars;
//		$this->_columns[$column]['label'] = empty($label) ? $column : $label;
//
//		if (!empty($sortfield)) {
//			$this->_sortFieldsAliases[$column] = $sortfield;
//		}
		
		return $this;
	}
	
	/**
	 *
	 * @param GO_Base_Data_Column $column
	 * @return GO_Base_Data_ColumnModel 
	 */
	public function addColumn(GO_Base_Data_Column $column){
		$this->_columns[$column->getDataIndex()]=$column;
		$this->_columnSort[$column->getDataIndex()]=$column->getSortIndex();
		
		return $this;
	}

	
	/**
	 * Get the columns of this columnModel.
	 * 
	 * This function returns all columns that are set in this columnModel as an array.
	 *  
	 * @return GO_Base_Data_Column[]
	 */
	public function getColumns() {
		$this->_sortColumns();					
		return $this->_columns;
	}
	
	/**
	 * Get a column by data index
	 * 
	 * @param string $dataindex
	 * @return GO_Base_Data_Column 
	 */
	public function getColumn($dataindex){
		
		if(empty($this->_columns[$dataindex]))
			return false;
		return $this->_columns[$dataindex];
	}
	
	public function sort($columnNames){
		
		
		for($i=0;$i<count($columnNames);$i++){
			$column = $this->getColumn($columnNames[$i]);
			if($column){
				$column->setSortIndex($i);
				$this->_columnSort[$columnNames[$i]]=$i;
			}
		}
		
		unset($this->_columnsSorted);
		$this->_sortColumns();
		
	}
	
	
	/**
	 * Give an array with the columnheaders in the order that you want.
	 * The existing columns will be ordered to the given columnheaders array.
	 * 
	 * Columns that are set in the existing columns and that are not given in the 
	 * culumnNames array will be pasted at the end.
	 * 
	 * @param array $columnNames  
	 */
	private function _sortColumns(){
		
		if(!isset($this->_columnsSorted)){
			asort($this->_columnSort);	

			$sorted = array();
			foreach($this->_columnSort as $column=>$sort){
				$sorted[$column] = $this->_columns[$column];
			}
			$this->_columns = $sorted;
			unset($this->_columnSort);
			
			$this->_columnsSorted=true;
		}
	}

	/**
	 * Turn a sort alias into the real column name. 
	 * 
	 * @param string $alias
	 * @return mixed String or array of columns 
	 */
	public function getSortColumn($alias) {		
		return isset($this->_columns[$alias]) ? $this->_columns[$alias]->getSortColumn() : $alias;
	}
	
	/**
	 * Turn an array of sort aliases into an array of the real column names.
	 * 
	 * @param array $aliases
	 * @return array 
	 */
	public function getSortColumns($aliases){
		$columns = array();
			for($i=0;$i<count($aliases);$i++){
				 $column = $this->getSortColumn($aliases[$i]);
				 if(is_array($column))
				 {					 
					 $columns = array_merge($columns, $column);
				 }else
				 {
					 $columns[]=$column;
				 }
			}
			return $columns;
	}

	public function removeColumn($columnName) {
		unset($this->_columns[$columnName]);
		unset($this->_columnSort[$columnName]);
	}

	
	public function resetColumns($columns) {
		$this->_columns = $columns;
		$this->_columnSort=array_keys($columns);
	}

	/**
	 *
	 * @param GO_Base_Model $model
	 * @return array formatted grid row key value array
	 */
	public function formatModel($model) {

		$oldLevel = error_reporting(E_ERROR); //suppress errors in the eval'd code
		
		$formattedRecord = array();
		if($model instanceof GO_Base_Db_ActiveRecord)
		  $formattedRecord = $model->getAttributes($this->_modelFormatType);
		$columns = $this->getColumns();

		foreach($columns as $column){	
			
			$column->setModelFormatType($this->_modelFormatType);
			
			$formattedRecord[$column->getDataIndex()]=$column->render($model);			
		}
			
		error_reporting($oldLevel);		
		
		if (isset($this->_formatRecordFunction)){
			$formattedRecord = call_user_func($this->_formatRecordFunction, $formattedRecord, $model, $this);
			
			if(!$formattedRecord){
				if(is_array($this->_formatRecordFunction)){
					$str = $this->_formatRecordFunction[1];
				}else
				{
					$str = $this->_formatRecordFunction;
				}
				throw new Exception("Fatal error: $str should return the record");
			}
		}

		return $formattedRecord;
	}

	/**
	 * Set the format type used in the GO_Base_Db_ActiveRecord
	 * @param string $type @see GO_Base_Db_ActiveRecord::getAttributes()
	 */
	public function setModelFormatType($type) {
		$this->_modelFormatType = $type;
	}

	/**
	 * Set a function that will be called with call_user_func to format a record.
	 * The function will be called with parameters:
	 * 
	 * Array $formattedRecord, GO_Base_Db_ActiveRecord $model, GO_Base_Data_ColumnModel $cm
	 * 
	 * @param mixed $func Function name string or array($object, $functionName)
	 * 
	 * @return GO_Base_Data_ColumnModel
	 */
	public function setFormatRecordFunction($func) {
		$this->_formatRecordFunction = $func;
		
		return $this;
	}
	
	public function getColumnCount() {
		return count($this->_columns);
	}
}