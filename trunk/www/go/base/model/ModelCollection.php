<?php
/**
 * A collection of models. Finds models and indexes them by their primary key
 * 
 * $Collection->pk->modelproperty
 */
class GO_Base_Model_ModelCollection{
	
	protected $_models;
	
	public function __construct($model, $findParams=array()){
		$model = new $model;
		
		$findParams['ignoreAcl']=true;
		$stmt = $model->find($findParams);
		
		while($m = $stmt->fetch()){
			$this->_models[$m->pk]=$m;
		}
	}
	
	public function __get($name){
		return $this->_models[$name];
	}
	
	public function __isset($name){
		return $this->_models[$name];
	}
	
	public function getAll(){
		return $this->_models;
	}
}