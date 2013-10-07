<?php
	/** 
		* @copyright Copyright Boso d.o.o.
		* @author Mihovil Stanić <mihovil.stanic@boso.hr>
	*/
 
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('email'));

$lang['email']['name'] = 'E-mail';
$lang['email']['description'] = 'E-mail klijent. Svi korisnici će biti u mogućnosti slati i primati e-mailove';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = 'Niste unijeli primatelja';
$lang['email']['feedbackSMTPProblem'] = 'Došlo je do problema u komunikaciji sa SMTP: ';
$lang['email']['feedbackUnexpectedError'] = 'Pojavio se ne predviđeni problem sa izgradnjom e-maila: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Nije uspjelo kreiranje direktorija';
$lang['email']['feedbackDeleteFolderFailed'] = 'Nije uspjelo brisanje direktorija';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Neuspješna pretplata na direktorij';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Neuspješno brisanje pretplate na direktorij';
$lang['email']['feedbackCannotConnect'] = 'Neuspješno povezivanje sa %1$s na port %3$s<br /><br />Server za pošto je vratio: %2$s';
$lang['email']['inbox'] = 'Pristigla pošta';

$lang['email']['spam']='Spam';
$lang['email']['trash']='Smeće';
$lang['email']['sent']='Poslano';
$lang['email']['drafts']='Nacrti';

$lang['email']['no_subject']='Nema naslova';
$lang['email']['to']='Prima';
$lang['email']['from']='Šalje';
$lang['email']['subject']='Naslov';
$lang['email']['no_recipients']='Neobjavljeni primatelji';
$lang['email']['original_message']='--- Originalna poruka ispod ---';
$lang['email']['attachments']='Privitci';

$lang['email']['notification_subject']='Čitaj: %s';
$lang['email']['notification_body']='Vaša poruka sa naslovom "%s" prikazana je kod %s';

$lang['email']['errorGettingMessage']='Nije bilo moguće skiniti poruku sa servera';
$lang['email']['no_recipients_drafts']='Nema primatelja';
$lang['email']['usage_limit'] = '%s od %s iskorišteno';
$lang['email']['usage'] = '%s iskorišteno';

$lang['email']['event']='Sastanak';
$lang['email']['calendar']='kalendar';

$lang['email']['quotaError']="Vaš poštanski sandučić je pun. Prvo ispraznite svoj pretinac za smeće. Ako je već prazan i vaš poštanski sandučić je još uvijek pun, morate onemogućiti pretinac smeća da briše poruke iz drugih pretinaca. Možete ga onemogućiti ovdje:\n\nAdministracija -> Računi -> Dupli klik na željeni račun -> Direktoriji.";

$lang['email']['draftsDisabled']="Poruku nije moguće sačuvati zbog toga što je sandučić 'Nacrti' onemogućen.<br /><br />Idite u E-mail -> Administracija -> Računi -> Dupli klik na željeni račun -> Direktoriji kako bi ga omogućili.";
$lang['email']['noSaveWithPop3']='Poruku nije moguće sačuvati zbog toga što POP3 račun to ne podržava.';

$lang['email']['goAlreadyStarted']='Group-Office je već pokrenut. Pisanje e-maila je sada učitano u Group-Office. Zatvorite ovaj prozor i napišite svoju poruku Group-Office.';

//At Tuesday, 07-04-2009 on 8:58 Group-Office Administrator <test@intermeshdev.nl> wrote:
$lang['email']['replyHeader']='U %s, %s u %s %s je napisao:';
$lang['email']['alias']='Pseudonim';
$lang['email']['aliases']='Pseudonimi';
$lang['email']['alias']='Pseudonim';
$lang['email']['aliases']='Pseudonimi';

$lang['email']['noUidNext']='Vaš e-mail server ne podržava UIDNEXT. Sandučić \'Nacrti\' je automatski onemogućen za ovaj račun.';

$lang['email']['disable_trash_folder']='Premještanje e-mail poruke u pretinac smeća nije uspio. Ovo se može dogoditi ako nemate dovoljno diskovnog prostora. Možete osloboditi nešto diskovnog prostora tako da onemogućite rad pretinca smeća u Administracija -> Računi -> Dupli klik na željeni račun -> Direktoriji';

$lang['email']['error_move_folder']='Nije moguće premjestiti direktorij';

$lang['email']['error_getaddrinfo']='Navedena je pogrešna adresa host-a';
$lang['email']['error_authentication']='Pogrešno korisničko ime ili lozinka';
$lang['email']['error_connection_refused']='Veza sa serverom je odbijena. Molimo provjeri adresu servera i port.';

$lang['email']['iCalendar_event_invitation']='Ova poruka sadrži pozivnicu za događaj.';
$lang['email']['iCalendar_event_not_found']='Ova poruka sadrži ažuriranja za događaj koji više ne postoji.';
$lang['email']['iCalendar_update_available']='Ova poruka sadrži ažuriranja za postojeći događaj.';
$lang['email']['iCalendar_update_old']='Ova poruka sadrži događaj koji je već obrađen.';
$lang['email']['iCalendar_event_cancelled']='Ova poruka sadrži otkaz događaja.';
$lang['email']['iCalendar_event_invitation_declined']='Ova poruka sadrži pozivnicu za događaj koji ste već odbili.';