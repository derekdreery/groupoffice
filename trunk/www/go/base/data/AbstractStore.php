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
 * Abstract class for the datastores * 
 * 
 * @package GO.base.data
 * @version $Id: AbstractStore.php 7607 2011-06-15 09:17:42Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @authorWesley Smits <wsmits@intermesh.nl> 
 * @abstract
 */
abstract class GO_Base_Data_AbstractStore {

	/**
	 * The columnmodel for this store
	 * 
	 * @var GO_Base_Data_ColumnModel 
	 */
	protected $_columnModel=false;
	
	protected $_limit;
	protected $_defaultSortOrder='';
	protected $_defaultSortDirection='ASC';
	
  /**
	 * The constructor of the Store
	 * 
	 * A Store always needs an GO_Base_Data_ColumnModel so you can add a 
	 * columnModel on the creation of a new object of the Store class.
	 * If no GO_Base_Data_ColumnModel is given then this Store object makes his own 
	 * empty columnModel
	 * 
	 * @param GO_Base_Data_ColumnModel $columnModel 
	 */
  public function __construct($columnModel=false) {        
		if($columnModel)
			$this->_columnModel = $columnModel;
		else
			$this->_columnModel = new GO_Base_Data_ColumnModel();
  }
	
		
	/**
	 * Set the default column to sort on.
	 * @param String / Array $order 
	 */
	public function setDefaultSortOrder($order, $direction){
		$this->_defaultSortOrder=$order;
		$this->_defaultSortDirection=$direction;
	}
	
	
	/**
	 * A function that can be overridden in the child classes.
	 * 
	 * Return allways an array with the default parameters.
	 * 
	 * @param Array $params
	 * @return Array 
	 */
	public function getDefaultParams($params=array()) {
		return $params;
	}
	
	
	/**
	 * A function that needs to be overridden in the child classes.
	 */
	public function getData() {
		
	}
	
}