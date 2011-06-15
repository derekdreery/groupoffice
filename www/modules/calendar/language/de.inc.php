<?php
require(GO::language()->get_fallback_language_file('calendar'));
$lang['calendar']['name']='Kalender';
$lang['calendar']['description']='Modul zum Verwalten von Terminen';
$lang['link_type'][1]='Termin';
$lang['calendar']['groupView']='Gruppenansicht';
$lang['calendar']['event']='Ereignis';
$lang['calendar']['startsAt']='Beginnt am';
$lang['calendar']['endsAt']='Endet am';
$lang['calendar']['exceptionNoCalendarID']='ACHTUNG: Keine Kalender-ID!';
$lang['calendar']['appointment']='Termin: ';
$lang['calendar']['allTogether']='Alle zusammen';
$lang['calendar']['location']='Ort';
$lang['calendar']['invited']='Sie sind zu folgendem Termin eingeladen';
$lang['calendar']['acccept_question']='Möchten Sie diesen Termin wahrnehmen?';
$lang['calendar']['accept']='Annehmen';
$lang['calendar']['decline']='Ablehnen';
$lang['calendar']['bad_event']='Der Termin ist nicht mehr vorhanden';
$lang['calendar']['subject']='Betreff';
$lang['calendar']['status']='Status';
$lang['calendar']['statuses']['NEEDS-ACTION']='Handlungsbedarf';
$lang['calendar']['statuses']['ACCEPTED']='Angenommen';
$lang['calendar']['statuses']['DECLINED']='Abgelehnt';
$lang['calendar']['statuses']['TENTATIVE']='Vorläufig';
$lang['calendar']['statuses']['DELEGATED']='Aufgeteilt';
$lang['calendar']['statuses']['COMPLETED']='Erledigt';
$lang['calendar']['statuses']['IN-PROCESS']='In Bearbeitung';
$lang['calendar']['accept_mail_subject']='Einladung für \'%s\' angenommen';
$lang['calendar']['accept_mail_body']='%s hat ihre Einladung angenommen:';
$lang['calendar']['decline_mail_subject']='Einladung für \'%s\' abgelehnt';
$lang['calendar']['decline_mail_body']='%s hat Ihre Einladung abgelehnt:';
$lang['calendar']['location']='Ort';
$lang['calendar']['and']='und';
$lang['calendar']['repeats']='Wiederhole alle %s';
$lang['calendar']['repeats_at']='Wiederhole alle %s am %s';
$lang['calendar']['repeats_at_not_every']='Wiederhole alle %s %s am %s';
$lang['calendar']['until']='bis'; 
$lang['calendar']['not_invited']='Sie sind zu diesem Termin nicht eingeladen.';
$lang['calendar']['accept_title']='Akzeptiert';
$lang['calendar']['accept_confirm']='Der Eigentümer wird informiert, dass Sie den Termin angenommen haben';
$lang['calendar']['decline_title']='Abgelehnt';
$lang['calendar']['decline_confirm']='Der Eigentümer wird informiert, dass Sie den Termin abgelehnt haben';
$lang['calendar']['cumulative']='Falsche Wiederholung. Die Wiederholung kann nicht starten, bevor die Vorherige beendet ist.';
$lang['calendar']['already_accepted']='Sie haben diesen Termin schon angenommen.';
$lang['calendar']['private']='Privat';
$lang['calendar']['import_success']='%s Termine wurden importiert';
$lang['calendar']['printTimeFormat']='Von %s bis %s';
$lang['calendar']['printLocationFormat']=' in/bei "%s"';
$lang['calendar']['printPage']='Seite %s von %s';
$lang['calendar']['printList']='Terminliste';
$lang['calendar']['printAllDaySingle']='Ganzer Tag';
$lang['calendar']['printAllDayMultiple']='Ganze Tage vom %s bis zum %s';
$lang['calendar']['calendars']='Kalender';
$lang['calendar']['open_resource']='Freie Ressource';
$lang['calendar']['resource_mail_subject']='Die Ressource \'%s\', gebucht für \'%s\' am \'%s\'';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_mail_body']='%s hat die Ressource \'%s\' gebucht. Sie sind der Verwalter dieser Ressource. Bitte öffnen Sie die betreffende Buchung, um dieser zuzustimmen oder zu widersprechen.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['resource_modified_mail_subject']='Resource \'%s\' booking for \'%s\' on \'%s\' modified';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_modified_mail_body']='%s hat eine Buchung für die Ressource \'%s\' verändert. Sie sind der Verwalter dieser Ressource. Bitte öffnen Sie die betreffende Buchung, um dieser zuzustimmen oder zu widersprechen.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['your_resource_modified_mail_subject']='Ihre Buchung für \'%s\' am \'%s\' mit Status \'%s\' wurde verändert';//is resource name, %s is event name, %s is start date
$lang['calendar']['your_resource_modified_mail_body']='%s hat Ihre Buchung für die Ressource \'%s\' verändert.';
$lang['calendar']['your_resource_accepted_mail_subject']='Ihre Buchung für \'%s\' am \'%s\' wurde akzeptiert';//%s is resource name, %s is start date
$lang['calendar']['your_resource_accepted_mail_body']='%s hat Ihre Buchung für die Ressource \'%s\' akzeptiert.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['your_resource_declined_mail_subject']='Ihre Buchung für \'%s\' am \'%s\' wurde abgelehnt';//%s is resource name, %s is start date
$lang['calendar']['your_resource_declined_mail_body']='%s hat Ihre Buchung für die Ressource \'%s\' abgelehnt.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['birthday_name']='Geburtstag: {NAME}';
$lang['calendar']['birthday_desc']='{NAME} wurde heute {AGE}';
$lang['calendar']['unauthorized_participants_write']='Sie haben keine Befugnisse, Termine für die folgenden Benutzer festzulegen:<br /><br />{NAMES}<br /><br />Wenn Sie Ihnen eine Einladung schicken, werden die Benutzer den Terminen aber eventuell selbst zustimmen.';
$lang['calendar']['noCalSelected'] = 'Für diese Übersicht wurden keine Kalender ausgewählt. Wählen Sie in der Administration zumindest einen Kalender aus.';
$lang['calendar']['month_times'][1]='am Ersten';
$lang['calendar']['month_times'][2]='am Zweiten';
$lang['calendar']['month_times'][3]='am Dritten';
$lang['calendar']['month_times'][4]='am Vierten';
$lang['calendar']['month_times'][5]='am Fünften';
$lang['calendar']['repeats_not_every']= 'Wird alle %s %s wiederholt';
$lang['calendar']['rightClickToCopy']='Rechts klicken um Link zu kopieren';
$lang['calendar']['invitation']='Einladung';
$lang['calendar']['invitation_update']='Aktualisierung';
$lang['calendar']['cancellation']='Absage';
$lang['calendar']['non_selected']= 'nicht in ausgewähltem Kalendar';
