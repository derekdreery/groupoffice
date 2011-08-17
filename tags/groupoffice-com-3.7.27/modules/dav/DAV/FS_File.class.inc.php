<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
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
	}

	public function checkWritePermission($delete=false){
		global $GO_SECURITY, $files;

		$this->folder=$files->resolve_path(dirname($this->relpath));

		if(!$files->has_write_permission($GO_SECURITY->user_id, $this->folder))
				throw new Sabre_DAV_Exception_Forbidden();

		/*if($delete){
			if(!$this->files->has_delete_permission($GO_SECURITY->user_id, $this->folder))
				throw new Sabre_DAV_Exception_Forbidden();
		}else {
			if(!$this->files->has_write_permission($GO_SECURITY->user_id, $this->folder))
				throw new Sabre_DAV_Exception_Forbidden();
		}*/
	}

    /**
     * Updates the data
     *
     * @param resource $data
     * @return void
     */
    public function put($data) {
      global $files;
      $this->checkWritePermission();


      file_put_contents($this->path,$data);
      $file_id = $files->import_file($this->path, $this->folder['id']);
      go_debug('ADDED FILE WITH WEBDAV -> FILE_ID: '.$file_id);

    }

	/**
     * Renames the node
     *
     * @param string $name The new name
     * @return void
     */
    public function setName($name) {
		global $files;
		$this->checkWritePermission();

        parent::setName($name);

		$this->relpath = $files->strip_server_path($this->path);
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
		global $files;
		$this->checkWritePermission();

		go_debug('FSFile::move('.$this->path.' -> '.$newPath.')');

		if(!rename($this->path, $newPath)){
			go_debug('Failed to rename');
			throw new Exception('Failed to rename!');
		}

		$destFolder = $files->resolve_path($files->strip_server_path(dirname($newPath)));
    $this->file=$files->resolve_path($this->relpath);
		$files->move_file($this->file, $destFolder);

		$this->path = $newPath;
		$this->relpath = $files->strip_server_path($this->path);
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

		return File::get_mime($this->path);

        //return null;

    }

}

