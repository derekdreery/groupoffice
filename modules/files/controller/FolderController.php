<?php

class GO_Files_Controller_Folder extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Files_Model_Folder';
	
	

	protected function allowGuests() {
		if($this->isCli())
			return array('syncfilesystem');
		else
			return parent::allowGuests();
	}
	
	
	protected function actionCache($params){
		GO_Files_Model_SharedRootFolder::model()->rebuildCache(GO::user()->id);
	}

	protected function actionSyncFilesystem($params){	
		
		if(!$this->isCli() && !GO::modules()->tools)
			throw new GO_Base_Exception_AccessDenied();
		
		$oldAllowDeletes = GO_Base_Fs_File::setAllowDeletes(false);

		GO::$disableModelCache=true; //for less memory usage
		ini_set('max_execution_time', '0');

		GO::session()->runAsRoot();

		$folders = array('users','projects','addressbook','notes','tickets');
		
		$billingFolder = new GO_Base_Fs_Folder(GO::config()->file_storage_path.'billing');
		if($billingFolder->exists()){
			$bFolders = $billingFolder->ls();
			
			foreach($bFolders as $folder){		
					if($folder->isFolder() && $folder->name()!='notifications'){
						$folders[]='billing/'.$folder->name();
					}
			}		
		}

		echo "<pre>";
		foreach($folders as $name){
			echo "Syncing ".$name."\n";
			try{
				$folder = GO_Files_Model_Folder::model()->findByPath($name, true);
				$folder->syncFilesystem(true);
			}
			catch(Exception $e){
				if (PHP_SAPI != 'cli')
					echo "<span style='color:red;'>".$e->getMessage()."</span>\n";
				else
					echo $e->getMessage()."\n";
			}
		}

		echo "Done\n";

		GO_Base_Fs_File::setAllowDeletes($oldAllowDeletes);
		$folders = array('email', 'billing/notifications');

		foreach($folders as $name){

			echo "Deleting ".$name."\n";
			GO_Files_Model_Folder::$deleteInDatabaseOnly=true;
			GO_Files_Model_File::$deleteInDatabaseOnly=true;
			try{
				$folder = GO_Files_Model_Folder::model()->findByPath($name);
				if($folder)
						$folder->delete();
			}
			catch(Exception $e){
				if (PHP_SAPI != 'cli')
					echo "<span style='color:red;'>".$e->getMessage()."</span>\n";
				else
					echo $e->getMessage()."\n";
			}
		}
	}

	private function _getExpandFolderIds($params){
		$expandFolderIds=array();
		if(!empty($params['expand_folder_id']) && $params['expand_folder_id']!='shared') {
			$expandFolderIds=  GO_Files_Model_Folder::model()->getFolderIdsInPath($params['expand_folder_id']);
		}
		return $expandFolderIds;
	}

	private function _buildSharedTree($expandFolderIds){
		
		
		GO_Files_Model_SharedRootFolder::model()->rebuildCache(GO::user()->id);
		
		$response=array();
		
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->joinRelation('sharedRootFolders')
						->ignoreAcl()
						->order('name','ASC')
						->limit(200);
		
		$findParams->getCriteria()
					->addCondition('user_id', GO::user()->id,'=','sharedRootFolders');
		
		
		
		$shares = GO_Files_Model_Folder::model()->find($findParams);
		foreach($shares as $folder){
			$response[]=$this->_folderToNode($folder, $expandFolderIds, false);
		}

		return $response;

	}


	protected function actionTree($params) {

		//refresh forces sync with db
		if(!empty($params['sync_folder_id'])){
			if($params['sync_folder_id']=="shared"){
				GO_Files_Model_SharedRootFolder::model()->rebuildCache(GO::user()->id, true);
			}else
			{
				
			
				$syncFolder = GO_Files_Model_Folder::model()->findByPk($params['sync_folder_id']);
				if($syncFolder)
					$syncFolder->syncFilesystem();
			}
		}

		$response = array();

		$expandFolderIds = $this->_getExpandFolderIds($params);

		$showFiles = isset($params['showFiles']);

		switch ($params['node']) {
			case 'shared':
				$response=$this->_buildSharedTree($expandFolderIds);
				break;
			case 'root':
				if (!empty($params['root_folder_id'])) {
					$folder = GO_Files_Model_Folder::model()->findByPk($params['root_folder_id']);
//					$folder->checkFsSync();
					$node = $this->_folderToNode($folder, $expandFolderIds, true, $showFiles);
					$response[] = $node;
				} else {
					$folder = GO_Files_Model_Folder::model()->findHomeFolder(GO::user());

//					$folder->checkFsSync();

					$node = $this->_folderToNode($folder, $expandFolderIds, true, $showFiles);
					$node['text'] = GO::t('personal', 'files');
					$node['iconCls'] = 'folder-home';
					$node['path'] = $folder->path;
					$response[] = $node;


					$node = array(
							'text' => GO::t('shared', 'files'),
							'id' => 'shared',
							'readonly' => true,
							'draggable' => false,
							'allowDrop' => false,
							'parent_id'=>0,
							'iconCls' => 'folder-shares',
							'path'=>"shared"							
					);
					
					//expand shares for non admins only. Admin may see too many folders.
					if(!GO::user()->isAdmin()){
						$node['expanded']=true;
						$node['children']=$this->_buildSharedTree($expandFolderIds);
					}
					
					$response[] = $node;

					if (GO::modules()->addressbook) {
						$contactsFolder = GO_Files_Model_Folder::model()->findByPath('addressbook');

						if ($contactsFolder) {
							$node = $this->_folderToNode($contactsFolder, $expandFolderIds, false, $showFiles);
							$node['text'] = GO::t('addressbook', 'addressbook');
							$response[] = $node;
						}
					}

					if (GO::modules()->projects) {
						$projectsFolder = GO_Files_Model_Folder::model()->findByPath('projects');

						if ($projectsFolder) {
							$node = $this->_folderToNode($projectsFolder, $expandFolderIds, false, $showFiles);
							$node['text'] = GO::t('projects', 'projects');
							$response[] = $node;
						}
					}
					
					if(GO::user()->isAdmin()){
						$logFolder = GO_Files_Model_Folder::model()->findByPath('log', true);
//						$logFolder->syncFilesystem();
						
						$node = $this->_folderToNode($logFolder, $expandFolderIds, false, $showFiles);
						$node['text']=GO::t('logFiles');
						
						$response[]=$node;
					}
				}



				break;

			default:
				$folder = GO_Files_Model_Folder::model()->findByPk($params['node']);
				if(!$folder)
					return false;
				
//				$folder->checkFsSync();

				$stmt = $folder->getSubFolders(GO_Base_Db_FindParams::newInstance()
							->limit(200)//not so nice hardcoded limit
							->order('name','ASC'));

				while ($subfolder = $stmt->fetch()) {
					$response[] = $this->_folderToNode($subfolder, $expandFolderIds, false, $showFiles);
					if ($showFiles) {
						$response = array_merge($response, $this->_addFileNodes($subfolder->parent));
					}
				}

				break;
		}

		return $response;
	}

	private function _folderToNode($folder, $expandFolderIds=array(), $withChildren=true, $withFiles = false) {
		$expanded = $withChildren || in_array($folder->id, $expandFolderIds);
		$node = array(
				'text' => $folder->name,
				'id' => $folder->id,
				'draggable' => false,
				'iconCls' => !$folder->acl_id || $folder->readonly ? 'folder-default' : 'folder-shared',
				'expanded' => $expanded,
				'parent_id'=>$folder->parent_id,
				'path'=>$folder->path
		);

		if ($expanded) {
			$node['children'] = array();
			$stmt = $folder->getSubFolders(GO_Base_Db_FindParams::newInstance()
							->limit(100)//not so nice hardcoded limit
							->order('name','ASC'));
			while ($subfolder = $stmt->fetch()) {
				$node['children'][] = $this->_folderToNode($subfolder, $expandFolderIds, false, $withFiles);
			}

			if ($withFiles) {
				$node['children'] = array_merge($node['children'], $this->_addFileNodes($folder));
			}
		} else {
			if (!$folder->hasChildren()) {
				//it doesn't habe any subfolders so instruct the client about this
				//so it can present the node as a leaf.
				$node['children'] = array();
				$node['expanded'] = true;

				if ($withFiles) {
					$node['children'] = array_merge($node['children'], $this->_addFileNodes($folder));
				}
			}
		}

		return $node;
	}

	private function _addFileNodes($folder) {
		$stmt = $folder->files();

		$files = array();
		while($file = $stmt->fetch()) {
			$fileNode = array(
				'text' => $file->name,
				'id' => $file->id,
				'draggable' => false,
				'leaf' => true,
				'path'=> $folder->path . '/' . $file->name,
				'iconCls' => 'filetype-' . strtolower($file->extension),
				'checked' => false
			);

			$files[] = $fileNode;
			GO::debug($file);
		}
		return $files;
	}

	protected function beforeSubmit(&$response, &$model, &$params) {

		if(isset($params['share']) && !$model->readonly && !$model->isSomeonesHomeFolder() && $model->checkPermissionLevel(GO_Base_Model_Acl::MANAGE_PERMISSION)){
			if ($params['share']==1 && $model->acl_id == 0) {
				$model->visible = 1;

//				$acl = new GO_Base_Model_Acl();
//				$acl->description = $model->tableName() . '.' . $model->aclField();
//				$acl->user_id = GO::user() ? GO::user()->id : 1;
//				$acl->save();
				$model->setNewAcl();
				
				//for enabling the acl permissions panel
				$response['acl_id']=$model->acl_id;
			}

			if ($params['share']==0 && $model->acl_id > 0) {
				$model->acl->delete();
				$model->acl_id = $response['acl_id'] = 0;
			}
		}

		if(!empty($params['name']) && GO::config()->convert_utf8_filenames_to_ascii)
			$params['name']=GO_Base_Util_String::utf8ToASCII ($params['name']);

		return parent::beforeSubmit($response, $model, $params);
	}

	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {

		//output the new path of the file if we changed the name.
		if (isset($modifiedAttributes['name']))
			$response['new_path'] = $model->path;

		$notifyRecursive = !empty($params['notifyRecursive']) && $params['notifyRecursive']=='true' ? true : false;

		if(isset($params['notify'])){
			if ($params['notify']==1)
				$model->addNotifyUser(GO::user()->id,$notifyRecursive);

			if ($params['notify']==0)
				$model->removeNotifyUser(GO::user()->id,$notifyRecursive);
		}

		parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}

	protected function afterLoad(&$response, &$model, &$params) {

		$response['data']['path'] = $model->path;
		$response['data']['notify'] = $model->hasNotifyUser(GO::user()->id);
		$response['data']['is_someones_home_dir'] = $model->isSomeonesHomeFolder();
		$response['data']['username'] = !empty($model->user) ? $model->user->name : '';
		$response['data']['musername'] = !empty($model->mUser) ? $model->mUser->name : '';
		
		$response['data']['url']=$model->externalUrl;

		return parent::afterLoad($response, $model, $params);
	}

	protected function afterDisplay(&$response, &$model, &$params) {
		$response['data']['path'] = $model->path;
		$response['data']['type'] = GO::t('folder', 'files');
		
		$response['data']['url']=$model->externalUrl;

		return parent::afterDisplay($response, $model, $params);
	}

	protected function actionPaste($params) {

		$response['success'] = true;

		if (!isset($params['overwrite']))
			$params['overwrite'] = 'ask'; //can be ask, yes, no


		if (isset($params['ids']) && $params['overwrite'] == 'ask')
			GO::session()->values['files']['pasteIds'] = $this->_splitFolderAndFileIds(json_decode($params['ids'], true));

		$destinationFolder = GO_Files_Model_Folder::model()->findByPk($params['destination_folder_id']);

		if (!$destinationFolder->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION))
			throw new GO_Base_Exception_AccessDenied();

		while ($file_id = array_shift(GO::session()->values['files']['pasteIds']['files'])) {
			$file = GO_Files_Model_File::model()->findByPk($file_id);

			$newFileName=$file->name;

			$existingFile = $destinationFolder->hasFile($file->name);

			//if it's a copy-paste in the same folder then append a number.
			if($existingFile && $existingFile->id==$file->id){
				if($params['paste_mode'] == 'cut')
					continue;
				else
				{
					$fsFile = $existingFile->fsFile;
					$fsFile->appendNumberToNameIfExists();
					$newFileName = $fsFile->name();
					$existingFile=false;
				}
			}

			if ($existingFile) {
				switch ($params['overwrite']) {
					case 'ask':
						array_unshift(GO::session()->values['files']['pasteIds']['files'], $file_id);
						$response['fileExists'] = $file->name;
						return $response;
						break;

					case 'yestoall':
					case 'yes':
						$existingFile->delete();

						if ($params['overwrite'] == 'yes')
							$params['overwrite'] = 'ask';
						break;

					case 'notoall':
					case 'no':
						if ($params['overwrite'] == 'no')
							$params['overwrite'] = 'ask';

						continue;

						break;
				}
			}

			if ($params['paste_mode'] == 'cut') {
				if (!$file->move($destinationFolder))
					throw new Exception("Could not move " . $file->name);
			}else {
				if (!$file->copy($destinationFolder,$newFileName))
					throw new Exception("Could not copy " . $file->name);
			}
		}

		while ($folder_id = array_shift(GO::session()->values['files']['pasteIds']['folders'])) {
			$folder = GO_Files_Model_Folder::model()->findByPk($folder_id);
			
			if($params['paste_mode']=='copy' && $folder->parent_id==$destinationFolder->id){
				//pasting in the same folder. Append (1).
				$fsFolder = $folder->fsFolder;
				$fsFolder->appendNumberToNameIfExists();
				$folderName=$fsFolder->name();				
			}  else {
				$folderName = $folder->name;
			}

			$existingFolder = $destinationFolder->hasFolder($folderName);
			if ($existingFolder) {
				switch ($params['overwrite']) {
					case 'ask':
						array_unshift(GO::session()->values['files']['pasteIds']['folders'], $folder_id);
						$response['fileExists'] = $folderName;
						return $response;
						break;

					case 'yestoall':
					case 'yes':
						//$existingFolder->delete();

						if ($params['overwrite'] == 'yes')
							$params['overwrite'] = 'ask';
						break;

					case 'notoall':
					case 'no':
						if ($params['overwrite'] == 'no')
							$params['overwrite'] = 'ask';

						continue;

						break;
				}
			}

			if ($params['paste_mode'] == 'cut') {
				if (!$folder->move($destinationFolder))
					throw new Exception("Could not move " . $folder->name);
			}else {
				if (!$folder->copy($destinationFolder, $folderName))
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

	private function _listShares($params) {

		//$store = GO_Base_Data_Store::newInstance(GO_Files_Model_Folder::model());
//
//      //set sort aliases
//      $store->getColumnModel()->formatColumn('type', '$model->type',array(),'name');
//      $store->getColumnModel()->formatColumn('size', '"-"',array(),'name');
//
//      $store->getColumnModel()->setFormatRecordFunction(array($this, 'formatListRecord'));
//      $findParams = $store->getDefaultParams($params);
//      $stmt = GO_Files_Model_Folder::model()->findShares($findParams);
//      $store->setStatement($stmt);
//
//      $response = $store->getData();
		
		
//		$fp = GO_Base_Db_FindParams::newInstance()->limit(100);
		
		//$fp = GO_Base_Db_FindParams::newInstance()->calcFoundRows();
		
		$cm = new GO_Base_Data_ColumnModel('GO_Files_Model_Folder');
		$cm->setFormatRecordFunction(array($this, 'formatListRecord'));
		
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->joinRelation('sharedRootFolders')
						->ignoreAcl()
						->order('name','ASC');
		
		$findParams->getCriteria()
					->addCondition('user_id', GO::user()->id,'=','sharedRootFolders');
		
		
		$store = new GO_Base_Data_DbStore('GO_Files_Model_Folder',$cm, $params, $findParams);
		$response = $store->getData();
		$response['permission_level']=GO_Base_Model_Acl::READ_PERMISSION;
//		$response['results']=array();
//		$shares =GO_Files_Model_Folder::model()->getTopLevelShares($fp);
//		foreach($shares as $folder){
//			$record=$folder->getAttributes("html");
//			$record = $this->formatListRecord($record, $folder, false);
//			$response['results'][]=$record;
//		}
//		$response['total']=$shares->foundRows;
		return $response;
	}

	private $_listFolderPermissionLevel;

	protected function actionList($params) {

            if (!empty($params['query']))
                return $this->_searchFiles($params);
            
		if ($params['folder_id'] == 'shared')
			return $this->_listShares($params);

		//get the folder that contains the files and folders to list.
		//This will check permissions too.
		$folder = GO_Files_Model_Folder::model()->findByPk($params['folder_id']);
		if(!$folder)
			return false;

		$this->_listFolderPermissionLevel=$folder->permissionLevel;

		$response['permission_level']=$folder->permissionLevel;//$folder->readonly ? GO_Base_Model_Acl::READ_PERMISSION : $folder->permissionLevel;

		if(empty($params['skip_fs_sync']))
			$folder->checkFsSync();

		//useful information for the view.
		$response['path'] = $folder->path;

		//Show this page in thumbnails or list
		$folderPreference = GO_Files_Model_FolderPreference::model()->findByPk(array('user_id'=>GO::user()->id,'folder_id'=>$folder->id));
		if($folderPreference)
			$response['thumbs']=$folderPreference->thumbs;
		else
			$response['thumbs']=0;

		$response['parent_id'] = $folder->parent_id;

		//locked state
		$response['lock_state']=!empty($folder->apply_state);
		$response['cm_state']=isset($folder->cm_state) && !empty($folder->apply_state) ? $folder->cm_state : "";
		$response['may_apply_state']=GO_Base_Model_Acl::hasPermission($folder->getPermissionLevel(), GO_Base_Model_Acl::MANAGE_PERMISSION);

//      if($response["lock_state"]){
//          $state = json_decode($response["cm_state"]);
//
//          if(isset($state->sort)){
//              $params['sort']=$state->sort->field;
//              $params['dir']=$state->sort->direction;
//          }
//      }


		$store = GO_Base_Data_Store::newInstance(GO_Files_Model_Folder::model());

		//set sort aliases
		$store->getColumnModel()->formatColumn('type', '',array(),'name');
		$store->getColumnModel()->formatColumn('size', '"-"',array(),'name');
		$store->getColumnModel()->formatColumn('locked_user_id', '"0"');


		//handle delete request for both files and folder
		if (isset($params['delete_keys'])) {

			$ids = $this->_splitFolderAndFileIds(json_decode($params['delete_keys'], true));

			$params['delete_keys'] = json_encode($ids['folders']);
			$store->processDeleteActions($params, "GO_Files_Model_Folder");

			$params['delete_keys'] = json_encode($ids['files']);
			$store->processDeleteActions($params, "GO_Files_Model_File");
		}


		$store->getColumnModel()->setFormatRecordFunction(array($this, 'formatListRecord'));

		$findParams = $store->getDefaultParams($params);

		//sorting on custom fields doesn't work for folders
		if(isset($params['sort']) && substr($params['sort'],0,4)=='col_')
			$findParams->order ("name", $params['dir']);

		$findParamsArray = $findParams->getParams();
		if(!isset($findParamsArray['start']))
			$findParamsArray['start']=0;

		if(!isset($findParamsArray['limit']))
			$findParamsArray['limit']=0;

		//$stmt = $folder->folders($findParams);

		$stmt = $folder->getSubFolders($findParams);

		$store->setStatement($stmt);

		$response = array_merge($response, $store->getData());

		//add files to the listing if it fits
		$folderPages = floor($stmt->foundRows / $findParamsArray['limit']);
		$foldersOnLastPage = $stmt->foundRows - ($folderPages * $findParamsArray['limit']);

		//$isOnLastPageofFolders = $stmt->foundRows < ($findParams['limit'] + $findParams['start']);

		if (count($response['results'])) {
			$fileStart = $findParamsArray['start'] - $folderPages * $findParamsArray['limit'];
			$fileLimit = $findParamsArray['limit'] - $foldersOnLastPage;
		} else {
			$fileStart = $findParamsArray['start'] - $stmt->foundRows;
			$fileLimit = $findParamsArray['limit'];
		}

		if ($fileStart >= 0) {

			$store->resetResults();

			$store->getColumnModel()->formatColumn('type', '',array(),'extension');
			$store->getColumnModel()->formatColumn('locked', '$model->isLocked()');
			$store->getColumnModel()->formatColumn('locked_user_id', '$model->locked_user_id');
			$store->getColumnModel()->formatColumn('folder_id', '$model->folder_id');

			$findParams = $store->getDefaultParams($params)
							->limit($fileLimit)
							->start($fileStart);
			
			// Handle the files filter
			if(!empty($params['files_filter'])){
				$extensions= explode(',',$params['files_filter']);
				$findParams->getCriteria()->addInCondition('extension', $extensions);
			}

			$stmt = $folder->files($findParams);
			$store->setStatement($stmt);

			$filesResponse = $store->getData();

			$response['total']+=$filesResponse['total'];
			$response['results'] = array_merge($response['results'], $filesResponse['results']);
		} else {
			$record = $folder->files(GO_Base_Db_FindParams::newInstance()->single()->select('count(*) as total'));
			$response['total']+=$record->total;
		}

		return $response;
	}

        private function _searchFiles($params) {
            $response['success'] = true;
            
            $queryStr = !empty($params['query']) ? '%'.$params['query'].'%' : '%';
            $limit = !empty($params['limit']) ? $params['limit'] : 30;
            $start = !empty($params['start']) ? $params['start'] : 0;
            
            $aclJoinCriteria = GO_Base_Db_FindCriteria::newInstance()->addRawCondition('a.acl_id', 'sc.acl_id', '=', false);

            $aclWhereCriteria = GO_Base_Db_FindCriteria::newInstance()
                ->addCondition('user_id', GO::user()->id, '=', 'a', false)
                ->addInCondition("group_id", GO_Base_Model_User::getGroupIds(GO::user()->id), "a", false);
            
            $findParams = GO_Base_Db_FindParams::newInstance()
                ->select('*')
                ->ignoreAcl()
                ->joinModel(array(
                  'model'=>'GO_Base_Model_SearchCacheRecord',
                  'localTableAlias'=>'t',
                  'localField'=>'id',
                  'foreignField'=>'model_id',
                  'tableAlias'=>'sc'
                ))
                ->join(GO_Base_Model_AclUsersGroups::model()->tableName(), $aclJoinCriteria, 'a', 'INNER')->debugSql()
                ->criteria(
                  GO_Base_Db_FindCriteria::newInstance()
                    ->addCondition('model_type_id', GO::getModel('GO_Files_Model_File')->modelTypeId(),  '=', 'sc', true)
                    ->mergeWith(
                      GO_Base_Db_FindCriteria::newInstance()
                        ->addCondition('name', $queryStr, 'LIKE', 'sc', false)
                        ->addCondition('keywords', $queryStr, 'LIKE', 'sc', false)
                    )
                    ->mergeWith($aclWhereCriteria)
                );
            
            $filesStmt = GO_Files_Model_File::model()->find($findParams);
            
            $response['total'] = $filesStmt->rowCount();
            
            $filesStmt = GO_Files_Model_File::model()->find(
              $findParams
                ->start($start)
                ->limit($limit)
            );
            
            $response['results'] = array();
            $response['cm_state'] = '';
            $response['may_apply_state'] = false;
            $response['lock_state'] = false;
            $response['permission_level'] = 0;
            
            foreach ($filesStmt as $searchFileModel) {
                $response['results'][] = $searchFileModel->getJsonData();
            }
            
            return $response;
        }
        
	public function formatListRecord($record, $model, $store) {

		$record['path'] = $model->path;

		if ($model instanceof GO_Files_Model_Folder) {
			$record['type_id'] = 'd:' . $model->id;
			$record['type'] = GO::t('folder', 'files');
			$record['size'] = '-';
			$record['extension'] = 'folder';
			$record['readonly']=$model->readonly;
		} else {
			$record['type_id'] = 'f:' . $model->id;
			$record['type'] = $model->fsFile->typeDescription();
			$record['extension'] = strtolower($model->extension);
			$record['size']=$model->size;
			$record['permission_level']=$this->_listFolderPermissionLevel;
			$record['unlock_allowed']=$model->unlockAllowed();
			$record['handler']='startjs:function(){'.$model->getDefaultHandler()->getHandler($model).'}:endjs';
		}
		$record['thumb_url'] = $model->thumbURL;

		return $record;
	}

	private function _checkExistingModelFolder($model, $folder, $mustExist=false) {

		GO::debug("Check existing model folder ".$model->className()."(ID:".$model->id." Folder ID: ".$folder->id." ACL ID: ".$model->findAclId().")");

		if(!$folder->fsFolder->exists())
		{
			//throw new Exception("Fs folder doesn't exist! ".$folder->fsFolder->path());
			GO::debug("Deleting it because filesystem folder doesn't exist");
			$folder->readonly = 1; //makes sure acl is not deleted
			$folder->delete(true);
			if($mustExist || isset($model->acl_id))
				return $this->_createNewModelFolder($model);
			else
				return 0;
		}

		//todo test this:
//      if(!isset($model->acl_id) && empty($params['mustExist'])){
//          //if this model is not a container like an addressbook but a contact
//          //then delete the folder if it's empty.
//          $ls = $folder->fsFolder->ls();
//          if(!count($ls) && $folder->fsFolder->mtime()<time()-60){
//              $folder->delete();
//              $response['files_folder_id']=$model->files_folder_id=0;
//              $model->save();
//              return $response['files_folder_id'];
//          }
//      }



		$currentPath = $folder->path;
		$newPath = $model->buildFilesPath();

		if(!$newPath)
			return false;

		if(GO::router()->getControllerAction()=='checkdatabase'){
			//Always ensure folder exists on check database
			$destinationFolder = GO_Files_Model_Folder::model()->findByPath(
							dirname($newPath), true, array('acl_id'=>$model->findAclId(),'readonly'=>1));
		}

		if ($currentPath != $newPath) {

			GO::debug("Moving folder ".$currentPath." to ".$newPath);

			//model has a new path. We must move the current folder
			$destinationFolder = GO_Files_Model_Folder::model()->findByPath(
							dirname($newPath), true, array('acl_id'=>$model->findAclId(),'readonly'=>1));


			//sometimes the folder must be moved into a folder with the same. name
			//for example:
			//projects/Name must be moved into projects/Name/Name
			//then we temporarily move it to a temp name
			if($destinationFolder->id==$folder->id){
				GO::debug("Destination folder is the same!");
				$folder->name=uniqid();
				$folder->systemSave=true;
				$folder->save(true);

				GO::debug("Moved folder to temp:".$folder->fsFolder->path());

				GO::modelCache()->remove("GO_Files_Model_Folder");

				$destinationFolder = GO_Files_Model_Folder::model()->findByPath(
							dirname($newPath), true);

				GO::debug("Now moving to:".$destinationFolder->fsFolder->path());

			}

			if($destinationFolder->id==$folder->id){
				throw new Exception("Same ID's!");
			}

			$fsFolder = new GO_Base_Fs_Folder($newPath);
//          $fsFolder->appendNumberToNameIfExists();

			if(($existingFolder = $destinationFolder->hasFolder($fsFolder->name()))){
				GO::debug("Merging into existing folder.".$folder->path.' ('.$folder->id.') -> '.$existingFolder->path.' ('.$existingFolder->id.')');
				//if (!empty($model->acl_id))
				$existingFolder->acl_id = $model->findAclId();
				$existingFolder->visible = 0;
				$existingFolder->readonly = 1;
				$existingFolder->save(true);

				$folder->systemSave = true;

				$existingFolder->moveContentsFrom($folder, true);

				//delete empty folder.
				$folder->readonly = 1; //makes sure acl is not deleted
				$folder->delete();

				return $existingFolder->id;

			}else
			{
//              if ($model->acl_id>0)
//                  $folder->acl_id = $model->acl_id;
//              else
//                  $folder->acl_id=0;
				$folder->acl_id = $model->findAclId();

				$folder->name = $fsFolder->name();
				$folder->parent_id = $destinationFolder->id;
				$folder->systemSave = true;
				$folder->visible = 0;
				$folder->readonly = 1;
				if($folder->isModified())
					$folder->save(true);
			}
		}else
		{
			GO::debug("No change needed");
//          if ($model->acl_id>0)
//              $folder->acl_id = $model->acl_id;
//          else
//              $folder->acl_id=0;
			$folder->acl_id = $model->findAclId();
			$folder->systemSave = true;
			$folder->visible = 0;
			$folder->readonly = 1;
			if($folder->isModified())
				$folder->save(true);
		}

		return $folder->id;
	}

	private function _createNewModelFolder(GO_Base_Db_ActiveRecord $model) {

		//GO::debug("Create new model folder ".$model->className()."(ID:".$model->id.")");

		$folder = GO_Files_Model_Folder::model()->findByPath($model->buildFilesPath(),true, array('acl_id'=>$model->findAclId(),'readonly'=>1));
		
		if(!$folder){
			throw new Exception("Failed to create folder ".$model->buildFilesPath());
		}
//      if (!empty($model->acl_id))
//          $folder->acl_id = $model->acl_id;

		$folder->acl_id=$model->findAclId();
		$folder->visible = 0;
		$folder->readonly = 1;
		$folder->systemSave = true;
		$folder->save(true);

		return $folder->id;
	}

	/**
	 * check if a model folder exists
	 *
	 * @param type $params
	 * @return type
	 */
	protected function actionCheckModelFolder($params) {
		$model = GO::getModel($params['model'])->findByPk($params['id'],false, true);

		$response['success'] = true;
		$response['files_folder_id'] = $this->checkModelFolder($model, true, !empty($params['mustExist']));
		return $response;
	}

	public function checkModelFolder(GO_Base_Db_ActiveRecord $model, $saveModel=false, $mustExist=false) {
		$oldAllowDeletes = GO_Base_Fs_File::setAllowDeletes(false);

		$folder = false;
		if ($model->files_folder_id > 0)
			$folder = GO_Files_Model_Folder::model()->findByPk($model->files_folder_id, false, true);

		if ($folder) {
			$model->files_folder_id = $this->_checkExistingModelFolder($model, $folder, $mustExist);

			if ($saveModel && $model->isModified())
				$model->save(true);
		}elseif (isset($model->acl_id) || $mustExist) {
			//this model has an acl_id. So we should create a shared folder with this acl.
			//this folder should always exist.
			//only new models that have it's own acl field should always have a folder.
			//otherwise it will be created when first accessed.
			$model->files_folder_id = $this->_createNewModelFolder($model);

			if ($saveModel && $model->isModified())
				$model->save(true);
		}

		if(empty($model->files_folder_id))
			$model->files_folder_id=0;

		 GO_Base_Fs_File::setAllowDeletes($oldAllowDeletes);

		return $model->files_folder_id;
	}

	protected function actionProcessUploadQueue($params) {

		$response['success'] = true;

		if (!isset($params['overwrite']))
			$params['overwrite'] = 'ask'; //can be ask, yes, no

		$destinationFolder = GO_Files_Model_Folder::model()->findByPk($params['destination_folder_id']);

		if (!$destinationFolder->checkPermissionLevel(GO_Base_Model_Acl::CREATE_PERMISSION))
			throw new GO_Base_Exception_AccessDenied();


		while ($tmpfile = array_shift(GO::session()->values['files']['uploadqueue'])) {


			if(is_dir($tmpfile)){
				$folder = new GO_Base_Fs_Folder($tmpfile);
				if($folder->exists()){
					$folder->move($destinationFolder->fsFolder,false, true);
					$destinationFolder->addFileSystemFolder($folder);
				}
			} else {

				$file = new GO_Base_Fs_File($tmpfile);
				if($file->exists()){

					$existingFile = $destinationFolder->hasFile($file->name());
					if ($existingFile) {
						switch ($params['overwrite']) {
							case 'ask':
								array_unshift(GO::session()->values['files']['uploadqueue'], $tmpfile);
								$response['fileExists'] = $file->name();
								return $response;
								break;

							case 'yestoall':
							case 'yes':
								//we dont want overwrite file in no case
								$existingFile->replace($file);
								if ($params['overwrite'] == 'yes')
									$params['overwrite'] = 'ask';
								break;

							case 'notoall':
							case 'no':
								if ($params['overwrite'] == 'no')
									$params['overwrite'] = 'ask';

								continue;

								break;
						}
					} else {
						$destinationFolder->addFileSystemFile($file);
					}
					$response['success'] = true;
				}
			}
		}

		return $response;
	}

	protected function actionCompress($params) {

		$sources = json_decode($params['compress_sources'], true);


		$workingFolder = GO_Files_Model_Folder::model()->findByPk($params['working_folder_id']);
		$destinationFolder = GO_Files_Model_Folder::model()->findByPk($params['destination_folder_id']);
		$archiveFile = new GO_Base_Fs_File(GO::config()->file_storage_path.$destinationFolder->path . '/' . $params['archive_name'] . '.zip');
		
		if($archiveFile->exists())
			throw new Exception(sprintf(GO::t('filenameExists','files'), $archiveFile->stripFileStoragePath()));
		
		$sourceObjects = array();
		for($i=0;$i<count($sources);$i++){			
			$path = GO::config()->file_storage_path.$sources[$i];			
			$sourceObjects[]=GO_Base_Fs_Base::createFromPath($path);
		}
		
		if(GO_Base_Fs_Zip::create($archiveFile, $workingFolder->fsFolder, $sourceObjects)){
			GO_Files_Model_File::importFromFilesystem($archiveFile);
			$response['success']=true;
		}  else {
			throw new Exception("ZIP creation failed");
		}

		return $response;
	}


	protected function actionDecompress($params){
		if (!GO_Base_Util_Common::isWindows())
			putenv('LANG=en_US.UTF-8');

		$sources = json_decode($params['decompress_sources'], true);


		$workingFolder = GO_Files_Model_Folder::model()->findByPk($params['working_folder_id']);

		$workingPath = GO::config()->file_storage_path.$workingFolder->path;
		chdir($workingPath);


		while ($filePath = array_shift($sources)) {
			$file = new GO_Base_Fs_File(GO::config()->file_storage_path.$filePath);
			switch(strtolower($file->extension())) {
				case 'zip':					
					
					$folder = GO_Base_Fs_Folder::tempFolder(uniqid());
					
					if(class_exists("ZipArchive")){
						$zip = new ZipArchive;
						$zip->open($file->path());
						$zip->extractTo($folder->path());									
						$this->_convertZipEncoding($folder);
					}else
					{
						chdir($folder->path());					
						$cmd = GO::config()->cmd_unzip.' -n '.escapeshellarg($file->path());
						exec($cmd, $output, $ret);
						if($ret!=0)
						{
							throw new Exception("Could not decompress\n".implode("\n",$output));
						}
					}
					
					$items = $folder->ls();
					
					foreach($items as $item){
						$item->move(new GO_Base_Fs_Folder($workingPath));
					}
					
					$folder->delete();
					
					break;
				case 'gz':
				case 'tgz':
					$cmd = GO::config()->cmd_tar.' zxf '.escapeshellarg($file->path());
					exec($cmd, $output, $ret);

					if($ret!=0)
					{
						throw new Exception("Could not decompress\n".implode("\n",$output));
					}
					break;

				case 'tar':
					$cmd = GO::config()->cmd_tar.' xf '.escapeshellarg($file->path());
					
					exec($cmd, $output, $ret);

					if($ret!=0)
					{
						throw new Exception("Could not decompress\n".implode("\n",$output));
					}
					break;
			}
		}
		
		$workingFolder->syncFilesystem(true);

		return array('success'=>true);

	}
	
	private function _convertZipEncoding(GO_Base_Fs_Folder $folder, $charset='CP850'){
		$items = $folder->ls();
		
		foreach($items as $item){
			
			if(!GO_Base_Util_String::isUtf8($item->name()))
				$item->rename(GO_Base_Util_String::clean_utf8($item->name(), $charset));

			if($item->isFolder()){
				$this->_convertZipEncoding($item, $charset);
			}
		}
	}


	/**
	 * The savemailas module can send attachments along to be stored as files with
	 * a note, task, event etc.
	 *
	 * @param type $response
	 * @param type $model
	 * @param type $params
	 */
	public function processAttachments(&$response, &$model, &$params){
		//Does this belong in the controller?
		if (!empty($params['tmp_files'])) {
			$tmp_files = json_decode($params['tmp_files'], true);

			if(count($tmp_files)){
				$folder_id = $this->checkModelFolder($model, true, true);

				$folder = GO_Files_Model_Folder::model()->findByPk($folder_id);

				while ($tmp_file = array_shift($tmp_files)) {
					if (!empty($tmp_file['tmp_file'])) {

						$file = new GO_Base_Fs_File(GO::config()->tmpdir.$tmp_file['tmp_file']);
						$file->move(new GO_Base_Fs_Folder(GO::config()->file_storage_path . $folder->path));

						$folder->addFile($file->name());
					}
				}
			}
		}
	}


	protected function actionImages($params){
		if(isset($params["id"])){
			$currentFile = GO_Files_Model_File::model()->findByPk($params["id"]);
		}else
		{
			$currentFile = GO_Files_Model_File::model()->findByPath($params["path"]);
		}

		$folder = $currentFile->folder();

		$thumbParams = json_decode($params['thumbParams'], true);

		$response["success"]=true;
		$response['images']=array();
		$response['index']=$index=0;

		if(!isset($params["sort"]))
			$params["sort"]="name";

		if(!isset($params["dir"]))
			$params["dir"]="ASC";

		$findParams = GO_Base_Db_FindParams::newInstance()
						->order($params["sort"], $params["dir"]);

		$stmt = $folder->files($findParams);
		while($file = $stmt->fetch()){
			if($file->isImage()){
				if($file->id == $currentFile->id)
					$response['index']=$index;

				$index++;

				$response['images'][]=array(
					"name"=>$file->name,
					"download_path"=>$file->downloadUrl,
					"src"=>$file->getThumbUrl($thumbParams)
				);
			}
		}

		return $response;
	}
}
