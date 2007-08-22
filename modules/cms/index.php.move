<?php
/*
Copyright Intermesh 2004
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 10  May 2004

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.

Put this file in the document root of your server and all Group-Office configured domains
will automatically work.
*/

//load Group-Office (Change the path if necessary)
require('groupoffice/Group-Office.php');

//get the cms module
if ($cms_module = $GO_MODULES->get_module('cms'))
{
 require($cms_module['path'].'view.inc');
}else
{
 die('<b>Fatal error:</b> Failed to get Content Management Module');
}
?>