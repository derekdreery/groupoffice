<?php
/************************************************************************/
/* TTS: Ticket tracking system                                          */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2002 by Meir Michanie                                  */
/* http://www.riunx.com                                                 */
/*                                                                      */
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
require_once($GO_LANGUAGE->get_language_file('opentts'));

//set the page title for the header file
$page_title = "Opentts";

require_once($GO_THEME->theme_path."header.inc");
$tts= new db();
require_once("classes.php");

require_once("menu.php");
$tabtable = new tabtable('stats_tabtable', $helpdesk_title_stat , '100%', '400');
$tabtable->print_head();
if(Security::is_action_allowed("admin")){

echo "<center><font class=content>". Opentts::status_stat()."</center>";
echo "<br>";
echo "<center><font class=content>". Opentts::cat_stat()."</center>";
echo "<br>";
echo "<center><font class=content>". Opentts::cat_status_stat()."</center>";
}
$tabtable->print_foot();
?>

	
