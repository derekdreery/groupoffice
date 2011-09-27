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
	public function __construct($model=false,$excludeColumns=array()) {
		if($model)
			$this->setColumnsFromModel($model,$excludeColumns);
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
	 * @param String $column
	 * @param String $format
	 * @param Array $extraVars
	 * @param String $sortfield 
	 */
	public function formatColumn($column, $format, $extraVars=array(), $sortfield='') {
		$this->_columns[$column]['format'] = $format;
    $this->_columns[$column]['extraVars'] = $extraVars;
		
		if(!empty($sortfield)){
			$this->_sortFieldsAliases[$column]=$sortfield;
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
	
	
}