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
 * @property String $name
 * @property String $path
 * @property Boolean $visible When this folder is shared it only shows up in the tree when visible is set to true
 * @property int $acl_id
 * @property String $comments
 * @property Boolean $thumbs Show this folder in thumbnails
 * @property int $ctime
 * @property int $mtime
 * @property Boolean $readonly Means this folder is readonly even to the administrator! eg. Home folders may never be edited.
 * @property String $cm_state
 * @property int $apply_state
 * @property GO_Base_Fs_Folder $fsFolder
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
		if ($this->acl_id > 0)
			return parent::findAclId();
		elseif($this->parent)
			return $this->parent->findAclId();
		else
			return false;
	}
	
	public function hasLinks() {
		return true;
	}
	
//	protected function appendAclJoin($findParams, $aclJoin){		
//			
//		$sql .= "\nLEFT JOIN go_acl ON (`".$aclJoin['table']."`.`".$aclJoin['aclField']."` = go_acl.acl_id";
//		if(isset($params['permissionLevel']) && $findParams['permissionLevel']>GO_Base_Model_Acl::READ_PERMISSION){
//			$sql .= " AND go_acl.level>=".intval($findParams['permissionLevel']);
//		}
//		$sql .= " AND (go_acl.user_id=".intval($findParams['userId'])." OR go_acl.group_id IN (".implode(',',GO_Base_Model_User::getGroupIds($findParams['userId']))."))) ";		
//		
//		$sql .= "OR ISNULL(a.acl_id) OR a.acl_id=0";
//		
//		return $sql;
//	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'fs_folders';
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
			while ($currentFolder = $currentFolder->parent) {
				$this->_path = $currentFolder->name . '/' . $this->_path;
			}
		}
		return $this->_path;
	}

	protected function getFsFolder() {
		return new GO_Base_Fs_Folder(GO::config()->file_storage_path . $this->path);
	}
	
	public function validate() {
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
			
			if($this->isModified('name')){
				
				$oldFsFolder = new GO_Base_Fs_Folder(dirname($this->fsFolder->path()).'/'.$this->getOldAttributeValue('name'));
				
				$oldFsFolder->rename($this->name);
			}
			
			if($this->isModified('parent_id')){
				//file will be moved so we need the old folder path.
				$oldFolderId = $this->getOldAttributeValue('parent_id');
				$oldFolder = GO_Files_Model_Folder::model()->findByPk($oldFolderId);				
				$oldRelPath = $oldFolder->path;				
				
				$oldPath = GO::config()->file_storage_path . $oldRelPath . '/' . $this->name;
							
				$fsFolder = new GO_Base_Fs_Folder($oldPath);
				
				//unset($this->_path);
				
				$newRelPath = $this->getPath(true);
				
				$newFsFolder = new GO_Base_Fs_Folder(GO::config()->file_storage_path . dirname($newRelPath));
				
				if (!$fsFolder->move($newFsFolder))
					throw new Exception("Could not rename folder on the filesystem");
			}
		}

		return parent::afterSave($wasNew);
	}
	

	protected function afterDelete() {		
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
	public function findByPath($relpath, $autoCreate=false) {
		

		$oldIgnoreAcl = GO::$ignoreAclPermissions;
		GO::$ignoreAclPermissions=true;
		
		$folder=false;
		if (substr($relpath, -1) == '/') {
			$relpath = substr($relpath, 0, -1);
		}
		$parts = explode('/', $relpath);
		$parent_id = 0;
		while ($folderName = array_shift($parts)) {
			$folder = $this->findSingleByAttributes(array(
					'parent_id' => $parent_id,
					'name' => $folderName
							));
			if (!$folder) {
				if (!$autoCreate)
					return false;

				$folder = new GO_Files_Model_Folder();
				$folder->name = $folderName;
				$folder->parent_id = $parent_id;
				$folder->save();					
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
		return $this->parent->name=='users' && $this->parent->parent_id=0;
	}

	
	/**
	 * Add a file to this folder
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
	 * Add a subfolder.
	 * 
	 * @param String $name
	 * @return GO_Files_Model_Folder 
	 */
	public function addFolder($name){
		$folder = new GO_Files_Model_Folder();
		$folder->parent_id = $this->id;
		$folder->name = $name;
		$folder->save();

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

		if($this->fsFolder->exists()){
			$items = $this->fsFolder->ls();
			

			foreach ($items as $item) {
				if ($item->isFile()) {
					$file = $this->hasFile($item->name());
					
					if (!$file)
						$this->addFile($item->name());

				}else
				{
					$folder = $this->hasFolder($item->name());
					if(!$folder)
						$folder = $this->addFolder($item->name());

					if($recurseOneLevel || $recurseAll)
						$folder->syncFilesystem($recurseAll, false);				
				}
			}
		}
		
		$stmt= $this->folders();
		while($folder = $stmt->fetch()){
			if(!$folder->fsFolder->exists())
				$folder->delete();
		}
		
		$stmt= $this->files();
		while($file = $stmt->fetch()){
			if(!$file->fsFile->exists())
				$file->delete();
		}
	}
	
	/**
	 * Compares the database timestamp with the filesystem timestamp and syncs the
	 * folder if necessary.
	 */
	public function checkFsSync(){
		
		if(!$this->fsFolder->exists())
			throw new Exception("Folder ".$this->path." doesn't exist on the filesystem! Please run a database check.");
		
		if($this->mtime < $this->fsFolder->mtime()){
			$this->syncFilesystem ();
			$this->mtime=time();
			$this->save();
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
		return $this->files(array(
				'criteriaObject'=>GO_Base_Db_FindCriteria::newInstance()
						->addModel(GO_Files_Model_File::model())
						->addCondition('name', $filename),
				'single'=>true
		));
	}
	
	/**
	 * Check if this folder has a file by filename and return the model.
	 * 
	 * @param String $filename
	 * @return GO_Files_Model_Folder
	 */
	public function hasFolder($filename){		
		return $this->folders(array(
				'criteriaObject'=>GO_Base_Db_FindCriteria::newInstance()
						->addModel(GO_Files_Model_Folder::model())
						->addCondition('name', $filename),
				'single'=>true
		));
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
	 * Get all the subfolders of this folder.
	 * Unlike the standard folders relation it handles some folders differently.
	 * On special folders like addressbooks, projects etc it checks authentication
	 * for all subfolders. On normal folders it doesn't check authentication.
	 * 
	 * @return GO_Base_Db_ActiveStatement 
	 */
	public function getSubFolders(){
		if($this->parent_id==0){
			//this is a special folder like addressbooks, projects etc.
			//we must check acl's here

			return GO_Files_Model_Folder::model()->find(
							GO_Base_Db_FindParams::newInstance()
							->limit(100)//not so nice hardcoded limit
							->criteria(GO_Base_Db_FindCriteria::newInstance()
									->addModel(GO_Files_Model_Folder::model())
									->addCondition('parent_id', $this->id))
							
					);
		}else
		{
			//relational queries don't check acl's
			return $this->folders();
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
		
//		//sort by path and only list top level shares
//		$shares = array();
//		while($folder = $stmt->fetch())
//		{
//			$shares[$folder->path]=$folder;
//		}
//		ksort($shares);
//		
//		$response=array();
//		foreach($shares as $path=>$folder){
//			$isSubDir = isset($lastFolder) && $folder->isSubFolderOf($lastFolder);
//			
//			if(!$isSubDir)
//				$response[]=$folder;
//			$lastFolder=$folder;
//		}
//		
//		return $response;
	}
	
	
}