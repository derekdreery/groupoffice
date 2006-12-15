<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Save the database of a PHPNuke web site                              */
/*                                                                      */
/* Copyright (c) 2001 by Thomas Rudant (thomas.rudant@grunk.net)        */
/* http://www.grunk.net                                                 */
/* http://www.securite-internet.org                                     */
/*									*/
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
require_once("../../Group-Office.php");

//authenticate the user
//if $GO_SECURITY->authenticate(true); is used the user needs admin permissons

$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('opentts');

//set the page title for the header file
$page_title = "Opentts";

require_once($GO_THEME->theme_path."header.inc");

$tts= new db();

require_once("classes.php");
$db_host = $tts->db_host;
$db_name = $tts->db_name;
$db_user = $tts->db_user;
$db_pass = $tts->db_pass;

if (Security::is_action_allowed("db_backup")){
$pre= "{$prefix}{$hlpdsk_prefix}";
                $tables=array ("{$pre}_activities",
                "{$pre}_categories","{$pre}_colors_tables",
                "{$pre}_config","{$pre}_groups",
                "{$pre}_lang","{$pre}_menu",
                "{$pre}_permissions","{$pre}_priorities",
                "{$pre}_status","{$pre}_tasks",
                "{$pre}_activities","{$pre}_tickets",
		"{$pre}_stages",
		"{$pre}_groups_members","{$pre}_projects");
$tables=join(" ",$tables);
if ($dbpass){
	$sqldump="mysqldump -h$db_host  -u$db_user -p$db_pass $db_name $tables";
}else{
	 $sqldump="mysqldump -h$db_host  -u$db_user  $db_name $tables";
}
exec("$sqldump > opentts{$hlpdsk_prefix}.dump");
$tarexec="tar --exclude snapshots CVS -chvlzf modules/$name/snapshots/$name$hlpdsk_prefix.$tts_version.tarz -C modules/ $name";
exec($tarexec);
echo "snapshot <a href=\"snapshots/$name$hlpdsk_prefix.$tts_version.tarz\">$name$hlpdsk_prefix.$tts_version.tarz</a> done!";
}else{
echo "ups";
}

?>
