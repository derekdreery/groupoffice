<?php

if(!isset($GO_CONFIG)){
	chdir(dirname(__FILE__));
	require('../../Group-Office.php');
}

if(!class_exists('GO', false)){
	require($GO_CONFIG->root_path.'GO.php');
}

GO::session()->values['user_id']=1;

$ab = GO_Addressbook_Model_Addressbook::model()->findSingleByAttribute('name', GO::t('users','users'));
if(!$ab){
	$ab = new GO_Addressbook_Model_Addressbook();
	$ab->name='users';
	$ab->users=true;
	$ab->save();
}

$db = GO::getDbConnection();

$stmt = $db->query("SELECT * FROM go_users");
while($user=$stmt->fetch()){
	$c = new GO_Addressbook_Model_Contact();
	$c->setAttributes($user);
	$c->addressbook_id=$ab->id;
	$c->save();
}