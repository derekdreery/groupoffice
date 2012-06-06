<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: GO_Files_Model_Folder.php 7607 2011-09-01 15:44:36Z <<USERNAME>> $
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */

/**
 * The GO_Files_Model_Folder model
 * 
 * Top level folders with parent_id=0 are readable to everyone with access to 
 * the files module automatically. This is done in the validate() function of this model.
 * 
 * A shared folder has an acl_id set. When the system checks permissions it will
 * recursively search up the tree until it finds a folder that has an acl_id.
 * 
 * @property int $user_id
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $path Relative path from GO::config()->file_storage_path
 * @property boolean $visible When this folder is shared it only shows up in the tree when visible is set to true
 * @property int $acl_id
 * @property string $comments
 * @property boolean $thumbs Show this folder in thumbnails
 * @property int $ctime
 * @property int $mtime
 * @property boolean $readonly Means this folder is readonly even to the administrator! eg. Home folders may never be edited.
 * @property string $cm_state The stored state of the column model whebn apply state is true
 * @property boolean $apply_state Apply the configured state of the column model to everybody.
 * @property GO_Base_Fs_Folder $fsFolder
 * @property int $acl_write
 */
class GO_Files_Model_Folder extends GO_Base_Db_ActiveRecord {
	
	private $_path;
	
	//prevents acl id's to be generated automatically by the activerecord.
	public $joinAclField=true;
	
	/**
	 *
	 * @var boolean Set to true by a system save so the readonly flag won't take effect in beforeSave
	 */
	public $systemSave=false;

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Files_Model_Folder
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * Enable this function if you want this model to check the acl's automatically.
	 */
	public function aclField() {
		return 'acl_id';
	}

	public function findAclId() {
		//folder may have an acl ID if they don't have one we must recurse up the tree
		//to find the acl.		
		if ($this->acl_id > 0){			
			return $this->acl_id;
		}elseif($this->parent)
			return $this->parent->findAclId();
		else
			return false;
	}
	
	public function hasLinks() {
		return true;
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'fs_folders';
	}
	
	public function getLogMessage($action){
		return $this->path;
	}

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	public function relations() {
		return array(
				'parent' => array('type' => self::BELONGS_TO, 'model' => 'GO_Files_Model_Folder', 'field' => 'parent_id'),
				'folders' => array('type' => self::HAS_MANY, 'model' => 'GO_Files_Model_Folder', 'field' => 'parent_id', 'delete' => true, 'findParams'=>  GO_Base_Db_FindParams::newInstance()->order('name','ASC')),
				'files' => array('type' => self::HAS_MANY, 'model' => 'GO_Files_Model_File', 'field' => 'folder_id', 'delete' => true),
				'notifyUsers'=>array('type' => self::HAS_MANY, 'model' => 'GO_Files_Model_FolderNotification', 'field' => 'folder_id', 'delete' => true),
				'preferences'=>array('type' => self::HAS_MANY, 'model' => 'GO_Files_Model_FolderPreference', 'field' => 'folder_id', 'delete' => true),
		);
	}
	
	protected function getLocalizedName() {
		return GO::t('folder', 'files');
	}

	/**
	 * This getter recursively builds the folder path.
	 * @return string 
	 */
	protected function getPath($forceResolve=false) {
				
		if($forceResolve || !isset($this->_path)){
			$this->_path = $this->name;
			$currentFolder = $this;
			
			$ids=array($this->id);
			
			while ($currentFolder = $currentFolder->parent) {				
				
				if(in_array($currentFolder->id, $ids))
					throw new Exception("Infinite folder loop detected in ".$this->_path." ".implode(",", $ids));
				else
					$ids[]=$currentFolder->id;
				
				$this->_path = $currentFolder->name . '/' . $this->_path;
			}
		}
		return $this->_path;
	}	
	
	public function getFolderIdsInPath($folder_id){
		$ids=array();
		$currentFolder = GO_Files_Model_Folder::model()->findByPk($folder_id);
		
		if(!$currentFolder)
			return $ids;
		
		while ($currentFolder = $currentFolder->parent) {
			$ids[] = $currentFolder->id;
		}	
		return $ids;
	}

