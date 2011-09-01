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
	public function getContents(){
		return file_get_contents($this->path);
	}
	
	
	/**
	 * Returns the mime type for the file.
	 * If it can't detect it it will return application/octet-stream
	 * 
	 * @return String 
	 */
	public static function mimeType()
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

			$start_of_line = String::rstrpos($types, "\n", $pos);
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
}