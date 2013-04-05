<?php

$l['name'] = 'Kalendar';
$l['description'] = 'Kalendar modul; Svi korisnici mogu dodati, urediti ili obrisati sastanak. Sastanci drugih korisnika se mogu vidjeti i ako je potrebno promijeniti.';
$l['groupView'] = 'Grupni pogled';
$l['event']='Događaj';
$l['exceptionNoCalendarID'] = 'GREŠKA: Ne postoji ID kalendara!';
$l['allTogether'] = 'Sve zajedno';
$l['invited']='Pozvani se na slijedeći događaj';
$l['acccept_question']='Prihvaćate li ovaj događaj?';
$l['accept']='Prihvati';
$l['decline']='Odbij';
$l['bad_event']='Ovaj događaj više ne postoji';
$l['subject']='Naslov';
$l['statuses']['NEEDS-ACTION'] = 'Potrebno djelovati';
$l['statuses']['ACCEPTED'] = 'Prihvaćeno';
$l['statuses']['DECLINED'] = 'Odbijeno';
$l['statuses']['TENTATIVE'] = 'Privremeno';
$l['statuses']['DELEGATED'] = 'Delegirano';
$l['statuses']['COMPLETED'] = 'Završeno';
$l['statuses']['IN-PROCESS'] = 'U procesu';
$l['statuses']['CONFIRMED'] = 'Potvrđeno';
$l['accept_mail_subject'] = 'Poziv za \'%s\' prihvaćen';
$l['accept_mail_body'] = '%s je prihvatio vaš poziv za:';
$l['decline_mail_subject'] = 'Poziv za \'%s\' odbijen';
$l['decline_mail_body'] = '%s je odbio vaš poziv za:';
$l['not_invited']='Niste pozvani na ovaj događaj. Možda se morate prijaviti kao drugi korisnik.';
$l['accept_title']='Prihvaćeno';
$l['accept_confirm']='Vlasnik će biti obavješten da ste prihvatili događaj';
$l['decline_title']='Odbijeno';
$l['decline_confirm']='Vlasnik će biti obavješten da ste odbili događaj';
$l['cumulative']='Pogrešno pravilo ponavljanja. Slijedeće ponavljanje ne može početi prije nego li je prošlo završilo.';
$l['already_accepted']='Već ste prihvatili ovaj događaj.';
$l['private']='Privatno';
$l['import_success']='%s događaja je uvezeno';
$l['printTimeFormat']='Od %s do %s';
$l['printLocationFormat']=' na lokaciji "%s"';
$l['printPage']='Stranica %s od %s';
$l['printList']='Lista sastanaka';
$l['printAllDaySingle']='Cijeli dan';
$l['printAllDayMultiple']='Cijeli dan od %s do %s';
$l['open_resource']='Otvorene rezervacije';
$l['resource_mail_subject']='Resurs \'%s\' je rezerviran za \'%s\' na \'%s\'';//%s is resource name, %s is event name, %s is start date;
$l['resource_mail_body']='%s je napravio rezervaciju za resurs \'%s\'. Vi ste održavatelj ovog resursa. Otvorite rezervacije kako biste je odobrili ili odbili.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['resource_modified_mail_subject']='Resurs \'%s\' rezerviran za \'%s\' na \'%s\' je promjenjen';//%s is resource name, %s is event name, %s is start date;
$l['resource_modified_mail_body']='%s je promjenio rezervaciju za resurs \'%s\'. Vi ste održavatelj ovog resursa. Otvorite rezervacije kako biste je odobrili ili odbili.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['your_resource_modified_mail_subject']='Vaša rezervacija za \'%s\' na \'%s\' u statusu \'%s\' je promjenjena';//is resource name, %s is event name, %s is start date;
$l['your_resource_modified_mail_body']='%s je promjenio vašu rezervaciju za resurs \'%s\'.';
$l['your_resource_accepted_mail_subject']='Vaša rezervacija za \'%s\' na \'%s\' je prihvaćena';//%s is resource name, %s is start date;
$l['your_resource_accepted_mail_body']='%s je prihvatio vašu rezervaciju za resurs \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['your_resource_declined_mail_subject']='Vaša rezervacija za \'%s\' na \'%s\' je odbijena';//%s is resource name, %s is start date;
$l['your_resource_declined_mail_body']='%s je odbio vašu rezervaciju za resurs \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['birthday_name']='Rođendan: {NAME}';
$l['birthday_desc']='{NAME} je danas napunio {AGE}';
$l['unauthorized_participants_write']='Nemate potrebne dozvole kako biste zakazali sastanak za sljedeće korisnike:<br /><br />{NAMES}<br /><br />Možete im poslati pozivnicu za sastanak koju će moći odobriti i zakazati sastanak.';
$l['noCalSelected'] = 'Niti jedan kalendar nije odabran za ovaj pregled. Odaberite barem jedan kalendar u Administraciji.';
$l['rightClickToCopy']='Desni klik kako biste kopirali lokaciju poveznice';
$l['invitation']='Pozivnica';
$l['invitation_update']='Ažurirana pozivnica';
$l['cancellation']='Otkazivanje';
$l['non_selected'] = 'u ne odabranom kalendaru';
$l['linkIfCalendarNotSupported']='Koristite poveznice ispod samo ako vaš program e-pošte ne podržava kalendarske funkcije.';
$l["addressbook"]='Adresar';
$l["appointment"]= 'Sastanak';
$l["appointments"]= 'Sastanci';
$l["recurrence"]= 'Ponavljanje';
$l["options"]= 'Opcije';
$l["repeatForever"]= 'ponavljaj zauvijek';
$l["repeatEvery"]= 'Ponavljaj svaki';
$l["repeatUntil"]= 'Ponavljaj do';
$l["busy"]= 'Prikaži kao zauzet';
$l["allDay"]= 'Vrijeme nije primjenjivo';
$l["navigation"]= 'Navigacija';
$l["oneDay"]= '1 Dan';
$l["fiveDays"]= '5 Dana';
$l["sevenDays"]= '7 Dana';
$l["month"]= 'Mjesec';
$l["recurringEvent"]= 'Ponavljajući događaj';
$l["deleteRecurringEvent"]= 'Želite li obrisati jedan primjerak ili sve primjerke ovog ponavljajućeg događaja?';
$l["singleOccurence"]= 'Jedno ponavljanje';
$l["entireSeries"]= 'Cijela serija';
$l["calendar"]= 'Kalendar';
$l["calendars"]= 'Kalendari';
$l["views"]= 'Pogledi';
$l["administration"]= 'Administracija';
$l["needsAction"]= 'Potrebno djelovati';
$l["accepted"]= 'Prihvaćeno';
$l["declined"]= 'Odbijeno';
$l["tentative"]= 'Privremeno';
$l["delegated"]= 'Delegirano';
$l["noRecurrence"]= 'Bez ponavljanja';
$l["notRespondedYet"]= 'Nije još odgovorio';
$l["days"]= 'dana';
$l["weeks"]= 'tjedana';
$l["monthsByDate"]= 'mjeseci po datumu';
$l["monthsByDay"]= 'mjeseci po danu';
$l["years"]= 'godine';
$l["atDays"]= 'Na dane';
$l["noReminder"]= 'Bez podsjetnika';
$l["reminder"]='Podsjetnik';
$l["participants"]= 'Sudionici';
$l["checkAvailability"]= 'Provjeri dostupnost';
$l["sendInvitation"]= 'Pošalji pozivnicu';
$l["emailSendingNotConfigured"]= 'Slanje e-pošte nije podešeno.';
$l["privateEvent"]= 'Privatno';
$l["noInformationAvailable"]= 'Informacija nije dostupna';
$l["noParticipantsToDisplay"]= 'Nema sudionika za prikaz';
$l["previousDay"]= 'Prošli dan';
$l["nextDay"]= 'Slijedeći dan';
$l["noAppointmentsToDisplay"]= 'Nema sastanaka za prikaz';
$l["selectCalendar"]= 'Odaberi kalendar';
$l["selectCalendarForAppointment"]= 'Odaberite kalendar u koji želite smjestiti ovaj sastanak';
$l["closeWindow"]= 'Sastanak je prihvaćen i zakazan. Možete zatvoriti ovaj prozor.';
$l["list"]='Lista';
$l["editRecurringEvent"]='Želite li urediti ovaj primjerak ili cijelu seriju?';
$l["selectIcalendarFile"]='Odaberite ikalendar (*.ics) datoteku';
$l["location"]='Lokacija';
$l["startsAt"]='Počinje';
$l["endsAt"]='Završava';
$l["eventDefaults"]='Zadane postavke za sastanke';
$l["importToCalendar"]='Dodaj sastanke direktno u kalendare';
$l["default_calendar"]='Zadani kalendar';
$l["status"]='Status';
$l["resource_groups"]='Grupe resursa';
$l["resource_group"]='Grupa resursa';
$l["resources"]='Resursi';
$l["resource"]='Resurs';
$l["calendar_group"]='Kalendarska grupa';
$l["admins"]='Administratori';
$l["no_group_selected"]='U vašem unosu postoje greške. Trebate odabrati grupu za ovaj resurs.';
$l["visibleCalendars"]='Vidljivi kalendari';
$l["visible"]='Vidljivo';
$l["group"]='Grupa';
$l["no_status"]='Novi';
$l["no_custom_fields"]='Nisu dostupne dodatne opcije.';
$l["show_bdays"]='Prikaži rođendane iz adresara';
$l["show_tasks"]='Prikaži zadatke iz liste zadataka';
$l["myCalendar"]='Moj kalendar';
$l["merge"]='Spoji';
$l["ownColor"]= 'Dodjeli svakom kalendaru drugu boju';
$l["ignoreConflictsTitle"]= 'Ignoriraj konflikte?';
$l["ignoreConflictsMsg"]= 'Ovaj događaj se preklapa sa drugim događajem iz vašeg kalendara. Svejedno spremi događaj?';
$l["resourceConflictTitle"]= 'Preklapanje resursa';
$l["resourceConflictMsg"]= 'Jedan ili više resursa u ovom događaju se već koriste u isto vrijeme:</br>';
$l["view"]= 'Pogled';
$l["calendarsPermissions"]='Dozvole kalendara';
$l["resourcesPermissions"]='Dozvole resursa';
$l["categories"]='Kategorije';
$l["category"]='Kategorija';
$l["globalCategory"]='Globalna kategorija';
$l["selectCategory"]='Odaberi kategoriju';
$l["duration"]='Vremenski raspon';
$l["move"]='Premjesti';
$l["showInfo"]='Detalji';
$l["copyEvent"]='Kopiraj događaj';
$l["moveEvent"]='Premjesti događaj';
$l["eventInfo"]='Detalji događaja';
$l["isOrganizer"]='Organizator';
$l["sendInvitationInitial"]='Želite li sudionicima poslati pozivnice za sastanak?';
$l["sendInvitationUpdate"]='Želite li sudionicima poslati ažurirane informacije o sastanku?';
$l["sendCancellation"]='Želite li svim sudionicima poslati obavijest o otkazivanju sastanka?';
$l["forthcomingAppointments"]='Nadolazeći sastanci';
$l["quarterShort"]= 'Q';
$l["globalsettings_templatelabel"]= 'Predložak';
$l["globalsettings_allchangelabel"]= 'Preimenuj sve postojeće';
$l["globalsettings_renameall"]= 'Jeste li sigurni da želite preimenovati sve zadane korisničke kalendare?';
$l["publishICS"]='Objavi iKalendar datoteku sa zadnjih mjesec dana i budućim događajima. Oprez! Kalendar će biti čitljiv svima.';
$l["addTimeRegistration"]='Uvezi u vrijeme upisa';
$l["showNotBusy"]='Ne pokazuj nove rezervacije kao zauzete';

