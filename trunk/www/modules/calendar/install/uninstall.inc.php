<?php

global $GO_MODULES;
require_once ($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');

$cf = new customfields();

$cf->delete_link_type(1);