<?php

class GO_Files_Controller_Folder extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Files_Model_Folder';
	
	
	protected function actionSyncFilesystem($params){
		
		
		GO::$ignoreAclPermissions=true; //allow this script access to all
		GO::$disableModelCache=true; //for less memory usage
		ini_set('max_execution_time', '300');
		session_write_close();		
		
		$folders = array('users','projects','addressbook','billing','notes','tickets');	
		
		echo "<pre>";
		foreach($folders as $name){
			
			echo "Syncing ".$name."\n";
			
			$folder = GO_Files_Model_Folder::model()->findByPath($name, true);
			$folder->syncFilesystem(true);
		}
		
		echo "Done\n";
		
		
//		$folders = array('billing','email');	
//		
//		foreach($folders as $name){
//			
//			echo "Deleting ".$name."\n";
//			
//			$folder = GO_Files_Model_Folder::model()->findByPath($name);
//			if($folder)
//				$folder->delete();
//		}
	}
	

	protected function actionTree($params) {
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
				$stmt = GO_Files_Model_Folder::model()->findShares(GO_Base_Db_FindParams::newInstance()->limit(100));
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
							'path'=>$folder->path
									//						'expanded'=>true,
									//						'children'=>array()
					);
					$response[] = $node;

					if (GO::modules()->addressbook) {
						$contactsFolder = GO_Files_Model_Folder::model()->findByPath('addressbook');

						if ($contactsFolder) {
							$node = $this->_folderToNode($contactsFolder, false);
							$node['text'] = GO::t('addressbook', 'addressbook');
							$response[] = $node;
						}
					}

					if (GO::modules()->projects) {
						$projectsFolder = GO_Files_Model_Folder::model()->findByPath('projects');

						if ($projectsFolder) {
							$node = $this->_folderToNode($projectsFolder, false);
							$node['text'] = GO::t('projects', 'projects');
							$response[] = $node;
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
				'expanded' => $withChildren,
				'parent_id'=>$folder->parent_id,
				'path'=>$folder->path
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

		$notifyRecursive = !empty($params['notifyRecursive']) && $params['notifyRecursive']=='true' ? true : false;
		
		if (isset($params['notify'])) {
			$model->addNotifyUser(GO::user()->id,$notifyRecursive);
		}

		if (!isset($params['notify'])){
			$model->removeNotifyUser(GO::user()->id,$notifyRecursive);			
		}

		parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}

	protected function afterLoad(&$response, &$model, &$params) {

		$response['data']['path'] = $model->path;
		$response['data']['notify'] = $model->hasNotifyUser(GO::user()->id);
		$response['data']['is_someones_home_dir'] = $model->isSomeonesHomeFolder();

		return parent::afterLoad($response, $model, $params);
	}

	protected function afterDisplay(&$response, &$model, &$params) {
		$response['data']['path'] = $model->path;
		$response['data']['type'] = GO::t('folder', 'files');

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

			$existingFile = $destinationFolder->hasFile($file->name);
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
				if (!$file->copy($destinationFolder))
					throw new Exception("Could not copy " . $file->name);
			}
		}

		while ($folder_id = array_shift(GO::session()->values['files']['pasteIds']['folders'])) {
			$folder = GO_Files_Model_Folder::model()->findByPk($folder_id);

			$existingFolder = $destinationFolder->hasFolder($folder->name);
			if ($existingFolder) {
				switch ($params['overwrite']) {
					case 'ask':
						array_unshift(GO::session()->values['files']['pasteIds']['folders'], $folder_id);
						$response['fileExists'] = $folder->name;
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

	private function _listShares($params) {

		$store = GO_Base_Data_Store::newInstance(GO_Files_Model_Folder::model());
		$store->getColumnModel()->setFormatRecordFunction(array($this, 'formatListRecord'));
		$findParams = $store->getDefaultParams($params);
		$stmt = GO_Files_Model_Folder::model()->findShares($findParams);
		$store->setStatement($stmt);

		return $store->getData();
	}

	protected function actionList($params) {

		if ($params['folder_id'] == 'shared')
			return $this->_listShares($params);

		//get the folder that contains the files and folders to list.
		//This will check permissions too.
		$folder = GO_Files_Model_Folder::model()->findByPk($params['folder_id']);

		if(!$folder)
			return false;

		//useful information for the view.
		$response['path'] = $folder->path;
		$response['thumbs'] = $folder->thumbs; //Show this page in thumbnails or list
		$response['parent_id'] = $folder->parent_id;


		$store = GO_Base_Data_Store::newInstance(GO_Files_Model_Folder::model());

		//set sort aliases
		$store->getColumnModel()->formatColumn('type', '$model->type',array(),'name');
		$store->getColumnModel()->formatColumn('size', '"-"',array(),'name');
		
		//handle delete request for both files and folder
		if (isset($params['delete_keys'])) {

			$ids = $this->_splitFolderAndFileIds(json_decode($params['delete_keys'], true));

			$params['delete_keys'] = json_encode($ids['folders']);
			$store->processDeleteActions($params, "GO_Files_Model_Folder");

			$params['delete_keys'] = json_encode($ids['files']);
			$store->processDeleteActions($params, "GO_Files_Model_File");
		}


		$store->getColumnModel()->setFormatRecordFunction(array($this, 'formatListRecord'));
		
		$findParams = $store->getDefaultParams($params)
						->ignoreAcl();
		
		$findParamsArray = $findParams->getParams();
		if(!isset($findParamsArray['start']))
			$findParamsArray['start']=0;
		
		if(!isset($findParamsArray['limit']))
			$findParamsArray['limit']=0;
		
		$stmt = $folder->folders($findParams);

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
			$store->getColumnModel()->formatColumn('type', '$model->type',array(),'extension');
			
			$findParams = $store->getDefaultParams($params)
							->limit($fileLimit)
							->start($fileStart);

			$stmt = $folder->files($findParams);			
			$store->setStatement($stmt);

			$filesResponse = $store->getData();

			$response['total']+=$filesResponse['total'];
			$response['results'] = array_merge($response['results'], $filesResponse['results']);
		} else {
			$record = $folder->files(array(
					'single' => true,
					'fields' => 'count(*) as total'
							));
			$response['total']+=$record['total'];
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
		} else {
			$record['type_id'] = 'f:' . $model->id;
			$record['type'] = $model->fsFile->typeDescription();
			$record['extension'] = $model->extension;
			$record['size']=$model->size;
		}
		$record['thumb_url'] = $model->thumbURL;

		return $record;
	}

	private function _checkExistingModelFolder($model, $folder, $mustExist=false) {

		
		$files_folder_id = 0;
		
		if(!$folder->fsFolder->exists())
		{
			$folder->delete();
			if($mustExist || $model->acl_id)
				return $this->_createNewModelFolder($model);
			else
				return 0;
		}

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


		if ($currentPath != $newPath) {

			//model has a new path. We must move the current folder					
			$destinationFolder = GO_Files_Model_Folder::model()->findByPath(
							dirname($newPath), true);
			
			$fsFolder = new GO_Base_Fs_Folder($newPath);
			$fsFolder->appendNumberToNameIfExists();
			
			if($existingFolder = $destinationFolder->hasFolder($fsFolder->name())){
				//a folder with the required name already exists. We'll use that.
				if (!$folder->acl_id && isset($model->acl_id)) {
					$existingFolder->acl_id = $model->acl_id;
				}
				$existingFolder->visible = 0;
				$existingFolder->readonly = 1;
				$existingFolder->save();
				return $existingFolder->id;
				
				//What should we do with the other folder???
			}else
			{
				$folder->name = $fsFolder->name();			
				$folder->parent_id = $destinationFolder->id;
				$folder->systemSave = true;
				$folder->visible = 0;
				$folder->readonly = 1;
				$folder->save();
			}
		}

		return $folder->id;
	}

	private function _createNewModelFolder($model) {

		$f = new GO_Base_Fs_Folder(GO::config()->file_storage_path . $model->buildFilesPath());
		//$fullPath = $f->appendNumberToNameIfExists();
		$relPath = $f->stripFileStoragePath();

		$parentFolder = GO_Files_Model_Folder::model()->findByPath(dirname($relPath), true);
		$name = $f->name();
		$i=1;
		while($parentFolder->hasFolder($name)){
			$name = $f->name().' ('.$i.')';
			$i++;
		}
		
		$folder = new GO_Files_Model_Folder();
		$folder->name=$name;
		$folder->parent_id=$parentFolder->id;
		
		if (!$folder->acl_id && isset($model->acl_id)) {
			$folder->acl_id = $model->acl_id;
		}

		$folder->visible = 0;
		$folder->readonly = 1;
		$folder->systemSave = true;
		$folder->save();

		return $folder->id;
	}

	/**
	 * check if a model folder exists
	 * 
	 * @param type $params
	 * @return type 
	 */
	protected function actionCheckModelFolder($params) {
		$model = GO::getModel($params['model'])->findByPk($params['id']);

		$response['success'] = true;
		$response['files_folder_id'] = $this->checkModelFolder($model, true, !empty($params['mustExist']));
		return $response;
	}

	public function checkModelFolder($model, $saveModel=false, $mustExist=false) {
		$folder = false;
		if ($model->files_folder_id > 0) {
			$folder = GO_Files_Model_Folder::model()->findByPk($model->files_folder_id);
		}

		if ($folder) {
			$model->files_folder_id = $this->_checkExistingModelFolder($model, $folder, $mustExist);

			if ($saveModel)
				$model->save();
		}elseif (isset($model->acl_id) || $mustExist) {
			//this model has an acl_id. So we should create a shared folder with this acl.
			//this folder should always exist.
			//only new models that have it's own acl field should always have a folder.
			//otherwise it will be created when first accessed.
			$model->files_folder_id = $this->_createNewModelFolder($model);

			if ($saveModel)
				$model->save();
		}

		if(!$model->files_folder_id)
			$model->files_folder_id=0;
		
		return $model->files_folder_id;
	}

	protected function actionProcessUploadQueue($params) {
		$response['success'] = true;

		if (!isset($params['overwrite']))
			$params['overwrite'] = 'ask'; //can be ask, yes, no

		$destinationFolder = GO_Files_Model_Folder::model()->findByPk($params['destination_folder_id']);

		if (!$destinationFolder->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION))
			throw new GO_Base_Exception_AccessDenied();

		while ($tmpfile = array_shift(GO::session()->values['files']['uploadqueue'])) {
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

				$file->move(new GO_Base_Fs_Folder(GO::config()->file_storage_path . $destinationFolder->path));

				if (!GO_Files_Model_File::importFromFilesystem($file))
					$response['success'] = false;
			}
		}

		return $response;
	}

	protected function actionCompress($params) {
	
		if (!GO_Base_Util_Common::isWindows())
			putenv('LANG=en_US.UTF-8');

		$sources = json_decode($params['compress_sources'], true);


		$workingFolder = GO_Files_Model_Folder::model()->findByPk($params['working_folder_id']);
		$destinationFolder = GO_Files_Model_Folder::model()->findByPk($params['destination_folder_id']);


		$workingPath = GO::config()->file_storage_path.$workingFolder->path;
		chdir($workingPath);

		for($i=0;$i<count($sources);$i++){
			$sources[$i]=str_replace($workingFolder->path.'/', '', $sources[$i]);
		}

		$archiveFile = new GO_Base_Fs_File(GO::config()->file_storage_path.$destinationFolder->path . '/' . $params['archive_name'] . '.zip');

		$cmd = GO::config()->cmd_zip . ' -r "' . $archiveFile->path() . '" "' . implode('" "', $sources) . '"';

		exec($cmd, $output);

		if (!$archiveFile->exists()) {
			throw new Exception('Command failed: ' . $cmd . "<br /><br />" . implode("<br />", $output));
		}

		GO_Files_Model_File::importFromFilesystem($archiveFile);

		$response['success']=true;		

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
			switch($file->extension()) {
				case 'zip':
					$cmd = GO::config()->cmd_unzip.' "'.$file->path().'"';					
					break;
				case 'gz':
				case 'tgz':
					$cmd = GO::config()->cmd_tar.' zxf "'.$file->path().'"';
					break;

				case 'tar':
					$cmd = GO::config()->cmd_tar.' xf "'.$file->path().'"';
					break;
			}
		}
		exec($cmd, $output, $ret);
		
		if($ret>1)
		{
			throw new Exception(implode("\n",$output));
		}
		
		$workingFolder->syncFilesystem(true);

		return array('success'=>true);

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
			
			$folder_id = $this->checkModelFolder($model, true, true);
			
			$folder = GO_Files_Model_Folder::model()->findByPk($folder_id);
			
			while ($tmp_file = array_shift($tmp_files)) {
				if (!empty($tmp_file['tmp_file'])) {
					
					$file = new GO_Base_Fs_File($tmp_file['tmp_file']);
					$file->move(new GO_Base_Fs_Folder(GO::config()->file_storage_path . $folder->path));
					
					$folder->addFile($file->name());					
				}
			}
		}
	}
}
