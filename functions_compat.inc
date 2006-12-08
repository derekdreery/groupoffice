<?php
/**
 * @copyright Intermesh 2004
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.6 $ $Date: 2006/10/27 08:58:19 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
 /**
 * This file holds custom functions that don't exist in older versions of PHP to
 * ensure compatibility.
 *
 * @package Framework
 * @author   Merijn Schering <mschering@intermesh.nl>
 * @since    Group-Office 2.12
 */
 
if (version_compare(phpversion(), '5.0') < 0) {
        eval('
                        function php4_clone($object) {
                        return $object;
                        }
                        ');
}else {
        eval('
                        function php4_clone($object) {
                        return clone $object;
                        }
                        ');
}

if(!function_exists('file_get_contents')) {
   function file_get_contents($file) {
       $file = file($file);
       return !$file ? false : implode('', $file);
   }
}
 
 
if(!function_exists('html_entity_decode'))
{
	function html_entity_decode ($string , $quote_style=ENT_QUOTES ,$charset='ISO-8859-1')
	{
	   $trans_tbl = get_html_translation_table(HTML_ENTITIES, $quote_style);
	   $trans_tbl = array_flip($trans_tbl);
	   return strtr($string, $trans_tbl);
	}
}

if (!function_exists('str_split')) {
  function str_split($string, $split_length = 1) {
    if (!is_numeric($split_length)) {
      trigger_error('str_split() expects parameter 2 to be long, '.gettype($split_length).' given', E_USER_WARNING);
      return false;
    }

    if ($split_length < 1) {
      trigger_error('str_split() The the length of each segment must be greater then zero', E_USER_WARNING);
      return false;
    }

    preg_match_all('/.{1,'.$split_length.'}/s', $string, $matches);
    return $matches[0];
  }
}


if (!function_exists('mime_content_type')) 
{
	function mime_content_type($path) {
			return mime_content_type_by_extension(get_extension($path));
	}
}
?>
