<?php

class GO_Base_Component_MultiSelectGrid {

	private $_requestParamName;
		/**
	 * The selected model ID's
	 * 
	 * @var array
	 */
	public $selectedIds=array();
	private $_modelName;
	private $_models;
	/**
	 *
	 * @var GO_Base_Data_AbstractStore 
	 */
	private $_store;
	
	
	private $_checkPermissions=false;

	/**
	 * A component for a MultiSelectGrid. eg. Select multiple addressbooks to display contacts.
	 * 
	 * You must create two instances. One in AddressbookController and the other one in ContactController.
	 * 
	 * Create them in GO_Base_Controller_AbstractModelController::beforeStoreStatement
	 * 
	 * @param string $requestParamName The name of the request parameter. It's the id of the MultiSelectGrid in the ExtJS view.
	 * @param string $modelName Name of the model that the selected ID's belong to.
	 * @param array $requestParams The request parameters
	 * @param boolean $checkPermission  Enable permission checking on this model. This makes sure that only 
	 * readbable addressbooks are used with contacts for example.
	 * This will disable acl checking for the contacts query which improves performance.
	 */
	public function __construct($requestParamName, $modelName, GO_Base_Data_AbstractStore $store, array $requestParams, $checkPermissions=false) {

		$this->_requestParamName = $requestParamName;
		$this->_store = $store;
		$this->_modelName = $modelName;			
		
		if(GO::config()->debug && !class_exists($modelName))
			throw new Exception("Invalid argument \$modelName for GO_Base_Component_MultiSelectGrid. Class $modelName does not exist.");
		
		$this->_checkPermissions=$checkPermissions;
		
		if(empty($requestParams['noMultiSelectFilter']))
			$this->_setSelectedIds($requestParams);
	}
	
	/**
	 * Call this if you want the first item or all items to be selected by default.
	 * 
	 * @param GO_Base_Db_FindParams $findParams
	 * @param boolean $selectAll 
	 */
	public function setFindParamsForDefaultSelection(GO_Base_Db_FindParams $findParams, $selectAll=false){

		if(empty($this->selectedIds)){
			$findParamsCopy = clone $findParams;
			$findParamsCopy->ignoreAcl(false);
			if(!$selectAll){				
				
				$findParamsCopy->limit(1)->single();
				$model = GO::getModel($this->_modelName)->find($findParamsCopy);

				$this->selectedIds=array($model->pk);		
				GO::debug($this->selectedIds);
			}else{
				$stmt = GO::getModel($this->_modelName)->find($findParamsCopy);
				while($model = $stmt->fetch()){
					$this->selectedIds[]=$model->pk;
				}
			}			
			$this->_save();
		}
	}

	private function _save(){
		GO::config()->save_setting('ms_' . $this->_requestParamName, implode(',', $this->selectedIds), GO::session()->values['user_id']);
	}

	private function _setSelectedIds(array $requestParams) {
		if (isset($requestParams[$this->_requestParamName])) {
			$this->selectedIds = json_decode($requestParams[$this->_requestParamName], true);
			$this->_save();
		} else {
			$this->selectedIds = GO::config()->get_setting('ms_' . $this->_requestParamName, GO::session()->values['user_id']);

			$this->selectedIds = $this->selectedIds!==false && $this->selectedIds !=""  ? explode(',', $this->selectedIds) : array();

			//this will validate the selection
			if($this->_checkPermissions)
				$this->_validateSelection();
		}
		
		
		//add all the allowed models if it's empty. It's faster to find all allowed 
		//addressbooks then too join the acl table.
		//That's why this component add's ignoreAcl() to the findParams automatically 
		//in the addSelectedToFindCriteria() function. The permissions are checked by 
		//the following query.
		
		if($this->_checkPermissions && empty($this->selectedIds)){
			$stmt = GO::getModel($this->_modelName)->find();
			foreach($stmt as $model){
				$this->selectedIds[]=$model->pk;
			}
		}
		//GO::debug($this->selectedIds);
	}
	
	/**
	 * Format the "checked" column for the store response.
	 * Use this in the model controller of the selected items. eg. Use in AddressbookController and not in ContactController. 
	 */
	public function formatCheckedColumn(){
		$this->_store->getColumnModel()->
						formatColumn('checked','in_array($model->id, $multiSelectGrid->selectedIds)', array('multiSelectGrid'=>$this));

	}

	/**
	 * Add the selected id's to the findCriteria. You use this in the other controller. eg. ContactController and not AddressbookController.
	 * 
	 * Should be called in GO_Base_Controller_AbstractModelController::beforeStoreStatement
	 */
	public function addSelectedToFindCriteria(GO_Base_Db_FindParams $findParams, $columnName, $tableAlias = 't', $useAnd = true, $useNot = false) {
		
		//ignore here. Permissions are checked in by _setSelectedIds.
		if($this->_checkPermissions)
			$findParams->ignoreAcl();
		
		$findParams->getCriteria()->addInCondition($columnName, $this->selectedIds, $tableAlias, $useAnd, $useNot);
	}
	
	/**
	 * Checks if all selected id's are accessible. If not it removes the models 
	 * from the selection. 
	 */
	private function _validateSelection(){
		$models = $this->_getSelectedModels();
		
		if(count($models) != count($this->selectedIds)){
			//one of the selections could not be fetched. This may happen when something is
			//deleted or a user doesn't have permissions anymore.
			//remove the id's from the selection.
			$this->selectedIds=array();
			foreach($this->_models as $model){
				$this->selectedIds[]=$model->pk;
			}
			$this->_save();
		}
	}

	/**
	 * Get all selected models
	 * 
	 * @return GO_Base_Db_ActiveRecord[] 
	 */
	private function _getSelectedModels(){
		if(!isset($this->_models))
		{			
			$this->_models=array();
			foreach ($this->selectedIds as $modelId) {
				try{
					$model = GO::getModel($this->_modelName)->findByPk($modelId);				
					if($model)
						$this->_models[]=$model;

				}
				catch(Exception $e){
					//might happen when a user no longer has access to a selected model
				}
			}		
			
		}
		return $this->_models;
	}

	/**
	 * Set the title for the store. This will be outputted in the JSON response.
	 * 
	 * Should be called in GO_Base_Controller_AbstractModelController::beforeStoreStatement
	 * 
	 * @param GO_Base_Data_AbstractStore $store
	 * @param string $titleAttribute 
	 */
	public function setStoreTitle( $titleAttribute = 'name') {
		$titleArray = array();
		$models = $this->_getSelectedModels();
		foreach ($models as $model) 
			$titleArray[] = $model->$titleAttribute;
		
		if(count($titleArray))
			$this->_store->setTitle(implode(', ',$titleArray));		
	}
	
	/**
	 * Return information for add and delete buttons in the view. It tells wether add or delete is allowed.
	 * 
	 * @param array $response 
	 */
	public function setButtonParams(&$response){
		$models = $this->_getSelectedModels();
		foreach ($models as $model) {		
			if(!isset($response['buttonParams']) && GO_Base_Model_Acl::hasPermission($model->getPermissionLevel(),GO_Base_Model_Acl::CREATE_PERMISSION)){

				//instruct the view for the add action.
				$response['buttonParams']=array('id'=>$model->id,'name'=>$model->name, 'permissionLevel'=>$model->getPermissionLevel());
			}
		}
	}

}