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


$site = $cms->get_site($_REQUEST['site_id']);
if ($site['acl_read'] > 0)
{
  $GO_SECURITY->authenticate();
}

if ($site['acl_read'] == 0 || $GO_SECURITY->has_permission($GO_SECURITY->user_id, $site['acl_read']) || $GO_SECURITY->has_permission($GO_SECURITY->user_id, $site['acl_write']))
{
  if ($file = $cms->get_file($_REQUEST['file_id']))
  {
    $browser = detect_browser();

    //header('Content-Length: '.$file['size']);
    header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
    if ($browser['name'] == 'MSIE')
    {
      header('Content-Type: '.mime_content_type_by_extension($file['extension']));
      header('Content-Disposition: inline; filename="'.$file['name'].'"');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
    }else
    {
      header('Content-Type: '.mime_content_type_by_extension($file['extension']));
      header('Pragma: no-cache');
      header('Content-Disposition: inline; filename="'.$file['name'].'"');
    }
    header('Content-Transfer-Encoding: binary');
    echo $file['content'];
  }
}else
{
  header('Location: '.$GO_CONFIG->host.'error_docs/401.php');
  exit();
}
