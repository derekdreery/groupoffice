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
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('notes');
load_basic_controls();
require_once($GO_LANGUAGE->get_language_file('notes'));

$post_action = isset($_REQUEST['post_action']) ? $_REQUEST['post_action'] : '';
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? htmlspecialchars($_REQUEST['link_back']) : $_SERVER['REQUEST_URI'];

//load contact management class
require_once($GO_MODULES->class_path."notes.class.inc");
$notes = new notes();

require_once($GO_MODULES->modules['notes']['class_path'].'notes_list.class.inc');
$nl = new notes_list('notes_list', $GO_SECURITY->user_id, false, true, 'notes_form', $GO_MODULES->modules['notes']['url']);

$GO_HEADER['head'] = $nl->get_header();
require_once($GO_THEME->theme_path."header.inc");



$form = new form('notes_form');

$menu = new button_menu();
$menu->add_button('notes', $cmdAdd, $GO_MODULES->modules['notes']['url'].'note.php');
$menu->add_button('delete_big', $cmdDelete, $nl->get_delete_handler());
//$menu->add_button('delete_big', $cmdDelete, "javascript:alert('  test' );"  );

$form->add_html_element($menu);

$form->add_html_element($nl);

echo $form->get_html();
require_once($GO_THEME->theme_path."footer.inc");
?>
