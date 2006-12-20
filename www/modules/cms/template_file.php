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

if ($file = $cms->get_template_file($_REQUEST['template_file_id']))
{
  $browser = detect_browser();

  //header('Content-Length: '.$file['size']);
  header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
  header('Content-Type: '.mime_content_type_by_extension(get_extension($file['name'])));
  if ($browser['name'] == 'MSIE')
  {    
    header('Content-Disposition: inline; filename="'.$file['name'].'"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
  }else
  {
    header('Pragma: no-cache');
    header('Content-Disposition: inline; filename="'.$file['name'].'"');
  }
  header('Content-Transfer-Encoding: binary');
  echo $file['content']; 
  exit();
}