$l["confirmed"]= 'Potvrđeno';
$l["globalCategories"]='Globalne kategorije';
$l["sendEmailParticipants"]= 'Stvori e-poštu za sudionike';
$l['cancelMessage']='Sljedeći događaj na koji ste bili pozvani je otkazan.';
$l['eventAccepted']='Prihvatili ste događaj.';
$l['eventScheduledIn']='Događaj je upisan u vaš kalendar %s sa statusom %s.';
$l['eventDeclined']="Odbili ste događaj.";
$l['eventUpdatedIn']='Događaj u kalendaru %s je ažuriran sa statusom %s';

$l['months']= 'mjeseci';
$l['statuses']['CANCELLED']= 'Otkazano';
$l['updateReponses']['ACCEPTED']='%s je prihvatio događaj %s';
$l['updateReponses']['DECLINED']='%s je odbio događaj %s';
$l['updateReponses']['TENTATIVE']='%s je označio događaj %s kao upitan';
$l['directUrl']='Izravni URL';
$l['errorOrganizerOnly']= 'Niste ovlašteni uređivati ovaj događaj jer niste organizator događaja.';
$l['errorOrganizerOnlyTitle']= 'Niste organizator događaja';
$l['cantRemoveOrganizer']="Nemožete ukloniti organizatora";
$l['calendarColor']='Boja kalendara';
$l['eventDeleted']="Događaj je obrisan iz vašeg kalendara";
$l['attendance']='Prisustvovanje';
$l['organizer']='Organizator';
$l['notifyOrganizer']="Obavjesti organizatora putem e-pošte o mojoj odluci";
$l['iWillAttend']="Prisustvovat ću";
$l['iMightAttend']="Možda ću prisustvovati";
$l['iWillNotAttend']="Neću prisustvovati";
$l['iWillDecideLater']="Nisam još odlučio";
$l['eventUpdated']="Sljedeći događaj je ažuriran od strane organizatora";
$l['notifyCancelParticipants']='Želite li poslati poruku od otkazivanju sastanka sudionicima?';
$l['notifyCancelOrganizer']='Želite li obavjestiti organizatora putem e-pošte da neće prisustvovati?';
$l['notifyParticipants']='Obavjesti sudionike?';
$l['sendNotificationTitle']='Pošalji obavjest?';
$l['sendNotification']='Želite li obavjestiti sudionike putem e-pošte?';
$l['openCalendar']='Otvori kalendar';
$l['createPermission']="Stvori dozvolu";

$l['pastAppointments']='Prošli događaji';
$l['show_holidays']="Prikaži praznike";
$l['participant']='Sudionik';
$l['clickForAttendance']='Kliknite ovdje kako biste postavili svoj status prisustvovanja';
$l['viewDay']='Dan';
$l['viewMorning']='Jutro';
$l['viewAfternoon']='Poslije podne';
$l['viewEvening']='Večer';
$l['cronEventAndTaskReportMailer']='Slanje današnjih događaja i zadataka';
$l['cronEventAndTaskReportMailerDescription']='Šalje e-poštu sa današnjim događajima i zadacima svakom korisniku u cron-u';
$l['cronEventAndTaskReportMailerPdfSubtitle']='Današnji događaji i zadaci';
$l['cronEventAndTaskReportMailerSubject']='Današnji događaji i zadaci';
$l['cronEventAndTaskReportMailerContent']='Možete pronaći popis današnjih događaja i zadataka u priloženom PDF-u.';