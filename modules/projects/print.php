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

load_basic_controls();

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('projects');
require_once($GO_LANGUAGE->get_language_file('projects'));

$page_title=$menu_projects;
require_once($GO_MODULES->class_path."projects.class.inc");
$projects = new projects();

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

//check for the addressbook module
$ab_module = isset ($GO_MODULES->modules['addressbook']) ? $GO_MODULES->modules['addressbook'] : false;
if ($ab_module && $ab_module['read_permission']) {
	require_once ($ab_module['class_path'].'addressbook.class.inc');
	$ab = new addressbook();
} else {
	$ab_module = false;
}

require_once($GO_THEME->theme_path."header.inc");
$container = new form('print_form');
require_once("load.inc");
echo $container->get_html();
echo "\n<script type=\"text/javascript\">\nwindow.print();\n</script>\n";
require_once($GO_THEME->theme_path."footer.inc");
