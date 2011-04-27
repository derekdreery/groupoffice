<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('blacklist'));
$lang['blacklist']['name']="Bloqueio de IP's";
$lang['blacklist']['description']='Um módulo para bloquear endereços IP após 5 tentativas de logon inválidas.';
$lang['blacklist']['ip']='IP';
$lang['blacklist']['ips']='Endereços IP';

$lang['blacklist']['blacklisted']='Seu endereço IP (%s) foi bloqueado devido a 5 tentativas consecutivas de login sem suceeso. Entre em contato com o administrador do sistema e forneça seu endereço IP para desbloquea-lo.';
