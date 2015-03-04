<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The GO_Favorites_Controller_TasklistFavorites controller
 *
 * @package GO.modules.Favorites
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

class GO_Favorites_Controller_TasklistFavorites extends GO_Base_Controller_AbstractMultiSelectModelController {
	
	/**
	 * The name of the model from where the MANY_MANY relation is called
	 * @return String 
	 */
	public function modelName() {
		return 'GO_Tasks_Model_Tasklist';
	}
	
	/**
	 * Returns the name of the model that handles the MANY_MANY relation.
	 * @return String 
	 */
	public function linkModelName() {
		return 'GO_Favorites_Model_Tasklist';
	}
	
	/**
	 * The name of the field in the linkModel where the key of the current model is defined.
	 * @return String
	 */
	public function linkModelField() {
		return 'tasklist_id';
	}
	
	/**
	 * Return all new items for a grid. 
	 * So this are the items that are not already selected.
	 * 
	 * Parameters:
	 *	model_id =	The value of one of the keys from the combined primary key of the linkModel that is not given in the linkModelField;
	 *			Example:	The combined key of the linkModel is: [user_id,tasklist_id].
	 *								The given linkModelField is: [tasklist_id].
	 *								Then the model_id needs to be the other value of the combined key so in this example: The value for [user_id]
	 *							
	 * 
	 * @param Array $params
	 * @return type 
	 */
	protected function actionSelectNewStore($params){
		
		$model = GO::getModel($this->modelName());
		$linkModel = GO::getModel($this->linkModelName());
		
		$store = GO_Base_Data_Store::newInstance($model);
		
		$joinCriteria = GO_Base_Db_FindCriteria::newInstance()
			->addCondition($this->getRemoteKey(), $params['model_id'],'=','lt')
			->addCondition($model->primaryKey(), 'lt.'.$this->linkModelField(), '=', 't', true, true);			
		
		$this->formatColumns($store->getColumnModel());
		
		$findParams = $store->getDefaultParams($params);
		
		if($this->uniqueSelection){
			$findParams->join($linkModel->tableName(), $joinCriteria, 'lt', 'LEFT');

			$findCriteria = GO_Base_Db_FindCriteria::newInstance()
							->addCondition($this->linkModelField(), null,'IS','lt');
			$findParams->criteria($findCriteria);
		}
		
		
		$availableModels = $model->find($findParams);
		
		$store->setStatement($availableModels);

		return $store->getData();
	}
}