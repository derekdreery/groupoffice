<?php
	/** 
		* @copyright Copyright Boso d.o.o.
		* @author Mihovil Stanić <mihovil.stanic@boso.hr>
	*/
 
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('calendar'));

$lang['calendar']['name'] = 'Kalendar';
$lang['calendar']['description'] = 'Kalendar modul; Svi korisnici mogu dodati, urediti ili obrisati sastanak. Sastanci drugih korisnika se mogu vidjeti i ako je potrebno promijeniti.';

$lang['link_type'][1]='Sastanak';

$lang['calendar']['groupView'] = 'Grupni pogled';
$lang['calendar']['event']='Događaj';
$lang['calendar']['startsAt']='Počinje u';
$lang['calendar']['endsAt']='Završava u';

$lang['calendar']['exceptionNoCalendarID'] = 'GREŠKA: Ne postoji ID kalendara!';
$lang['calendar']['appointment'] = 'Sastanak: ';
$lang['calendar']['allTogether'] = 'Sve zajedno';

$lang['calendar']['location']='Lokacija';

$lang['calendar']['invited']='Pozvani se na slijedeći događaj';
$lang['calendar']['acccept_question']='Da li prihvaćate ovaj događaj?';

$lang['calendar']['accept']='Prihvati';
$lang['calendar']['decline']='Odbij';

$lang['calendar']['bad_event']='Ovaj događaj više ne postoji';

$lang['calendar']['subject']='Naslov';
$lang['calendar']['status']='Status';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Potrebno djelovati';
$lang['calendar']['statuses']['ACCEPTED'] = 'Prihvaćeno';
$lang['calendar']['statuses']['DECLINED'] = 'Odbijeno';
$lang['calendar']['statuses']['TENTATIVE'] = 'Privremeno';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegirano';
$lang['calendar']['statuses']['COMPLETED'] = 'Završeno';
$lang['calendar']['statuses']['IN-PROCESS'] = 'U procesu';
$lang['calendar']['statuses']['CONFIRMED'] = 'Potvrđeno';


$lang['calendar']['accept_mail_subject'] = 'Poziv za \'%s\' prihvaćen';
$lang['calendar']['accept_mail_body'] = '%s je prihvatio vaš poziv za:';

$lang['calendar']['decline_mail_subject'] = 'Poziv za \'%s\' odbijen';
$lang['calendar']['decline_mail_body'] = '%s je odbio vaš poziv za:';

$lang['calendar']['location']='Lokacija';
$lang['calendar']['and']='i';

$lang['calendar']['repeats'] = 'Ponavlja svaki %s';
$lang['calendar']['repeats_at'] = 'Ponavlja svaki %s na %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Ponavlja svaki %s %s na %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['repeats_not_every'] = 'Ponavlja svaki %s %s';
$lang['calendar']['until']='do'; 

$lang['calendar']['not_invited']='Niste pozvani na ovaj događaj. Možda se morate prijaviti kao drugi korisnik.';


$lang['calendar']['accept_title']='Prihvaćeno';
$lang['calendar']['accept_confirm']='Vlasnik će biti obavješten da ste prihvatili događaj';

$lang['calendar']['decline_title']='Odbijeno';
$lang['calendar']['decline_confirm']='Vlasnik će biti obavješten da ste odbili događaj';

$lang['calendar']['cumulative']='Pogrešno pravilo ponavljanja. Slijedeće ponavljanje ne može početi prije nego li je prošlo završilo.';

$lang['calendar']['already_accepted']='Već ste prihvatili ovaj događaj.';

$lang['calendar']['private']='Privatno';

$lang['calendar']['import_success']='%s događaja je uvezeno';

$lang['calendar']['printTimeFormat']='Od %s do %s';
$lang['calendar']['printLocationFormat']=' na lokaciji "%s"';
$lang['calendar']['printPage']='Stranica %s od %s';
$lang['calendar']['printList']='Lista sastanaka';

$lang['calendar']['printAllDaySingle']='Cijeli dan';
$lang['calendar']['printAllDayMultiple']='Cijeli dan od %s do %s';

$lang['calendar']['calendars']='Kalendari';

$lang['calendar']['open_resource']='Otvorene rezervacije';

$lang['calendar']['resource_mail_subject']='Resurs \'%s\' je rezerviran za \'%s\' na \'%s\'';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_mail_body']='%s je napravio rezervaciju za resurs \'%s\'. Vi ste održavatelj ovog resursa. Molimo da otvorite rezervacije kako bi ste je odobrili ili odbili.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['resource_modified_mail_subject']='Resurs \'%s\' rezerviran za \'%s\' na \'%s\' je promjenjen';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_modified_mail_body']='%s je promjenio rezervaciju za resurs \'%s\'. Vi ste održavatelj ovog resursa. Molimo da otvorite rezervacije kako bi ste je odobrili ili odbili.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['your_resource_modified_mail_subject']='Vaša rezervacija za \'%s\' na \'%s\' u statusu \'%s\' je promjenjena';//is resource name, %s is event name, %s is start date
$lang['calendar']['your_resource_modified_mail_body']='%s je promjenio vašu rezervaciju za resurs \'%s\'.';

$lang['calendar']['your_resource_accepted_mail_subject']='Vaša rezervacija za \'%s\' na \'%s\' je prihvaćena';//%s is resource name, %s is start date
$lang['calendar']['your_resource_accepted_mail_body']='%s je prihvatio vašu rezervaciju za resurs \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['your_resource_declined_mail_subject']='Vaša rezervacija za \'%s\' na \'%s\' je odbijena';//%s is resource name, %s is start date
$lang['calendar']['your_resource_declined_mail_body']='%s je odbio vašu rezervaciju za resurs \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['birthday_name']='Rođendan: {NAME}';
$lang['calendar']['birthday_desc']='{NAME} je danas napunio {AGE}';

$lang['calendar']['unauthorized_participants_write']='Nemate potrebne dozvole kako bi ste zakazali sastanak za slijedeće korisnike:<br /><br />{NAMES}<br /><br />Možete im poslati pozivnicu za sastanak koju će moći odobriti i zakazati sastanak.';

$lang['calendar']['noCalSelected'] = 'Niti jedan kalendar nije odabran za ovaj pregled. Odaberite barem jedan kalendar u Administraciji.';

$lang['calendar']['month_times'][1]='prvi';
$lang['calendar']['month_times'][2]='drugi';
$lang['calendar']['month_times'][3]='treći';
$lang['calendar']['month_times'][4]='četvrti';
$lang['calendar']['month_times'][5]='peti';

$lang['calendar']['rightClickToCopy']='Desni klik kako bi ste kopirali lokaciju linka';

$lang['calendar']['invitation']='Pozivnica';
$lang['calendar']['invitation_update']='Ažurirana pozivnica';
$lang['calendar']['cancellation']='Otkazivanje';

$lang['calendar']['non_selected'] = 'u ne odabranom kalendaru';

$lang['calendar']['linkIfCalendarNotSupported']='Koristite linkove ispod samo ako vaš e-mail program ne podržava kalendarske funkcije.';