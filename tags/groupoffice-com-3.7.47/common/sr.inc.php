<?php
	/** 
		* @copyright Copyright Boso d.o.o.
		* @author Mihovil Stanić <mihovil.stanic@boso.hr>
		* @author Petar Benke <petar@benke.co.uk>
	*/
 
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_base_language_file('common'));

$lang['common']['about']='Verzija: %s

Autorska prava (c) 2003-%s, Intermesh
Sva prava pridržana.
Ovaj program je zaštićen zakonom o autorskim pravim i Group-Office licencom.

Za pitanja oko podrške kontaktirajte vašeg administratora:
%s

Za više informacija o Group-Office posetite:
http://www.group-office.com

Group-Office je kreirao Intermesh. Za više informacija o Intermesh posetite:
http://www.intermesh.nl/en/';

$lang['common']['totals']='Ukupno';
$lang['common']['printPage']='Stranica %s od %s';

$lang['common']['htmldirection']= 'ltr';

$lang['common']['quotaExceeded']='Nemate više prostora na disku. Obrišite deo datoteka ili kontaktirajte vašeg dobavljača kako bi dodao još prostora.';
$lang['common']['errorsInForm'] = 'Pojavile su se greške u unosu. Ispravite ih i probajte ponovo.';

$lang['common']['moduleRequired']='Za ovu funkciju je neophodan modul %s';

$lang['common']['loadingCore']= 'Pokrećem sistem';
$lang['common']['loadingLogin'] = 'Pokrećem dijalog za prijavu';
$lang['common']['renderInterface']='Iscrtavam interfejs';
$lang['common']['loadingModules']='Pokrećem module';
$lang['common']['loadingModule'] = 'Pokrećem modul';

$lang['common']['loggedInAs'] = "Prijavljen kao ";
$lang['common']['search']='Traži';
$lang['common']['settings']='Podešavanja';
$lang['common']['adminMenu']='Admin meni';
$lang['common']['startMenu']='Početni meni';
$lang['common']['help']='Pomoć';
$lang['common']['logout']='Odjavi se';
$lang['common']['badLogin'] = 'Pogrešno korisničko ime ili lozinka';
$lang['common']['badPassword'] = 'Uneli ste pogrešnu trenutnu lozinku';

$lang['common']['passwordMatchError']='Lozinke nisu iste';
$lang['common']['accessDenied']='Pristup odbijen';
$lang['common']['saveError']='Greška pri snimanju podataka';
$lang['common']['deleteError']='Greška pri brisanju podataka';
$lang['common']['selectError']='Greška pri pokušaju čitanja podataka';
$lang['common']['missingField'] = 'Niste popunili sva obavezna polja.';
$lang['common']['invalidEmailError']='E-mail adresa nije ispravna';
$lang['common']['invalidDateError']='Uneli ste neispravan datum';
$lang['common']['noFileUploaded']='Datoteka nije primljena';
$lang['common']['error']='Greška';
$lang['common']['fileCreateError']='Nije bilo moguće kreirati datoteku';
$lang['common']['illegalCharsError']='Ime sadrži jedan od sledećih nedopuštenih znakova %s';

