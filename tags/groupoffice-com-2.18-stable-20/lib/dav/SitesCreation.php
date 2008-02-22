#!/usr/bin/php
<?php
/*
   Copyright Intermesh 2003
   Author: Michael Borko <michael.borko@tgm.ac.at>
   Version: 1.0 Release date: 28 April 2004

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */

//Apache-Log-Input recognition from STDINPUT
$filename = $argv[1];

//Init of all necessary globals
require_once("/var/www/groupoffice/Group-Office.php");
global $GO_DAV, $GO_CONFIG;
$db = new db();

$namelist = $GO_DAV->file_get_contents($filename);
$namelist = explode( "\n", $namelist);
array_pop($namelist);
$namelist = array_unique($namelist);

foreach( $namelist as $user )
  $GO_DAV->check_login($user);
