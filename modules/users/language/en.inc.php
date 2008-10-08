<?php
//Uncomment this line in new translations!
//require_once($GO_LANGUAGE->get_fallback_language_file('users'));
$lang['users']['name'] = 'Users';
$lang['users']['description'] = 'Admin module; Managing system users.';

$lang['users']['deletePrimaryAdmin'] = 'You can\t delete the primary administrator';
$lang['users']['deleteYourself'] = 'You can\'t delete yourself';

$link_type[8]=$us_user = 'User';

$lang['users']['error_username']='You have invalid characters in the username';
$lang['users']['error_username_exists']='Sorry, that username already exists';
$lang['users']['error_email_exists']='Sorry, that e-mail address is already registered here. You can use the forgotten password feature to recover your password.';
$lang['users']['error_match_pass']='The passwords didn\'t match';
$lang['users']['error_email']='You entered an invalid e-mail address';