<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('users'));

$strLogins = "Logins";
$strRegistrationDate = "Registration Date";
$strUsers = "ÐÏÌØÚÏ×ÁÔÅÌØ(É)";
$user_profile = "ðÒÏÆÉÌØ ÐÏÌØÚÏ×ÁÔÅÌÑ";
$ac_login_info = "Login information";
$ac_registration_time = "Registration time";
$ac_lastlogin = "ðÏÓÌÅÄÎÉÊ ×ÈÏÄ";
$ac_logins = "÷ÓÅÇÏ ×ÈÏÄÏ×";
$strMaxOf = "registered from the maximum of";
$strUsername = "éÍÑ ÐÏÌØÚÏ×ÁÔÅÌÑ";
$delete_fail = "You can't delete yourself and the superuser.";
$admin_new_user = "îÏ×ÙÊ ÐÏÌØÚÏ×ÁÔÅÌØ";

$admin_modules = 'íÏÄÕÌÉ';
$admin_module_access = 'This user has access to the following modules:';

$admin_password = 'ðÁÒÏÌØ';
$admin_confirm_password = 'ðÏÄÔ×ÅÒÖÄÅÎÉÅ ÐÁÒÏÌÑ';
$admin_change_password = 'éÚÍÅÎÅÎÉÅ ÐÁÒÏÌÑ';
$admin_pass_match = 'The passwords didn\'t match. Please try again.';

//registration
$registration_personal_info = "ðÅÒÓÏÎÁÌØÎÁÑ ÉÎÆÏÒÍÁÃÉÑ";
$registration_account_info = "éÎÆÏÒÍÁÃÉÑ ÕÞÅÔÎÏÊ ÚÁÐÉÓÉ";
$registartion_username_text = "Enter a username and password for this user to login to the site.";

$registration_question_text=<<<registration_question_text
Enter a secret question to which only this user knows the answer to. We will ask this question in case the user forgets his or her password.
When the correct answer is given, an e-mail will be send to the registered e-mail address with the username and a new password to log in.
registration_question_text;

$registration_text=<<<registration_text
Fill out this form and click on 'Ok' to register. The fields marked with a * are required.<br />
The information you provide is only for use at Group-Office so we will not provide them to third parties.
registration_text;

$registartion_authcode_text = "Enter an authorisation code. The authorisationcode is a code that other users will need to add this user to thier contactlist and view your details.";
$registartion_confirm_authcode = "Confirm authorisation code";
$registration_visibility = "Make this user visible to all users.";
$registration_success = "<h1>Congratulations!</h1>You are successfully registered and a welcome message has been sent to:";
$registration_failure = "An error occured while trying to register your information. Please try again.";
$registration_mail_subject = "Welcome to Group-Office.";
$registration_folder_error = "Failed creating the personal folder. Contact us to restore this!";
$registration_title = "Register at Group-Office";

$registration_email = "Create an E-mail address for this user at Group-Office.";
$registration_samba = "File & Print server.";
$registration_email_error = "An error occured while creating your e-mail account. Contact us for support.";
$strPasswordConfirm = "ðÏÄÔ×ÅÒÖÄÅÎÉÅ ÐÁÒÏÌÑ";
$strPassword = "ðÁÒÏÌØ";

$reg_company_info = "éÎÆÏÒÍÁÃÉÑ Ï ËÏÍÐÁÎÉÉ";

$max_user_limit = "Maximum ammount of users reached.";
$max_users_text = "Sorry, you can't register more users. Contact us if you want to increase your number of users.";
$ml_sent_items = "Sent items";
$ml_spam = "óðáí";
$ac_samba_user = "User has acces to the file and print server.";
$ml_email_only = "Only required when you don't create an E-mail address";
$register_new_user = 'îÏ×ÙÊ Group-Office ÐÏÄÐÉÓÞÉË';
$register_thanks = '<h2>óÐÁÓÉÂÏ</h2>Your subscription will be processed as soon as possible. You will recieve further notice.';
$register_accept = 'äÏÂÁ×ÉÔØ ÐÏÌØÚÏ×ÁÔÅÌÑ';
$register_addressbook = 'äÏÂÁ×ÉÔØ ÁÄÒÅÓÎÕÀ ËÎÉÇÕ';
$register_title = 'Subscribe';
$registration_mail_body=<<<registration_mail_body
Dear %sex% %last_name%,

You have been registered at Group-Office.
We hope you will enjoy our service.

You can login with the following data:

Username: %username%
Password: %password%

Click on the link below to login:

registration_mail_body;

//1.98
$reg_language = 'ñÚÙË';
$reg_theme = 'ôÅÍÁ';

//2.05
$admin_use = 'éÓÐÏÌØÚÏ×ÁÎÉÅ';
$admin_module = 'íÏÄÕÌØ';
$admin_manage = 'Manage';

$admin_groups = 'çÒÕÐÐÙ ÐÏÌØÚÏ×ÁÔÅÌÑ';
$admin_groups_user = 'User is member of the following user groups';

//2.11

//Modified string (Added %groupoffice_title%)
$registration_mail_body=<<<registration_mail_body
Dear %title% %last_name%,

You have been registered at %groupoffice_title%.
We hope you will enjoy this service.

You can login with the following data:

Username: %username%
Password: %password%

Click on the link below to login:

registration_mail_body;

//2.13
$registration_mail_subject = "äÏÂÒÏ ÐÏÖÁÌÏ×ÁÔØ × %groupoffice_title%"; //changed string
$registration_title = "òÅÇÉÓÔÒÁÃÉÑ × %groupoffice_title%"; //changed string

$lang_modules['users'] = 'ðÏÌØÚÏ×ÁÔÅÌÉ';
