<?php

global $GO_MODULES;

if(isset(GO::modules()->modules['customfields']))
{
    require_once (GO::modules()->modules['customfields']['class_path'].'customfields.class.inc.php');

    $cf = new customfields();

    $cf->delete_link_type(2);
    $cf->delete_link_type(3);
}