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
require_once($GO_LANGUAGE->get_language_file('opentts'));

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('opentts');

//set the page title for the header file
$page_title = "Opentts";

require_once($GO_THEME->theme_path."header.inc");
$tts= new db();
require_once("classes.php");


require_once("menu.php");
$tabtable = new tabtable('newticket_tabtable', $helpdesk_title_queries , '100%', '400');
$tabtable->print_head();
if (Security::is_action_allowed("query_search")){
$strtosearch=isset($_POST['strtosearch']) ? $_POST['strtosearch'] : '';
if (isset($_SESSION['show_hidden'])){
		$show_hidden=$_SESSION['show_hidden'];
}
echo Search::show_search();
	if (isset($_POST['action'])){
		$submit=$_POST['action'];
	}
	if (isset($submit)) {
		$search_status=$_POST['search_status'];
		echo Search::printdb();
	}
}
$tabtable->print_foot();
