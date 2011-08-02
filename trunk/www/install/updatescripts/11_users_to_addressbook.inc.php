<?php

if(!isset($GO_CONFIG)){
	chdir(dirname(__FILE__));
	require('../../Group-Office.php');
}

if(!class_exists('GO', false)){
	require($GO_CONFIG->root_path.'GO.php');
}


$ab = GO_Addressbook_Model_Addressbook::model()->findSingleByAttribute('name', 'users');
if(!$ab){
	$ab = new GO_Addressbook_Model_Addressbook();
	$ab->name='users';
	$ab->save();
}

$db = GO::getDbConnection();

$db->query("SELECT * FROM go_users");