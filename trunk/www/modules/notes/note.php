<?php
/**
 * @copyright Copyright Intermesh 2007
 * @version $Revision: 1.47 $ $Date: 2006/11/21 16:25:40 $
 * 
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
      
 * @package Notes
 * @category Notes
 */
require_once("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('notes');
require_once($GO_LANGUAGE->get_language_file('notes'));

load_basic_controls();

$page_title=$lang_modules['notes'];
require_once($GO_MODULES->class_path."notes.class.inc");
$notes = new notes();

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$note_id = isset($_REQUEST['note_id']) ? $_REQUEST['note_id'] : 0;

$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = isset($_REQUEST['link_back']) ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];

//creates the basic html and loads default controls
//require($GO_THEME->theme_path.'page_header.inc');



//add html to body that comes from page header
$container_div = new html_element('div');
$container_div->set_attribute('id','container');

$toolbar_div = new html_element('div');
$toolbar_div->set_attribute('id','toolbar');

$note_form_div = new html_element('div');
$note_form_div->set_attribute('id','note_form');

$container_div->add_html_element($toolbar_div);
$container_div->add_html_element($note_form_div);

echo $container_div->get_html();

//$body->add_html_element($container_div);


//finish html document and output to the browser
//require($GO_THEME->theme_path.'page_footer.inc');
