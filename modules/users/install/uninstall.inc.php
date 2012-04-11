<?php

global $GO_MODULES;

if(isset($GLOBALS['GO_MODULES']->modules['customfields']))
{
    require_once ($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');

    $cf = new customfields();

    $cf->delete_link_type(8);
}