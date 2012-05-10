<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('blacklist'));
$lang['blacklist']['name']='IP blacklist';
$lang['blacklist']['description']='Módulo que bloquea direcciones IP despues de 5 intentos fallidos de logueo.';
$lang['blacklist']['ip']='IP';
$lang['blacklist']['ips']='Direcciones IP';
$lang['blacklist']['blacklisted']='Su dirección IP %s fue bloqueada porque hubieron 5 intentos fallidos de logueo desde la misma. Contacte el administrador para que la desbloquee.';