$lang['common']['salutation']='Pozdrav';
$lang['common']['firstName'] = 'Ime';
$lang['common']['lastName'] = 'Prezime';
$lang['common']['middleName'] = 'Srednje ime';
$lang['common']['sirMadam']['M'] = 'gospodin';
$lang['common']['sirMadam']['F'] = 'gospođa';
$lang['common']['initials'] = 'Inicijali';
$lang['common']['sex'] = 'Pol';
$lang['common']['birthday'] = 'Rođendan';
$lang['common']['sexes']['M'] = 'Muško';
$lang['common']['sexes']['F'] = 'Žensko';
$lang['common']['title'] = 'Titula';
$lang['common']['addressNo'] = 'Adresa 2';
$lang['common']['workAddressNo'] = 'Adresa 2 (posao)';
$lang['common']['postAddress'] = 'Adresa (pošta)';
$lang['common']['postAddressNo'] = 'Adresa 2 (pošta)';
$lang['common']['postCity'] = 'Grad (pošta)';
$lang['common']['postState'] = 'Regija (pošta)';
$lang['common']['postCountry'] = 'Država (post)';
$lang['common']['postZip'] = 'Poštanski broj (pošta)';
$lang['common']['visitAddress'] = 'Adresa za posete';
$lang['common']['postAddressHead'] = 'Poštanska adresa';
$lang['common']['name'] = 'Ime';
$lang['common']['name2'] = 'Ime 2';
$lang['common']['user'] = 'Korisnik';
$lang['common']['username'] = 'Korisničko ime';
$lang['common']['password'] = 'Lozinka';
$lang['common']['authcode'] = 'Autorizacioni kod';
$lang['common']['country'] = 'Država';
$lang['common']['address_format']='Format adrese';
$lang['common']['state'] = 'Regija';
$lang['common']['city'] = 'Grad';
$lang['common']['zip'] = 'Poštanski broj';
$lang['common']['address'] = 'Adresa';
$lang['common']['email'] = 'E-mail';
$lang['common']['phone'] = 'Telefon';
$lang['common']['workphone'] = 'Telefon (posao)';
$lang['common']['cellular'] = 'Mobilni';
$lang['common']['company'] = 'Kompanija';
$lang['common']['department'] = 'Sektor';
$lang['common']['function'] = 'Funkcija';
$lang['common']['question'] = 'Tajno pitanje';
$lang['common']['answer'] = 'Odgovor';
$lang['common']['fax'] = 'Faks';
$lang['common']['workFax'] = 'Faks (posao)';
$lang['common']['homepage'] = 'Web stranica';
$lang['common']['workAddress'] = 'Adresa (posao)';
$lang['common']['workZip'] = 'Poštanski broj (posao)';
$lang['common']['workCountry'] = 'Država (posao)';
$lang['common']['workState'] = 'Regija (posao)';
$lang['common']['workCity'] = 'Grad (posao)';
$lang['common']['today'] = 'Danas';
$lang['common']['tomorrow'] = 'Sutra';

$lang['common']['SearchAll'] = 'Sva polja';
$lang['common']['total'] = 'ukupno';
$lang['common']['results'] = 'rezultati';


$lang['common']['months'][1]='Januar';
$lang['common']['months'][2]='Februar';
$lang['common']['months'][3]='Mart';
$lang['common']['months'][4]='April';
$lang['common']['months'][5]='Maj';
$lang['common']['months'][6]='Jun';
$lang['common']['months'][7]='Jul';
$lang['common']['months'][8]='Avgust';
$lang['common']['months'][9]='Septembar';
$lang['common']['months'][10]='Oktobar';
$lang['common']['months'][11]='Novembar';
$lang['common']['months'][12]='Decembar';

$lang['common']['short_days'][0]="Ne";
$lang['common']['short_days'][1]="Po";
$lang['common']['short_days'][2]="Ut";
$lang['common']['short_days'][3]="Sr";
$lang['common']['short_days'][4]="Če";
$lang['common']['short_days'][5]="Pe";
$lang['common']['short_days'][6]="Su";


$lang['common']['full_days'][0] = "Nedelja";
$lang['common']['full_days'][1] = "Ponedjeljak";
$lang['common']['full_days'][2] = "Utorak";
$lang['common']['full_days'][3] = "Sreda";
$lang['common']['full_days'][4] = "Četvrtak";
$lang['common']['full_days'][5]= "Petak";
$lang['common']['full_days'][6] = "Subota";

$lang['common']['default']='Zadate';
$lang['common']['description']='Opis';
$lang['common']['date']='Datum';

$lang['common']['default_salutation']['M']='Dragi gospodine';
$lang['common']['default_salutation']['F']='Draga gospođo';
$lang['common']['default_salutation']['unknown']='Dragi gospodine / gospođo';
$lang['common']['dear']='Dragi';

