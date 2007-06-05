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
$GO_MODULES->authenticate('email');
load_basic_controls();
require_once($GO_LANGUAGE->get_language_file('email'));

$post_action = isset($_REQUEST['post_action']) ? $_REQUEST['post_action'] : '';
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? htmlspecialchars($_REQUEST['link_back']) : $_SERVER['REQUEST_URI'];


require($GO_THEME->theme_path.'page_header.inc');


$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src',$em_js_lang);
$head->add_html_element($script);


$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src','email.js');
$head->add_html_element($script);

$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src','../../links.js');
$head->add_html_element($script);

$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src','../../common.js');
$head->add_html_element($script);

$eastdiv = new html_element('div');
$eastdiv->set_attribute('id','east');


$centerdiv = new html_element('div');
$centerdiv->set_attribute('id','center');

$toolbar = new html_element('div');
$toolbar->set_attribute('id','emailtb');
$centerdiv->add_html_element($toolbar);

$emaildiv = new html_element('div');
$emaildiv->set_attribute('id', 'email-grid');
$centerdiv->add_html_element($emaildiv);

$body->add_html_element($eastdiv);
$body->add_html_element($centerdiv);


require($GO_THEME->theme_path.'page_footer.inc');
