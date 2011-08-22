<?php
/**
 * A file on the filesystem
 */
class GO_Base_Fs_File{
	
	protected $path;

	public function __construct($path) {
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
	 * Filesize in bytes
	 * 
	 * @return int Filesize in bytes
	 */
	public function size(){
		return filesize($this->path);
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
		return unlink($this->path);
	}
	
	/**
	 * Returns the extension of a filename
	 *
	 * @access public
	 * @return string  The extension of a filename
	 */
	public function extension() {
		$extension = '';
		$pos = strrpos($this->path, '.');
		if ($pos) {
			$extension = substr($this->path, $pos +1, strlen($this->path));
		}
		return strtolower($extension);
	}
	
	/**
	 * Get the file name with out extension
	 * @return String 
	 */
	public function nameWithoutExtension(){
		$filename=$this->name();
		$pos = strrpos($filename, '.');
		if ($pos) {
			$filename = substr($filename, 0, $pos);
		}
		return $filename;
	}
}