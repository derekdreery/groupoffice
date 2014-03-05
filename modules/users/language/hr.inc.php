<?php
	/** 
		* @copyright Copyright Boso d.o.o.
		* @author Mihovil Stanić <mihovil.stanic@boso.hr>
	*/
 
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('users'));

$lang['users']['name'] = 'Korisnici';
$lang['users']['description'] = 'Administratorski modul. Upravljanje sa korisnicima programa.';

$lang['users']['deletePrimaryAdmin'] = 'Ne možete obrisati primarnog administratora';
$lang['users']['deleteYourself'] = 'Ne možete obrisati sebe';

$lang['link_type'][8]=$us_user = 'Korisnik';

$lang['users']['error_username']='Imate nedopuštene znakove u korisničkom imenu';
$lang['users']['error_username_exists']='Žao nam je, korisnik sa tim korisničkim imenom već postoji';
$lang['users']['error_email_exists']='Žao nam je, ta e-mail adresa je već registrirana u programu.';
$lang['users']['error_match_pass']='Lozinke nisu jednake';
$lang['users']['error_email']='Unjeli ste pogrešnu e-mail adresu';
$lang['users']['error_user']='Korisnika nije moguće napraviti';

$lang['users']['imported']='Uvezeno %s korisnika';
$lang['users']['failed']='Nije uspjelo';

$lang['users']['incorrectFormat']='Datoteka nije u ispravnom CSV formatu';

$lang['users']['register_email_subject']='Detalji o vašem {product_name} računu';
$lang['users']['register_email_body']='{product_name} račun je kreiran za vas na {url}
Podaci za prijavu su:

Korisničko ime: {username}
Lozinka: {password}';


$lang['users']['max_users_reached']='Dosegnut je maksimalan broj korisnika za ovaj sistem.';