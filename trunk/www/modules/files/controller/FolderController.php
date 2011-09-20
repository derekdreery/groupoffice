<?php
class GO_Files_Controller_Folder extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Files_Model_Folder';
	
	
	public function actionTree($params){
		//GO::$ignoreAclPermissions=true;
		if(empty($params['node']) || $params['node']=='root'){
			$folder = GO_Files_Model_Folder::model()->findByPath('users/'.GO::user()->username, true);
			
			$folder->syncFilesystem();			
		}
		
		$response = array();
		
		switch($params['node']){
			case 'root':
				if(!empty($_POST['root_folder_id'])) {					
					$folder = GO_Files_Model_Folder::model()->findByPk($_POST['root_folder_id']);
				}else {
					$folder = GO_Files_Model_Folder::model()->findHomeFolder(GO::user());						
				}
				$folder->checkFsSync();
				

				//TODO
//				$expand_folder_ids=array();
//				if(!empty($_POST['expand_folder_id'])) {
//					//This is the active folder. We need to make sure this folder is
//					//provided
//					$pathinfo=array();
//					$path = $files->build_path($_POST['expand_folder_id'], $pathinfo);
//
//					$files->check_folder_sync($folder, $path);
//
//					$expand_folder_ids=array();
//					foreach($pathinfo as $expandfolder) {
//						$expand_folder_ids[] = $expandfolder['id'];
//					}
//				}
				
				
				
				break;
		}
		
		
		$node = $this->_folderToNode($folder);
				
		$response[]=$node;

		
		return $response;
	}
	
	private function _folderToNode($folder, $withChildren=true){
		$node = array(
				'text'=>$folder->name,
				'id'=>$folder->id,
				'expanded'=>true,
				'draggable'=>false,
				'iconCls'=>'folder-default',
				'children'=>array(),
				'notreloadable'=>true
		);
		
		if($withChildren){
			$stmt = $folder->folders();
			while($subfolder = $stmt->fetch()){
				$node['children'][]=$this->_folderToNode($subfolder, false);
			}
		}
		
		return $node;
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
		$response['data']['is_someones_home_dir']=$model->isSomeonesHomeFolder();
		
		return parent::afterLoad($response, $model, $params);
	}
	
	
	
	
	
	
	public function actionList($params){
		
		
		$grid = new GO_Base_Provider_Grid();		
		$grid->setDefaultSortOrder('name','ASC');
		
		$grid->formatColumn('name','$model->name',array(),array('first_name','last_name'));
		$grid->formatColumn('cf', '$model->id.":".$model->name');//special field used by custom fields. They need an id an value in one.
		
		$grid->setFormatRecordFunction(array($this, 'formatLsRecord'));
		$findParams = $grid->getDefaultParams(array(
				'by'=>array(array('parent_id',$params['folder_id']))
		));
		$stmt = GO_Files_Model_Folder::model()->find($findParams);
		$grid->setStatement($stmt);		
		
		$folderData = $grid->getData();
		
		
		//add files to the listing if it fits
		$folderPages = floor($stmt->foundRows/$findParams['limit']);
		$foldersOnLastPage = $stmt->foundRows-($folderPages*$findParams['limit']);
		
		$isOnLastPageofFolders = $stmt->foundRows < ($findParams['limit']+$findParams['start']);
		
		if($isOnLastPageofFolders){			
			$fileStart = $findParams['start']-$folderPages*$findParams['limit'];
			$fileLimit = $findParams['limit']-$foldersOnLastPage;
		}else
		{
			$fileStart = $findParams['start']-$folderPages*$findParams['limit'];
			$fileLimit = $findParams['limit']-$foldersOnLastPage;
		}
	}
	
	public function formatListRecord($record, $model, $grid){
		
		$record['path']=$model->path;
		
		if($model instanceof GO_Files_Model_Folder){
			$record['type_id']='d:'.$model->id;
			$record['type']=GO::t('folder','files');
			$record['size']='-';
			$record['extension']='folder';
		}else
		{
			$record['type_id']='f:'.$model->id;
			$record['type']=GO::t('file','files');
		}
		
		return $record;
	}
}

