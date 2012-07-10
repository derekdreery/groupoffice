<?php


$l['name'] = 'Kalender';
$l['description'] = 'Modul som ger användare tillgång till en eller flera kalendrar. Kalendrar kan även delas mellan användare eller grupper.';
$l['groupView'] = 'Gruppvy';
$l['event']= 'Händelse';
$l['startsAt']= 'Börjar vid';
$l['endsAt']= 'Slutar vid';
$l['exceptionNoCalendarID'] = 'STOP: Inget kalender-ID!';
$l['appointment'] = 'Möte:';
$l['allTogether'] = 'Alla tillsammans';
$l['location']= 'Plats';
$l['invited']= 'Du är inbjuden till följande händelse';
$l['acccept_question']= 'Accepterar du denna händelse?';
$l['accept']= 'Acceptera';
$l['decline']= 'Avböj';
$l['bad_event']= 'Händelsen existerar inte längre';
$l['subject']= 'Ämne';
$l['status']= 'Status';
$l['statuses']['NEEDS-ACTION'] = 'Åtgärd krävs';
$l['statuses']['ACCEPTED'] = 'Accepterad';
$l['statuses']['DECLINED'] = 'Avvisad';
$l['statuses']['TENTATIVE'] = 'Preliminär';
$l['statuses']['DELEGATED'] = 'Delegerad';
$l['statuses']['COMPLETED'] = 'Avslutad';
$l['statuses']['IN-PROCESS'] = 'Pågående';
$l['statuses']['CONFIRMED'] = 'Bekräftad';
$l['accept_mail_subject'] = 'Inbjudan till \'%s\' accepterad';
$l['accept_mail_body'] = '%s har accepterat din inbjudan till:';
$l['decline_mail_subject'] = 'Inbjudan till \'%s\' avböjd';
$l['decline_mail_body'] = '% s har avböjt din inbjudan till:';
$l['location']= 'Plats';
$l['and']= 'och';
$l['repeats'] = 'Upprepas varje %s';
$l['repeats_at'] = 'Upprepas varje %s på %sen';//t.ex. Upprepas varje månad på den första Måndagen;
$l['repeats_at_not_every'] = 'Upprepas med %s %ss mellanrum på %sar';//t.ex. Upprepas med 2 veckors mellanrum på Måndagar;
$l['repeats_not_every'] = 'Upprepas varje %s %s';
$l['until']= 'tills'; ;
$l['not_invited']= 'Du är inte inbjuden till den här händelsen. Du kanske behöver logga in som en annan användare.';
$l['accept_title']= 'Acceptera';
$l['accept_confirm']= 'Ägaren kommer meddelas att du accepterat händelsen';
$l['decline_title']= 'Avböj';
$l['decline_confirm']= 'Ägaren kommer meddelas att du avböjt händelsen';
$l['cumulative']= 'Ogiltig regel för upprepning. Nästa händelse kan inte börja innan den föregående har avslutats.';
$l['already_accepted']= 'Du har redan accepterat den här händelsen.';
$l['private']= 'Privat';
$l['import_success']= '%s händelser importerades';
$l['printTimeFormat']='Från %s till %s';
$l['printLocationFormat']=' på platsen "%s"';
$l['printPage']='Sida %s av %s';
$l['printList']='Lista med möten';
$l['printAllDaySingle']='Hela dagen';
$l['printAllDayMultiple']='Hela dagen från %s till %s';
$l['calendars']='Kalendrar';
$l['open_resource']='Öppna bokning';
$l['resource_mail_subject']='Resursen \'%s\' bokades för \'%s\' på \'%s\'';//%s is resource name, %s is event name, %s is start date;
$l['resource_mail_body']='%s har bokat resursen \'%s\'. Du är ansvarig för den här resursen. Vänligen öppna bokningen för att godkänna eller neka den.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['resource_modified_mail_subject']='Resursen \'%s\' som bokats för \'%s\' på \'%s\' har ändrats';//%s is resource name, %s is event name, %s is start date;
$l['resource_modified_mail_body']='%s har ändrat en bokning av resursen \'%s\'. Du är ansvarig för den här resursen. Vänligen öppna bokningen för att godkänna eller neka den.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['your_resource_modified_mail_subject']='Din bokning av \'%s\' för \'%s\' på \'%s\' har ändrats';//is resource name, %s is event name, %s is start date;
$l['your_resource_modified_mail_body']='%s har ändrat din bokning av resursen \'%s\'.';
$l['your_resource_accepted_mail_subject']='Din bokning av \'%s\' på \'%s\' har accepterats';//%s is resource name, %s is start date;
$l['your_resource_accepted_mail_body']='%s har accepterat din bokning av resursen \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['your_resource_declined_mail_subject']='Din bokning av \'%s\' på \'%s\' har nekats';//%s is resource name, %s is start date;
$l['your_resource_declined_mail_body']='%s har nekat din bokning av resursen \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['birthday_name']='Födelsedag: {NAME}';
$l['birthday_desc']='{NAME} har fyllt {AGE} idag';
$l['unauthorized_participants_write']='Du har otillräcklig behörighet för att schemalägga möten för följande användare:<br /><br />{NAMES}<br /><br />Du kan istället skicka en inbjudan så användaren själv kan acceptera och schemalägga mötet.';
$l['noCalSelected'] = 'Ingen kalender har valts för den här översikten. Välj minst en kalender under Administration.';
$l['month_times'][1]='den första';
$l['month_times'][2]='den andra';
$l['month_times'][3]='den tredje';
$l['month_times'][4]='den fjärde';
$l['month_times'][5]='den femte';
$l['rightClickToCopy']='Högerklicka för att kopiera länkadressen';
$l['invitation']='Inbjudan';
$l['invitation_update']='Uppdaterad inbjudan';
$l['cancellation']='Kancellering';
$l['non_selected'] = 'i icke-vald kalender';
$l['linkIfCalendarNotSupported']='Använd länkarna nedan endast om din mejlklient saknar stöd för kalenderfunktioner.';