	protected function getFsFolder() {
		return new GO_Base_Fs_Folder(GO::config()->file_storage_path . $this->path);
	}
	
	
	private function _checkParentId(){
		if($this->isModified("parent_id")){
			$ids=array($this->id);
			
			$currentFolder=$this;
			
			while ($currentFolder = $currentFolder->parent) {				
				if(in_array($currentFolder->id, $ids)){					
					$this->setValidationError ("parent_id", "Can not move folder into this folder because it's a child");
					break;
				}
			}
			
			//throw new Exception("test");
		}
	}
	
	public function validate() {
		
		$this->_checkParentId();
		
		if($this->parent_id==0){
			//top level folders are readonly to everyone.
			$this->readonly=1;
			$this->acl_id=GO::modules()->files->acl_id;			
		}
		return parent::validate();
	}
	
	protected function beforeSave() {

		if(!$this->systemSave && !$this->isNew && $this->readonly){
			if($this->isModified('name') || $this->isModified('folder_id'))
				return false;
		}			
		
		if($this->parent){
			$existingFolder = $this->parent->hasFolder($this->name);
			if($existingFolder && $existingFolder->id!=$this->id)
				throw new Exception(GO::t('folderExists','files'));
		}
		
		return parent::beforeSave();
	}
	
	protected function afterSave($wasNew) {

		if ($wasNew) {
			
			$this->fsFolder->create();
			
		
			
		} else {
			
			unset($this->_path);
			
			if(!$this->fsFolder->exists()){				

				if($this->isModified('parent_id')){
					//file will be moved so we need the old folder path.
					$oldFolderId = $this->getOldAttributeValue('parent_id');
					$oldFolder = GO_Files_Model_Folder::model()->findByPk($oldFolderId);				
					$oldRelPath = $oldFolder->path;
					
					$oldName = $this->isModified('name') ? $this->getOldAttributeValue('name') : $this->name;

					$oldPath = GO::config()->file_storage_path . $oldRelPath . '/' . $oldName;

					$fsFolder = new GO_Base_Fs_Folder($oldPath);

					$newRelPath = $this->getPath(true);

					$newFsFolder = new GO_Base_Fs_Folder(GO::config()->file_storage_path . dirname($newRelPath));

					if (!$fsFolder->move($newFsFolder))
						throw new Exception("Could not rename folder on the filesystem");
				}
				
				//if the filesystem folder is missing check if we need to move it when the name or parent folder changes.
				if($this->isModified('name')){
					GO::debug("Renaming from ".$this->getOldAttributeValue('name')." to ".$this->name);

					$oldFsFolder = new GO_Base_Fs_Folder(dirname($this->fsFolder->path()).'/'.$this->getOldAttributeValue('name'));

					$oldFsFolder->rename($this->name);
					
				}
			}
		}
		
		//sync parent timestamp
		if($this->parent){
//				$this->parent->mtime=$this->parent->fsFolder->mtime();
//				$this->parent->save();			
			$this->parent->touch();
		}

		return parent::afterSave($wasNew);
	}
	

	protected function afterDelete() {
		
		GO::debug("after delete ".$this->path." ".$this->fsFolder->path());
		$this->fsFolder->delete();		
		
		//Read only flag is set for addressbooks, tasklists etc. They share the same acl so deleting it would make addressbooks inaccessible.
		if(!$this->readonly){
			//normally this is done automatically. But we overide $this->joinAclfield to prevent acl management.
			$acl = GO_Base_Model_Acl::model()->findByPk($this->{$this->aclField()});			
			if($acl)
				$acl->delete();
		}
		
		return parent::afterDelete();
	}
	
	
	

