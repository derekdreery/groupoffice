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
 * 
 * @deprecated Move stuff into GO_Base_Fs_File
 */

class GO_Base_Util_File
{
	const INVALID_CHARS = '/[\/:\*\?"<>|\\\]/';

	public function __construct($path){
		$this->path = $path;
	}

	/**
	 * Creates a directory recursively
	 *
	 * @param	string $path
	 * @access public
	 * @return bool True on success
	 */

	public static function mkdir($path) {
	  global $GO_CONFIG;

	  if(!file_exists($path))
	  {
	  	if(mkdir($path, GO::config()->folder_create_mode, true)){
			if(!empty(GO::config()->file_change_group))
				chgrp($path, GO::config()->file_change_group);
			return true;
		}else
		{
			go_debug('Failed creating: '.$path);
			return false;
		}

	  }
	  return true;
	}

	public static function convert_to_utf8($path){
		$enc='UTF-8';
		$str = file_get_contents($path);
		if(!$str){
			return false;
		}
		if(function_exists('mb_detect_encoding'))
		{
			$enc = mb_detect_encoding($str, "ASCII,JIS,UTF-8,ISO-8859-1,ISO-8859-15,EUC-JP,SJIS");
		}
		return file_put_contents($path, GO_Base_Util_String::clean_utf8($str, $enc));
	}


	public static function is_full_path($path)
	{
		return ($path[0]=='/' || substr($path, 1, 2) == ':/' || substr($path, 1, 2) == ':\\');
	}

	public static function get_directory_size($dir)
	{
		$cmd = 'du -sk "'.$dir.'" 2>/dev/null';

		$io = popen ($cmd, 'r' );

		if($io){
			$size = fgets ( $io, 4096);
			$size = preg_replace('/[\t\s]+/', ' ', trim($size));

			$size = substr ( $size, 0, strpos ( $size, ' ' ) );
			//go_debug($cmd.' Size: '.$size);
		}else
		{
			return false;
		}

		return $size;
	}

	public static function has_invalid_chars($filename){
		return preg_match(GO_Base_Util_File::INVALID_CHARS, $filename);
	}

	public static function strip_invalid_chars($filename){
		$filename = trim(preg_replace(GO_Base_Util_File::INVALID_CHARS,'', $filename));

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

	public static function path_leads_to_parent($path){
		return strpos($path, '../') !== false || strpos($path, '..\\')!==false;
	}

	/*public static function get_filetype_image($extension=null) {

		if(!isset($extension))
		{
			$extension = $this->get_extension();
		}

		global $GO_THEME;

		if (isset ($GLOBALS['GO_THEME']->filetypes[$extension])) {
			return $GLOBALS['GO_THEME']->filetypes[$extension];
		} else {
			return $GLOBALS['GO_THEME']->filetypes['unknown'];
		}
	}*/


	public static function get_filetype_description_by_path($path)
	{
		return GO_Base_Util_File::get_filetype_description(GO_Base_Util_File::get_extension($path));
	}


	public static function get_filetype_description($extension) {
		global $lang, $GO_LANGUAGE;

		require_once($GLOBALS['GO_LANGUAGE']->get_base_language_file('filetypes'));

	

		if (isset ($lang['filetypes'][$extension])) {
			return $lang['filetypes'][$extension];
		} else {
			return $lang['filetypes']['unknown'];
		}
	}



	public static function get_mime($path)
	{
		global $GO_CONFIG;

		$types = file_get_contents(GO::config()->root_path.'mime.types');

		$extension = GO_Base_Util_File::get_extension($path);

		if(empty($extension))
		{
			return 'application/octet-stream';
		}

		$pos = strpos($types, ' '.$extension);

		if($pos)
		{
			$pos++;

			$start_of_line = GO_Base_Util_String::rstrpos($types, "\n", $pos);
			$end_of_mime = strpos($types, ' ', $start_of_line);
			$mime = substr($types, $start_of_line+1, $end_of_mime-$start_of_line-1);

			return $mime;
		}

		if(file_exists($path)){
			if(function_exists('finfo_open')){
					$finfo    = @finfo_open(FILEINFO_MIME);
					$mimetype = @finfo_file($finfo, $path);
					finfo_close($finfo);
					return $mimetype;
			}elseif(function_exists('mime_content_type'))
			{
				return @mime_content_type($path);
			}
		}
    
    return 'application/octet-stream';    
	}


	/**
	 * Return a filename without extension
	 *
	 * @param	string $filename The complete filename
	 * @access public
	 * @return string A filename without the extension
	 */
	public static function strip_extension($filename) {

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
	public static function get_extension($filename) {
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
	public static function checkfilename($filepath)
	{
		$dir = dirname($filepath).'/';
		$name = GO_Base_util_File::utf8Basename($filepath);
		$x=1;
		while(file_exists($filepath))
		{
			$extension = GO_Base_Util_File::get_extension($name);
			$newname = GO_Base_Util_File::strip_extension($name);
			$filepath = $dir.$newname.'_'.$x.'.'.$extension;
			$x++;
		}

		return $filepath;
	}
	
	
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



}
