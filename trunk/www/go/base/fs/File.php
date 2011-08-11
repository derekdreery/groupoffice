<?php
class GO_Base_Fs_File{
	
	protected $path;

	public function __construct($path) {
		$this->path = dirname($path) . '/' . GO_Base_util_File::utf8Basename($path);
	}
	
	public function mtime(){
		return filemtime($this->path);
	}
	
	public function size(){
		return filesize($this->path);
	}
	public function name(){
		return GO_Base_util_File::utf8Basename($this->path);
	}
	
	public function exists(){
		return file_exists($this->path);
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
	
	public function nameWithoutExtension(){
		$filename=$this->name();
		$pos = strrpos($filename, '.');
		if ($pos) {
			$filename = substr($filename, 0, $pos);
		}
		return $filename;
	}
}