	/**
	 * Find a folder by path relative to GO::config()->file_storage_path
	 * 
	 * @param String $relpath 
	 * @param boolean $autoCreate True to auto create the folders. ACL's will be ignored.
	 * @return GO_Files_Model_Folder 
	 */
	public function findByPath($relpath, $autoCreate=false, $autoCreateAttributes=array()) {
		

		$oldIgnoreAcl = GO::$ignoreAclPermissions;
		GO::$ignoreAclPermissions=true;
		
		$folder=false;
		if (substr($relpath, -1) == '/') {
			$relpath = substr($relpath, 0, -1);
		}
		$parts = explode('/', $relpath);
		$parent_id = 0;
		while ($folderName = array_shift($parts)) {
			
			$findParams = GO_Base_Db_FindParams::newInstance();
			$findParams->getCriteria()
							->addCondition('parent_id', $parent_id)
							->addBindParameter(':name', $folderName)
							->addRawCondition('t.name COLLATE utf8_bin', ':name'); //use utf8_bin for case sensivitiy and special characters.
			
			$folder = $this->findSingle($findParams);
			if (!$folder) {
				if (!$autoCreate)
					return false;

				$folder = new GO_Files_Model_Folder();
				$folder->setAttributes($autoCreateAttributes);
				$folder->name = $folderName;
				$folder->parent_id = $parent_id;
				$folder->save();					
			}elseif(!empty($autoCreateAttributes))
			{
				//should not apply it to existing folders. this leads to unexpected results.
//				$folder->setAttributes($autoCreateAttributes);
//				$folder->save();	
			}

			$parent_id = $folder->id;
		}
		
		GO::$ignoreAclPermissions=$oldIgnoreAcl;

		return $folder;
	}
	/**
	 * Return the home folder of a user.
	 * 
	 * @param GO_Base_Model_User $user 
	 */
	public function findHomeFolder($user){
		
		$folder = GO_Files_Model_Folder::model()->findByPath('users/'.$user->username, true);
		
		if(empty($folder->acl_id)){
				$folder->setNewAcl($user->id);
		}		
		
		$folder->user_id=$user->id;
		$folder->visible=1;
		$folder->readonly=1;
		//GO::$ignoreAclPermissions=true;
		$folder->save();			
		//GO::$ignoreAclPermissions=false;
		
		return $folder;
	}
	
	/**
	 * Check if this folder is the home folder of a user.
	 * 
	 * @return boolean 
	 */
	public function isSomeonesHomeFolder(){
		if($this->isNew)
			return false;
		
		return $this->parent && $this->parent->name=='users' && $this->parent->parent_id==0;
	}

	
	/**
	 * Add a file to this folder. The file must already be present on the filesystem.
	 * 
	 * @param String $name
	 * @return GO_Files_Model_File 
	 */
	public function addFile($name) {
		$file = new GO_Files_Model_File();
	
		$file->folder_id = $this->id;
		$file->name = $name;
		
		
		$file->save();

		return $file;
	}
	
	/**
	 * Add a filesystem file to this folder. The file will be moved to this folder
	 * and added to the database.
	 * 
	 * @param GO_Base_Fs_File $file
	 * @return GO_Files_Model_File 
	 */
	public function addFilesystemFile(GO_Base_Fs_File $file){
		
		if(!GO_Files_Model_File::checkQuota($file->size()))
			throw new GO_Base_Exception_InsufficientDiskspace();
		
		$file->move($this->fsFolder);
		return $this->addFile($file->name());
	}
	
	/**
	 * Add a filesystem file to this folder. The file will be moved to this folder
	 * and added to the database.
	 * 
	 * @param GO_Base_Fs_File $file
	 * @return GO_Files_Model_File 
	 */
	public function addFilesystemFolder(GO_Base_Fs_Folder $folder){
		$folder->move($this->fsFolder);
		return $this->addFolder($folder->name(), true);
	}
	
	/**
	 * Add a subfolder.
	 * 
	 * @param String $name
	 * @return GO_Files_Model_Folder 
	 */
	public function addFolder($name, $syncFileSystem=false, $syncOnNextAccess=false){
		$folder = new GO_Files_Model_Folder();
		$folder->parent_id = $this->id;
		$folder->name = $name;
		
		//file manager will compare database timestamp with filesystem when it's accessed.
		if($syncOnNextAccess)
			$folder->mtime=1;
			
		$folder->save();
		
		if($syncFileSystem)
			$folder->syncFilesystem();

		return $folder;
	}

