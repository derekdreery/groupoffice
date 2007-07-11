<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 28 Februari 2005

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

require_once('../Group-Office.php');

require($argv[1]);

$CONFIG_FILE = $GO_CONFIG->get_config_file();

require_once('install.inc');

foreach($config as $key=>$value)
{
	$GO_CONFIG->$key=$value;
}

save_config($GO_CONFIG);