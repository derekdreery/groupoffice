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


require($GO_THEME->theme_path.'page_header.inc');


$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src',$no_js_lang);
$head->add_html_element($script);


$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src','notes.js');
$head->add_html_element($script);


$containerdiv = new html_element('div');
$containerdiv->set_attribute('id','container');

$eastdiv = new html_element('div');
$eastdiv->set_attribute('id','no-east');

$centerdiv = new html_element('div');
$centerdiv->set_attribute('id','no-center');
$centerdiv->set_attribute('style', 'width:100%;height:100%');

$toolbar = new html_element('div');
$toolbar->set_attribute('id','notestb');
$containerdiv->add_html_element($toolbar);

$toolbar = new html_element('div');
$toolbar->set_attribute('id','notetb');
$eastdiv->add_html_element($toolbar);

$noteform = new html_element('div', 'bladiebla');
$noteform->set_attribute('id','noteform');
$eastdiv->add_html_element($noteform);


$notesdiv = new html_element('div');
$notesdiv->set_attribute('id', 'notes-grid');
$notesdiv->set_attribute('style', 'width:100%;height:100%');


$containerdiv->add_html_element($centerdiv);
$containerdiv->add_html_element($eastdiv);

$body->add_html_element($containerdiv);





require($GO_THEME->theme_path.'page_footer.inc');
