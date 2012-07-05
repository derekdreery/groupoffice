<?php
global $GO_SECURITY, $GO_CONFIG, $GO_MODULES;
$GLOBALS['GO_SECURITY']->add_group_to_acl($GLOBALS['GO_CONFIG']->group_everyone, $GLOBALS['GO_MODULES']->modules['freebusypermissions']['acl_id']);