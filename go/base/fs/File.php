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
	
	
	private static $_allowDeletes=true;
	
	/**
	 * Set this to false if you want to make sure no files are deleted
	 * in a script. If a file is deleted
	 * 
	 * @param boolean
	 * @return boolean old value
	 */
	public static function setAllowDeletes($allowDeletes){
		
//		GO::debugCalledFrom();
//		GO::debug("Allowed deletes is ".($allowedDeletes ? "true" : "false"));
		
		$old = self::$_allowDeletes;
		self::$_allowDeletes=$allowDeletes;
		
		return $old;
	}
	
	/**
	 * Get a unique temporary file.
	 * 
	 * @param string $filename
	 * @param string $extension
	 * @return GO_Base_Fs_File 
	 */
	public static function tempFile($filename='',$extension=''){
		$folder = GO::config()->getTempFolder();
		
		if(!empty($filename))
			$p=$folder->path().'/'.$filename;
		else
			$p=$folder->path().'/'.uniqid(time());
		
		if(!empty($extension))
			$p.='.'.$extension;
		
		return new GO_Base_Fs_File($p);
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
	 * Get the size formatted nicely like 1.5 MB
	 * 
	 * @param int $decimals
	 * @return string 
	 */
	public function humanSize($decimals = 1) {
		$size = $this->size();
		if($size==0)
			return 0;
		
		switch ($size) {
			case ($size > 1073741824) :
				$size = GO_Base_Util_Number::localize($size / 1073741824, $decimals);
				$size .= " GB";
				break;

			case ($size > 1048576) :
				$size = GO_Base_Util_Number::localize($size / 1048576, $decimals);
				$size .= " MB";
				break;

			case ($size > 1024) :
				$size = GO_Base_Util_Number::localize($size / 1024, $decimals);
				$size .= " KB";
				break;

			default :
				$size = GO_Base_Util_Number::localize($size, $decimals);
				$size .= " bytes";
				break;
		}
		return $size;
	}
	
	/**
	 * Delete the file
	 * 
	 * @return boolean 
	 */
	public function delete(){
		
		if(!file_exists($this->path))
			return true;
		
		if(self::$_allowDeletes)		
			return unlink($this->path);
		else{
			$errorMsg = "The program tried to delete a file (".$this->stripFileStoragePath().") while GO_Base_Fs_File::\$allowDeletes is set to false.";
			GO::debug($errorMsg);
			throw new Exception($errorMsg);
		}
	}
	
	/**
	 * Returns the extension of a filename
	 *
	 * @access public
	 * @return string  The extension of a filename
	 */
	public function extension() {
		$extension = '';
		$filename = $this->name();
		$pos = strrpos($filename, '.');
		if ($pos) {
			$extension = substr($filename, $pos +1);
		}
		//return trim(strtolower($extension)); // Does not work when extension on disk is in capital letters (.PDF, .XLSX)
		return trim($extension);
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
	 * Checks if a filename exists and renames it.
	 *
	 * @param	string $filepath The complete path to the file
	 * @access public
	 * @return string  New filepath
	 */
	public function appendNumberToNameIfExists()
	{
		$dir = $this->parent()->path();		
		$origName = $this->nameWithoutExtension();
		$extension = $this->extension();
		$x=1;
		while($this->exists())
		{			
			$this->path=$dir.'/'.$origName.' ('.$x.').'.$extension;
			$x++;
		}
		return $this->path;
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
			$this->setDefaultPermissions();
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
		return file_get_contents($this->path());
	}
	
	/**
	 * Get the contents of this file.
	 * 
	 * @return String  
	 */
	public function contents(){
		return file_get_contents($this->path);
	}
	
	/**
	 * Get human friendly file type description. eg. Text document.
	 * 
	 * @param string $extension
	 * @return string 
	 */
	public static function getFileTypeDescription($extension) {		
		$lang = GO::t($extension,'base','filetypes');
		
		if($lang==$extension)
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
	 * @todo rename to contentType
	 * @return String 
	 */
	public function mimeType()
	{
		$types = file_get_contents(GO::config()->root_path.'mime.types');

		if($this->extension()!='')
		{			

			$pos = stripos($types, ' '.$this->extension());

			if($pos)
			{
				$pos++;

				$start_of_line = GO_Base_Util_String::rstrpos($types, "\n", $pos);
				$end_of_mime = strpos($types, ' ', $start_of_line);
				$mime = substr($types, $start_of_line+1, $end_of_mime-$start_of_line-1);

				return $mime;
			}
		}

		//if($this->exists()){ Don't use exists function becuase MemoryFile returns true but it does not exist on disk
		if(file_exists($this->path())){
			if(function_exists('finfo_open')){
					$finfo    = finfo_open(FILEINFO_MIME);
					$mimetype = finfo_file($finfo, $this->path());
					finfo_close($finfo);
					return $mimetype;
			}elseif(function_exists('mime_content_type'))
			{
				return mime_content_type($this->path());
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
			case 'svg':

				return true;
			default:
				return false;
		}
	}
	
	/**
	 * Output the contents of this file to standard out (browser).
	 */
	public function output() {
		@ob_clean();
		@flush();

		//readfile somehow caused a memory exhausted error. This stopped when I added 
		//ob_clean and flush above, but the browser hung with presenting the download 
		//dialog until the entire download was completed.
		//The code below seems to work better.
		//
		//readfile($this->path());
		
		$handle = fopen($this->path(), "rb");

		if (!is_resource($handle))
			throw new Exception("Could not read file");
		
		while (!feof($handle)) {
			echo fread($handle, 1024);
			flush();
		}
	}
	
	/**
	 * Move a file to another folder.
	 * 
	 * @param GO_Base_Fs_Folder $destinationFolder 
	 * @param string $newFileName Optionally rename the file too.
	 * @param boolean $isUploadedFile Check if this file was upload for security reasons.
	 * @param boolean $appendNumberToNameIfDestinationExists Rename the file like "File (1)" if it already exists. 
	 * @return boolean
	 */
	public function move($destinationFolder, $newFileName=false, $isUploadedFile=false,$appendNumberToNameIfDestinationExists=false){
		
		if(!$newFileName)
			$newFileName=$this->name();
		
		$newPath = $destinationFolder->path().'/'.$newFileName;
		
		if($appendNumberToNameIfDestinationExists){
			$file = new GO_Base_Fs_File($newPath);
			$file->appendNumberToNameIfExists();
			$newPath = $file->path();
		}
		
		if($isUploadedFile){
			if(!is_uploaded_file($this->path()))
				return false;
			
			if(move_uploaded_file($this->path(), $newPath)){
				$this->path = $newPath;
				return true;
			}
		}else
		{		
			if(rename($this->path, $newPath))
			{
				$this->path = $newPath;
				return true;
			}
		}
		
		return false;						
	}
	
	/**
	 * Copy a file to another folder.
	 * 
	 * @param GO_Base_Fs_Folder $destinationFolder 
	 * @return GO_Base_Fs_File
	 */
	public function copy(GO_Base_Fs_Folder $destinationFolder, $newFileName=false){
		
		if(!$newFileName)
			$newFileName=$this->name();
			
		$newPath = $destinationFolder->path().'/'.$newFileName;
		GO::debug('copy: '.$this->path.' > '.$newPath);
		
		if(!copy($this->path, $newPath)){
			
			$old = str_replace(GO::config()->file_storage_path, '', $this->path);
			$new = str_replace(GO::config()->file_storage_path, '', $newPath);
			
			throw new Exception("Could not copy ".$old." to ".$new);
		}
				
		chmod($newPath, octdec(GO::config()->file_create_mode));
		if(GO::config()->file_change_group)
			chgrp($newPath, GO::config()->file_change_group);
						
		return new GO_Base_Fs_File($newPath);
	}
	
	/**
	 *
	 * @param array $uploadedFileArray
	 * @param GO_Base_Fs_Folder  $destinationFolder
	 * @param boolean $overwrite If false this function will append a number. eg. Filename (1).jpg
	 * @return GO_Base_Fs_File[]
	 */
	public static function moveUploadedFiles($uploadedFileArray, $destinationFolder, $overwrite=false){
		
		if(!is_array($uploadedFileArray['tmp_name'])){
			$uploadedFileArray['tmp_name']=array($uploadedFileArray['tmp_name']);
			$uploadedFileArray['name']=array($uploadedFileArray['name']);
		}
		
		$files = array();
		for($i=0;$i<count($uploadedFileArray['tmp_name']);$i++){
			if (is_uploaded_file($uploadedFileArray['tmp_name'][$i])) {
				$destinationFile = new GO_Base_Fs_File($destinationFolder->path().'/'.$uploadedFileArray['name'][$i]);
				if(!$overwrite)
					$destinationFile->appendNumberToNameIfExists();

				if(move_uploaded_file($uploadedFileArray['tmp_name'][$i], $destinationFile->path())){		
					$destinationFile->setDefaultPermissions();

					$files[]=$destinationFile;
				}
			}
		}
		
		return $files;
	}
	
	/**
	 * Set's default permissions and group ownership
	 */
	public function setDefaultPermissions(){
		@chmod($this->path, octdec(GO::config()->file_create_mode));
		if(!empty(GO::config()->file_change_group))
			@chgrp($this->path, GO::config()->file_change_group);
	}
	
	/**
	 * Try to detect the encoding. See PHP manual mb_detect_encoding
	 * 
	 * @return string 
	 */
	public function detectEncoding($str){
		$enc = false;
		if(function_exists('mb_detect_encoding'))
		{
			$enc = mb_detect_encoding($this->getContents(), "ASCII,JIS,UTF-8,ISO-8859-1,ISO-8859-15,EUC-JP,SJIS");
		}
		
		return $enc;
	}
	
	/**
	 * Convert and clean the file to ensure it has valid UTF-8 data.
	 * 
	 * @return boolean 
	 */
	public function convertToUtf8(){
		
		if(!$this->isWritable())
			return false;
		
		$str = $this->getContents();
		if(!$str){
			return false;
		}
		
		$enc = $this->detectEncoding($str);
		if(!$enc)
			$enc='UTF-8';
		
		return $this->putContents(GO_Base_Util_String::clean_utf8($str, $enc));
	}
	
	/**
	 * Get the md5 hash from this file
	 * 
	 * @return string
	 */
	public function md5Hash(){
		return md5_file($this->path);
	}
	
	/**
	 * Compare this file with an other file.
	 *
	 * @param GO_Base_Fs_File $file
	 * @return bool True if the file is different, false if file is the same.
	 */
	public function diff(GO_Base_Fs_File $file){
		if($this->md5Hash() != $file->md5Hash())
			return true;
		else
			return false;
	}
	
	/**
	 * Create the file
	 * 
	 * @param boolean $createPath Create the folders for this file also?
	 * @return bool $successfull
	 */
	public function touch($createPath=false){
		if($createPath)
			$this->parent()->create();
		
		return touch($this->path());
	}
	
	
	/**
	 * Get the end of a text file.
	 * 
	 * @param int $lines Number of lines
	 * @return string
	 */
	public function tail($lines=20) {
    //global $fsize;
    $handle = fopen($this->path(), "r");
    $linecounter = $lines;
    $pos = -2;
    $beginning = false;
    $text = array();
    while ($linecounter > 0) {
        $t = " ";
        while ($t != "\n") {
            if(fseek($handle, $pos, SEEK_END) == -1) {
                $beginning = true;
                break;
            }
            $t = fgetc($handle);
            $pos --;
        }
        $linecounter --;
        if ($beginning) {
            rewind($handle);
        }
        $text[$lines-$linecounter-1] = fgets($handle);
        if ($beginning) break;
    }
    fclose ($handle);
    return implode("",array_reverse($text));
}
}