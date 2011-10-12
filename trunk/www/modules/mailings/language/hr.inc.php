<?php
	/** 
		* @copyright Copyright Boso d.o.o.
		* @author Mihovil Stanić <mihovil.stanic@boso.hr>
	*/
 
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('mailings'));

$lang['mailings']['name'] = 'E-mail predlošci i liste adresa';
$lang['mailings']['description'] = 'Dodaje e-mail predloške i liste e-mail adresa za slanje obavjesti kontaktima iz adresara.';

$lang['mailings']['templateAlreadyExists'] = 'Predložak koji pokušavate napraviti već postoji';
$lang['mailings']['mailingAlreadyExists'] = 'Lista adresa koju pokušavate napraviti već postoji';

$lang['mailings']['greet']='Sa štovanjem';

$lang['mailings']['unsubscribe']='Otkaži pretplatu';
$lang['mailings']['unsubscription']='Kliknite ovdje kako bi ste otkazali pretplatu na ove obavjesti.';
$lang['mailings']['r_u_sure'] = 'Da li ste sigurni da želite otkazati pretplatu na ove obavjesti?';
$lang['mailings']['delete_success'] = 'Uspješno ste otkazali pretplatu.';
$lang['mailings']['setCurrentTemplateAsDefault']='Postavite trenutni predložak kao zadani';