<?php
//Polish Translation v1.1
//Author : Paweł Dmitruk pawel.dmitruk@gmail.com
//Date : September, 05 2010
//Polish Translation v1.1
//Author : rajmund
//Date : January, 26 2011
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('blacklist'));
$lang['blacklist']['name']='Czarna lista IP';
$lang['blacklist']['description']='Moduł blokujący adresy IP po 5 nieudanych logowaniach z rzędu.';
$lang['blacklist']['ip']='IP';
$lang['blacklist']['ips']='Adresy IP';

$lang['blacklist']['blacklisted']='Twój adres IP %s jest zablokowany ponieważ podjęto z niego 5 nieudanych prób logowania. Skontaktuj się z administratorem i podaj adres IP w celu odblokowania go.';
$lang['blacklist']['captchaIncorrect']='Wpisany kod captcha jest niepoprawny. Proszę spróbować ponownie.';
$lang['blacklist']['captchaActivated']='Nastąpiły trzy błędy logowania pod rząd. Aby się zalogować należy wpisać kod captcha.';