	/**
	 * Adds missing files and folders from the filesystem to the database and 
	 * removes files and folders from the database that are not on the filesystem.
	 * 
	 * @param boolean $recurseAll
	 * @param boolean $recurseOneLevel 
	 */
	public function syncFilesystem($recurseAll=false, $recurseOneLevel=true) {

		if(GO::config()->debug)
			GO::debug("syncFilesystem ".$this->path);
		
		$oldCache = GO::$disableModelCache;
		
		GO::$disableModelCache=true;
		
//		if(class_exists("GO_Filesearch_FilesearchModule"))
//			GO_Filesearch_FilesearchModule::$disableIndexing=true;
		
		if($this->fsFolder->exists()){
			$items = $this->fsFolder->ls();
			

			foreach ($items as $item) {
				//GO::debug("FS SYNC: Adding fs ".$item->name()." to database");
				if ($item->isFile()) {
					$file = $this->hasFile($item->name());
					
					if (!$file)
						$this->addFile($item->name());

				}else
				{
					
					$willSync = $recurseOneLevel || $recurseAll;
					
					$folder = $this->hasFolder($item->name());
					if(!$folder)
						$folder = $this->addFolder($item->name(), false, !$willSync);

					if($willSync)
						$folder->syncFilesystem($recurseAll, false);				
				}
			}
		}else
		{
			$this->fsFolder->create();
		}
		
		$stmt= $this->folders();
		while($folder = $stmt->fetch()){
			try{
				if(!$folder->fsFolder->exists())
					$folder->delete();
			}catch(Exception $e){
				echo $e->getMessage()."\n";
			}
		}
		
		$stmt= $this->files();
		while($file = $stmt->fetch()){
			try{
				if(!$file->fsFile->exists())
					$file->delete();
			}catch(Exception $e){
				echo $e->getMessage()."\n";
			}
		}
		
		$this->mtime=$this->fsFolder->mtime();
		$this->save();
		
		GO::$disableModelCache=$oldCache;
	}
	
	/**
	 * Compares the database timestamp with the filesystem timestamp and syncs the
	 * folder if necessary.
	 */
	public function checkFsSync(){
		
		if(!$this->fsFolder->exists())
			throw new Exception("Folder ".$this->path." doesn't exist on the filesystem! Please run a database check.");
		
		GO::debug('checkFsSync '.$this->path.' : '.$this->mtime.' < '.$this->fsFolder->mtime());
		
		if($this->mtime < $this->fsFolder->mtime()){
			GO::debug("Filesystem folder ".$this->path." is not in sync with database. Will sync now.");
			$this->syncFilesystem ();
//			$this->mtime=$this->fsFolder->mtime();
//			$this->save();
		}
	}
	
	/**
	 * Add a user that will be notified by e-mail when something changes in the
	 * folder.
	 * 
	 * @param int $user_id
	 * @param boolean $recursively If true, apply this to all subfolders.
	 */
	public function addNotifyUser($user_id,$recursively=false){
		if(!$this->hasNotifyUser($user_id)){
			$m = new GO_Files_Model_FolderNotification();
			$m->folder_id = $this->id;
			$m->user_id = $user_id;
			$m->save();
		}
		if ($recursively) {
			$childFolderStmt = GO_Files_Model_Folder::model()->findByAttribute('parent_id',$this->id);
			while ($childFolder = $childFolderStmt->fetch())
				$childFolder->addNotifyUser($user_id,true);
		}
  }
	
	/**
	 * Remove a user that will be notified by e-mail when something changes in the
	 * folder.
	 * 
	 * @param int $user_id
	 * @param boolean $recursively If true, apply this to all subfolders.
	 */
	public function removeNotifyUser($user_id, $recursively=false){
		$model = GO_Files_Model_FolderNotification::model()->findByPk(array('user_id'=>$user_id, 'folder_id'=>$this->pk));
		if($model)
			$model->delete();
		
		if ($recursively) {
			$childFolderStmt = GO_Files_Model_Folder::model()->findByAttribute('parent_id',$this->id);
			while ($childFolder = $childFolderStmt->fetch())
				$childFolder->removeNotifyUser($user_id,true);
		}
	}
  
