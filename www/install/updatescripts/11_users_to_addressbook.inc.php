<?php
$ab = GO_Addressbook_Model_Addressbook::model()->findSingleByAttribute('users', '1');//GO::t('users','base'));
if(!$ab){
	
	$ab = new GO_Addressbook_Model_Addressbook();
	$ab->name=GO::t('users');
	$ab->users=true;
	$ab->save();

	$pdo = GO::getDbConnection();

	$pdo->query("INSERT INTO ab_contacts (`addressbook_id`,`first_name`, `middle_name`, `last_name`, `initials`, `title`, `sex`, `birthday`, `email`, `department`, `function`, `home_phone`, `work_phone`, `fax`, `cellular`, `country`, `state`, `city`, `zip`, `address`, `address_no`,`go_user_id`) SELECT {$ab->id},`first_name`, `middle_name`, `last_name`, `initials`, `title`, `sex`, `birthday`, `email`, `department`, `function`, `home_phone`, `work_phone`, `fax`, `cellular`, `country`, `state`, `city`, `zip`, `address`, `address_no`,`id`  FROM `go_users` ");

}

