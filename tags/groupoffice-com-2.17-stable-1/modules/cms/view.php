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
//get the site properties and remember the site id
if(isset($_REQUEST['site_id']))
{
	$_SESSION['site_id'] = $_REQUEST['site_id'];
}else
{
	$_REQUEST['site_id'] = $_SESSION['site_id'];
}


if(!isset($_SERVER['HTTP_REFERER']) || !eregi('cms', $_SERVER['HTTP_REFERER']))
{
	//Hey someone came from another place the the GO CMS. Try to redirect him to the
	//real page
	$cms_site_module = $GO_MODULES->get_module('cms');
	require_once($cms_site_module['class_path'].'cms.class.inc');
	$cms = new cms();
	if($site = $cms->get_site($_SESSION['site_id']))
	{
		header('Location: http://'.$site['domain']);
	}else
	{
		header("HTTP/1.0 404 Not Found");
	}
	exit();
}

require_once('view.inc');