  /**
   * Check if a user receives notifications about changes in the folder.
   * 
   * @param type $user_id
   * @return GO_Files_Model_FolderNotification or false 
   */
  public function hasNotifyUser($user_id){
    return GO_Files_Model_FolderNotification::model()->findByPk(array('user_id'=>$user_id, 'folder_id'=>$this->pk)) !== false;
  }
	
	
	/**
	 * Check if this folder has a file by filename and return the model.
	 * 
	 * @param String $filename
	 * @return GO_Files_Model_File 
	 */
	public function hasFile($filename){		
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->single();
		$findParams->getCriteria()							
							->addBindParameter(':name', $filename)
							->addRawCondition('t.name COLLATE utf8_bin', ':name'); //use utf8_bin for case sensivitiy and special characters.
		
		return $this->files($findParams);
	}
	
	/**
	 * Check if this folder has a file by filename and return the model.
	 * 
	 * @param String $filename
	 * @return GO_Files_Model_Folder
	 */
	public function hasFolder($filename){		
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->single();
		$findParams->getCriteria()							
							->addBindParameter(':name', $filename)
							->addRawCondition('t.name COLLATE utf8_bin', ':name'); //use utf8_bin for case sensivitiy and special characters.
		
		return $this->folders($findParams);
	}
	
	
	/**
	 * Checks if a filename exists and renames it.
	 *
	 * @access public
	 * @return string  New filename
	 */
	public function appendNumberToNameIfExists()
	{
		$origName= $this->name;
		$x=1;
		while($this->parent->hasFolder($this->name))
		{			
			$this->name=$origName.' ('.$x.')';
			$x++;
		}
		return $this->name;
	}
	
	/**
	 * Move a folder to another folder
	 * 
	 * @param GO_Files_Model_Folder $destinationFolder
	 * @return boolean 
	 */
	public function move($destinationFolder){
		
		$this->parent_id=$destinationFolder->id;		
		return $this->save();
	}
	
	/**
	 * Copy a folder to another folder
	 * 
	 * @param GO_Files_Model_Folder $destinationFolder
	 * @return boolean 
	 */
	public function copy($destinationFolder){
		
		$existing = $destinationFolder->hasFolder($this->name);
		if(!$existing){
			$copy = $this->duplicate();
			$copy->parent_id=$destinationFolder->id;
			if(!$copy->save())
				return false;

			if(!$this->fsFolder->copy($copy->fsFolder->parent()))
				return false;
		}else
		{
			$copy = $existing;
			//if folder exist then merge the folder.
		}
		
		$stmt = $this->folders();
		while($folder = $stmt->fetch()){
			if(!$folder->copy($copy))
				return false;
		}
		
		$stmt = $this->files();
		while($file = $stmt->fetch()){
			if(!$file->copy($copy))
				return false;
		}
		
		return true;
	}
	
	protected function getThumbURL() {
		return GO::url('core/thumb', 'src=' . urlencode($this->path) . '&lw=100&ph=100&zc=1&filemtime=' . $this->fsFolder->mtime());
	}
	
	/**
	 * Get all the subfolders of this folder. This function checks permissions in a
	 * special way. When folder have acl_id=0 they inherit permissions of the parent folder.
	 * 
	 * @return GO_Base_Db_ActiveStatement 
	 */
	public function getSubFolders($findParams=false, $noGrouping=false){			
			if(!$findParams)
				$findParams=GO_Base_Db_FindParams::newInstance();
			
			$findParams->ignoreAcl(); //We'll build a special acl check for folders that inherit permissions here.
			
			$findParams->debugSql();
			
			$aclJoinCriteria = GO_Base_Db_FindCriteria::newInstance()
							->addRawCondition('a.acl_id', 't.acl_id','=', false);
			
			$aclWhereCriteria = GO_Base_Db_FindCriteria::newInstance()
							->addRawCondition('a.acl_id', 'NULL','IS', false)
							->addCondition('user_id', GO::user()->id,'=','a', false)
							->addInCondition("group_id", GO_Base_Model_User::getGroupIds(GO::user()->id),"a", false);
			
			$findParams->join(GO_Base_Model_AclUsersGroups::model()->tableName(), $aclJoinCriteria, 'a', 'LEFT');
			
			$findParams->criteria(GO_Base_Db_FindCriteria::newInstance()
									->addModel(GO_Files_Model_Folder::model())
									->addCondition('parent_id', $this->id)
									->mergeWith($aclWhereCriteria));
			
			if(!$noGrouping)
				$findParams->group(array('t.id'));
		
			return GO_Files_Model_Folder::model()->find($findParams);
	}
	
