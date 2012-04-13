<?php
//Uncomment this line in new translations!
//require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name'] = 'Users';
$lang['users']['description'] = 'Admin module. Managing system users.';

$lang['users']['deletePrimaryAdmin'] = 'You can\'t delete the primary administrator';
$lang['users']['deleteYourself'] = 'You can\'t delete yourself';

$lang['link_type'][8]=$us_user = 'User';

$lang['users']['error_username']='You have invalid characters in the username';
$lang['users']['error_username_exists']='Sorry, that username already exists';
$lang['users']['error_email_exists']='Sorry, that e-mail address is already registered here.';
$lang['users']['error_match_pass']='The passwords didn\'t match';
$lang['users']['error_email']='You entered an invalid e-mail address';
$lang['users']['error_user']='User could not be created';

$lang['users']['imported']='Imported %s users';
$lang['users']['failed']='Failed';

$lang['users']['incorrectFormat']='File was not in correct CSV format';

$lang['users']['register_email_subject']='Your {product_name} account details';
$lang['users']['register_email_body']='A {product_name} account has been created for you at {url}
Your login details are:

Username: {username}
Password: {password}';


$lang['users']['max_users_reached']='The maximum number of users has been reached for this system.';