$lang['common']['mins'] = 'Minuti';
$lang['common']['hour'] = 'sat';
$lang['common']['hours'] = 'sati';
$lang['common']['day'] = 'dan';
$lang['common']['days'] = 'dani';
$lang['common']['week'] = 'sedmica';
$lang['common']['weeks'] = 'sedmice';
$lang['common']['month'] = 'mesec';
$lang['common']['strMonths'] = 'meseci';

$lang['common']['group_everyone']='Svi';
$lang['common']['group_admins']='Administratori';
$lang['common']['group_internal']='Interni';

$lang['common']['admin']='Administrator';

$lang['common']['beginning']='Pozdrav';

$lang['common']['max_emails_reached']= "Maksimalni broj e-mail poruka za SMTP %s od %s na dan je dostignut.";
$lang['common']['usage_stats']='Korišćenje prostora na disku po %s';
$lang['common']['usage_text']='Ova instalacija Group-Office koristi';

$lang['common']['database']='Baza podataka';
$lang['common']['files']='Datoteke';
$lang['common']['email']='E-mail';
$lang['common']['total']='Ukupno';


$lang['common']['confirm_leave']='Ako sada napustite Group-Office izgubićete nesačuvane promene';
$lang['common']['dataSaved']='Podaci su uspešno sačuvani';

$lang['common']['uploadMultipleFiles'] = 'Kliknite na \'Pregled\' kako biste izabrali datoteke i/ili direktorijume sa vašeg računara. Kliknite na \'Prenos\' kako biste preneli datoteke u Group-Office. Ovaj prozor će se automatski zatvoriti kada prenos datoteka bude završen.';


$lang['common']['loginToGO']='Kliknite ovde kako biste se prijavili u Group-Office';
$lang['common']['links']='Linkovi';
$lang['common']['GOwebsite']='Group-Office stranica';
$lang['common']['GOisAProductOf']='<i>Group-Office</i> je <a href="http://www.intermesh.nl/en/" target="_blank">Intermesh</a> proizvod.';

$lang['common']['yes']='Da';
$lang['common']['no']='Ne';

$lang['common']['system']='Sistem';

$lang['common']['goAlreadyStarted']='Group-Office je već pokrenut. Traženi ekran je pokrenut u Group-Office. Možete da zatvorite ovaj prozor/karticu i da nastavite rad u Group-Office.';
$lang['common']['no']='Ne';

$lang['commmon']['logFiles']='Log datoteke';

$lang['common']['reminder']='Podsetnik';
$lang['common']['unknown']='Nepoznato';
$lang['common']['time']='Vreme';

$lang['common']['dontChangeAdminsPermissions']='Ne možete promeniti dozvole admin grupe';
$lang['common']['dontChangeOwnersPermissions']='Ne možete promeniti dozvole vlasnika';


$lang['common']['running_sys_upgrade']='Pokrenuta je nadogradnja sistema';
$lang['common']['sys_upgrade_text']='Jedan trenutak molim. Sav izlaz će biti zabeležen.';
$lang['common']['click_here_to_contine']='Kliknite ovde kako biste nastavili';
$lang['common']['parentheses_invalid_error']='Zagrade u vašem upitu su pogrešne. Molimo da ih ispravite.';


$lang['common']['nReminders']='%s podsetnika';
$lang['common']['oneReminder']='1 podsetnik';

//Example: you have 1 reminders in Group-Office.
$lang['common']['youHaveReminders']='Imate %s u %s.';

$lang['common']['createdBy']='Kreirao';
$lang['common']['none']='Nijedan';
$lang['common']['alert']='Oprez';
$lang['common']['theFolderAlreadyExists']='Direktorijum sa tim imenom već postoji';

$lang['common']['other']='Drugi';
$lang['common']['copy']='kopiraj';

$lang['common']['upload_file_to_big']='Datoteka koji ste pokušali da prenesete je veća od maksimalno dozvoljene veličine od %s.';