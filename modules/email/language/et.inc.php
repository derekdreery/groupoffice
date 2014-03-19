<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = 'E-mail';
$lang['email']['description'] = 'Täisfunktsionaale e-posti klient. Igal kasutajal on võimalik saaata ja vastu võtta e-kirju';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = 'Sa ei sisestanud saajat';
$lang['email']['feedbackSMTPProblem'] = 'Probleem ühenduse loomisel SMTP-ga: ';
$lang['email']['feedbackUnexpectedError'] = 'Probleem e-kirja tegemisel: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Kausta tegemine ebaõnnestus';
$lang['email']['feedbackDeleteFolderFailed'] = 'Kausta kustutamine ebaõnnestus';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Kausta tellimine ebaõnnestus';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Kausta tellimise lõpetamine ebaõnnestus';
$lang['email']['feedbackCannotConnect'] = 'Ühenduse loomine %1$s pordi %3$s kaudu ebaõnnestus<br /><br />Meiliserverilt tuli vastus: %2$s';
$lang['email']['inbox'] = 'Postkast';

$lang['email']['spam']='Rämpspost';
$lang['email']['trash']='Prügikast';
$lang['email']['sent']='Saadetud kirjad';
$lang['email']['drafts']='Mustandid';

$lang['email']['no_subject']='Teema puudub';
$lang['email']['to']='Kellel';
$lang['email']['from']='Kellelt';
$lang['email']['subject']='Teemat';
$lang['email']['no_recipients']='Avalikustamata saajad';
$lang['email']['original_message']='--- Originaalsõnum ---';
$lang['email']['attachments']='Manused';

$lang['email']['notification_subject']='Loetud: %s';
$lang['email']['notification_body']='Sinu sõnum teemal "%s" on vaadatud %s';

$lang['email']['errorGettingMessage']='Sõnumi hankimine serverist ebaõnnestus';
$lang['email']['no_recipients_drafts']='Saaja puudub';
$lang['email']['usage_limit'] = '%s  %s -st on kasutatud';
$lang['email']['usage'] = '%s kasutatud';

$lang['email']['event']='Kontumine';
$lang['email']['calendar']='kalender';

$lang['email']['quotaError']="Sinu postkast on täis. Tühjenda kõigepealt oma prügikast. Kui oled seda juba teinud ja prügikast on siiski täis, siis lülita välja prügikasti tühjendamine teistest kaustadest. Saad seda teha siin:\n\nSeaded -> Kontod -> Topeltklikk kontol -> Kaustad.";

$lang['email']['draftsDisabled']="Sõnumi salvestamine ebaõnnestus kuna 'Mustandid' kaust ei ole aktiveeritud.<br /><br />Seadistamiseks mine Seaded -> Kontod -> Topeltklikk kontol -> Kaustad.";
$lang['email']['noSaveWithPop3']='Sõnumit ei salvestatud kuna POP3 konto ei toeta seda.';

$lang['email']['goAlreadyStarted']='Group-Office on juba käivitatud. E-kirja koostajat laetakse. Kirja koostamiseks sulge see aken.';

//At Tuesday, 07-04-2009 on 8:58 Group-Office Administrator <test@intermeshdev.nl> wrote:
$lang['email']['replyHeader']='Kell %s, %s  %s %s kirjutas:';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Aliased';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Aliased';

$lang['email']['noUidNext']='Sinu meiliserver ei toeta UIDNEXT.  \'Mustandid\' kaust on selle konto jaoks kahjuks automaatselt välja lülitatud.';

$lang['email']['disable_trash_folder']='Kirja liigutamine prügikasti ebaõnnestus. Selle võis põhjustada serveriruumi puudus. ruumi saad vabastada prügikasti välja lülitades, minnes Administratsioon -> Kontod -> Topletklikk kontol -> Kaustad';

$lang['email']['error_move_folder']='Kausta liigutamine ebaõnnestus';

$lang['email']['error_getaddrinfo']='Vigane hosti aadress';
$lang['email']['error_authentication']='Vigane kasutajanimi või parool';
$lang['email']['error_connection_refused']='Ühendust ei lubatud. Kontrolli hosti või pordi numbrit';
$lang['email']['iCalendar_event_invitation']='See sõnum sisaldab mingisuguse sündmuse kutset';
$lang['email']['iCalendar_event_not_found']='See sõnum sisaldab mingisuguse sündmuse, mida enam ei ole, kutset';
$lang['email']['iCalendar_update_available']='See sõnum sisaldab mingisuguse sündmuse kutse uuendamise teadet.';
$lang['email']['iCalendar_update_old']='See sõnum sisaldab mingisuguse juba käsitletud sündmuse kutset';
$lang['email']['iCalendar_event_cancelled']='See sõnum sisaldab mingisuguse sündmuse kutse tühistamist';
$lang['email']['iCalendar_event_invitation_declined']='See sõnum sisaldab mingisuguse sinu poolt tagasi lükatud sündmuse kutset';