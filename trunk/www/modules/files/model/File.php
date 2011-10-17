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

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	public function relations() {
		return array(
				'folder' => array('type' => self::BELONGS_TO, 'model' => 'GO_Files_Model_Folder', 'field' => 'folder_id')
		);
	}

	protected function init() {
		$this->columns['expire_time']['gotype'] = 'unixdate';
		parent::init();
	}
	
	protected function beforeSave() {
		
		if(!$this->isNew){
			if($this->isModified('name')){				
				//rename filesystem file.
				$oldFsFile = new GO_Base_Fs_File(dirname($this->fsFile->path()).'/'.$this->getOldAttributeValue('name'));				
				$oldFsFile->rename($this->name);
			}

			if($this->isModified('folder_id')){
				//file will be moved so we need the old folder path.
				$oldFolderId = $this->getOldAttributeValue('folder_id');
				$oldFolder = GO_Files_Model_Folder::model()->findByPk($oldFolderId);				
				$oldRelPath = $oldFolder->path;				
				$oldPath = GO::config()->file_storage_path . $oldRelPath . '/' . $this->name;

				$fsFile= new GO_Base_Fs_File($oldPath);

				if (!$fsFile->move(new GO_Base_Fs_Folder(GO::config()->file_storage_path . dirname($this->path))))
					throw new Exception("Could not rename folder on the filesystem");
			}
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

	protected function afterSave($wasNew) {

		if ($wasNew) {
			//$this->fsFile->create();
		} else {
			
//			if($this->isModified('name')){				
//				//rename filesystem file.
//				$oldFsFile = new GO_Base_Fs_File(dirname($this->fsFile->path()).'/'.$this->getOldAttributeValue('name'));				
//				$oldFsFile->rename($this->name);
//			}
//			
//			if($this->isModified('folder_id')){
//				//file will be moved so we need the old folder path.
//				$oldFolderId = $this->getOldAttributeValue('folder_id');
//				$oldFolder = GO_Files_Model_Folder::model()->findByPk($oldFolderId);				
//				$oldRelPath = $oldFolder->path;				
//				$oldPath = GO::config()->file_storage_path . $oldRelPath . '/' . $this->name;
//				
//				$fsFile= new GO_Base_Fs_File($oldPath);
//				
//				if (!$fsFile->move(new GO_Base_Fs_Folder(GO::config()->file_storage_path . dirname($this->path))))
//					throw new Exception("Could not rename folder on the filesystem");
//			}
		}

		return parent::afterSave($wasNew);
	}

	protected function getPath() {
		return $this->folder->path . '/' . $this->name;
	}

	protected function getFsFile() {
		return new GO_Base_Fs_File(GO::config()->file_storage_path . $this->path);
	}

	protected function afterDelete() {
		$this->fsFile->delete();

		return parent::afterDelete();
	}

	protected function getDownloadURL() {
		if (!empty($this->expire_time) && !empty($this->random_code)) {
			return GO::url('files/file/download', 'id=' . $this->id . '&random_code=' . $this->random_code, false);
		}
	}

	protected function getThumbURL() {
		return GO::url('core/thumb', 'src=' . urlencode($this->path) . '&lw=100&ph=100&zc=1&filemtime=' . $this->fsFile->mtime());
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
	
	public function copy($destinationFolder){
		
		$copy = $this->duplicate(array('folder_id'=>$destinationFolder->id), false);
		$this->fsFile->copy($copy->fsFile->parent());
		$copy->save();
		
		return true;
	}
	
	
	public static function importFromFilesystem($fsFile){
		
		$folderPath = str_replace(GO::config()->file_storage_path,"",$fsFile->parent()->path());
		
		$folder = GO_Files_Model_Folder::model()->findByPath($folderPath, true);
		return $folder->addFile($fsFile->name());	
	}
}
