<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * Used for compressing the large Javascript and CSS
 */
require('Group-Office.php');
session_write_close();

$file = $GLOBALS['GO_CONFIG']->file_storage_path.'cache/'.basename($_REQUEST['file']);

$ext = File::get_extension($file);

$type = $ext =='js' ? 'text/javascript' : 'text/css';

$use_compression = $GLOBALS['GO_CONFIG']->use_zlib_compression();

if($use_compression){
	ob_start();
	ob_start('ob_gzhandler');
}
$offset = 30*24*60*60;
header ("Content-Type: $type; charset: UTF-8");
header("Expires: " . date("D, j M Y G:i:s ", time()+$offset) . 'GMT');
header('Cache-Control: cache');
header('Pragma: cache');
if(!$use_compression){
	header("Content-Length: ".filesize($file));
}
readfile($file);

if($use_compression){
	ob_end_flush();  // The ob_gzhandler one

	header("Content-Length: ".ob_get_length());

	ob_end_flush();  // The main one
}