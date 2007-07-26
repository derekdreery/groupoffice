<?php
//Uncomment this line in new translations! LI 20.01.2006
require_once($GO_LANGUAGE->get_fallback_language_file('addressbook'));

//contacts
$contacts_title = "Osoitekirja";
$contacts_no_contacts = "Yhteystietoja ei löytynyt.";
$contacts_members = "Jäsenet";
$contacts_contacts = "Yhteystiedot";
$contacts_add = "Uusi yhteystieto";
$contacts_groups = "Ryhmät";
$contacts_other = "Muut yhteydet";
$contacts_move_to_group = "Siirrä valitut yhteydet ryhmään...";
$groups_no_groups = "Sinulla ei ole ryhmiä";
$contacts_group = "Ryhmä";
$contacts_empty_group = "Käytä alasveto-valikkoa siirtääksesi yhteyksiä tähän ryhmään.";
$contacts_this_group = 'tämä ryhmä';

$add_contacts_text = "Täytä jäsenten sähköpostit ja auktorisointikoodit ja paina 'Ok'";
$add_contacts_success = "Käyttäjä lisätty";
$add_contacts_wrong = "Väärä sähköposti tai auktorisointikoodi.";
$add_personal_text = "Paina 'Tallenta' lisätäksesi yhteystiedot. *:llä merkityt kentät ovat pakolliset.";
$contacts_select = "valitse lisättävät yhteydet ja paina 'Ok'.";
$contacts_select_title = "Yhteystiedot";
$contact_profile = "Profiili";
$user_profile = "Käyttäjä profiili";
$contacts_authorise = "Auktorisointi";
$contacts_add_member = "Lisää tämä jäsen yhteyksiisi";
$strConfirmSynchronise = "Tämä päivittää yhteyden tiedoittolla jotka käyttäjä itse on lisännyt. Käsin tehdyt muutokset katoavat. Oletko varma että haluat jatkaa?";
$contacts_import = "Tuo";
$contacts_export = "Vie";
$contacts_export_text = "Valitse minkälaisen CSV:n haluat luoda.";
$contacts_delete = "Poista";

$contacts_import_text_group = "Valitse ryhmä ja CSV tiedosto. Paina 'Ok'.";
$fbNoFile = "Tiedostoa ei vastaanotettu";
$fbPleaseWait = "Odota, tiedostoa ladataan...";

$strNotIncluded = "Älä tuo";
$contacts_import_feedback = "Seuraavat sarakkeet löytyivät CSV tiedostosta.<br />";
$contacts_import_feedback .= "Valitse ryhmä jonka haluat tuoda tai automaattisesti luo ryhmiä tiedostossa olevan sarakkeen mukaan.<br />";
$contacts_import_feedback .= " Sovita vastaavat Group-Office sarakkeet yhteen tiedoston sarakkeisiin ja varmista että et valitse sarakkeita kahteen kertaan.";
$contacts_separator = "Sarakkeiden erotus merkki";
$contacts_no_separator = "Et syöttänyt erotus merkkiä.";
$fbFilename = "Tiedostonimi";
$contacts_import_title = "Tuo osoitekirje";
$contacts_import_success = "Onnistuneesti tuotu tiedosto";
$contacts_import_double = "Olet valinnut yhden tai usemman sarakkeen kahteen kertaan. Korjaa se ja yritä uudestaan.";
//changed:
$contacts_import_noname = 'Sinun pitää valita kentät \'Etu_nimi\' ja \'Suku nimi\' ';
$contacts_import_incompatible = "Yhteensopimaton tiedostomuoto.<br /><br />CSV tiedosto jossa kentäntät ovat erotetut toisistaan (piste)pilkulla.";
$strMember = "Jäsen";
$contact_exist_warning = "<b>Huom:</b> Tämä käyttäjä löytyy yhteystiedoista.";
$contacts_add_to_group = "Lisää valittu käyttäjät yhteyksiin ryhmässä...";
$contacts_add_to = "Lisää valittu käyttäjät yhteyksiin ";
$contacts_add_user = "lisää";
$contacts_edit = "Muokkaa";
$contacts_no_select = "Et valinnut mitään";
$contacts_this_contact = "tämä yhtetys";

