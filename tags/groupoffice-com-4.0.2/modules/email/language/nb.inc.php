<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = 'E-post';
$lang['email']['description'] = 'Fullverdig e-postklient. Hver bruker kan sende og motta e-post.';

$lang['link_type'][9]='E-post';

$lang['email']['feedbackNoReciepent'] = 'Du må angi en mottager!';
$lang['email']['feedbackSMTPProblem'] = 'Problemer med kommunikasjon med SMTP: ';
$lang['email']['feedbackUnexpectedError'] = 'Uventet problem ved bygging av e-post: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Kunne ikke lage mappen';
$lang['email']['feedbackDeleteFolderFailed'] = 'Kunne ikke slette mappen';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Kunne ikke abonnere på mappen';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Kunne ikke deaktivere abonnement på mappen';
$lang['email']['feedbackCannotConnect'] = 'Kan ikke koble til %1$s via port %3$s<br /><br />Mailserveren returnerer: %2$s';
$lang['email']['inbox'] = 'Inbox';

$lang['email']['spam']='Søppelpost';
$lang['email']['trash']='Papirkurv';
$lang['email']['sent']='Sendt';
$lang['email']['drafts']='Utkast';

$lang['email']['no_subject']='Emne mangler';
$lang['email']['to']='Til';
$lang['email']['from']='Fra';
$lang['email']['subject']='Emne';
$lang['email']['no_recipients']='Undisclosed recipients';
$lang['email']['original_message']='--- Originalmelding følger her ---';
$lang['email']['attachments']='Vedlegg';

$lang['email']['notification_subject']='Lest: %s';
$lang['email']['notification_body']='Din e-post med emne "%s" ble vist på mottagers skjerm den %s';

$lang['email']['errorGettingMessage']='Kan ikke hente meldinger fra serveren';
$lang['email']['no_recipients_drafts']='Ingen mottagere';
$lang['email']['usage_limit'] = '%s av %s brukt';
$lang['email']['usage'] = '%s brukt';

$lang['email']['event']='Avtale';
$lang['email']['calendar']='kalender';

$lang['email']['quotaError']="Din e-postboks er full. Forsøk først å tømme papirkurven. Hvis e-postboksen fotsatt er full må du slå av papirkurven for å slette meldinger i andre mapper. Du kan slå av papirkurven under:\n\nInnstillinger -> Kontoer -> Dobbeltklikk på konto -> Mapper.";

$lang['email']['draftsDisabled']="Meldingen kan ikke lagres fordi mappen 'Utkast' er slått av.<br /><br />Gå til Innstillinger -> Kontoer -> Dobbeltklikk på konto -> Mapper for konfigurering.";
$lang['email']['noSaveWithPop3']='Meldingen kan ikke lagres fordi POP3 kontoer ikke støtter dette.';

$lang['email']['goAlreadyStarted']='Group-Office kjører allerede. E-postprogrammet er nå lastet i Group-Office. Lukk dette vinduet og skriv meldingen din i Group-Office.';

//At Tuesday, 07-04-2009 on 8:58 Group-Office Administrator <test@intermeshdev.nl> wrote:
$lang['email']['replyHeader']='%s, %s kl %s skrev %s :';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Aliaser';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Aliaser';

$lang['email']['noUidNext']='Din mailserver støtter ikke UIDNEXT. Mappen \'Utkast\' er derfor automatisk slått av for denne kontoen.';

$lang['email']['disable_trash_folder']='Feil ved flytting av e-post til papirkurv. Årsaken kan være at du ikke har mer diskplass. Du kan bare frigjøre plass ved å deaktivere mappen for papirkurv på: Administrasjon -> Kontoer -> Dobbeltklikk på din konto -> Mapper';

$lang['email']['error_move_folder']='Kunne ikke flytte mappen';
$lang['email']['error_getaddrinfo']='Ugyldig serveradresse angitt';
$lang['email']['error_authentication']='Ugyldig brukernavn eller passord';
$lang['email']['error_connection_refused']='Tilkoblingen ble avvist. Kontroller server og portnummer.';
$lang['email']['iCalendar_event_invitation']='Meldingen inneholder en invitasjon til en hendelse.';
$lang['email']['iCalendar_event_not_found']='Meldingen inneholder en oppdatering av en hendelse som ikke eksisterer lenger.';
$lang['email']['iCalendar_update_available']='Meldingen inneholder en oppdatering av en eksisterende hendelse.';
$lang['email']['iCalendar_update_old']='Meldingen inneholder en hendelse som allerede er behandlet.';
$lang['email']['iCalendar_event_cancelled']='Meldingen inneholder kansellering av en hendelse.';
$lang['email']['iCalendar_event_invitation_declined']='Meldingen inneholder en invitasjon til en hendelse du har avvist.';
$lang['email']['untilDateError']='Forsøkte å behandle fram til angitt dato, men ble avbrutt på grunn av at det oppstod en feil';