<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: FS_File.class.inc.php 7942 2011-08-22 14:25:46Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
class GO_Dav_Fs_File extends Sabre\DAV\FS\File {

	protected $folder;
	protected $write_permission;
	protected $relpath;

	public function __construct($path) {

		$this->relpath = $path;
		$path = GO::config()->file_storage_path . $path;

		parent::__construct($path);
	}

	public function checkWritePermission($delete=false) {

		$fsFile = new GO_Base_Fs_File($this->path);

		$this->folder = GO_Files_Model_Folder::model()->findByPath($fsFile->parent()->stripFileStoragePath());
		if (!GO_Base_Model_Acl::hasPermission($this->folder->getPermissionLevel(), GO_Base_Model_Acl::WRITE_PERMISSION)){
			throw new Sabre\DAV\Exception\Forbidden("DAV: User ".GO::user()->username." doesn't have write permission for file '".$this->relpath.'"');
		}

		/* if($delete){
		  if(!$this->files->has_delete_permission($GLOBALS['GO_SECURITY']->user_id, $this->folder))
		  throw new Sabre\DAV\Exception\Forbidden();
		  }else {
		  if(!$this->files->has_write_permission($GLOBALS['GO_SECURITY']->user_id, $this->folder))
		  throw new Sabre\DAV\Exception\Forbidden();
		  } */
	}

	/**
	 * Updates the data
	 *
	 * @param resource $data
	 * @return void
	 */
	public function put($data) {
		
		GO::debug("DAVFile:put( ".$this->relpath.")");
		$this->checkWritePermission();
		
//		$file = GO_Files_Model_File::model()->findByPath($this->relpath);
//		$file->saveVersion();
//		$file->putContents($data);

		file_put_contents($this->path, $data);
		GO_Files_Model_File::model()->findByPath($this->relpath);

		//GO::debug('ADDED FILE WITH WEBDAV -> FILE_ID: ' . $file_id);
	}

	/**
	 * Renames the node
	 *
	 * @param string $name The new name
	 * @return void
	 */
	public function setName($name) {
		
		GO::debug("DAVFile::setName($name)");
		$this->checkWritePermission();

		parent::setName($name);
		
		$file = GO_Files_Model_File::model()->findByPath($this->relpath);
		$file->name=$name;
		$file->save();
		
		$this->relpath = $file->path;
		$this->path = GO::config()->file_storage_path.$this->relpath;
	}

	public function getServerPath() {
		return $this->path;
	}

	/**
	 * Movesthe node
	 *
	 * @param string $name The new name
	 * @return void
	 */
	public function move($newPath) {
		$this->checkWritePermission();

		GO::debug('DAVFile::move(' . $this->path . ' -> ' . $newPath . ')');
		
		$destFsFolder = new GO_Base_Fs_Folder(dirname($newPath));		
		$destFolder = GO_Files_Model_Folder::model()->findByPath($destFsFolder->stripFileStoragePath());
		
		$file = GO_Files_Model_File::model()->findByPath($this->relpath);
		$file->folder_id=$destFolder->id;
		$file->name = GO_Base_Fs_File::utf8Basename($newPath);
		$file->save();
		
		$this->relpath = $file->path;
		$this->path = GO::config()->file_storage_path.$this->relpath;
	}

	/**
	 * Returns the data
	 *
	 * @return string
	 */
	public function get() {

		return fopen($this->path, 'r');
	}

	/**
	 * Delete the current file
	 *
	 * @return void
	 */
	public function delete() {
		$this->checkWritePermission(true);
		$file = GO_Files_Model_File::model()->findByPath($this->relpath);
		$file->delete();
	}

	/**
	 * Returns the size of the node, in bytes
	 *
	 * @return int
	 */
	public function getSize() {

		return filesize($this->path);
	}

	/**
	 * Returns the ETag for a file
	 *
	 * An ETag is a unique identifier representing the current version of the file. If the file changes, the ETag MUST change.
	 *
	 * Return null if the ETag can not effectively be determined
	 *
	 * @return mixed
	 */
	public function getETag() {
		return '"' . md5_file($this->path) . '"';
	}

	/**
	 * Returns the mime-type for a file
	 *
	 * If null is returned, we'll assume application/octet-stream
	 *
	 * @return mixed
	 */
	public function getContentType() {
		
		$fsFile = new GO_Base_Fs_File($this->path);

		return $fsFile->mimeType();	

	}

}

