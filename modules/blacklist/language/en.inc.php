<?php
//Uncomment this line in new translations!
//require($GO_LANGUAGE->get_fallback_language_file('blacklist'));
$lang['blacklist']['name']='IP blacklist';
$lang['blacklist']['description']='A module that will require the user to enter a captcha after 3 consecutive login failures.';
$lang['blacklist']['ip']='IP';
$lang['blacklist']['ips']='IP addresses';

$lang['blacklist']['blacklisted']='Your IP address %s is being blocked because there were 3 consecutive login failures from this IP address. Contact the system administrator and supply your IP address to unblock it.';
$lang['blacklist']['captchaIncorrect']='The security code you entered is incorrect, please try again.';
$lang['blacklist']['captchaActivated']='There have been three login failures detected. In order to login you need to type the security code.';
