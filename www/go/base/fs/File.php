<?php
/**
 * A file on the filesystem
 */
class GO_Base_Fs_File extends GO_Base_Fs_Base{
	/**
	 * Filesize in bytes
	 * 
	 * @return int Filesize in bytes
	 */
	public function size(){
		return filesize($this->path);
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
	
	
	/**
	 * Put data in the file. (See php function file_put_contents())
	 * 
	 * @param string $data
	 * @param type $flags
	 * @param type $context
	 * @return boolean 
	 */
	public function putContents($data, $flags=null, $context=null){
		if(file_put_contents($this->path, $data, $flags, $context)){
			chmod($this->path, GO::config()->file_create_mode);
			if(GO::config()->file_change_group)
				chgrp ($this->path, GO::config()->file_change_group);
			return true;
		}else
		{
			return false;
		}
	}
	
	/**
	 * Get the contents of this file.
	 * 
	 * @return String  
	 */
	public function getContents(){
		return file_get_contents($this->path);
	}
}