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
require_once("../../Group-Office.php");
$cms_module = $GO_MODULES->get_module('cms');
require_once($cms_module['class_path'].'cms.class.inc');
$cms = new cms();

header('Content-Type: text/xml');
header("Cache-Control: max-age=2592000");

$template = $cms->get_template($_REQUEST['template_id']);
if ($template && !empty($template['fckeditor_styles']))
{
  echo $template['fckeditor_styles'];
}else {
	echo '<?xml version="1.0" ?>'."\n";
	echo '<styles></styles>';
}
