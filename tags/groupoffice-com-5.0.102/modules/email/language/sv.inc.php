<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = 'E-post';
$lang['email']['description'] = 'Funktionsrik, webbaserad e-postklient. Användare kan sända och ta emot e-post.';

$lang['link_type'][9]= 'E-post';

$lang['email']['feedbackNoReciepent'] = 'Du angav ingen mottagare';
$lang['email']['feedbackSMTPProblem'] = 'Ett problem uppstod i kommunikationen med SMTP-servern:';
$lang['email']['feedbackUnexpectedError'] = 'Ett oväntat problem uppstod vid skapandet av e-post:';
$lang['email']['feedbackCreateFolderFailed'] = 'Det gick inte att skapa mappen';
$lang['email']['feedbackDeleteFolderFailed'] = 'Kunde inte ta bort mappen';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Det gick inte att prenumerera på mappen';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Det gick inte att av-prenumerera mappen';
$lang['email']['feedbackCannotConnect'] = 'Kunde inte ansluta till %1$s på port %3$s<br /><br /> E-postservern svarade: %2$s';
$lang['email']['inbox'] = 'Inkorg';

$lang['email']['spam']='Skräppost';
$lang['email']['trash']='Papperskorgen';
$lang['email']['sent']='Skickat';
$lang['email']['drafts']='Utkast';

$lang['email']['no_subject']='Inget ämne';
$lang['email']['to']='Till';
$lang['email']['from']='Från';
$lang['email']['subject']='Ämne';
$lang['email']['no_recipients']='Dold mottagare';
$lang['email']['original_message']='--- Originalmeddelande följer ---';
$lang['email']['attachments']='Bilagor';

$lang['email']['notification_subject']='Läst: %s';
$lang['email']['notification_body']='Ditt meddelande med ämne "%s" lästes vid %s';

$lang['email']['errorGettingMessage']='Det gick inte att hämta meddelande från servern';
$lang['email']['no_recipients_drafts']='Ingen mottagare';
$lang['email']['usage_limit'] = '%s av %s används';
$lang['email']['usage'] = '%s används';

$lang['email']['event']='Möte';
$lang['email']['calendar']='kalender';

$lang['email']['quotaError']='Din brevlåda är full. Börja med att tömma Papperskorgen. Om den redan är tom och din brevlåda fortfarande är full måste du inaktivera Papperskorgen för att kunna radera meddelanden från andra mappar. Du inaktiverar den genom att klicka på knappen Administration i din e-post, välj Konton, dubbelklicka på ditt eget konto, gå till fliken Mappar, klicka på Hantera mappar och bocka ur rutan framför Papperskorgen.';

$lang['email']['draftsDisabled']="Meddelandet kunde inte sparas eftersom mappen \'Drafts\' är inaktiverad.<br /><br /> Aktivera den genom att klicka på knappen Administration i din e-post, välj Konton, dubbelklicka på ditt eget konto, gå till fliken Mappar, klicka på Hantera mappar och kryssa i rutan framför \'Drafts\'.";
$lang['email']['noSaveWithPop3']='Meddelandet kunde inte sparas eftersom ett POP3-konto inte har stöd för detta.';

$lang['email']['goAlreadyStarted']='Group-Office har redan startats. E-posten laddas nu i Group-Office. Stäng det här fönstret och skriv ditt meddelande i Group-Office.';

//Tisdagen, den 07-04-2009 kl 8:58 skrev Group-Office Administrator <test@intermeshdev.nl>:
$lang['email']['replyHeader']='%sen, den %s kl %s skrev %s:';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Alias';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Alias';

$lang['email']['noUidNext']='Din e-postserver stöder inte UIDNEXT. Mappen \'Drafts\' inaktiveras nu automatiskt för det här kontot.';

$lang['email']['disable_trash_folder']='Misslyckades med att flytta e-post till Papperskorgen. Detta kan bero på att du har slut på diskutrymme. Du kan frigöra utrymme genom att klicka på knappen Administration i din e-post, välj Konton, dubbelklicka på ditt eget konto, gå till fliken Mappar, klicka på Hantera mappar och bocka ur rutan framför Papperskorgen.';

$lang['email']['error_move_folder']='Kunde inte flytta mappen';

$lang['email']['error_getaddrinfo']='Ogiltig värdadress angiven';
$lang['email']['error_authentication']='Ogiltigt användarnamn eller lösenord';
$lang['email']['error_connection_refused']='Anslutningen nekades. Kontrollera värdadressen och portnumret.';

$lang['email']['iCalendar_event_invitation']='Detta meddelande innehåller en inbjudan till en händelse.';
$lang['email']['iCalendar_event_not_found']='Detta meddelande innehåller en uppdatering till en händelse som inte längre finns.';
$lang['email']['iCalendar_update_available']='Detta meddelande innehåller en uppdatering till en befintlig händelse.';
$lang['email']['iCalendar_update_old']='Detta meddelande innehåller en händelse som redan har blivit hanterat.';
$lang['email']['iCalendar_event_cancelled']='Detta meddelande innehåller en kancellering av en händelse.';
$lang['email']['iCalendar_event_invitation_declined']='Detta meddelande innehåller en inbjudan till en händelse som du har avböjt.';