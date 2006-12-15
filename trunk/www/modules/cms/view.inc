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

$cms_site_module = $GO_MODULES->get_module('cms');

require_once($cms_site_module['class_path'].'cms.class.inc');
require_once($cms_site_module['class_path'].'cms_site.class.inc');

require_once($GO_LANGUAGE->get_language_file('cms'));

if(!$cms_site = new cms_site())
{
  die('No site or invalid site requested');
}

header('Content-Type: text/html; charset='.$charset);
echo $cms_site->generate_page();
?>
