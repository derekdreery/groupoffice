<?php

$module = $this->get_module('customfields');
require_once($module['class_path'].'customfields.class.inc.php');
$cf = new customfields();

$cf->delete_link_type(1);