<?php
require_once($GLOBALS['GO_CONFIG']->root_path.'install/updatescripts/functions.inc.php');

$tpl = $GLOBALS['GO_CONFIG']->get_setting('calendar_name_template');
if(!$tpl)
	$GLOBALS['GO_CONFIG']->save_setting('calendar_name_template', '{first_name} {middle_name} {last_name}');
?>