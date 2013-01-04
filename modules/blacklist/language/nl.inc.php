<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('blacklist'));
$lang['blacklist']['name']='IP blacklist';
$lang['blacklist']['description']='Blokkeert IP adressen na drie opeenvolgende foute aanmeldingen.';
$lang['blacklist']['ip']='IP';
$lang['blacklist']['ips']='IP adressen';

$lang['blacklist']['blacklisted']='Uw IP adres [%s] wordt geblokkeerd omdat er vanaf dit adres 3 maal achter elkaar verkeerd is aangemeld. Neem contact op met de systeembeheerder om uw IP adres van de blacklist af te halen.';
$lang['blacklist']['captchaIncorrect']='De door u ingevoerde beveilingscode is incorrect, probeer het nog eens.';
$lang['blacklist']['captchaActivated']='Er zijn drie foutieve inlogpogingen gesignaleerd. Om in te loggen dient u de beveiligingscode over te typen.';