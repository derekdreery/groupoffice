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
$GO_MODULES->authenticate('newnotes');
load_basic_controls();
require_once($GO_LANGUAGE->get_language_file('newnotes'));

$post_action = isset($_REQUEST['post_action']) ? $_REQUEST['post_action'] : '';
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? htmlspecialchars($_REQUEST['link_back']) : $_SERVER['REQUEST_URI'];

//load contact management class
require_once($GO_MODULES->class_path."notes.class.inc");
$notes = new notes();

//require_once($GO_MODULES->modules['notes']['class_path'].'notes_list.class.inc');
//$nl = new notes_list('notes_list', $GO_SECURITY->user_id, false, true, 'notes_form', $GO_MODULES->modules['notes']['url']);

require($GO_THEME->theme_path.'header2.inc');



$link = new html_element('link');
$link->set_attribute('rel','stylesheet');
$link->set_attribute('type','text/css');
$link->set_attribute('href',$GO_CONFIG->host.'ext/resources/css/ext-all.css');
$head->add_html_element($link);


$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src',$GO_CONFIG->host.'ext/adapter/yui/yui-utilities.js');
$head->add_html_element($script);

$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src',$GO_CONFIG->host.'ext/adapter/yui/ext-yui-adapter.js');
$head->add_html_element($script);

$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src',$GO_CONFIG->host.'ext/ext-all.js');
$head->add_html_element($script);

$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src','notes.js');
$head->add_html_element($script);





$containerdiv = new html_element('div');
$containerdiv->set_attribute('id','sum-container');

$southdiv = new html_element('div');
$southdiv->set_attribute('id','no-south');


$centerdiv = new html_element('div');
$centerdiv->set_attribute('id','no-center');
$centerdiv->set_attribute('style', 'width:100%;height:100%');

$toolbar = new html_element('div');
$toolbar->set_attribute('id','toolbar');
$containerdiv->add_html_element($toolbar);

$notesdiv = new html_element('div');
$notesdiv->set_attribute('id', 'notes-grid');
$notesdiv->set_attribute('style', 'width:100%;height:100%');

//$centerdiv->add_html_element($notesdiv);



$containerdiv->add_html_element($centerdiv);

$containerdiv->add_html_element($southdiv);

//echo $containerdiv->get_html();
$body->add_html_element($containerdiv);





require($GO_THEME->theme_path.'footer2.inc');
