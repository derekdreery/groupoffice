<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

require_once("../Group-Office.php");
//load file management class
$GO_SECURITY->authenticate();

require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();

$path = $GO_CONFIG->file_storage_path.'userlogs/'.$GO_SECURITY->user_id.'/'.smart_stripslashes($_REQUEST['log']);

$filename = utf8_basename($path);

header('Content-Length: '.filesize($path));
header('Content-Type: text/plain');
header('Content-Disposition: inline; filename="'.$filename.'"');

echo file_get_contents($path);
