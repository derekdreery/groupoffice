<?php
global $GO_SECURITY, $GO_MODULES;
$GLOBALS['GO_SECURITY']->add_group_to_acl($GLOBALS['GO_CONFIG']->group_everyone,$GLOBALS['GO_MODULES']->modules['search']['acl_id']);