$contacts_seperator_text = 'Arvot erottaa';
$contacts_quote = 'Arvojen sulkumerkki';
$contacts_clrf = 'Rivi päättyy';
$contacts_auto_group = 'Luo ryhmät automaattisesti tiedostosta';
$contacts_import_to_group = 'Tuo ryhmään';

$contacts_search = 'Haku';
$cmdSearch = 'Haku';
$contacts_search_text = "Paina kirjainta tai syötä hakulause ja paina 'Haku'. Voit käyttää * villi merkkinä.";
$contacts_results = 'tulokset';
$contacts_result = 'tulos';


$contacts_color = 'Väri';

$contacts_colors['black'] = 'Musta';
$contacts_colors['red'] = 'Punainen';
$contacts_colors['green'] = 'Vihreä';
$contacts_colors['blue'] = 'Sininen';
$contacts_colors['pink'] = 'Pinkki';
$contacts_colors['orange'] = 'Oranssi';
$contacts_colors['brown'] = 'Ruskea';

$ab_addressbooks = 'Osoitekirjat';
$ab_owner = 'Omistaja';
$ab_subscribed = 'Tilattu';
$ab_new_ab = 'Uusi osoitekirja';
$ab_addressbook_exists = 'Sillä nimellä oleva osoitekirja on jo olemassa';
$ab_addressbook = 'Osoitekirja';
$ab_no_addressbook = 'Ei osoitekirjaa';
$contacts_select_single = 'Valitse yhteys valitaksesi tämä henkilö';

$ab_group_exists = 'Tämän niminen osoitekirja on jo olemassa';
$ab_group_on_file = 'Ryhmä yhteydet perustuvat tiedostoon';
$ab_default = 'Oletus';

//translation required
$ab_edit_contact = 'Paina \'Ok\' muutosten tallettamiseksi. *:lla merkityt kentät vaaditaan';
$ab_group_on_file = 'Ryhmä yhteyden perustuvat tiedostoon';
$ab_default = 'Oletus';
$ab_browse = 'Selaa';
$ab_comment = 'Kommentti';

$ab_contact = 'Yhteystieto';
$ab_send_message = 'Uusi sähköposti';
$ab_new_event = 'Uusi tapahtuma';

$ab_templates = 'Mallit';
$ab_your_templates = 'Sinun';
$ab_all_templates = 'Kaikki';

$template_type[0] = 'Sähköposti';
$template_type[1] = 'OpenOffice.Org';
$ab_type = 'Tyyppi';
$ab_template_exists = 'Tämän niminen mallipohja on jo olemassa.';

$sir_madam['M'] = 'Herra'; //'Herra'
$sir_madam['F'] = 'Rouva'; //'Rouva'

$ab_upload_oo = 'Lataa OpenOffice.org mallipohjat';
$ab_upload_nofile = 'Virhe tiedoston lähetyksessä.';

$ab_oo_doc = 'Tekstitiedosto';
$ab_select_template = 'Valitse malli tälle dokumentille:';
$ab_add_field_data = 'Voit käyttää seuraavia arvoja dokumetissasi:';
$ab_datafield = 'Datakenttä';

$ab_no_template = 'Ei mallia';
$ab_new_note = 'Muistiinpano';

$ab_events = 'Tapaamiset';

$ab_new_contact = 'Uusi&nbsp;yhteystieto';
$ab_new_company = 'Uusi&nbsp;yritys';
$ab_companies = 'Yritykset';
$ab_company_properties = 'Yritys&nbsp;profiili';
$ab_bank_no = 'Pankki&nbsp;numero';
$ab_companies_count_single = 'yritys';
$ab_companies_count = 'yritykset';
$ab_no_companies = 'Yrityksiä ei löytynyt';
$ab_no_company = 'Yritystä ei löytynyt';

$ab_import_type = 'Tuonti';
$ab_no_company_found = 'Yrityksiä ei löytynyt';
$ab_search_company = 'Etsi yritys';
$ab_edit_company = 'Muuta yritysprofiilia';
$ab_new_group = 'Uusi ryhmä';
$ab_export_type = 'Vie';

$ab_custom_fields = 'Ylimääräinen&nbsp;datakenttä';
$ab_field_size = 'Koko';

