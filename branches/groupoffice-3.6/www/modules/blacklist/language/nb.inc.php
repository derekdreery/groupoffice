<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('blacklist'));
$lang['blacklist']['name']='IP-svarteliste';
$lang['blacklist']['description']='Modulen sperrer IP-adresser etter 5 mislykkede innloggingsforsøk på rad.';
$lang['blacklist']['ip']='IP';
$lang['blacklist']['ips']='IP-adresser';

$lang['blacklist']['blacklisted']='Din IP-adresse %s er blitt svartelistet fordi vi har registrert 5 mislykkede innloggingsforsøk fra denne IP-adressen. Kontak systemadministrator og oppgi IP-adressen din, slik at vi kan fjerne sperringen.';
$lang['blacklist']['captchaIncorrect']='Sikkerhetskoden er feil! Vennligst prøv igjen.';
$lang['blacklist']['captchaActivated']='Vi har oppdaget fem mislykkede innloggingsforsøk. For å logge inn må du nå derfor også angi sikkerhetskoden.';