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
 * @property Boolean $readonly
 * @property String $cm_state
 * @property int $apply_state
 * @property GO_Base_Fs_Folder $fsFolder
 */
class GO_Files_Model_Folder extends GO_Base_Db_ActiveRecord {
	
	private $_path;
	
	public $joinAclField=true;

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
	
	protected function appendAclJoin($findParams, $aclJoin){		
			
		$sql .= "\nLEFT JOIN go_acl ON (`".$aclJoin['table']."`.`".$aclJoin['aclField']."` = go_acl.acl_id";
		if(isset($params['permissionLevel']) && $findParams['permissionLevel']>GO_Base_Model_Acl::READ_PERMISSION){
			$sql .= " AND go_acl.level>=".intval($findParams['permissionLevel']);
		}
		$sql .= " AND (go_acl.user_id=".intval($findParams['userId'])." OR go_acl.group_id IN (".implode(',',GO_Base_Model_User::getGroupIds($findParams['userId']))."))) ";		
		
		$sql .= "OR ISNULL(a.acl_id) OR a.acl_id=0";
		
		return $sql;
	}

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
				'folders' => array('type' => self::HAS_MANY, 'model' => 'GO_Files_Model_Folder', 'field' => 'parent_id', 'delete' => true),
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
	protected function getPath() {
		if(!isset($this->_path)){
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
	
	protected function beforeSave() {
		
		$existingFolder = $this->parent->hasFolder($this->name);
		if($existingFolder && $existingFolder->id!=$this->id)
			throw new Exception(GO::t('folderExists','files'));
		
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
				
				if (!$fsFolder->move(new GO_Base_Fs_Folder(GO::config()->file_storage_path . dirname($this->path))))
					throw new Exception("Could not rename folder on the filesystem");
			}
		}

		return parent::afterSave($wasNew);
	}
	

	protected function afterDelete() {		
		$this->fsFolder->delete();
		return parent::afterDelete();
	}
	
	
	

	/**
	 * Find a folder by path relative to GO::config()->file_storage_path
	 * 
	 * @param String $relpath 
	 * @param boolean $autoCreate
	 * @return GO_Files_Model_Folder 
	 */
	public function findByPath($relpath, $autoCreate=false) {
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
				if (!$folder->save())
					throw new GO_Base_Exception_Save($relpath);
			}

			$parent_id = $folder->id;
		}

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
				$folder->user_id=$user->id;
				$folder->visible=1;
				$folder->save();
		}
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
	 */
	public function syncFilesystem($recurseOneLevel=true) {

		if($this->fsFolder->exists()){
			$items = $this->fsFolder->ls();

			foreach ($items as $item) {
				if ($item instanceof GO_Base_Fs_File) {
					$file = $this->files(array('single'=>true,'name'=>$item->name()));

					if (!$file)
						$this->addFile($item->name());

				}else
				{
					$folder = $this->folders(array('single'=>true,'name'=>$item->name()));
					if(!$folder)
						$folder = $this->addFolder($item->name());

					if($recurseOneLevel)
						$folder->syncFilesystem(false);				
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
	 * @return boolean
	 */
	public function addNotifyUser($user_id){
		if(!$this->hasNotifyUser($user_id)){
			$m = new GO_Files_Model_FolderNotification();
			$m->folder_id = $this->id;
			$m->user_id = $user_id;
			return $m->save();
		}else
		{
			return true;
		}
  }
	
	/**
	 * Remove a user that will be notified by e-mail when something changes in the
	 * folder.
	 * 
	 * @param int $user_id
	 * @return boolean
	 */
	public function removeNotifyUser($user_id){
		$model = GO_Files_Model_FolderNotification::model()->findByPk(array('user_id'=>$user_id, 'folder_id'=>$this->pk));
		if($model)
			return $model->delete();
		else
			return true;
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
				'criteriaObject'=>GO_Base_Db_FindCriteria::newInstance()->addCondition('name', $filename),
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
				'criteriaObject'=>GO_Base_Db_FindCriteria::newInstance()->addCondition('name', $filename),
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
	
	public function copy($destinationFolder){
		
		$copy = $this->duplicate();
		$copy->parent_id=$destinationFolder->id;
		$copy->save();
		
		$this->fsFolder->copy($copy->fsFolder->parent());		
	}
	
	protected function getThumbURL() {
		return GO::url('core/thumb', 'src=' . urlencode($this->path) . '&lw=100&ph=100&zc=1&filemtime=' . $this->fsFolder->mtime());
	}
}