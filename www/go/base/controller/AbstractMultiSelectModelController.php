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
 * Abstract class for Group-Office Models that needed to be multiselected.
 * 
 * Any function that starts with action will be publicly accessible by:
 * 
 * index.php?r=module/controllername/functionNameWithoutAction&security_token=1233456
 * 
 * This function will be called with one parameter which holds all request
 * variables.
 * 
 * A security token must be supplied in each request to prevent cross site 
 * request forgeries.
 * 
 * The functions must return a response object. In case of ajax controllers this
 * should be a an array that will be converted to Json or XMl by an Exporter.
 * 
 * 
 * @package GO.base.controller
 * @version $Id: AbstractMultiSelectModelController.php 7607 2011-06-15 09:17:42Z wsmits $
 * @copyright Copyright Intermesh BV.
 * @author Wesley Smits <wsmits@intermesh.nl> 
 * @abstract
 */
abstract class GO_Base_Controller_AbstractMultiSelectModelController extends GO_Base_Controller_AbstractController{
		
	/**
	 * The current model from where the relation is called
	 */
	abstract public function modelName();
	
	/**
	 * The model that handles the MANY_MANY relation.
	 */
	abstract public function linkModelName();
	
	/**
	 * The key (from the combined key) of the linkmodel that identifies the linked model.
	 */
	abstract public function linkModelField();
	
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
	public function actionSelectNewStore($params){
		
		$model = GO::getModel($this->modelName());
		$linkModel = GO::getModel($this->linkModelName());
		
		$store = GO_Base_Data_Store::newInstance($model);
		
		$joinCriteria = GO_Base_Db_FindCriteria::newInstance()
			->addCondition($model->primaryKey(), 'lt.'.$this->linkModelField(), '=', 't', true, true);			
		
		$findParams = $store->getDefaultParams();
		$findParams->join($linkModel->tableName(), $joinCriteria, 'lt', 'LEFT');
		
		$findCriteria = GO_Base_Db_FindCriteria::newInstance()
						->addCondition($this->_getRemoteKey(), $params['model_id'])
						->addCondition($this->linkModelField(), null,'IS','lt');
		
		$findParams->criteria($findCriteria);
		
		$availableModels = $model->find($findParams);
		
		$store->setStatement($availableModels);

		return $store->getData();
	}
	
	/**
	 * Return the selected items for a grid.
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
	public function actionSelectedStore($params){
		
		if(!empty($params['add'])) {
			$ids = json_decode($params['add'],true);
			
			$linkmodelField = $this->linkModelField();
			$remoteKey = $this->_getRemoteKey();
			$linkModelName = $this->linkModelName();
			
			
			foreach($ids as $id){
				$linkModel = new $linkModelName();
				$linkModel->$linkmodelField = $id;
				$linkModel->$remoteKey = $params['model_id'];
				$linkModel->save();
			}
		}
		
		$model = GO::getModel($this->modelName());
		$linkModel = GO::getModel($this->linkModelName());
		
		$store = GO_Base_Data_Store::newInstance($model);
		
		$store->processDeleteActions($params, $this->linkModelName(), array($this->_getRemoteKey()=>$params['model_id']));
		
		$joinCriteria = GO_Base_Db_FindCriteria::newInstance()
			->addCondition($model->primaryKey(), 'lt.'.$this->linkModelField(), '=', 't', true, true)
			->addCondition($this->_getRemoteKey(), $params['model_id']);			
		
		$findParams = $store->getDefaultParams();
		$findParams->join($linkModel->tableName(), $joinCriteria, 'lt', 'INNER');

		$selectedModels = $model->find($findParams);
		
		$store->setStatement($selectedModels);

		return $store->getData();
	}
	
	/**
	 * Find the remote key in the combined key of the linkModel.
	 * 
	 * @return String The remote key 
	 */
	private function _getRemoteKey(){
		$linkModel = GO::getModel($this->linkModelName());
		$key = $linkModel->primaryKey();
		
		return $key[0]==$this->linkModelField() ? $key[1] : $key[0];
	}

}