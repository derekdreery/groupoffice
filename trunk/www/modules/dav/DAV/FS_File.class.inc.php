<?php

/**
 * File class
 *
 * @package Sabre
 * @subpackage DAV
 * @copyright Copyright (C) 2007-2010 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class GO_DAV_FS_File extends Sabre_DAV_FS_Node implements Sabre_DAV_IFile {

	protected $files;
	protected $folder;
	protected $write_permission;
	protected $relpath;

	public function __construct($path){
		global $GO_CONFIG;


		$this->relpath=$path;
		$path = $GO_CONFIG->file_storage_path.$path;

		parent::__construct($path);

		/*
		 * Group-Office files module class
		 */
		$this->files = new files();		

	}

	public function checkWritePermission($delete=false){
		global $GO_SECURITY;
		
		$this->file=$this->files->resolve_path($this->relpath);
		$this->folder=$this->files->get_folder($this->file['folder_id']);

		if($delete){
			if(!$this->files->has_delete_permission($GO_SECURITY->user_id, $this->folder))
				throw new Sabre_DAV_Exception_Forbidden();
		}else {
			if(!$this->files->has_write_permission($GO_SECURITY->user_id, $this->folder))
				throw new Sabre_DAV_Exception_Forbidden();
		}
	}

    /**
     * Updates the data
     *
     * @param resource $data
     * @return void
     */
    public function put($data) {

		$this->checkWritePermission();
			

        file_put_contents($this->path,$data);

    }

	/**
     * Renames the node
     *
     * @param string $name The new name
     * @return void
     */
    public function setName($name) {
		$this->checkWritePermission();
		
        parent::setName($name);

		$this->relpath = $this->files->strip_server_path($this->path);
    }

	public function getServerPath(){
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

		rename($this->path, $newPath);

		$destFolder = $this->files->resolve_path($this->files->strip_server_path(dirname($newPath)));

		$this->files->move_file($this->file, $destFolder);
		
		$this->path = $newPath;
		$this->relpath = $this->files->strip_server_path($this->path);
    }

    /**
     * Returns the data
     *
     * @return string
     */
    public function get() {

        return fopen($this->path,'r');

    }

    /**
     * Delete the current file
     *
     * @return void
     */
    public function delete() {
		$this->checkWritePermission(true);
        unlink($this->path);

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

        return null;

    }

    /**
     * Returns the mime-type for a file
     *
     * If null is returned, we'll assume application/octet-stream
     *
     * @return mixed
     */
    public function getContentType() {

        return null;

    }

}

