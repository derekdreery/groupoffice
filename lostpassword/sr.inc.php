<?php
	/** 
		* @copyright Copyright Boso d.o.o.
		* @author Mihovil Stanić <mihovil.stanic@boso.hr>
		* @author Petar Benke <petar@benke.co.uk>
	*/
	
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_base_language_file('lostpassword'));

$lang['lostpassword']['success']='<h1>Lozinka promenjena</h1><p>Vaša lozinka je uspešno promenjena. Možete da nastavite do stranice za prijavu.</p>';
$lang['lostpassword']['send']='Pošalji';
$lang['lostpassword']['login']='Prijavi se';

$lang['lostpassword']['lost_password_subject']='Zahtev za novom lozinkom';
$lang['lostpassword']['lost_password_body']='%s,

Zatražili ste novu lozinku za %s. Vaše korisničko ime je "%s".

Kliknite na link ispod (ili ga kopirajte u vaš Internet čitač) kako biste promenili svoju lozinku:

%s

Ako niste zatražili novu lozinku molimo obrišite ovaj e-mail.';

$lang['lostpassword']['lost_password_error']='Unešena e-mail adresa nije pronađena.';
$lang['lostpassword']['lost_password_success']='E-mail sa uputstvima je poslat na vašu e-mail adresu.';

$lang['lostpassword']['enter_password']='Molimo unesite novu lozinku';

$lang['lostpassword']['new_password']='Nova lozinka';
$lang['lostpassword']['lost_password']='Izgubljena lozinka';

$lang['lostpassword']['confirm_password']='Potvrdi lozinku';
?>
