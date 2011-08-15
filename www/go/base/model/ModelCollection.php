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
		$this->model = call_user_func(array($model,'model'));		
	}
	
	public function __get($name){
		try{
			$model =  $this->model->findByPk($name);
		}catch(AccessDeniedException $e){
			return false;
		}
		
		return $model;
	}
	
	public function __isset($name){
		try{
			return $this->model->findByPk($name)!==false;
		}catch(AccessDeniedException $e){
			return false;
		}
	}
	
	public function getAll(){
		return $this->model->find(array('ignoreAcl'=>true));
	}
}