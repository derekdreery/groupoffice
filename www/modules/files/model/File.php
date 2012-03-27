<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: GO_Files_Model_File.php 7607 2011-09-01 15:40:20Z <<USERNAME>> $
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */

/**
 * The GO_Files_Model_File model
 * 
 * @property int $id
 * @property int $folder_id
 * @property String $name

 * @property int $locked_user_id
 * @property int $status_id
 * @property int $ctime
 * @property int $mtime
 * @property int $size
 * @property int $user_id
 * @property String $comments
 * @property String $extension
 * @property int $expire_time
 * @property String $random_code
 * 
 * @property String $thumbURL
 * 
 * @property String $path
 * @property GO_Base_Fs_File $fsFile
 * @property GO_Files_Model_Folder $folder
 * @property GO_Base_Model_User $lockedByUser
 */
class GO_Files_Model_File extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Files_Model_File
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * Enable this function if you want this model to check the acl's automatically.
	 */
	public function aclField() {
		return 'folder.acl_id';
	}

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'fs_files';
	}

	protected function getLocalizedName() {
		return GO::t('file', 'files');
	}
	
	public function customfieldsModel() {
		return "GO_Files_Customfields_Model_File";
	}

	public function hasLinks() {
		return true;
	}
	
	protected function getCacheAttributes() {
		return array('name'=>$this->name, 'description'=>$this->path);
	}
	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	public function relations() {
		return array(
				'lockedByUser' => array('type' => self::BELONGS_TO, 'model' => 'GO_Base_Model_User', 'field' => 'locked_user_id'),
				'folder' => array('type' => self::BELONGS_TO, 'model' => 'GO_Files_Model_Folder', 'field' => 'folder_id'),
				'versions' => array('type'=>self::HAS_MANY, 'model'=>'GO_Files_Model_Version', 'field'=>'file_id', 'delete'=>true),
		);
	}
	
	public function getPermissionLevel(){
		
		if(GO::$ignoreAclPermissions)
			return GO_Base_Model_Acl::MANAGE_PERMISSION;
		
		if(!$this->aclField())
			return -1;	
		
		if(!GO::user())
			return false;
		
		//if($this->isNew && !$this->joinAclField){
		if(empty($this->{$this->aclField()}) && !$this->joinAclField){
			//the new model has it's own ACL but it's not created yet.
			//In this case we will check the module permissions.
			$module = $this->getModule();
			if($module=='base'){
				return GO::user()->isAdmin() ? GO_Base_Model_Acl::MANAGE_PERMISSION : false;
			}else
				return GO::modules()->$module->permissionLevel;
			 
		}else
		{		
			if(!isset($this->_permissionLevel)){

				$acl_id = $this->findAclId();
				if(!$acl_id){
					throw new Exception("Could not find ACL for ".$this->className()." with pk: ".$this->pk);
				}

				$this->_permissionLevel=GO_Base_Model_Acl::getUserPermissionLevel($acl_id);// model()->findByPk($acl_id)->getUserPermissionLevel();
			}
			return $this->_permissionLevel;
		}
		
	}

	protected function init() {
		$this->columns['expire_time']['gotype'] = 'unixdate';
		parent::init();
	}
	
	/**
	 * Check if a file is locked by another user.
	 * 
	 * @return boolean 
	 */
	public function isLocked(){
		return !empty($this->locked_user_id) && $this->locked_user_id!=GO::user()->id;
	}
	
	private function _getOldFsFile(){
		$filename = $this->isModified('name') ? $this->getOldAttributeValue('name') : $this->name;
		if($this->isModified('folder_id')){
			//file will be moved so we need the old folder path.
			$oldFolderId = $this->getOldAttributeValue('folder_id');
			$oldFolder = GO_Files_Model_Folder::model()->findByPk($oldFolderId);				
			$oldRelPath = $oldFolder->path;				
			$oldPath = GO::config()->file_storage_path . $oldRelPath . '/' . $filename;

		}else{
			$oldPath = GO::config()->file_storage_path . $this->folder->path.'/'.$filename;
		}
		return new GO_Base_Fs_File($oldPath);
	}
	
	protected function beforeDelete() {
		
		if($this->isLocked())
			throw new Exception(GO::t("fileIsLocked","files"));
		
		return parent::beforeDelete();
	}
	
	protected function beforeSave() {		
		if(!$this->isNew){
			if($this->isModified('name')){				
				//rename filesystem file.
				//throw new Exception($this->getOldAttributeValue('name'));
				$oldFsFile = $this->_getOldFsFile();		
				if($oldFsFile->exists())
					$oldFsFile->rename($this->name);				
			}

			if($this->isModified('folder_id')){
//				//file will be moved so we need the old folder path.
//				$oldFolderId = $this->getOldAttributeValue('folder_id');
//				$oldFolder = GO_Files_Model_Folder::model()->findByPk($oldFolderId);				
//				$oldRelPath = $oldFolder->path;				
//				$oldPath = GO::config()->file_storage_path . $oldRelPath . '/' . $this->name;
//			
//
//				$fsFile= new GO_Base_Fs_File($oldPath);
				
				if(!isset($oldFsFile))
					$oldFsFile = $this->_getOldFsFile();

				if (!$oldFsFile->move(new GO_Base_Fs_Folder(GO::config()->file_storage_path . dirname($this->path))))
					throw new Exception("Could not rename folder on the filesystem");
			}
		}
		
		if($this->isModified('locked_user_id')){
			$old_locked_user_id = $this->getOldAttributeValue('locked_user_id');
			if(!empty($old_locked_user_id) && $old_locked_user_id != GO::user()->id && !GO::user()->isAdmin())
				throw new GO_Files_Exception_FileLocked();
		}
		

		$this->extension = $this->fsFile->extension();
		$this->size = $this->fsFile->size();
		$this->ctime = $this->fsFile->ctime();
		$this->mtime = $this->fsFile->mtime();
		
		$existingFile = $this->folder->hasFile($this->name);
		if($existingFile && $existingFile->id!=$this->id)
			throw new Exception(GO::t('filenameExists','files'));
		
		return parent::beforeSave();
	}

	protected function getPath() {
		return $this->folder->path . '/' . $this->name;
	}

	protected function getFsFile() {
		return new GO_Base_Fs_File(GO::config()->file_storage_path . $this->path);
	}

	protected function afterDelete() {
		$this->fsFile->delete();
		
		$versioningFolder = new GO_Base_Fs_Folder(GO::config()->file_storage_path.'versioning/'.$this->id);
		$versioningFolder->delete();

		return parent::afterDelete();
	}

	/**
	 * The link that can be send in an e-mail as download link.
	 * 
	 * @return string 
	 */
	protected function getEmailDownloadURL() {
		if (!empty($this->expire_time) && !empty($this->random_code)) {
			return GO::url('files/file/download', array('id'=>$this->id,'random_code'=>$this->random_code), false);
		}
	}
	
	
	/**
	 * The link to download the file.
	 * This function does not check the file download expire time and the random code
	 * 
	 * @return string 
	 */
	protected function getDownloadURL() {
		return GO::url('files/file/download', array('id'=>$this->id), false);		
	}

	
	protected function getThumbURL() {
		return GO::url('core/thumb', 'src=' . urlencode($this->path) . '&lw=100&ph=100&zc=1&filemtime=' . $this->mtime);
	}
	
	/**
	 * Move a file to another folder
	 * 
	 * @param GO_Files_Model_Folder $destinationFolder
	 * @return boolean 
	 */
	public function move($destinationFolder){
		
		$this->folder_id=$destinationFolder->id;		
		return $this->save();
	}
	
	/**
	 * Copy a file to another folder.
	 * 
	 * @param GO_Files_Model_Folder $destinationFolder
	 * @param string $newFileName. Leave blank to use the same name.
	 * @return GO_Files_Model_File 
	 */
	public function copy($destinationFolder, $newFileName=false, $appendNumberToNameIfExists=false){
		
		$copy = $this->duplicate(array('folder_id'=>$destinationFolder->id), false);
		
		if($newFileName)
			$copy->name=$newFileName;
		
		if($appendNumberToNameIfExists)
			$copy->appendNumberToNameIfExists();
			
		$this->fsFile->copy($copy->fsFile->parent(), $copy->name);
		
		$copy->save();
		
		return $copy;
	}
	
	/**
	 * Import a filesystem file into the database.
	 * 
	 * @param GO_Base_Fs_File $fsFile
	 * @return GO_Files_Model_File 
	 */
	public static function importFromFilesystem(GO_Base_Fs_File $fsFile){
		
		$folderPath = str_replace(GO::config()->file_storage_path,"",$fsFile->parent()->path());
		
		$folder = GO_Files_Model_Folder::model()->findByPath($folderPath, true);
		return $folder->addFile($fsFile->name());	
	}
	
	/**
	 * Replace filesystem file with given file.
	 * 
	 * @param GO_Base_Fs_File $fsFile 
	 */
	public function replace(GO_Base_Fs_File $fsFile, $isUploadedFile=false){
		
		if($this->isLocked())
			throw new GO_Files_Exception_FileLocked();
		
		$this->saveVersion();
				
		$fsFile->move($this->folder->fsFolder,$this->name, $isUploadedFile);
		
		$this->mtime=$fsFile->mtime();
	
		$this->save();
	}	
	
	/**
	 * Copy current file to the versioning system. 
	 */
	public function saveVersion(){
		if(empty(GO::config()->max_file_versions))
			GO::config()->max_file_versions=3;
		
		if(GO::config()->max_file_versions>1){
			$version = new GO_files_Model_Version();
			$version->file_id=$this->id;
			$version->save();
		}
	}
	
	/**
	 * Find the file model by relative path.
	 * 
	 * @param string $relpath Relative path from GO::config()->file_storage_path
	 * @return GO_Files_Model_File 
	 */
	public function findByPath($relpath){
		$folder = GO_Files_Model_Folder::model()->findByPath(dirname($relpath));
		if(!$folder)
			return false;
		else
		{
			return $folder->hasFile(GO_Base_Fs_File::utf8Basename($relpath));
		}
		
	}
	
	
	
	/**
	 * Checks if a filename exists and renames it.
	 *
	 * @param	string $filepath The complete path to the file
	 * @access public
	 * @return string  New filename
	 */
	public function appendNumberToNameIfExists()
	{
		$dir = $this->folder->path;		
		$origName = $this->fsFile->nameWithoutExtension();
		$extension = $this->fsFile->extension();
		$x=1;
		$newName=$this->name;
		while($this->folder->hasFile($newName))
		{			
			$newName=$origName.' ('.$x.').'.$extension;
			$x++;
		}
		$this->name=$newName;
		return $this->name;
	}
}
