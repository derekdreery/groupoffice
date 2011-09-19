<?php
	/** 
		* @copyright Copyright Boso d.o.o.
		* @author Mihovil Stanić <mihovil.stanic@boso.hr>
	*/
 
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('blacklist'));

$lang['blacklist']['name']='IP crna lista';
$lang['blacklist']['description']='Modul koji će tražiti od korisnika da unese "captcha" kod nakon 3 uzastopna neuspješna pokušaja prijave.';
$lang['blacklist']['ip']='IP';
$lang['blacklist']['ips']='IP adresa';

$lang['blacklist']['blacklisted']='Vaša IP adresa %s je blokirana zbog toga što se 3 puta zaredom niste uspjeli prijaviti u sustav sa ove IP adrese. Kontaktirajte sistem administratora i proslijedite mu vašu IP adresu kako bi ste uklonili blokadu.';
$lang['blacklist']['captchaIncorrect']='Sigurnosni kod koji ste unjeli je neispravan, molimo pokušajte ponovo.';
$lang['blacklist']['captchaActivated']='Tri puta se niste uspjeli prijaviti u sustav. Kako bi ste se prijavili u sustav morate unjeti sigurnosni kod.';
