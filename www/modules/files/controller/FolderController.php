<?php
class GO_Files_Controller_Folder extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Files_Model_Folder';
	
	
	public function actionTree($params){
		if(empty($params['node']) || $params['node']=='root'){
			$folder = GO_Files_Model_Folder::model()->findByPath('users/'.GO::user()->username, true);
			
			$folder->syncFilesystem();			
		}
	}
	
	protected function beforeSubmit(&$response, &$model, &$params) {
		
		if (isset($params['share']) && $model->acl_id==0) {
			$model->visible=1;
			
			$acl = new GO_Base_Model_Acl();
			$acl->description=$model->tableName().'.'.$model->aclField();
			$acl->user_id=GO::user() ? GO::user()->id : 1;
			$acl->save();			
			$model->acl_id = $response['acl_id']= $acl->id;
		}
		
		if (!isset($params['share']) && $model->acl_id>0)
		{
			$model->acl->delete();
			$model->acl_id= $response['acl_id']= 0;
		}
		
		return parent::beforeSubmit($response, $model, $params);
	}
	
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		
		//output the new path of the file if we changed the name.
		if(isset($modifiedAttributes['name']))
			$response['new_path']=$model->path;
		
		if(isset($params['notify']) && !$model->hasNotifyUser(GO::user()->id))
			$model->addNotifyUser(GO::user()->id);
		
		if(!isset($params['notify']) && $model->hasNotifyUser(GO::user()->id))
			$model->removeNotifyUser(GO::user()->id);			
		
		parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		$response['data']['path']=$model->path;		
		$response['data']['notify']= $model->hasNotifyUser(GO::user()->id);			
		$response['data']['is_someones_home_dir']=$model->isSomeonesHomeDir();
		
		return parent::afterLoad($response, $model, $params);
	}
}

