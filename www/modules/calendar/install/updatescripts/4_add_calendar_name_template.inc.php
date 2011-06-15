<?php
require_once(GO::config()->root_path.'install/updatescripts/functions.inc.php');

$tpl = GO::config()->get_setting('calendar_name_template');
if(!$tpl)
	GO::config()->save_setting('calendar_name_template', '{first_name} {middle_name} {last_name}');
?>