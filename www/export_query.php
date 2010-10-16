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

require('Group-Office.php');
require_once($GO_CONFIG->class_path.'export/export_query.class.inc.php');


//close writing to session so other concurrent requests won't be locked out.
session_write_close();

$type = basename($_REQUEST['type']);

if(strpos($_SERVER['QUERY_STRING'], '<script') || strpos(urldecode($_SERVER['QUERY_STRING']), '<script'))
				die('Invalid request');

$filename = $type.'.class.inc.php';

//$GO_CONFIG->root_path.$_REQUEST['export_directory'].$filename;

if(isset($_REQUEST['export_directory']) && file_exists($GO_CONFIG->root_path.$_REQUEST['export_directory'].$filename)){
	$file = $GO_CONFIG->root_path.$_REQUEST['export_directory'].$filename;
}else
{
	$file = $GO_CONFIG->class_path.'export/'.$filename;
	if(!file_exists($file)){
		$file = $GO_CONFIG->file_storage_path.'customexports/'.$filename;
	}
	if(!file_exists($file)){
		die('Custom export class not found.');
	}
}

require_once($file);


$eq = new $type();
$eq->download_headers();

$fp = fopen('php://output','w');
$eq->export($fp);
fclose($fp);
?>