<?php
$root = dirname(__FILE__).'/';
require_once($root.'GO/GO.php');
GO::init();

$router = new GO_Base_Router();
$router->run();