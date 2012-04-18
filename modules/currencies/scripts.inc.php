<?php
require_once($GLOBALS['GO_MODULES']->modules['currencies']['class_path'].'currencies.class.inc.php');
$cu = new currencies();
$dc = $cu->get_default_currency();
if($dc){
	$GO_SCRIPTS_JS .= 'GO.currencies.defaultCurrency="'.$dc['code'].'";';
}

