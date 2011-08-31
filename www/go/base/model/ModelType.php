<?php

class GO_Base_Model_ModelType extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Base_Model_LinkType 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function tableName(){
		return "go_model_types";
	}
	
	public function findByModelName($modelName){
		$model = $this->findSingleByAttribute('model_name', $modelName);
		if($model)
			return $model->id;
		
		$model = new GO_Base_Model_ModelType();
		$model->model_name=$modelName;
		$model->save();
		
		return $model->id;
	}
}