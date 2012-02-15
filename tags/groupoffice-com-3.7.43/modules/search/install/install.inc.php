<?php
global $GO_SECURITY, $GO_MODULES;
$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_everyone,$GO_MODULES->modules['search']['acl_id']);