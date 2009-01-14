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

/**
 * This file contains functions for file operations.
 *
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package go.utils
 * @since Group-Office 3.0
 */

class File
{
	var $path;
	

	function __construct($path){
		$this->path = $path;
	}
	function get_directory_size($dir)
	{
		$cmd = 'du -sk "'.$dir.'" 2>/dev/null';

		$io = popen ($cmd, 'r' );
		$size = fgets ( $io, 4096);

		$size = preg_replace('/[\t\s]+/', ' ', trim($size));


		$size = substr ( $size, 0, strpos ( $size, ' ' ) );
		//debug($cmd.' Size: '.$size);

		return $size;
	}
	
	function strip_invalid_chars($filename){
		return trim(preg_replace('/[&\/:\*\?"<>|\\\]/','', $filename));
	}

	function get_filetype_image($extension=null) {

		if(!isset($extension))
		{
			$extension = $this->get_extension();
		}

		global $GO_THEME;

		if (isset ($GO_THEME->filetypes[$extension])) {
			return $GO_THEME->filetypes[$extension];
		} else {
			return $GO_THEME->filetypes['unknown'];
		}
	}


	function get_filetype_description_by_path($path)
	{
		return File::get_filetype_description(File::get_extension($path));
	}


	function get_filetype_description($extension=null) {
		global $lang, $GO_LANGUAGE;
		
		require_once($GO_LANGUAGE->get_base_language_file('filetypes'));

		if(!isset($extension))
		{
			$extension = $this->get_extension();
		}

		if (isset ($lang['filetypes'][$extension])) {
			return $lang['filetypes'][$extension];
		} else {
			return $lang['filetypes']['unknown'];
		}
	}
	

	
	function get_mime($path)
	{
		global $GO_CONFIG;
		
		$types = file_get_contents($GO_CONFIG->root_path.'mime.types');
		
		$extension = File::get_extension($path);
		
		if(empty($extension))
		{
			return 'application/octet-stream';
		}
		
		$pos = strpos($types, ' '.$extension);
		
		if($pos)
		{
			$pos++;
			
			$start_of_line = String::rstrpos($types, "\n", $pos);			
			$end_of_mime = strpos($types, ' ', $start_of_line);
			$mime = substr($types, $start_of_line+1, $end_of_mime-$start_of_line-1);
			
			return $mime;		
		}	
		
		if(function_exists('finfo_open')){
        $finfo    = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $filename);
        finfo_close($finfo);
        return $mimetype;
    }elseif(function_exists('mime_content_type'))
    {
    	return mime_content_type($path);		
    }else
    {
    	return 'application/octet-stream';
    }		
	}


	/**
	 * Return a filename without extension
	 *
	 * @param	string $filename The complete filename
	 * @access public
	 * @return string A filename without the extension
	 */
	function strip_extension($filename=null) {

		if(!isset($filename))
		{
			$filename = utf8_basename($this->path);
		}


		$pos = strrpos($filename, '.');
		if ($pos) {
			$filename = substr($filename, 0, $pos);
		}
		return $filename;
	}

	/**
	 * Returns the extension of a filename
	 *
	 * @param	string $filename The complete filename
	 * @access public
	 * @return string  The extension of a filename
	 */
	function get_extension($filename=null) {

		if(!isset($filename))
		{
			$filename = utf8_basename($this->path);
		}

		$extension = '';
		$pos = strrpos($filename, '.');
		if ($pos) {
			$extension = substr($filename, $pos +1, strlen($filename));
		}
		return strtolower($extension);
	}

	/**
	 * Checks if a filename exists and renames it.
	 *
	 * @param	string $filepath The complete path to the file
	 * @access public
	 * @return string  New filepath
	 */
	function checkfilename($filepath)
	{
		$dir = dirname($filepath).'/';
		$name = utf8_basename($filepath);
		$x=1;
		while(file_exists($filepath))
		{
			$extension = File::get_extension($name);
			$newname = File::strip_extension($name);
			$filepath = $dir.$newname.'_'.$x.'.'.$extension;
			$x++;
		}

		return $filepath;
	}


}
