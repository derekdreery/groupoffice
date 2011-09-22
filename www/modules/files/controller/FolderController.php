<?php
class GO_Files_Controller_Folder extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Files_Model_Folder';
	
	
	public function actionTree($params){
		//GO::$ignoreAclPermissions=true;
//		if(empty($params['node']) || $params['node']=='root'){
//			$folder = GO_Files_Model_Folder::model()->findByPath('users/'.GO::user()->username, true);
//			
//			$folder->syncFilesystem();			
//		}
//		
		$response = array();
		
		
		switch($params['node']){
			case 'root':
				if(!empty($params['root_folder_id'])) {					
					$folder = GO_Files_Model_Folder::model()->findByPk($params['root_folder_id']);
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
				
				$node = $this->_folderToNode($folder);				
				$node['text']=GO::t('personal','files');
				$node['iconCls']='folder-home';
				$response[]=$node;
				
				
				$node= array(
						'text'=>GO::t('shared','files'),
						'id'=>'shared',
						'readonly'=>true,
						'draggable'=>false,
						'allowDrop'=>false,
						'iconCls'=>'folder-shares',
						//'expanded'=>true,
						//'children'=>$children
					);
					$response[]=$node;
				
				break;
				
				default:
					$folder = GO_Files_Model_Folder::model()->findByPk($params['node']);
					$folder->checkFsSync();
					
					$stmt = $folder->folders();
					while($subfolder = $stmt->fetch()){				
						$response[]=$this->_folderToNode($subfolder, false);
					}
				
					break;
		}
		
		
		

		
		return $response;
	}
	
	private function _folderToNode($folder, $withChildren=true){
		$node = array(
				'text'=>$folder->name,
				'id'=>$folder->id,
				'draggable'=>false,
				'iconCls'=>'folder-default'
		);
		
		if($withChildren){
			$stmt = $folder->folders();
			while($subfolder = $stmt->fetch()){
				$node['children'][]=$this->_folderToNode($subfolder, false);
			}
		}else
		{
			//check if folder has subfolders
			$firstSubfolder = $folder->folders(array(
				'single'=>true
			));
			if(!$firstSubfolder) {
				//it doesn't habe any subfolders so instruct the client about this
				//so it can present the node as a leaf.
				$node['children']=array();
				$node['expanded']=true;
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
	
	
	public function actionPaste($params){
		
		$response['success']=true;
		
		$ids = $this->_splitFolderAndFileIds(json_decode($params['ids'], true));		
		$destinationFolder = GO_Files_Model_Folder::model()->findByPk($params['destination_folder_id']);
		
		if(!$destinationFolder->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION))
			throw new GO_Base_Exception_AccessDenied();
		
		foreach($ids['files'] as $file_id){
			$file = GO_Files_Model_File::model()->findByPk($file_id);
			
			if($destinationFolder->hasFile($file->name)){
				if(empty($params['overwrite']))
				{
					$response['fileExists']=array($file->name, $file->id);
				}
			}
			
			if($params['paste_mode']=='cut'){
				if(!$file->move($destinationFolder))
					throw new Exception("Could not move ".$file->name);
			}else
			{
				if(!$file->copy($destinationFolder))
					throw new Exception("Could not copy ".$file->name);
			}			
		}
		
		foreach($ids['folders'] as $folder_id){
			$folder = GO_Files_Model_Folder::model()->findByPk($folder_id);
			
			if($params['paste_mode']=='cut'){
				if(!$folder->move($destinationFolder))
					throw new Exception("Could not move ".$folder->name);
			}else
			{
				if(!$folder->copy($destinationFolder))
					throw new Exception("Could not copy ".$folder->name);
			}
			
		}
		
		return $response;
		
	}	
	
	private function _splitFolderAndFileIds($ids){
		$fileIds = array();
		$folderIds = array();

		
		foreach ($ids as $typeId) {
			if(substr($typeId, 0,1)=='d'){
				$folderIds[]=substr($typeId,2);
			}else
			{
				$fileIds[]=substr($typeId,2);
			}
		}
		
		return array('files'=>$fileIds, 'folders'=>$folderIds);
	}
	
	
	private function _listShares(){
		$stmt = GO_Files_Model_Folder::model()->find(array(
				'criteriaObject'=>  GO_Base_Db_FindCriteria::newInstance()
						->addCondition('visible', 1)
		));
		
		//sort by path and only list top level shares
		$shares = array();
		while($folder = $stmt->fetch())
		{
			$shares[$folder->path]=$folder;
		}
		ksort($shares);
		
		$response=array();
		foreach($shares as $path=>$folder){
			$isSubDir = isset($lastFolder) && $folder->isSubFolderOf($lastFolder);
			
			if(!$isSubDir)
				$response[]=$this->_folderToNode ($folder, true);
			$lastFolder=$folder;
		}
		
		return $response;
	}
	
	public function actionList($params){
		
		if($params['folder_id']=='shared')
			return $this->_listShares();
		
		//get the folder that contains the files and folders to list.
		//This will check permissions too.
		$folder= GO_Files_Model_Folder::model()->findByPk($params['folder_id']);
		
		
		$grid = new GO_Base_Provider_Grid();		
		
		
		//handle delete request for both files and folder
		if (isset($params['delete_keys'])) {
			
			$ids = $this->_splitFolderAndFileIds(json_decode($params['delete_keys'],true));
			
			$params['delete_keys']=json_encode($ids['folders']);
			$grid->processDeleteActions($params, "GO_Files_Model_Folder");
			
			$params['delete_keys']=json_encode($ids['files']);
			$grid->processDeleteActions($params, "GO_Files_Model_File");
		}
		
		
		$grid->setFormatRecordFunction(array($this, 'formatListRecord'));
		$findParams = $grid->getDefaultParams(array(
				'ignoreAcl'=>true
		));
		$stmt = $folder->folders($findParams);
		$grid->setStatement($stmt);		
		
		$response = $grid->getData();
		
		
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
		
		$findParams = $grid->getDefaultParams(array(
				'limit'=>$fileLimit,
				'start'=>$fileStart
		));
		
		$stmt = $folder->files($findParams);
		$grid->setStatement($stmt);	
		
		$filesResponse=$grid->getData();
		
		$response['total']+=$filesResponse['total'];
		$response['results']=array_merge($response['results'],$filesResponse['results']);
		
		return $response;
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
			$record['type']=$model->fsFile->mimeType();		
			$record['extension']=$model->fsFile->extension();
		}
		$record['thumb_url']=$model->thumbURL;
		
		return $record;
	}
}

