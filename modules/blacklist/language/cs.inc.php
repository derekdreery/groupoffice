<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('blacklist'));
$lang['blacklist']['name']='Černá listina IP adres';
$lang['blacklist']['description']='Modul pro blokování IP adres po 5 špatných pokusech o přihlášení v řadě.';
$lang['blacklist']['ip']='IP';
$lang['blacklist']['ips']='IP adresy';

$lang['blacklist']['blacklisted']='Vaše IP adresa %s je blokována, protože bylo zjištěno 5 po sobě jdoucích špatných přihlášeních z této IP adresy. Kontaktuje správce systému pro odblokování Vaši IP adresy.';
