<?php
/**
 * A collection of models. Finds models and indexes them by their primary key
 * 
 * $Collection->pk->modelproperty
 */
class GO_Base_Model_ModelCollection{
	
	protected $_models;
	/**
	 *
	 * @var GO_Base_Db_ActiveRecord 
	 */
	protected $model;
	
	public function __construct($model){
		$this->model = $model::model();		
	}
	
	public function __get($name){
		
		$model =  $this->model->findByPk($name);
		
		return $model;
	}
	
	public function __isset($name){
		return $this->model->findByPk($name)!==false;
	}
}