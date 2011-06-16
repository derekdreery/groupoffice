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

		$model = new $this->model($_POST['id']);
		$model->setAttributes($_POST);

		$this->beforeSubmit($response, $model);

		$response['success'] = $model->save();

		$response['id']=$model->pk;

		//If the model has it's own ACL id then we return the newly created ACL id.
		//The model automatically creates it.
		if($model->aclField && !$model->aclFieldJoin){
			$response[$model->aclField]=$model->{$model->aclField};
		}	

		$this->afterSubmit(&$response, &$model);

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
		$model = new $this->model($_REQUEST['id']);

		$response['data']=$model->getAttributes();
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
}

