<?php
/**
 * A file on the filesystem
 */
abstract class GO_Base_Fs_Base{
	
	protected $path;

	public function __construct($path) {
		if(empty($path))
			throw new Exception("Path may not be empty in GO_Base_Fs_Base");
		
		$this->path = dirname($path) . '/' . GO_Base_util_File::utf8Basename($path);
	}
	
	
	/**
	 * Get the parent folder object
	 * 
	 * @return GO_Base_Fs_Folder Parent folder object
	 */
	public function parent(){
		return new GO_Base_Fs_Folder(dirname($this->path));
	}
	
	/**
	 * Return absolute filesystem path
	 * 
	 * @return String 
	 */
	public function path(){
		return $this->path;
	}
	
	/**
	 * Return the modification unix timestamp
	 * 
	 * @return int Unix timestamp
	 */
	public function mtime(){
		return filemtime($this->path);
	}
	
	/**
	 * Get the name of this file or folder
	 * 
	 * @return String  
	 */
	public function name(){
		return GO_Base_util_File::utf8Basename($this->path);
	}
	
	/**
	 * Check if the file or folder exists
	 * @return boolean 
	 */
	public function exists(){
		return file_exists($this->path);
	}
	
	/**
	 * Delete the file
	 * 
	 * @return boolean 
	 */
	public function delete(){
		return false;
	}
	
	public function __toString() {
		return $this->path;
	}
}