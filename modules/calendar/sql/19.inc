<?php
require_once($GO_CONFIG->root_path.'modules/calendar/classes/calendar.class.inc');
$cal = new calendar();
$db1 = new db();

$group['name'] = 'Calendars';
$group['acl_write'] = $GO_SECURITY->get_new_acl();
$cal->add_group($group);
?>
