<?php
require_once($GO_CONFIG->root_path.'install/updatescripts/functions.inc.php');

$tpl = $GO_CONFIG->get_setting('calendar_name_template');
if(!$tpl)
	$GO_CONFIG->save_setting('calendar_name_template', '{first_name} {middle_name} {last_name}');
?>