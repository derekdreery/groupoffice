<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('blacklist'));
$lang['blacklist']['name']='IP blacklist';
$lang['blacklist']['description']='Mòdul que bloqueja adreces IP després de 5 intents incorrectes de logueig.';
$lang['blacklist']['ip']='IP';
$lang['blacklist']['ips']='Adreces IP';
$lang['blacklist']['blacklisted']='La vostra adreça IP %s ha estat bloquejada per que hi ha hagut 5 intents incorrectes de logueig des de la mateixa. Contacteu amb l\'Administrador per que la desbloquegi.';

$lang['blacklist']['captchaIncorrect']='El codi de seguretat que heu introduït és incorrecte, si us plau torneu-ho a provar.';
$lang['blacklist']['captchaActivated']='S\'han detectar tres intents de logueig incorrectes. Per loguejar-vos heu d\'introduïr el codi de seguretat.';
?>