$ab_new_field = 'Uusi datakenttä';
$ab_invalid_field_size = 'Väärä koko. Valise arvo 10 ja 255 välillä.';
$ab_field_exists = 'Tämän niminen kenttä on jo olemassa.';

$ab_employees = 'Työntekijät';
$ab_no_employees = 'Työntekijöitä ei löytynyt';

$ab_employee_count_single = 'työntekijä';
$ab_employee_count_multiple = 'työntekijät';
$ab_vat_no = 'ALV numero';

$ab_add_new = 'Lisää uusi';
$ab_add_existing = 'Lisää olemassa oleva';

$ab_access = 'Käyttöoikeus';
//1.98
$ab_mailings = 'Sähköpostit';
$ab_mailing_groups = 'Sähköposti ryhmät';
$ab_no_mailing_groups = 'Sähköposti ryhmiä ei löytynyt';
$ab_no_templates = 'Mallitiedostoja ei löytynyt';
$ab_mailing_group_exists = 'Tällä nimellä oleva sähköposti ryhmä on jo olemassa.';
$ab_contacts_count_single = 'yhteystieto';
$ab_contacts_count_multiple = 'yhteystiedot';
$ab_new_mailing = 'Uusi sähköposi';
$ab_all_your_addressbooks = 'kaikki sinun osoitekirjat';

$ab_search_for = 'Etsi';
$ab_search_in = 'Kentistä';
$ab_search_on = 'Merkit';
$ab_search_keyword = 'Hakulause';
$ab_wildcard = '(Käytä \'%\' villimerkkinä)';

//2.03
$ab_copy_read_acl = 'Kopioi lukuoikeudet yhteystietoihin ja yrityksiin';
$ab_copy_write_acl = 'Kopioi kirjoitusoikeudet yhteystietoihin ja yrityksiin';

$ab_catagory_exists = 'Tämän niminen kategoria on jo olemassa.';
$ab_add_catagory = 'Lisää kategoria';
$ab_add_field = 'Lisää kenttä';
$ab_catagory = 'Kategoria';

//2.04
$ab_todos = 'Tehtävä\'s';


//2.05
$ab_download = 'Download';
$ab_download_template = 'Paina \'Download\' painiketta lataaksesi mallipohja tietokoneelle muokattavaksi.';
$ab_upload = 'Upload';
//modified string:
$ab_upload_oo_text = 'Kun olet muokannut OpenOffice.org mallipohjan valmiiksi talleta se Group-Officeen. Valitse tiedosto vai paina \'Upload\' mallitiedoston siirtömiseksi.';
$ab_file_type = 'Tiedostotyyppi';
$ab_import_vcf_file_ok = 'Lataus vaikuttaa onnistuneen. Paina \'Ok\' ottaaksesi se käyttöön osoitekirjassa.';
$ab_import_failed = 'Lataus epäonnistui.';
//modified string:
$contacts_import_text = "Valiste tiedosto joka sisältä tuotavat tiedot ja paina 'Ok'";

//2.06
$ab_select_mailing_groups = 'Valitse sähköpostiryhmät johon tämä yhteystieto kuuluu';

//2.11
$ab_move_to_ab = 'Siirrä valitut yhteydet osoitekirjaan...';
$ab_move_com_to_ab = 'Siirrä valitut yritykset osoitekirjaan...';

//2.12
$ab_search_sender = 'Etsi sähköposti';

$ab_contact_exists = 'Tämä yhteystieto vaikuttaa jo olevan olemassa. Paina %s siirtyäksesi olemassa olevaan yhteyteen tai paina \'Ok\' tai \'Käytä\' jos kumminkin haluat tallettaa yhteyden.';
$ab_company_exists = 'Tämä yritys vaikuttaa jo olevan olemassa. Paina %s siirtyäksesi olemassa olevaan yritykseen tai paina \'Ok\' tai \'Käytä\' jos kumminkin haluat tallettaa yrityksen.';
$ab_here = 'tässä';

//2.13
$ab_search_for_keyword = '';//'for'

$ab_search_in = 'Hae'; //changed
$ab_search_on_users = 'Hae käyttäjistä';

$ab_users_count_single='käyttäjä';
$ab_users_count_multiple='käyttäjät';
$ab_no_users='Käyttäjiä ei löytynyt';
