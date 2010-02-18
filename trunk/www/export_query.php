<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Date.class.inc.php 3589 2009-11-05 13:02:37Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require('Group-Office.php');
require_once($GO_CONFIG->class_path.'export/export_query.class.inc.php');

$type = $_REQUEST['type'];
$filename = $type.'.class.inc.php';

$file = $GO_CONFIG->class_path.'export/'.$filename;
if(!file_exists($file)){
	$file = $GO_CONFIG->file_storage_path.'customexports/'.$filename;
}
if(!file_exists($file)){
	die('Custom export class not found.');
}
require_once($file);

$eq = new $type();
$eq->download_headers();

$fp = fopen('php://output','w');
$eq->export($fp);
fclose($fp);
?>