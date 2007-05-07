<?php
//Uncomment this line in new translations! LI 20.01.2006
require_once($GO_LANGUAGE->get_fallback_language_file('users'));

$strLogins = "Sisään kirjautumisia";
$strRegistrationDate = "Reksteröinti päivä";
$strUsers = "käyttäjiä";
$user_profile = "Käyttäjä profiili";
$ac_login_info = "Kirjautumis tiedot";
$ac_registration_time = "Rekisteröinti aika";
$ac_lastlogin = "Viime kirjautuinen";
$ac_logins = "Kirjautumisten kokonaismäärä";
$strMaxOf = "rekisteröitynä maksimimäärästä";
$strUsername = "Käyttäjänimi";
$delete_fail = "Et voi poistaa itseääsi ja superkäyttäjää.";
$admin_new_user = "Uusi käyttäjä";

$admin_modules = 'Modulit';
$admin_module_access = 'Tällä käyttäjällä on käyttöoikeus seuraavin moduleihin:';

$admin_password = 'Salasana';
$admin_confirm_password = 'Varmista salasana';
$admin_change_password = 'Muuta salasana';
$admin_pass_match = 'Salasana eivä olleet samat. Yritä uudelleen.';

//registration
$registration_personal_info = "Henkilökohtaiset tiedot";
$registration_account_info = "Käyttäjätili tiedot";
$registartion_username_text = "Anna käyttäjätunnus ja salasana käyttäjälle.";

$registration_question_text=<<<registration_question_text
Anna salainen kysymys johon vain tämä käyttäjä tietää vastauksen. Kysymys asetetaan siinä tapauksessa että käyttäjä unohtaa salasanansa.
Kun tähän kysymykseen annetaan oikea vastaus, lähtee sähkäposti rekisteröityy sähköposti osoitteeseen käyttäjätunnuksella ja uudella salasanalla kirjautumista varten.
registration_question_text;

$registration_text=<<<registration_text
Täytä tämä lomake ja Paina 'Ok' rekisteröityäksesi. *:lla merkityt kentät ovat pakollisisa.<br />
Nämä tiedot tulevat vain Group-Officen käyttöön joten emme voi antaa niitä kolmannelle osapuolelle.
registration_text;

$registartion_authcode_text = "Syötä auktorisointikoodi. Auktorisointikoodin o koodi jonka muut käyttäjät tarvitsevat lisätäkseen käyttäjän yhteytietohinsa ja näkenään tietosi.";
$registartion_confirm_authcode = "Varmista auktorisointikoodi";
$registration_visibility = "Tee tästä käyttäjästä näkyvä kaikille käyttäjille.";
$registration_success = "<h1>Onnnea!</h1>Olet onnituneesti rekisteröitynyt ja tervetulo viesti on lähetetty:";
$registration_failure = "Tietojesi rekisteröinnissä on tpahtunut virhe. Yritää uudestaan.";

$registration_folder_error = "Hekilökohtaisen kansion luominen epäonnistui. Ota yhteys meihin sen palauttamiseksi!";


$registration_email = "Luo sähköposti osoite tälle Group-Office käyttäjälle.";
$registration_samba = "Tiedosto & Tulostus palvelin.";
$registration_email_error = "Sähköposti tilin luonnissa tapahtui virhe. Ota yhteys tukipalveluun.";
$strPasswordConfirm = "Vamista salasana";
$strPassword = "Salasana";

$reg_company_info = "Yritys tiedot";

$max_user_limit = "Käyttäjämäärä raja saavutettu.";
$max_users_text = "Et voi rekisteröidä enemmän käyttäjiä. Ota yhteys tukipalveluun jos haluat lisätä käyttäjä määrää.";
$ml_sent_items = "Lähetetyt kohteet";
$ml_spam = "Roskaposti";
$ac_samba_user = "Käyttäjällä on pääsy tiedosto ja tulostus palvelimelle.";
$ml_email_only = "Vaditaan vain silloinkun et luo sähköposti tiliä";
$register_new_user = 'Uusi Group-Office tilaaja';
$register_thanks = '<h2>Kiitos</h2>Tilauksesi käsitellään niin nopeasti kuin mahdollista. Saat lisätietoja.';
$register_accept = 'Lisää käyttäjiin';
$register_addressbook = 'Lisää osoitekirjaan';
$register_title = 'Tilaa';


//1.98
$reg_language = 'Kieli';
$reg_theme = 'Tema';

//2.05
$admin_use = 'Käytä';
$admin_module = 'Moduuli';
$admin_manage = 'Käsittele';

$admin_groups = 'Käyttäjä ryhmät';
$admin_groups_user = 'Käyttäjä on seuraavien ryhmien jäsen';

//2.11

//Modified string (Added %groupoffice_title%)
$registration_mail_body=<<<registration_mail_body
Terve %title% %last_name%.

Sinut on rekisteröity %groupoffice_title%.
Toivomme että sinulle on hyötyä palvelusta.

Voit kirjautua seuraavin tiedoin:

Käyttäjätunnus: %username%
Salasana: %password%

Paina alapuolista linkkiä kirjautuaksesi:

registration_mail_body;

//2.13
$registration_mail_subject = "Tervetuloa %groupoffice_title%"; //changed string
$registration_title = "Rekisteröidy %groupoffice_title%"; //changed string

$lang_modules['users'] = 'Käyttäjät';

//2.14
$users_enabled = 'Tili käytössä';

$admin_groups_visible = 'Käyttäjä näkyy seuraaville käyttäjä ryhmille';
$admin_look_feel = 'Ulkoasu';
