<?php
require('Group-Office.php');
load_basic_controls();
require($GO_LANGUAGE->get_base_language_file('login'));
require_once($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login_header.inc');

$p = new html_element('p', $login_fullscreen);
echo $p->get_html();

$button = new button($login_start, "openPopup('groupoffice', '".$GO_CONFIG->host."','','');", '200px');

echo $button->get_html();

require_once($GO_CONFIG->root_path.'login_screens/'.$GO_CONFIG->login_screen.'/login_footer.inc');
?>
