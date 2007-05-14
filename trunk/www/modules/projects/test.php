<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.8 $ $Date: 2006/11/22 09:35:41 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 

require_once("../../Group-Office.php");

load_basic_controls();
load_control('date_picker');

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('projects', true);
require_once($GO_LANGUAGE->get_language_file('projects'));

require_once($GO_MODULES->class_path."projects.class.inc");
$projects = new projects();

$totals = $projects->get_total_hours(225);

var_dump($totals);