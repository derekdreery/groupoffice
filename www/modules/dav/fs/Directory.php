<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: tickets.class.inc.php 9131 2010-10-01 10:03:59Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
class GO_Dav_Fs_Directory extends Sabre\DAV\FS\Directory{

	protected $_folder;
	protected $relpath;

	public function __construct($path) {

		$path = rtrim($path, '/');

		$this->relpath = $path;
		$path = GO::config()->file_storage_path . $path;
		
		//		if(!$this->_getFolder()->checkPermissionLevel(GO_Base_Model_Acl::READ_PERMISSION)){
//			GO::debug("DAV: User ".GO::user()->username." doesn't have write permission for ".$this->relpath);
//			throw new Sabre\DAV\Exception\Forbidden ("DAV: User ".GO::user()->username." doesn't have write permission for folder '".$this->relpath.'"');
//		}
		parent::__construct($path);
	}

	/**
	 *
	 * @return GO_Files_Model_Folder 
	 */
	private function _getFolder() {
		if (!isset($this->_folder)) {

			$this->_folder = GO_Files_Model_Folder::model()->findByPath($this->relpath);

			if (!$this->_folder) {
				throw new Sabre\DAV\Exception\NotFound('Folder not found: ' . $this->relpath);
			}
		}
		return $this->_folder;
	}

	/**
	 * Creates a new file in the directory
	 *
	 * data is a readable stream resource
	 *
	 * @param string $name Name of the file
	 * @param resource $data Initial payload
	 * @return void
	 */
	public function createFile($name, $data = null) {
		
		\GO::debug("FSD::createFile($name)");

		$folder = $this->_getFolder();

		if (!$folder->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION))
			throw new Sabre\DAV\Exception\Forbidden();

		$newFile = new GO_Base_Fs_File($this->path . '/' . $name);
		if($newFile->exists())
			throw new Exception("File already exists!");
		

		$newFile->putContents($data);
		
		if(!GO_Files_Model_File::checkQuota($newFile->size())){
			$newFile->delete();
							
			throw new Sabre\DAV\Exception\InsufficientStorage();
		}

		$folder->addFile($name);
	}

	/**
	 * Renames the node
	 *
	 * @param string $name The new name
	 * @return void
	 */
	public function setName($name) {

		$folder = $this->_getFolder();

		if (!$folder->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION))
			throw new Sabre\DAV\Exception\Forbidden();
		
		$folder->name = $name;
		$folder->save();

		$this->relpath = $folder->getPath();
		$this->path = GO::config()->file_storage_path.$this->relpath;
	}

	public function getServerPath() {
		return $this->path;
	}

	/**
	 * Moves the node
	 *
	 * @param string $name The new name
	 * @return void
	 */
	public function move($newPath) {

		\GO::debug("FSD::move($newPath)");

		if (!is_dir(dirname($newPath)))
			throw new Exception('Invalid move!');

		$folder = $this->_getFolder();

		if (!$folder->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION))
			throw new Sabre\DAV\Exception\Forbidden();
	
		$destFsFolder = new GO_Base_Fs_Folder($newPath);		
		
		//GO::debug("Dest folder: ".$destFsFolder->stripFileStoragePath());
		
		$destFolder = GO_Files_Model_Folder::model()->findByPath($destFsFolder->parent()->stripFileStoragePath());
		
		$folder->parent_id=$destFolder->id;
		$folder->name = $destFsFolder->name();
		$folder->save();

		$this->relpath = $folder->path;
		$this->path = GO::config()->file_storage_path.$this->relpath;
		
	}

	/**
	 * Creates a new subdirectory
	 *
	 * @param string $name
	 * @return void
	 */
	public function createDirectory($name) {

		GO::debug("FSD:createDirectory($this->relpath.'/'.$name)");
		
		$folder = $this->_getFolder();

		if (!$folder->checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION))
			throw new Sabre\DAV\Exception\Forbidden();

		$folder->addFolder($name);
	}

	/**
	 * Returns a specific child node, referenced by its name
	 *
	 * @param string $name
	 * @throws Sabre\DAV\Exception\NotFound
	 * @return Sabre\DAV\INode
	 */
	public function getChild($name) {

		$path = $this->path . '/' . $name;

		GO::debug("FSD:getChild($path)");

		if (is_dir($path)) {
			return new GO_Dav_Fs_Directory($this->relpath . '/' . $name);
		} else if (file_exists($path)) {
			return new GO_Dav_Fs_File($this->relpath . '/' . $name);
		} else {
			throw new Sabre\DAV\Exception\NotFound('File with name ' . $path . ' could not be located');
		}
	}

	/**
	 * Checks is a child-node exists.
	 *
	 * It is generally a good idea to try and override this. Usually it can be optimized.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function childExists($name) {
		
		

		$path = $this->path . '/' . $name;
		
		GO::debug("FSD:childExists($path)");

		try {
			if (!file_exists($path))
				throw new Sabre\DAV\Exception\NotFound('File with name ' . $path . ' could not be located');

			return true;
		} catch (Sabre\DAV\Exception\NotFound $e) {

			return false;
		}
	}

	/**
	 * Returns an array with all the child nodes
	 *
	 * @return Sabre\DAV\INode[]
	 */
	public function getChildren() {

		GO::debug('FSD::getChildren ('.$this->relpath.')');
		$nodes = array();
		//foreach(scandir($this->path) as $node) if($node!='.' && $node!='..') $nodes[] = $this->getChild($node);

		$f = $this->_getFolder();
		
		if (!$f) {
			throw new Sabre\DAV\Exception\NotFound("Folder not found in database");
		}
		
		$stmt = $f->getSubFolders();

		while ($folder = $stmt->fetch()) {
			$nodes[] = $this->getChild($folder->name);
		}

		$stmt = $f->files();
		while ($file = $stmt->fetch()) {
			$nodes[] = $this->getChild($file->name);
		}

		return $nodes;
	}

	/**
	 * Deletes all files in this directory, and then itself
	 *
	 * @return void
	 */
	public function delete() {
		
		GO::debug('FSD::delete('.$this->relpath.')');

		$folder = $this->_getFolder();
		
		if (!$folder->checkPermissionLevel(GO_Base_Model_Acl::DELETE_PERMISSION))
			throw new Sabre\DAV\Exception\Forbidden();


		$folder->delete();
	}

//	/**
//	 * Returns available diskspace information
//	 *
//	 * @return array
//	 */
//	public function getQuotaInfo() {
//
//		return array(
//				disk_total_space($this->path) - disk_free_space($this->path),
//				disk_free_space($this->path)
//		);
//	}

}
