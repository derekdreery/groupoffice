<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * A model has an ID stored in the database that is used for faster searches
 * of links.
 * 
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.model 
 * @property string $model_name
 * @property int $id
 */

namespace GO\Base\Model;

class ModelType extends \GO\Base\Db\ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return \GO\Base\Model_LinkType 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function tableName(){
		return "go_model_types";
	}
	
	public function findByModelName($modelName){
		
		if(empty($modelName))
			throw new \Exception("Model name may not be empty");
		
		//backwards compatibility for namespaces;
		$modelName=  str_replace('\\', '_', $modelName);
						
		
		$model = $this->findSingleByAttribute('model_name', $modelName);
		if($model)
			return $model->id;
		
		$model = new ModelType();
		$model->model_name=$modelName;
		$model->save();
		
		return $model->id;
	}
	
	public function checkDatabase() {
		
		//delete if module is no longer installed. This should happen automatically
		//after module uninstall but in some cases this went wrong.
		$parts = explode('_',$this->model_name);
		$module = strtolower($parts[1]);
		if($module!='base' && !\GO::modules()->isInstalled($module)){
			$this->delete();
		}else
		{		
			return parent::checkDatabase();
		}
	}
}