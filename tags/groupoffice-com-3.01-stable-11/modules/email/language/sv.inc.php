<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('email'));
$lang['email']['name'] = 'Email';
$lang['email']['description'] = 'E-post modul, Liten webbaserat e-postklient. Varje användare kommer att kunna sända, ta emot och vidarebefordra e-postmeddelanden';

$lang['link_type'][9]= 'E-post';

$lang['email']['feedbackNoReciepent'] = 'Du angav ingen mottagare';
$lang['email']['feedbackSMTPProblem'] = 'Det fanns ett problem i kommunikationen med SMTP:';
$lang['email']['feedbackUnexpectedError'] = 'Det var ett oväntat problem att bygga e-posten:';
$lang['email']['feedbackCreateFolderFailed'] = 'Det gick inte att skapa mappen';
$lang['email']['feedbackDeleteFolderFailed'] = 'Kunde inte ta bort mappen';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Det gick inte att prenumerera på mappen';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Det gick inte att av-prenumerera mappen';
$lang['email']['feedbackCannotConnect'] = 'Kunde inte ansluta till %1$s på port %3$s<br /><br /> E-postservern returnerar: %2$s';
$lang['email']['inbox'] = 'Inkorg';

$lang['email']['spam']= 'Skräppost';
$lang['email']['trash']= 'Papperskorgen';
$lang['email']['sent']= 'Skickat';
$lang['email']['drafts']= 'Utkast';

$lang['email']['no_subject']= 'Inget ämne';
$lang['email']['to']= 'Till';
$lang['email']['from']= 'Från';
$lang['email']['subject']= 'Ämne';
$lang['email']['no_recipients']= 'Hemlig mottagare';
$lang['email']['original_message']= '--- Originalmeddelande följer ---';
$lang['email']['attachments']= 'Bilagor';

$lang['email']['notification_subject']= 'Läs: %s';
$lang['email']['notification_body']= 'Ditt meddelande med ämne "%s" visades vid %s';

$lang['email']['errorGettingMessage']= 'Det gick inte att hämta meddelande från servern';
$lang['email']['no_recipients_drafts']= 'Ingen mottagare';
$lang['email']['usage_limit'] = '%s av% s används';
$lang['email']['usage'] = '%s används';

$lang['email']['event']= 'Möte';
$lang['email']['calendar']= 'kalender';

$lang['email']['quotaError']= 'Din brevlåda är full. Töm papperskorgen först. Om den redan är tom och din brevlåda fortfarande är full måste du avaktivera papperskorgen för att radera meddelanden från andra mappar. Du kan stänga av den på: \n\n -> Inställningar -> Konton -> Dubbelklicka konto -> mappar.';

$lang['email']['draftsDisabled']= "Meddelandet kunde inte sparas eftersom det 'Drafts' mappen är inaktiverad.<br /><br /> Gå till Inställningar -> Konton -> Dubbelklicka konto -> Mappar för att konfigurera den.";
$lang['email']['noSaveWithPop3']= 'Meddelandet kunde inte sparas eftersom ett POP3-konto har inte stöd för detta.';
