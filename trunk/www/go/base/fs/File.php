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
 * A file on the filesystem
 * 
 * @package GO.base.fs
 * @version $Id: RFC822.class.inc 7536 2011-05-31 08:37:36Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @copyright Copyright Intermesh BV.
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
		return !file_exists($this->path) || unlink($this->path);
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
		return trim(strtolower($extension));
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
	public function contents(){
		return file_get_contents($this->path);
	}
	
	
	public static function getFileTypeDescription($extension) {		
		$lang = GO::t($extension,'base','filetypes');
		
		if($lang==$extention)
			$lang = GO::t('unknown','base','filetypes');
		
		return $lang;
	}
	
	public function typeDescription(){
		return self::getFileTypeDescription($this->extension());
	}
	
	
	/**
	 * Returns the mime type for the file.
	 * If it can't detect it it will return application/octet-stream
	 * 
	 * @return String 
	 */
	public function mimeType()
	{
		$types = file_get_contents(GO::config()->root_path.'mime.types');

		if($this->extension()=='')
		{
			return 'application/octet-stream';
		}

		$pos = strpos($types, ' '.$this->extension());

		if($pos)
		{
			$pos++;

			$start_of_line = GO_Base_Util_String::rstrpos($types, "\n", $pos);
			$end_of_mime = strpos($types, ' ', $start_of_line);
			$mime = substr($types, $start_of_line+1, $end_of_mime-$start_of_line-1);

			return $mime;
		}

		if(file_exists($this->path())){
			if(function_exists('finfo_open')){
					$finfo    = @finfo_open(FILEINFO_MIME);
					$mimetype = @finfo_file($finfo, $this->path());
					finfo_close($finfo);
					return $mimetype;
			}elseif(function_exists('mime_content_type'))
			{
				return @mime_content_type($this->path());
			}
		}
    
    return 'application/octet-stream';    
	}
	
	/**
	 * Check if the file is an image.
	 * 
	 * @return boolean 
	 */
	public function isImage(){
		switch($this->extension()){
			case 'ico':
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			case 'xmind':

				return true;
			default:
				return false;
		}
	}
	
	/**
	 * Output the contents of this file to standard out (browser).
	 */
	public function output(){
		readfile($this->path());
	}
	
	/**
	 * Move a file to another folder.
	 * 
	 * @param GO_Base_Fs_Folder $destinationFolder 
	 * @return boolean
	 */
	public function move($destinationFolder){
		
		$newPath = $destinationFolder->path().'/'.$this->name();
		
		if(rename($this->path, $newPath))
		{
			$this->path = $newPath;
			return true;
		}else
		{
			return false;
		}
						
	}
	
	/**
	 * Copy a file to another folder.
	 * 
	 * @param GO_Base_Fs_Folder $destinationFolder 
	 * @return boolean
	 */
	public function copy($destinationFolder){
		
		$newPath = $destinationFolder->path().'/'.$this->name();
		GO::debug('copy: '.$this->path.' > '.$newPath);
		
		if(!copy($this->path, $newPath))
			throw new Exception("Could not copy ".$this->name());
				
		chmod($newPath, GO::config()->file_create_mode);
		if(GO::config()->file_change_group)
			chgrp($newPath, GO::config()->file_change_group);
						
		return true;
	}
	
	/**
	 *
	 * @param array $uploadedFileArray
	 * @param GO_Base_Fs_Folder  $destinationFolder
	 * @return GO_Base_Fs_File 
	 */
	public static function moveUploadedFiles($uploadedFileArray, $destinationFolder){
		
		if(!is_array($uploadedFileArray['tmp_name'])){
			$uploadedFileArray['tmp_name']=array($uploadedFileArray['tmp_name']);
			$uploadedFileArray['name']=array($uploadedFileArray['name']);
		}
		
		$files = array();
		for($i=0;$i<count($uploadedFileArray['tmp_name']);$i++){
			if (is_uploaded_file($uploadedFileArray['tmp_name'][$i])) {
				$destinationPath = $destinationFolder->path().'/'.$uploadedFileArray['name'][$i];
				echo $destinationPath;
				if(move_uploaded_file($uploadedFileArray['tmp_name'][$i], $destinationPath)){		
					$file = new GO_Base_Fs_File($destinationPath);
					$file->setDefaultPermissions();

					$files[]=$file;
				}
			}
		}
		
		return $files;
	}
	
	/**
	 * Set's default permissions and group ownership
	 */
	public function setDefaultPermissions(){
		chmod($this->path, GO::config()->file_create_mode);
		if(!empty(GO::config()->file_change_group))
			chgrp($this->path, GO::config()->file_change_group);
	}
}