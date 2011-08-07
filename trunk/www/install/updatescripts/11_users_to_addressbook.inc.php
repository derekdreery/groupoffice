<?php

if(!isset($GO_CONFIG)){
	chdir(dirname(__FILE__));
	require('../../Group-Office.php');
}

if(!class_exists('GO', false)){
	require($GO_CONFIG->root_path.'GO.php');
}

//TODO find a better way
GO::session()->values['user_id']=1;

$ab = GO_Addressbook_Model_Addressbook::model()->findSingleByAttribute('name', GO::t('users','base'));
if(!$ab){
	$ab = new GO_Addressbook_Model_Addressbook();
	$ab->name=GO::t('users','base');
	$ab->users=true;
	$ab->save();


	$db = GO::getDbConnection();

	$stmt = $db->query("SELECT * FROM go_users");
	while($user=$stmt->fetch()){
		$c = new GO_Addressbook_Model_Contact();
		$c->go_user_id=$user['id'];
		unset($user['id']);
		$c->setAttributes($user);
		$c->addressbook_id=$ab->id;
		$c->save();
		
		//todo copy adminusers files to contact
	}
	
	
	
	$db->query("ALTER TABLE `go_users`
  DROP `initials`,
  DROP `title`,
  DROP `sex`,
  DROP `birthday`,
  DROP `company`,
  DROP `department`,
  DROP `function`,
  DROP `home_phone`,
  DROP `work_phone`,
  DROP `fax`,
  DROP `cellular`,
  DROP `country`,
  DROP `state`,
  DROP `city`,
  DROP `zip`,
  DROP `address`,
  DROP `address_no`,
  DROP `homepage`,
  DROP `work_address`,
  DROP `work_address_no`,
  DROP `work_zip`,
  DROP `work_country`,
  DROP `work_state`,
  DROP `work_city`,
  DROP `work_fax`,
  DROP `contact_id`;");
}

