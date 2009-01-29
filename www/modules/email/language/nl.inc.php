<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('email'));
$lang['email']['name'] = 'Email';
$lang['email']['description'] = 'Email module; Een kleine web-based email client. Het is voor iedere gebruiker mogelijk om emails te verzenden, ontvangen en doorsturen';
$lang['email']['feedbackNoReciepent'] = 'U heeft geen ontvanger ingevuld';
$lang['email']['feedbackSMTPProblem'] = 'Er was een probleem in de communicatie met SMTP: ';
$lang['email']['feedbackUnexpectedError'] = 'Er was een onverwacht probleem bij het opstellen van de email: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Map kan niet worden gemaakt';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Fout bij het opslaan van de gegevens';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Fout bij het opslaan van de gegevens';
$lang['email']['feedbackCannotConnect'] = 'Kan geen verbinding maken met %1$s op poort %3$s<br /><br />De mail server antwoordde: %2$s';
$lang['email']['inbox'] = 'Postvak in';

$lang['email']['spam']='Spam';
$lang['email']['trash']='Prullenbak';
$lang['email']['sent']='Verzonden items';
$lang['email']['drafts']='Concepten';

$lang['email']['no_subject']='Geen onderwerp';
$lang['email']['to']='Naar';
$lang['email']['from']='Van';
$lang['email']['subject']='Onderwerp';
$lang['email']['no_recipients']='Vertrouwelijke ontvangers';
$lang['email']['original_message']='--- Origineel bericht volgt ---';
$lang['email']['attachments']='Bijlagen';
$lang['link_type'][9]='E-mail';

$lang['email']['notification_subject']='Gelezen: %s';
$lang['email']['notification_body']='Uw bericht met onderwerp "%s" is getoond op %s';

$lang['email']['errorGettingMessage']='Kon bericht niet ophalen van server';
$lang['email']['no_recipients_drafts']='Geen ontvangers';
$lang['email']['usage_limit'] = '%s van %s gebruikt';
$lang['email']['usage'] = '%s gebruikt';
$lang['email']['feedbackDeleteFolderFailed']= 'Failed to delete folder';

$lang['email']['event']='Afspraak';
$lang['email']['calendar']='agenda';

$lang['email']['quotaError']="Uw mailbox is vol. Leeg eerst uw 'Prullenbak' map. Indien die map al leeg is en uw mailbox is nog steeds vol, dan dient u de 'Prullenbak' map uit te schakelen om berichten uit andere mappen te kunnen verwijderen. U kunt de map uitschakkelen bij:\n\nInstellingen -> Accounts -> Dubbelklik account -> Mappen.";
