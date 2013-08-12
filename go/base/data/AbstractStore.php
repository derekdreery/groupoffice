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
	
	
	/**
	 * The response object eg.
	 * 
	 * array(
	 *	'results'=>array(
	 *			array('name'=>'Name','id'=>1)
	 *		)
	 *	'total'=>1
	 * );
	 * 
	 * @var array 
	 */
	
	protected $response;
	
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
		
		$this->response['results'] = array();
  }
	
	/**
	 * Reset the store results. 
	 */
	public function resetResults(){
		$this->response['results']=array();
	}
	
	/**
	 * Returns the column model
	 * 
	 * @return GO_Base_Data_ColumnModel 
	 */
	public function getColumnModel(){
		return $this->_columnModel;
	}
	
	/**
	 * DEPRICATED
	 * Return an array with all the records and the total number of rows in the store.
	 * 
	 * array('results'=>array(),'total'=>0);
	 * 
	 * @return array
	 */
	abstract public function getData();
	
	/**
	 * Return an array with the stores records
	 * Every item is an array of key value pairs
	 * @return array
	 */
	abstract public function getRecords();
	
	/**
	 * 
	 * Return the next record in the store.
	 * 
	 * @return array
	 */
  abstract public function nextRecord();
	
	/**
	 * Return the total number of records in the store.
	 * 
	 * @return int
	 */
	abstract public function getTotal();
	
	
	/**
	 * Add a raw record to the store.
	 * 
	 * @param array $attributes 
	 */
	public function addRecord($attributes){
		$this->response['results'][]=$attributes;
	}

	/**
	 * Set a title response
	 * @param String $title 
	 */
	public function setTitle($title){
		$this->response['title'] = $title;
	}
	/**
	 * Return title from response
	 * @return string Title of store's response
	 */
	public function getTitle(){
	  if(isset($this->response['title']))
		return $this->response['title'];
	  else
		return "";
	}
	
}