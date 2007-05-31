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

if ($template = $cms->get_template($_REQUEST['template_id']))
{
  header('Content-Type: text/css');
  #header('Content-Length: '.strlen($template['style']));
  header("Cache-Control: max-age=2592000");
  header('Content-Disposition: inline; filename=stylesheet.css');
  if(isset($_REQUEST['print']))
  {
  	echo $template['print_style'];
  }elseif(isset($_REQUEST['editor']))
  {
  	echo $template['additional_style'];
  	?>
@media all { 
cms\:plugin{
border:1px dashed red;
display:block;
padding:5px;
color: red;
background-color:#f1f1f1;
}
}

embed{
border:1px dashed red;
display:block;
padding:5px;
color: red;
background-color:#f1f1f1;
}
  	<?php
  }else
  {
  	echo $template['style'];
  }  
}