$l["addressbook"]= 'Adressbok';
$l["appointment"]= 'Möte';
$l["appointments"]= 'Möten';
$l["recurrence"]= 'Återkommande';
$l["options"]= 'Alternativ';
$l["repeatForever"]= 'Upprepa för evigt';
$l["repeatEvery"]= 'Upprepa varje';
$l["repeatUntil"]= 'Upprepa tills';
$l["busy"]= 'Visa som upptagen';
$l["allDay"]= 'Tidpunkt kan inte anges';
$l["navigation"]= 'Navigering';
$l["oneDay"]= '1 Dag';
$l["fiveDays"]= '5 Dagar';
$l["sevenDays"]= '7 Dagar';
$l["month"]= 'Månad';
$l["recurringEvent"]= 'Återkommande händelse';
$l["deleteRecurringEvent"]= 'Vill du radera en enstaka eller alla förekomster av denna återkommande händelse?';
$l["singleOccurence"]= 'Enstaka förekomst';
$l["entireSeries"]= 'Hela serien';
$l["calendar"]= 'Kalender';
$l["calendars"]= 'Kalendrar';
$l["views"]= 'Vyer';
$l["administration"]= 'Administration';
$l["needsAction"]= 'Åtgärd krävs';
$l["accepted"]= 'Accepterad';
$l["declined"]= 'Avvisad';
$l["tentative"]= 'Preliminär';
$l["delegated"]= 'Delegerad';
$l["noRecurrence"]= 'Ingen upprepning';
$l["notRespondedYet"]= 'Inte svarat ännu';
$l["days"]= 'dagar';
$l["weeks"]= 'veckor';
$l["monthsByDate"]= 'månader efter datum';
$l["monthsByDay"]= 'månader efter dag';
$l["years"]= 'år';
$l["atDays"]= 'På dagar';
$l["noReminder"]= 'Ingen påminnelse';
$l["reminder"]= 'Påminnelse';
$l["participants"]= 'Deltagare';
$l["checkAvailability"]= 'Kontrollera tillgänglighet';
$l["sendInvitation"]= 'Skicka inbjudan';
$l["emailSendingNotConfigured"]= 'E-postsändning är inte konfigurerad.';
$l["privateEvent"]= 'Privat';
$l["noInformationAvailable"]= 'Ingen information tillgänglig';
$l["noParticipantsToDisplay"]= 'Ingen deltagare att visa';
$l["previousDay"]= 'Föregående dag';
$l["nextDay"]= 'Nästa dag';
$l["noAppointmentsToDisplay"]= 'Inga möten att visa';
$l["selectCalendar"]= 'Välj kalender';
$l["selectCalendarForAppointment"]= 'Välj kalender att boka in detta möte i';
$l["closeWindow"]= 'Mötet har godkänts och schemalagts. Du kan stänga detta fönster.';
$l["list"]= 'Lista';
$l["editRecurringEvent"]= 'Vill du redigera denna händelse eller hela serien?';
$l["selectIcalendarFile"]= 'Välj en iCalendar (*.ics)-fil';
$l["location"]= 'Plats';
$l["startsAt"]= 'Börjar vid';
$l["endsAt"]= 'Slutar vid';
$l["eventDefaults"]='Standardinställning för bokningar';
$l["importToCalendar"]='Lägg till bokning direkt i kalendrarna';
$l["default_calendar"]='Standardkalender';
$l["status"]='Status';
$l["resource_groups"]='Resursgrupper';
$l["resource_group"]='Resursgrupp';
$l["resources"]='Resurser';
$l["resource"]='Resurs';
$l["calendar_group"]='Kalendergrupp';
$l["admins"]='Administratörer';
$l["no_group_selected"]='Det finns fel i ditt formulär. Du måste välja en grupp för den här resursen.';
$l["visibleCalendars"]='Synliga kalendrar';
$l["visible"]='Synlig';
$l["group"]='Grupp';
$l["no_status"]='Ny';
$l["no_custom_fields"]='Inga extra alternativ finns tillgängliga.';
$l["show_bdays"]='Visa födelsedagar från adressboken';
$l["show_tasks"]='Visa uppgifter från uppgiftslistor';
$l["myCalendar"]='Min kalender';
$l["merge"]='Slå ihop';
$l["ownColor"]= 'Ge varje kalender en egen färg';
$l["ignoreConflictsTitle"]= 'Ignorera konflikt?';
$l["ignoreConflictsMsg"]= 'Den här händelsen sammanfaller med en annan bokning i din kalender. Vill du spara den ändå?';
$l["resourceConflictTitle"]= 'Resurskonflikt';
$l["resourceConflictMsg"]= 'En eller flera resurser i den här bokningen används redan vid den angivna tidpunkten:</br>';
$l["view"]= 'Vy';
$l["calendarsPermissions"]='Kalenderbehörigheter';
$l["resourcesPermissions"]='Resursbehörigheter';
$l["categories"]='Kategorier';
$l["category"]='Kategori';
$l["globalCategory"]='Global kategori';
$l["selectCategory"]='Välj kategori';
$l["duration"]='Tidsutrymme';
$l["move"]='Flytta';
$l["showInfo"]='Detaljer';
$l["copyEvent"]='Kopiera händelse';
$l["moveEvent"]='Flytta händelse';
$l["eventInfo"]='Händelse detaljer';
$l["isOrganizer"]='Organisatör';
$l["sendInvitationInitial"]='Vill du skicka mötesinbjudan till deltagarna?';
$l["sendInvitationUpdate"]='Vill du skicka uppdaterad mötesinformation till deltagarna?';
$l["sendCancellation"]='Vill du skicka ett avbokningsmeddelande till alla deltagarna?';
$l["forthcomingAppointments"]='Kommande bokningar';
$l["quarterShort"]= 'Q';
$l["confirmed"]= 'Bekräftat';
$l["globalCategories"]='Globala kategorier';
$l["globalsettings_templatelabel"]= 'Mall';
$l["globalsettings_allchangelabel"]= 'Döp om alla existerande';
$l["globalsettings_renameall"]= 'Är det säkert att du vill döpa om alla standardanvändarkalendrar?';
$l["publishICS"]='Publicera iCalendar-file med senaste månaden och kommande händelser. Varning! Kalendern blir läsbar för alla.';
$l["addTimeRegistration"]='Importera till tidsregistrering';
$l["showNotBusy"]='Visa inte nya reserveringar som upptagna';
$l["sendEmailParticipants"]= 'Skapa e-post för deltagare';
$l['eventAccepted']='Du har accepterat händelsen.';
$l['eventScheduledIn']='Händelsen har lagts in i din kalender %s med status %s.';
$l['eventDeclined']="Du har avböjt händelsen.";
$l['eventUpdatedIn']='Händelsen i kalendern %s har uppdaterats med status %s';
$l['updateReponses'][1]='%s har accepterat händelsen %s';
$l['updateReponses'][2]='%s har avböjt händelsen %s';
$l['updateReponses'][3]='%s har markerat händelsen %s som preliminär';
