<?php

class GO_Files_Controller_Folder extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Files_Model_Folder';

	public function actionTree($params) {
		//GO::$ignoreAclPermissions=true;
//		if(empty($params['node']) || $params['node']=='root'){
//			$folder = GO_Files_Model_Folder::model()->findByPath('users/'.GO::user()->username, true);
//			
//			$folder->syncFilesystem();			
//		}
//		
		$response = array();


		switch ($params['node']) {
			case 'shared':
				//not so nice hardcoded limit on folder nodes.
				$stmt = GO_Files_Model_Folder::model()->findShares(array('limit' => 100));
				while ($folder = $stmt->fetch()) {
					$response[] = $this->_folderToNode($folder, false);
				}
				break;
			case 'root':
				if (!empty($params['root_folder_id'])) {
					$folder = GO_Files_Model_Folder::model()->findByPk($params['root_folder_id']);
					$folder->checkFsSync();
					$node = $this->_folderToNode($folder);
					$response[] = $node;
				} else {
					$folder = GO_Files_Model_Folder::model()->findHomeFolder(GO::user());
				
					$folder->checkFsSync();

					$node = $this->_folderToNode($folder);
					$node['text'] = GO::t('personal', 'files');
					$node['iconCls'] = 'folder-home';
					$response[] = $node;


					$node = array(
							'text' => GO::t('shared', 'files'),
							'id' => 'shared',
							'readonly' => true,
							'draggable' => false,
							'allowDrop' => false,
							'iconCls' => 'folder-shares',
	//						'expanded'=>true,
	//						'children'=>array()
					);
					$response[] = $node;

					if(GO::modules()->addressbook){
						$contactsFolder = GO_Files_Model_Folder::model()->findByPath('addressbook');

						if($contactsFolder){
							$node = $this->_folderToNode($contactsFolder, false);
							$node['text']=GO::t('addressbook','addressbook');
							$response[]=$node;
						}
					}
					
					if(GO::modules()->projects){
						$projectsFolder = GO_Files_Model_Folder::model()->findByPath('projects');

						if($projectsFolder){
							$node = $this->_folderToNode($projectsFolder, false);
							$node['text']=GO::t('projects','projects');
							$response[]=$node;
						}
					}

				}
				
				

				break;

			default:
				$folder = GO_Files_Model_Folder::model()->findByPk($params['node']);
				$folder->checkFsSync();
				
				$stmt = $folder->getSubFolders();
				
				while ($subfolder = $stmt->fetch()) {
					$response[] = $this->_folderToNode($subfolder, false);
				}

				break;
		}

		return $response;
	}

	private function _folderToNode($folder, $withChildren=true) {
		$node = array(
				'text' => $folder->name,
				'id' => $folder->id,
				'draggable' => false,
				'iconCls' => 'folder-default',
				'expanded' => $withChildren
		);

		if ($withChildren) {
			$stmt = $folder->folders();
			while ($subfolder = $stmt->fetch()) {
				$node['children'][] = $this->_folderToNode($subfolder, false);
			}
		} else {
			//check if folder has subfolders
			$firstSubfolder = $folder->folders(array(
					'single' => true
							));
			if (!$firstSubfolder) {
				//it doesn't habe any subfolders so instruct the client about this
				//so it can present the node as a leaf.
				$node['children'] = array();
				$node['expanded'] = true;
			}
		}

		return $node;
	}

	protected function beforeSubmit(&$response, &$model, &$params) {

		if (isset($params['share']) && $model->acl_id == 0) {
			$model->visible = 1;

			$acl = new GO_Base_Model_Acl();
			$acl->description = $model->tableName() . '.' . $model->aclField();
			$acl->user_id = GO::user() ? GO::user()->id : 1;
			$acl->save();
			$model->acl_id = $response['acl_id'] = $acl->id;
		}

		if (!isset($params['share']) && $model->acl_id > 0) {
			$model->acl->delete();
			$model->acl_id = $response['acl_id'] = 0;
		}

		return parent::beforeSubmit($response, $model, $params);
	}

	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {

		//output the new path of the file if we changed the name.
		if (isset($modifiedAttributes['name']))
			$response['new_path'] = $model->path;

		if (isset($params['notify']) && !$model->hasNotifyUser(GO::user()->id))
			$model->addNotifyUser(GO::user()->id);

		if (!isset($params['notify']) && $model->hasNotifyUser(GO::user()->id))
			$model->removeNotifyUser(GO::user()->id);

		parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}

	protected function afterLoad(&$response, &$model, &$params) {

		$response['data']['path'] = $model->path;
		$response['data']['notify'] = $model->hasNotifyUser(GO::user()->id);
		$response['data']['is_someones_home_dir'] = $model->isSomeonesHomeFolder();

		return parent::afterLoad($response, $model, $params);
	}
	
	protected function afterDisplay(&$response, &$model, &$params) {
		$response['data']['path']=$model->path;
		$response['data']['type']=GO::t('folder','files');
		
		return parent::afterDisplay($response, $model, $params);
	}

	public function actionPaste($params) {

		$response['success'] = true;
		
		if(!isset($params['overwrite']))
			$params['overwrite']='ask'; //can be ask, yes, no
			

		if(isset($params['ids']) && $params['overwrite']=='ask')
			GO::session()->values['files']['pasteIds'] = $this->_splitFolderAndFileIds(json_decode($params['ids'], true));
	
		$destinationFolder = GO_Files_Model_Folder::model()->findByPk($params['destination_folder_id']);

		if (!$destinationFolder->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION))
			throw new GO_Base_Exception_AccessDenied();

		while($file_id=array_shift(GO::session()->values['files']['pasteIds']['files'])){
			$file = GO_Files_Model_File::model()->findByPk($file_id);
			
			$existingFile = $destinationFolder->hasFile($file->name);
			if ($existingFile) {
				switch($params['overwrite']){
					case 'ask':
						array_unshift(GO::session()->values['files']['pasteIds']['files'], $file_id);
						$response['fileExists'] = $file->name;
						return $response;
						break;
					
					case 'yestoall':
					case 'yes':
						$existingFile->delete();
						
						if($params['overwrite']=='yes')
							$params['overwrite']='ask';
						break;
						
					case 'notoall':
					case 'no':	
						if($params['overwrite']=='no')
							$params['overwrite']='ask';
						
						continue;
						
						break;
						
				}
			}

			if ($params['paste_mode'] == 'cut') {
				if (!$file->move($destinationFolder))
					throw new Exception("Could not move " . $file->name);
			}else {
				if (!$file->copy($destinationFolder))
					throw new Exception("Could not copy " . $file->name);
			}
		}

		while($folder_id=array_shift(GO::session()->values['files']['pasteIds']['folders'])){
			$folder = GO_Files_Model_Folder::model()->findByPk($folder_id);
			
			$existingFolder = $destinationFolder->hasFolder($file->name);
			if ($existingFolder) {
				switch($params['overwrite']){
					case 'ask':
						array_unshift(GO::session()->values['folders']['pasteIds']['folders'], $file_id);
						$response['fileExists'] = $file->name;
						return $response;
						break;
					
					case 'yestoall':
					case 'yes':
						$existingFolder->delete();
						
						if($params['overwrite']=='yes')
							$params['overwrite']='ask';
						break;
						
					case 'notoall':
					case 'no':	
						if($params['overwrite']=='no')
							$params['overwrite']='ask';
						
						continue;
						
						break;
						
				}
			}

			if ($params['paste_mode'] == 'cut') {
				if (!$folder->move($destinationFolder))
					throw new Exception("Could not move " . $folder->name);
			}else {
				if (!$folder->copy($destinationFolder))
					throw new Exception("Could not copy " . $folder->name);
			}
		}

		return $response;
	}

	private function _splitFolderAndFileIds($ids) {
		$fileIds = array();
		$folderIds = array();


		foreach ($ids as $typeId) {
			if (substr($typeId, 0, 1) == 'd') {
				$folderIds[] = substr($typeId, 2);
			} else {
				$fileIds[] = substr($typeId, 2);
			}
		}

		return array('files' => $fileIds, 'folders' => $folderIds);
	}

	private function _listShares() {

		$grid = new GO_Base_Provider_Grid();
		$grid->setFormatRecordFunction(array($this, 'formatListRecord'));
		$findParams = $grid->getDefaultParams();
		$stmt = GO_Files_Model_Folder::model()->findShares($findParams);
		$grid->setStatement($stmt);

		return $grid->getData();
	}

	public function actionList($params) {

		if ($params['folder_id'] == 'shared')
			return $this->_listShares();

		//get the folder that contains the files and folders to list.
		//This will check permissions too.
		$folder = GO_Files_Model_Folder::model()->findByPk($params['folder_id']);


		//useful information for the view.
		$response['path'] = $folder->path;
		$response['thumbs'] = $folder->thumbs; //Show this page in thumbnails or list
		$response['parent_id'] = $folder->parent_id;


		$grid = new GO_Base_Provider_Grid();


		//handle delete request for both files and folder
		if (isset($params['delete_keys'])) {

			$ids = $this->_splitFolderAndFileIds(json_decode($params['delete_keys'], true));

			$params['delete_keys'] = json_encode($ids['folders']);
			$grid->processDeleteActions($params, "GO_Files_Model_Folder");

			$params['delete_keys'] = json_encode($ids['files']);
			$grid->processDeleteActions($params, "GO_Files_Model_File");
		}


		$grid->setFormatRecordFunction(array($this, 'formatListRecord'));
		$findParams = $grid->getDefaultParams(array(
				'ignoreAcl' => true
						));
		$stmt = $folder->folders($findParams);
		$grid->setStatement($stmt);

		$response = array_merge($response, $grid->getData());


		//add files to the listing if it fits
		$folderPages = floor($stmt->foundRows / $findParams['limit']);
		$foldersOnLastPage = $stmt->foundRows - ($folderPages * $findParams['limit']);

		//$isOnLastPageofFolders = $stmt->foundRows < ($findParams['limit'] + $findParams['start']);

		if (count($response['results'])) {
			$fileStart = $findParams['start'] - $folderPages * $findParams['limit'];
			$fileLimit = $findParams['limit'] - $foldersOnLastPage;
		} else {
			$fileStart = $findParams['start'] - $stmt->foundRows;
			$fileLimit = $findParams['limit'];
		}
		
		if($fileStart>=0) {
			$findParams = $grid->getDefaultParams(array(
					'limit' => $fileLimit,
					'start' => $fileStart
							));

			$stmt = $folder->files($findParams);
			$grid->setStatement($stmt);

			$filesResponse = $grid->getData();

			$response['total']+=$filesResponse['total'];
			$response['results'] = array_merge($response['results'], $filesResponse['results']);
		}else
		{
			$record = $folder->files(array(
					'single'=>true,
					'fields'=>'count(*) as total'
			));
			$response['total']+=$record['total'];
		}

		return $response;
	}

	public function formatListRecord($record, $model, $grid) {

		$record['path'] = $model->path;

		if ($model instanceof GO_Files_Model_Folder) {
			$record['type_id'] = 'd:' . $model->id;
			$record['type'] = GO::t('folder', 'files');
			$record['size'] = '-';
			$record['extension'] = 'folder';
		} else {
			$record['type_id'] = 'f:' . $model->id;
			$record['type'] = $model->fsFile->mimeType();
			$record['extension'] = $model->fsFile->extension();
		}
		$record['thumb_url'] = $model->thumbURL;

		return $record;
	}
	
	
	private function _checkExistingModelFolder($model,$folder,$mustExist=false){
		
		$files_folder_id=0;
		
		//todo test this:	
//		if(!isset($model->acl_id) && empty($params['mustExist'])){
//			//if this model is not a container like an addressbook but a contact
//			//then delete the folder if it's empty.
//			$ls = $folder->fsFolder->ls();
//			if(!count($ls) && $folder->fsFolder->mtime()<time()-60){
//				$folder->delete();
//				$response['files_folder_id']=$model->files_folder_id=0;
//				$model->save();
//				return $response;
//			}
//		}
		

				
		$currentPath = $folder->path;
		//strip the (n) part at the end of the path that is added when a duplicate
		//is found.
		$currentPath = preg_replace('/ \([0-9]+\)$/', '', $currentPath);
		$newPath = $model->buildFilesPath();
		
		
		if($currentPath!=$newPath){

			//model has a new path. We must move the current folder					
			$destinationFolder = GO_Files_Model_Folder::model()->findByPath(
							dirname($newPath), true);

			$folder->name = GO_Base_Fs_File::utf8Basename($newPath);
			$folder->folder_id=$destinationFolder->id;
			$folder->systemSave=true;
			$folder->save();
		}
		
		return $folder->id;
	}
	
	public function _createNewModelFolder($model){
		
		$f = new GO_Base_Fs_Folder(GO::config()->file_storage_path.$model->buildFilesPath());
		$fullPath = $f->appendNumberToNameIfExists();
		$relPath = str_replace(GO::config()->file_storage_path, '', $fullPath);

		$folder = GO_Files_Model_Folder::model()->findByPath($relPath, true);
		if(!$folder->acl_id && isset($model->acl_id)){
			$folder->acl_id=$model->acl_id;			
		}
		
		$folder->visible=0;
		$folder->readonly=1;
		$folder->systemSave=true;
		$folder->save();
		
		return $folder->id;
	}
	
	/**
	 * check if a model folder exists
	 * 
	 * @param type $params
	 * @return type 
	 */
	public function actionCheckModelFolder($params){		
		$model = GO::getModel($params['model'])->findByPk($params['id']);
		
		$response['success']=true;
		$response['files_folder_id']=$this->checkModelFolder($model, true, !empty($params['mustExist']));
		return $response;
	}
	
	
	public function checkModelFolder($model, $saveModel=false, $mustExist=false){
		$folder = false;
		if($model->files_folder_id>0){
			$folder = GO_Files_Model_Folder::model()->findByPk($model->files_folder_id);			
		}
		
		if($folder){			
			$model->files_folder_id=$this->_checkExistingModelFolder($model, $folder, $mustExist);
			
			if($saveModel)
				$model->save();
			
		}elseif(isset($model->acl_id) || $mustExist){
			//this model has an acl_id. So we should create a shared folder with this acl.
			//this folder should always exist.
			
			//only new models that have it's own acl field should always have a folder.
			//otherwise it will be created when first accessed.
			$model->files_folder_id=$this->_createNewModelFolder($model);
			
			if($saveModel)
				$model->save();			
		}		
		
		return $model->files_folder_id;		
	}

}

