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
	private $_columns = array();
	
	private $_sortFieldsAliases=array();

	private $_modelFormatType='formatted';

	/**
	 * Constructor of the ColumnModel class.
	 * 
	 * Use this to constructor a new ColumnModel. You can give two parameters.
	 * If you give the $model param then the columns of that model are set automatically in this columnModel.
	 * The public parameters and the customfield parameters are also set.
	 * The $excludeColumns are meant to give up the column names that need to be excluded in the columnModel.
	 * 
	 * @param GO_Base_Db_ActiveRecord $model The models where to get the columns from.
	 * @param Array $excludeColumns 
	 */
	public function __construct($model=false, $excludeColumns=array(), $includeColumns=array()) {
		if ($model)
			$this->setColumnsFromModel($model, $excludeColumns, $includeColumns);
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

		if (!count($includeColumns)) {
			$attributes = $model->getAttributes();
			$columns = array();
			foreach (array_keys($attributes) as $colName) {
				$columns[$colName] = array(
						'label' => $model->getAttributeLabel($colName)
				);
			}

			if ($model->customfieldsRecord) {
				$cfAttributes = array_keys($model->customfieldsRecord->columns);
				array_shift($cfAttributes); //remove model_id column

				foreach ($cfAttributes as $colName) {
					$columns[$colName] = array(
							'label' => $model->customfieldsRecord->getAttributeLabel($colName)
					);
				}
			}
		} else {
			$columns = $includeColumns;
		}

		foreach ($excludeColumns as $excl) {
			$offset = array_search($excl, $columns);
			if ($offset !== false)
				array_splice($columns, $offset, 1);
		}

		$this->_columns = array_merge($this->_columns, $columns);
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
	 * The extraVars param is optional an can include extra params that are needed for the $format.
	 * The sortfield param is optional an can be set if you want to set the default field for Sorting the columns
	 *  
	 * @todo column object
	 * 
	 * @param String $column
	 * @param String $format
	 * @param Array $extraVars
	 * @param String $sortfield 
	 */
	public function formatColumn($column, $format, $extraVars=array(), $sortfield='', $label='') {
		$this->_columns[$column]['format'] = $format;
		$this->_columns[$column]['extraVars'] = $extraVars;
		$this->_columns[$column]['label'] = empty($label) ? $column : $label;

		if (!empty($sortfield)) {
			$this->_sortFieldsAliases[$column] = $sortfield;
		}
	}

	/**
	 * Get the columns of this columnModel.
	 * 
	 * This function returns all columns that are set in this columnModel as an array.
	 *  
	 * @return Array 
	 */
	public function getColumns() {
		return $this->_columns;
	}

	public function getSortAlias($alias) {
		if (isset($this->_sortFieldsAliases[$alias]))
			$alias = $this->_sortFieldsAliases[$alias];

		return $alias;
	}

	public function removeColumn($columnName) {
		unset($this->_columns[$columnName]);
	}

	public function resetColumns($columns) {
		$this->_columns = $columns;
	}

	/**
	 *
	 * @param GO_Base_Db_ActiveRecord $model
	 * @return array formatted grid row key value array
	 */
	public function formatModel($model) {

		$oldLevel = error_reporting(E_ERROR); //suppress errors in the eval'd code

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
		$columns = $this->getColumns();

		foreach ($columns as $colName => $attributes) {
			if (!is_array($attributes)) {
				$colName = $attributes;
				$attributes = array();
			}

			if (isset($attributes['extraVars'])) {
				extract($attributes['extraVars']);
			}

			if (isset($attributes['format'])) {
				$result = '';
				eval('$result=' . $attributes['format'] . ';');
				$formattedRecord[$colName] = $result;
			} elseif (isset($array[$colName]))
				$formattedRecord[$colName] = $array[$colName];
		}

		error_reporting($oldLevel);

		if (isset($this->_formatRecordFunction)) {
			$formattedRecord = call_user_func($this->_formatRecordFunction, $formattedRecord, $model, $this);
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
	 * Array $formattedRecord, GO_Base_Db_ActiveRecord $model, GO_Base_Data_Store $store
	 * 
	 * @param mixed $func Function name string or array($object, $functionName)
	 */
	public function setFormatRecordFunction($func) {
		$this->_formatRecordFunction = $func;
	}
}