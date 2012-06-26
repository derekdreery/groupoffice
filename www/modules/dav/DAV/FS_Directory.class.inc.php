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
class GO_DAV_FS_Directory extends Sabre_DAV_FS_Node implements Sabre_DAV_ICollection, Sabre_DAV_IQuota {

	protected $files;
	protected $folder;
	protected $write_permission;
	protected $relpath;

	public function __construct($path){
		global $GO_CONFIG, $GO_SECURITY;
		
		$path = rtrim($path, '/');

		$this->relpath=$path;
		$path = $GO_CONFIG->file_storage_path.$path;
		
		$this->files = new files();
		
		if($this->relpath != "users/".$GLOBALS['userpass'][0] && !$this->files->has_read_permission($GO_SECURITY->user_id, $this->getFolder())){
			throw new Sabre_DAV_Exception_Forbidden();
		}

		parent::__construct($path);
	}

	private function getFolder(){
		global $files;
		if(!isset($this->folder)){
			
			$this->folder=$this->files->resolve_path($this->relpath);

			if(!$this->folder){
				throw new Sabre_DAV_Exception_FileNotFound('File not found: '.$this->relpath);
			}
		}
		return $this->folder;
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
		
		global $GO_SECURITY, $files;

		if(!$this->files->has_write_permission($GO_SECURITY->user_id, $this->getFolder()))
			throw new Sabre_DAV_Exception_Forbidden();

        $newPath = $this->path . '/' . $name;
        file_put_contents($newPath,$data);
				$file_id = $this->files->import_file($newPath, $this->folder['id']);
    }

	/**
     * Renames the node
     *
     * @param string $name The new name
     * @return void
     */
    public function setName($name) {
		global $GO_SECURITY, $files;

		if(!$this->files->has_write_permission($GO_SECURITY->user_id, $this->getFolder()))
			throw new Sabre_DAV_Exception_Forbidden();

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

		go_debug("FSD::move($newPath)");

		if(!is_dir(dirname($newPath)))
				throw new Exception('Invalid move!');

		global $GO_SECURITY, $files;

		if(!$this->files->has_write_permission($GO_SECURITY->user_id, $this->getFolder()))
			throw new Sabre_DAV_Exception_Forbidden();

		rename($this->path, $newPath);

		$destFolder = $this->files->resolve_path($this->files->strip_server_path(dirname($newPath)));

		$sourceFolder = $this->getFolder();
		$sourceFolder['name']=utf8_basename($newPath);

		$this->files->move_folder($sourceFolder, $destFolder);

		$this->path = $newPath;
		$this->relpath = $this->files->strip_server_path($this->path);
    }

    /**
     * Creates a new subdirectory
     *
     * @param string $name
     * @return void
     */
    public function createDirectory($name) {

		global $GO_SECURITY, $files;

		if(!$this->files->has_write_permission($GO_SECURITY->user_id, $this->getFolder()))
			throw new Sabre_DAV_Exception_Forbidden();

        //$newPath = $this->path . '/' . $name;
        //mkdir($newPath);

		$this->files->mkdir($this->getFolder(), $name);
    }

    /**
     * Returns a specific child node, referenced by its name
     *
     * @param string $name
     * @throws Sabre_DAV_Exception_FileNotFound
     * @return Sabre_DAV_INode
     */
    public function getChild($name) {

		global $GO_CONFIG;

        $path = $this->path.'/'.$name;

		go_debug("FSD:getChild($path)");
    
        if (is_dir($path)) {
            return new GO_DAV_FS_Directory($this->relpath . '/'. $name);
        } else if(file_exists($path)) {
            return new GO_DAV_FS_File($this->relpath . '/' . $name);
        }else
		{
			throw new Sabre_DAV_Exception_FileNotFound('File with name ' . $path . ' could not be located');
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

        try {
			if(!file_exists($path))
				throw new Sabre_DAV_Exception_FileNotFound('File with name ' . $path . ' could not be located');

			return true;

        } catch(Sabre_DAV_Exception_FileNotFound $e) {

            return false;

        }

    }

    /**
     * Returns an array with all the child nodes
     *
     * @return Sabre_DAV_INode[]
     */
    public function getChildren() {

		//go_debug('FSD::getChildren');

		global $files;
        $nodes = array();
        //foreach(scandir($this->path) as $node) if($node!='.' && $node!='..') $nodes[] = $this->getChild($node);

		$f = $this->getFolder();

		if(!$f)
		{
			go_debug('Could not get: '.$this->relpath);
		}

		$this->files->check_folder_sync($f, $this->relpath);
		$this->files->get_folders($f['id'],'name','ASC',0,0,true);

		while($folder = $this->files->next_record()) {
			$nodes[]=$this->getChild($folder['name']);
		}

		$this->files->get_files($f['id'], 'name', 'ASC', 0, 0);
		while($file = $this->files->next_record()) {
			$nodes[]=$this->getChild($file['name']);
		}

        return $nodes;

    }

    /**
     * Deletes all files in this directory, and then itself
     *
     * @return void
     */
    public function delete() {

		global $GO_SECURITY, $files;

		if(!$this->files->has_write_permission($GO_SECURITY->user_id, $this->getFolder()))
			throw new Sabre_DAV_Exception_Forbidden();


        //foreach($this->getChildren() as $child) $child->delete();
       // rmdir($this->path);

		$this->files->delete_folder($this->getFolder());

    }

    /**
     * Returns available diskspace information
     *
     * @return array
     */
    public function getQuotaInfo() {

        return array(
            disk_total_space($this->path)-disk_free_space($this->path),
            disk_free_space($this->path)
            );

    }

}
