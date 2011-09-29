<?php
$root = dirname(__FILE__).'/';
require_once($root.'go/GO.php');
GO::init();

$router = new GO_Base_Router();
$router->runController();