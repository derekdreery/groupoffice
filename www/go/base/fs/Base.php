<?php
/*
 * Copyright Intermesh BV
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * Base class for filesystem objects
 * 
 * @package GO.base.fs
 * @version $Id: RFC822.class.inc 7536 2011-05-31 08:37:36Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @copyright Copyright Intermesh BV.
 */
abstract class GO_Base_Fs_Base{
	
	protected $path;
	
	const INVALID_CHARS = '/[\/:\*\?"<>|\\\]/';

	public function __construct($path) {
		if(empty($path))
			throw new Exception("Path may not be empty in GO_Base_Fs_Base");
		
		if(!self::checkPathInput($path))
			throw new Exception("The supplied path '$path' was invalid");
		
		$this->path = dirname($path) . '/' . self::utf8Basename($path);
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
	 * Return the creation unix timestamp
	 * 
	 * @return int Unix timestamp
	 */
	public function ctime(){
		return filectime($this->path);
	}
	
	/**
	 * Get the name of this file or folder
	 * 
	 * @return String  
	 */
	public function name(){
		
		if(!function_exists('mb_substr'))
		{
			return basename($this->path);
		}

		if(empty($this->path))
		{
			return '';
		}
		$pos = mb_strrpos($this->path, '/');
		if($pos===false)
		{
			return $this->path;
		}else
		{
			return mb_substr($this->path, $pos+1);
		}
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
	
	/**
	 * Checks if a path send as a request parameter is valid.
	 * 
	 * @param String $path
	 * @return boolean 
	 */
	public static function checkPathInput($path){
		return strpos($path, '../') === false && strpos($path, '..\\')===false;
	}
	
	
	/**
	 * Get's the filename from a path string and works with UTF8 characters
	 * 
	 * @param String $path
	 * @return String 
	 */
	public static function utf8Basename($path)
	{
		if(!function_exists('mb_substr'))
		{
			return basename($path);
		}
		//$path = trim($path);
		if(substr($path,-1,1)=='/')
		{
			$path = substr($path,0,-1);
		}
		if(empty($path))
		{
			return '';
		}
		$pos = mb_strrpos($path, '/');
		if($pos===false)
		{
			return $path;
		}else
		{
			return mb_substr($path, $pos+1);
		}
	}
	
	/**
	 * Remove unwanted characters from a string so it can safely be used as a filename.
	 * 
	 * @param string $filename
	 * @return string 
	 */
	public static function stripInvalidChars($filename){
		$filename = trim(preg_replace(self::INVALID_CHARS,'', $filename));

		//IE likes to change a double white space to a single space
		//We must do this ourselves so the filenames will match.
		$filename =  preg_replace('/\s+/', ' ', $filename);

		//strip dots from start
		$filename=ltrim($filename, '.');

		if(empty($filename)){
			$filename = 'unnamed';
		}
		return $filename;
	}
	
	/**
	 * Check if this object is a folder.
	 * 
	 * @return boolean 
	 */
	public function isFolder(){
		return is_dir($this->path);
	}
	
	/**
	 * Check if this object is a file.
	 * 
	 * @return boolean 
	 */
	public function isFile(){
		return !is_dir($this->path);
	}
	
	/**
	 * Rename a file or folder
	 * 
	 * @param String $name
	 * @return boolean 
	 */
	public function rename($name){
		$oldPath = $this->path;
		$newPath = dirname($this->path).'/'.$name;
		
		if(rename($oldPath,$newPath))
		{
			$this->path = $newPath;
			return true;
		}else
		{
			return false;
		}		
	}
	
	/**
	 * Get the path without GO::config()->file_storage_path.
	 * 
	 * @return string 
	 */
	public function stripFileStoragePath(){
		return str_replace(GO::config()->file_storage_path,'', $this->path());
	}
	
	/**
	 * Get the path without GO::config()->tmpdir.
	 * 
	 * @return string 
	 */
	public function stripTempPath(){
		return str_replace(GO::config()->tmpdir,'', $this->path());
	}

}