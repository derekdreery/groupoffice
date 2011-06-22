<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */


/**
 * Extend this class if you want to use your model in a form.
 */
class GO_Base_Controller_FormController extends GO_Base_Controller_AbstractController{

	protected $model;

	function init($output){
		parent::init($output);
		$this->addPermissionCheck(GO::modules()->{$this->module}->acl_id, GO_SECURITY::READ_PERMISSION);
		//$this->addPermissionCheck(GO::modules()->modules['models']['acl_id'], GO_SECURITY::DELETE_PERMISSION,'delete');
	}

	/**
	 * This action is called when a form is submitted.
	 */
	public function actionSubmit(){

		$modelName = $this->model;
		if(!empty($_REQUEST['id']))
			$model = $modelName::model()->findByPk($_REQUEST['id']);
		else
			$model = $modelName::model();
		
		$model->setAttributes($_POST);

		$this->beforeSubmit($response, $model);

		$response['success'] = $model->save();

		$response['id']=$model->pk;

		//If the model has it's own ACL id then we return the newly created ACL id.
		//The model automatically creates it.
		if($model->aclField && !$model->aclFieldJoin){
			$response[$model->aclField]=$model->{$model->aclField};
		}	
		
		
		if(!empty($_POST['link']))
		{		
			require_once(GO::config()->class_path.'base/links.class.inc.php');
			$GO_LINKS = new GO_LINKS();
				
			//todo link type should be handled better.
			//Nicer would be $model->linkTo($othermodel);
			$link_props = explode(':', $_POST['link']);
			$GO_LINKS->add_link(
			($link_props[1]),
			($link_props[0]),
			$model->pk,
			$model->linkType);
		}

		$this->afterSubmit($response, $model);

		$this->output($response);
	}

	/**
	 * Useful to override
	 * 
	 * @param array $response The response array
	 * @param mixed $model
	 */
	protected function beforeSubmit(&$response, &$model){}


	/**
	 * Useful to override
	 *
	 * @param array $response The response array
	 * @param mixed $model
	 */
	protected function afterSubmit(&$response, &$model){}

	public function actionLoad(){
		//$model = new $this->model();
		
		$modelName = $this->model;
		$model = $modelName::model()->findByPk($_REQUEST['id']);
		

		$response['data']=array_merge($model->getAttributes(), $model->customfieldRecord->getAttributes());
		$response['success']=true;

		$response=$this->_loadComboTexts($response, $model);

		$response = $this->afterLoad($response, $model);

		$this->output($response);
	}

	protected function afterLoad($response, $model){
		return $response;
	}

	/**
	 * List all fields that require a remote text to load for a remote combobox.
	 * eg. with a model you want to provide the category name so that that the
	 * category combo store does not need to be loaded to show it.
	 *
	 * You would list that like this:
	 *
	 * 'category_id'=>array('category','name')
	 *
	 * The category name would be looked up in the model model ->category->name.
	 * A relation for this must be defined. See ActiveRecord->relations.
	 *
	 *
	 * @var array remote combo mappings
	 */

	protected $remoteComboFields=array(
			//'category_id'=>array('category','name')
	);

	private function _loadComboTexts($response, $model){

		$response['remoteComboTexts']=array();

		foreach($this->remoteComboFields as $property=>$map){
			$response['remoteComboTexts'][$property]=$model->{$map[0]}->{$map[1]};
		}

		return $response;

	}
	
	
	
	/**
	 * Override this function to supplie additional parameters to the 
	 * GO_Base_Db_ActiveRecord->find() function
	 * 
	 * @return array parameters for the GO_Base_Db_ActiveRecord->find() function 
	 */
	protected function getGridParams(){		
		return array();
	}
	
	/**
	 * Override this function to format the grid record data.
	 * 
	 * @param array $record The grid record returned from the GO_Base_Db_ActiveRecord->getAttributes
	 * @param GO_Base_Db_ActiveRecord $model
	 * @return array The grid record data
	 */
	protected function formatModelForGrid($record, $model){
		return $record;
	}
	
	public function actionGrid(){	
		
		
		if(isset($_POST['delete_keys']))
		{				
			try{
				$deleteIds = json_decode($_POST['delete_keys']);
				foreach($deleteIds as $model_id)
				{
					$model = new $this->model($model_id);
					$model->delete();
				}
				$response['deleteSuccess']=true;

			}catch(Exception $e)
			{
				$response['deleteSuccess']=false;
				$response['deleteFeedback']=$e->getMessage();
			}
		}
		
		
		
		$defaultParams = array(			
			'limit'=>isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 0,
			'start'=>isset($_REQUEST['start']) ? $_REQUEST['start'] : 0,
			'orderField'=>isset($_REQUEST['orderField']) ? $_REQUEST['orderField'] : '',
			'orderDirection'=>isset($_REQUEST['orderDirection']) ? $_REQUEST['orderDirection'] : '',
		);
		
		$modelName = $this->model;
		$model = $modelName::model();
		
		$params = array_merge($defaultParams, $this->getGridParams());
		
		
		$stmt = $model->find($params, $response['total']);
		
		$response['results']=array();
		
		while($model = $stmt->fetch()){
			$response['results'][]=$this->formatModelForGrid($model->getAttributes(), $model);
		}		
		
		$this->output($response);
		
		
		//var_dump(GO_Notes_Model_Note::$_models);
		
	}
}

