<?php

require_once("../../www/Group-Office.php");
require_once(GO::language()->get_language_file('users'));

require_once(GO::config()->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

for($i=1000;$i<20000;$i++){

	$user=array();
	$user['first_name']='user';
	$user['last_name']=$i;
	$user['username']='user'.$i;
	$user['password']='user'.$i;
	$user['email']='user'.$i.'@intermesh.dev';

	try{
	$user_id = $GO_USERS->add_user($user);
	}catch(Exception $e){

	}


}
