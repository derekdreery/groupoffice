<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('blacklist'));
$lang['blacklist']['name']='IP-Blacklist';
$lang['blacklist']['description']='Modul zur Sperrung von IP-Adressen nach 3 Login-Fehlversuchen';
$lang['blacklist']['ip']='IP';
$lang['blacklist']['ips']='IP-Adressen';
$lang['blacklist']['blacklisted']='Ihre IP-Adresse %s wurde gesperrt, da von dieser 3 Login-Fehlversuche in Folge unternommen wurden. Kontaktieren Sie den Systemadministrator und teilen Sie ihm die betreffende IP-Adresse mit, damit dieser die Sperre wieder aufheben kann.';
$lang['blacklist']['captchaIncorrect']='Der von Ihnen eingegebene Sicherheitscode ist nicht korrekt. Versuchen Sie es erneut.';
$lang['blacklist']['captchaActivated']='Es wurden drei fehlerhafte Loginversuche detektiert. Um sich einzuloggen, müssen Sie nun den Sicherheitscode eingeben.';