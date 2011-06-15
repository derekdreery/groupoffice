<?php
global $GO_SECURITY, $GO_MODULES;
GO::security()->add_group_to_acl(GO::config()->group_everyone,GO::modules()->modules['search']['acl_id']);