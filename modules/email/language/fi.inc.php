<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = 'Sähköposti';
$lang['email']['description'] = 'Sähköpostimoduuli; Pieni webpohjainen sähköpostin lukuohjelma. Kaikki käyttäjät voivat lähettää, vastaanottaa ja edelleen välittää sähköpostiviestejä';

$lang['link_type'][9]='Sähköposti';

$lang['email']['feedbackNoReciepent'] = 'Et syöttänyt vastaanottajaa';
$lang['email']['feedbackSMTPProblem'] = 'Yhteydessä SMTP-palveluun oli ongelmia: ';
$lang['email']['feedbackUnexpectedError'] = 'Odottamaton ongelma sähköpostiviestin rakentamisessa: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Hakemiston luominen epäonnistui';
$lang['email']['feedbackDeleteFolderFailed'] = 'Hakemiston poistaminen epäonnistui';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Hakemiston tilaaminen epäonnistui';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Hakemiston tilaamisen peruminen epäonnistui';
$lang['email']['feedbackCannotConnect'] = 'Yhteys kohteeseen %1$s porttiin %3$s epäonnistui<br /><br />Sähköpostipalvelin palautti: %2$s';
$lang['email']['inbox'] = 'Saapuneet';

$lang['email']['spam']='Roskaposti';
$lang['email']['trash']='Roskakori';
$lang['email']['sent']='Lähetetyt';
$lang['email']['drafts']='Luonnokset';

$lang['email']['no_subject']='Ei aihetta';
$lang['email']['to']='Vastaanottaja';
$lang['email']['from']='Lähettäjä';
$lang['email']['subject']='Aihe';
$lang['email']['no_recipients']='Salassapidetyt vastaanottajat';
$lang['email']['original_message']='--- Alkuperäinen viesti seuraa ---';
$lang['email']['attachments']='Liitteet';

$lang['email']['notification_subject']='Luettu: %s';
$lang['email']['notification_body']='Viestinne, jonka aihe oli "%s" luettiin %s';

$lang['email']['errorGettingMessage']='Viestiä ei saatu noudetuksi palvelimelta';
$lang['email']['no_recipients_drafts']='Ei vastaanottajia';
$lang['email']['usage_limit'] = '%s / %s käytetty';
$lang['email']['usage'] = '%s käytetty';

$lang['email']['event']='Merkintä';
$lang['email']['calendar']='kalenteri';

$lang['email']['quotaError']="Sähköpostisi on täynnä. Tyhjennä roskakori ensin. Jos se on jo tyhjä ja sähköpostisi on yhä täynnä, sinun täytyy poistaa roskakori ensin käytöstä voidaksesi poistaa sähköposteja muista kansioista. Voit poistaa sen käytöstä menemällä :\n\nAsetukset -> Tilit -> Tuplaklikkaa tiliä -> Kansiot.";

$lang['email']['draftsDisabled']="Viestiä ei voitu tallettaa, sillä 'luonnokset'-kansio on poistettu käytöstä.<br /><br />Mene Asetukset -> Tilit -> Tuplaklikkaa tiliä -> Kansiot määritelläksesi sen.";
$lang['email']['noSaveWithPop3']='Viestiä ei voitu tallentaa, sillä POP3-tili ei tue tätä ominaisuutta.';