	public function hasChildren(){
		return $this->getSubFolders(GO_Base_Db_FindParams::newInstance()->single(), true);
	}
	
	/**
	 * Move all the files and folders from a given source folder into this folder.
	 * 
	 * @param GO_Files_Model_Folder $sourceFolder 
	 */
	public function moveContentsFrom(GO_Files_Model_Folder $sourceFolder, $mergeFolders=false){
		
		//make sure database is in sync with filesystem.
		$sourceFolder->syncFilesystem(true);
		
		$stmt = $sourceFolder->folders();
		while($subfolder = $stmt->fetch()){
			GO::debug("MOVE ".$subfolder->name);
			$subfolder->systemSave=true;			
			if(!$mergeFolders){
				$subfolder->parent_id=$this->id;
				$subfolder->appendNumberToNameIfExists();
				$subfolder->save();
			}else
			{
				if(($existingFolder = $this->hasFolder($subfolder->name))){
					$existingFolder->moveContentsFrom($subfolder, true);
					$subfolder->delete();
				}else
				{
					$subfolder->parent_id=$this->id;
					$subfolder->save();
				}
			}			
		}
		
		$stmt = $sourceFolder->files();
		while($file = $stmt->fetch()){
			GO::debug("MOVE ".$file->name);
			$file->folder_id=$this->id;
			$file->appendNumberToNameIfExists();
			$file->save();
		}
	}
	
	public function copyContentsFrom(GO_Files_Model_Folder $sourceFolder, $mergeFolders=false){
		//make sure database is in sync with filesystem.
		$sourceFolder->syncFilesystem(true);
		
		
		$stmt = $sourceFolder->folders();
		while($subfolder = $stmt->fetch()){

			$subfolder->systemSave=true;			
			if(!$mergeFolders){
				$subfolder->copy($this);
			}else
			{
				if(($existingFolder = $this->hasFolder($subfolder->name))){
					$existingFolder->copyContentsFrom($subfolder, true);
				}else
				{
					$subfolder->copy($this);
				}
			}			
		}
		
		$stmt = $sourceFolder->files();
		while($file = $stmt->fetch()){
			$file->copy($this, false, true);
		}
	}

	/**
	 * Find all shared folders for the current user
	 * 
	 * @param GO_Base_Db_FindParams $findParams
	 * @return GO_Base_Db_ActiveStatement
	 */
	public function findShares($findParams=false){
		
		if(!$findParams)
			$findParams = new GO_Base_Db_FindParams();
				
		 $findParams->getCriteria()
					->addModel(GO_Files_Model_Folder::model())
					->addCondition('visible', 1)
					->addCondition('user_id', GO::user()->id,'!=');
		
		return GO_Files_Model_Folder::model()->find($findParams);
		
		
	}
	
	public function getTopLevelShares($findParams=false){
		
		$stmt = $this->findShares($findParams);
		//sort by path and only list top level shares
		$shares = array();
		while ($folder = $stmt->fetch()) {
			$folder->checkFsSync();
			
			//sort by path and only list top level shares		
			$shares[$folder->path]=$folder;
		}
		ksort($shares);
		
		$response=array();
		foreach($shares as $path=>$folder){
			$isSubDir = isset($lastPath) && strpos($path.'/', $lastPath.'/')===0;
			
			if(!$isSubDir){
				$response[]=$folder;			
				$lastPath=$path;
			}
		}
		
		return $response;
	}
}