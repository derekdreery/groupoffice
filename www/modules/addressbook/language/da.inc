<?php
//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_language_file('addressbook'));

//contacts
$contacts_title = "Addressebog";
$contacts_no_contacts = "Ingen kontaktpersoner registreret.";
$contacts_members = "Medlemmer";
$contacts_contacts = "Kontaktpersoner";
$contacts_add = "Ny kontaktperson";
$contacts_groups = "Grupper";
$contacts_other = "Andre kontaktpersoner";
$contacts_move_to_group = "Flyt valgte kontaktpersoner til gruppen...";
$groups_no_groups = "Der er ingen grupper";
$contacts_group = "Gruppe";
$contacts_empty_group = "Brug listen til at flytte kontaktpersoner til denne gruppe.";
$contacts_this_group = 'Denne gruppe';

$add_contacts_text = "Indtast medlemmets e-mail adresse og autorisationskode og klik på 'Ok'";
$add_contacts_success = "Bruger tilføjet";
$add_contacts_wrong = "Forkert e-mail adresse eller autorisationskode.";
$add_personal_text = "Klik på 'Gem' for at tilføje kontaktpersonen. Felterne markeret med * er påkrævede.";
$contacts_select = "Vælg de kontaktpersoner der skal tilføjes og klik på 'Ok'.";
$contacts_select_title = "Kontaktpersoner";
$contact_profile = "Kontaktperson profil";
$user_profile = "Brugerprofil";
$contacts_authorise = "Autorisation";
$contacts_add_member = "Tilføj medlem til dine kontakter";
$strConfirmSynchronise = "Dette vil opdatere kontaktpersonen med data brugeren selv har indtastet. Dine ændringer vil gå tabt. Er du sikker på du vil fortsætte?";
$contacts_import = "Importer";
$contacts_export = "Eksporter";
$contacts_export_text = "Vælg den CSV type du ønsker at oprette.";
$contacts_delete = "Slet";

$contacts_import_text_group = "Vælg en gruppe og en CSV fil. Klik på 'Ok'.";
$fbNoFile = "Filen blev ikke overført";
$fbPleaseWait = "Vent venligst mens filen overføres...";

$strNotIncluded = "Importer ikke";
$contacts_import_feedback = "Følgende kolonner blev fundet i CSV filen.<br />";
$contacts_import_feedback .= "Vælg gruppen du ønsker at importere til eller vælg at få grupperne oprettet automatisk på baggrund af en af filens kolonner.<br />";
$contacts_import_feedback .= " Match tilhørende Group-Office kolonner med kolonnerne fra filen. Sørg for ikke at vælge kolonner flere gange.";
$contacts_separator = "Værdier adskilt af";
$contacts_no_separator = "Du har ikke indtastet et separator tegn.";
$fbFilename = "Filnavn";
$contacts_import_title = "Importer adressebog";
$contacts_import_success = "Filen blev importeret";
$contacts_import_double = "Du har valgt en eller flere kolonner flere gange. Ret fejlen og prøv igen.";
//changed:
$contacts_import_noname = 'Du skal vælge felterne \'Fornavn\' og \'Efternavn\' ';
$contacts_import_incompatible = "Inkompatibelt filformat.<br /><br />Filen skal bestå af felter adskilt af komma.";
$strMember = "Medlem";
$contact_exist_warning = "<b>Bemærk:</b> Denne bruger er allerede i din kontaktpersons liste.";
$contacts_add_to_group = "Tilføj valgte brugere til kontaktpersonerne i gruppen...";
$contacts_add_to = "Tilføj valgte brugere til kontaktpersoner";
$contacts_add_user = "Tilføj";
$contacts_edit = "Rediger";
$contacts_no_select = "Du har ikke valgt noget";
$contacts_this_contact = "Denne kontaktperson";

$contacts_seperator_text = 'Værdier adskilt af';
$contacts_quote = 'Værdier omsluttet af';
$contacts_clrf = 'Linjer afsluttet af';
$contacts_auto_group = 'Opret automatisk grupper fra fil';
$contacts_import_to_group = 'Importer til gruppe';

$contacts_search = 'Søg';
$cmdSearch = 'Søg';
$contacts_search_text = "Klik på et tegn eller indtast søgeord og klik på 'Søg'. Du kan bruge * til at matche vilkårlig tekst.";
$contacts_results = 'resultater';
$contacts_result = 'resultat';


$contacts_color = 'Farve';

$contacts_colors['black'] = 'Sort';
$contacts_colors['red'] = 'Rød';
$contacts_colors['green'] = 'Grøn';
$contacts_colors['blue'] = 'Blå';
$contacts_colors['pink'] = 'Pink';
$contacts_colors['orange'] = 'Orange';
$contacts_colors['brown'] = 'Brun';

$ab_addressbooks = 'Addressbøger';
$ab_owner = 'Ejer';
$ab_subscribed = 'Tilmeldt';
$ab_new_ab = 'Ny adressebog';
$ab_addressbook_exists = 'En adressebog ved det navn eksisterer allerede';
$ab_addressbook = 'Adressebog';
$contacts_select_single = 'Klik på en kontaktperson for at vælge denne';

$ab_group_exists = 'En adressebog ved det navn eksisterer allerede';
$ab_group_on_file = 'Gruppe kontaktpersoner baseret på fil';
$ab_default = 'Standard';

//translation required
$ab_edit_contact = 'Klik på \'Ok\' for at gemme ændringer. Felter markeret med * er påkrævede';
$ab_group_on_file = 'Gruppe kontaktpersoner baseret på filen';
$ab_default = 'Standard';
$ab_browse = 'Vis';
$ab_comment = 'Kommentar';

$ab_contact = 'Kontakt';
$ab_send_message = 'E-mail';
$ab_new_event = 'Hændelse';

$ab_templates = 'Skabeloner';
$ab_your_templates = 'Dine';
$ab_all_templates = 'Alle';

$template_type[0] = 'E-mail';
$template_type[1] = 'OpenOffice.Org';
$ab_type = 'Type';
$ab_template_exists = 'En skabelon ved det navn eksisterer allerede.';

$sir_madam['M'] = 'Herr';
$sir_madam['F'] = 'Fru';

$ab_upload_oo = 'Overfør OpenOffice.org skabelon';
$ab_upload_nofile = 'Fejl ved afsendelse af fil.';

$ab_oo_doc = 'Tekstdokument';
$ab_select_template = 'Vælg en skabelon for dette dokument:';
$ab_add_field_data = 'Du kan bruge følgende værdier i dokumentet:';
$ab_datafield = 'Datafelt';

$ab_no_template = 'Ingen skabelon';
$ab_new_note = 'Note';

$ab_events = 'Møder';

$ab_new_contact = 'Ny&nbsp;kontaktperson';
$ab_new_company = 'Nyt&nbsp;firma';
$ab_companies = 'Firmaer';
$ab_company_properties = 'Firmaprofil';
$ab_bank_no = 'Banknummer';
$ab_companies_count_single = 'firma';
$ab_companies_count = 'firmaer';
$ab_no_companies = 'Ingen firmaer fundet';
$ab_no_company = 'Intet firma';

$ab_import_type = 'Importer';
$ab_no_company_found = 'Igen firmaer fundet';
$ab_search_company = 'Find firma';
$ab_edit_company = 'Skift firmaprofil';
$ab_new_group = 'Ny gruppe';
$ab_export_type = 'Eksporter';

$ab_custom_fields = 'Ekstra&nbsp;datafelter';
$ab_field_size = 'Størrelse';

$ab_new_field = 'Nyt datafelt';
$ab_invalid_field_size = 'Ugyldig størrelse. Vælg en værdi mellem 10 og 255.';
$ab_field_exists = 'Et felt ved det navn eksisterer allerede.';

$ab_employees = 'Ansatte';
$ab_no_employees = 'Ingen ansatte fundet';

$ab_employee_count_single = 'ansat';
$ab_employee_count_multiple = 'ansatte';
$ab_vat_no = 'CVR nummer';

$ab_add_new = 'Tilføj ny';
$ab_add_existing = 'Tilføj eksisterende';

$ab_access = 'Tilgå';
//1.98
$ab_mailings = 'Post';
$ab_mailing_groups = 'Postgrupper';
$ab_no_mailing_groups = 'Ingen postgrupper fundet';
$ab_no_templates = 'Ingen skabeloner fundet';
$ab_mailing_group_exists = 'En postgruppe ved det navn eksisterer allerede.';
$ab_contacts_count_single = 'kontaktperson';
$ab_contacts_count_multiple = 'kontaktpersoner';
$ab_new_mailing = 'Ny post';
$ab_all_your_addressbooks = 'alle dine adressebøger';

$ab_search_for = 'Søg efter';
$ab_search_in = 'i';
$ab_search_on = 'på';
$ab_search_keyword = 'Søgefrase';
$ab_wildcard = '(Brug \'%\' for at matche vilkårlig tekst)';

//2.03
$ab_copy_read_acl = 'Kopier læserettigheder til kontaktpersoner og firmaer';
$ab_copy_write_acl = 'Kopier skriverettigheder til kontaktpersoner og firmaer';

$ab_catagory_exists = 'En kategori ved det navn eksisterer allerede.';
$ab_add_catagory = 'Tilføj kategori';
$ab_add_field = 'Tilføj felt';
$ab_catagory = 'Kategori';

//2.04
$ab_todos = 'Noter';


//2.05
$ab_download = 'Download';
$ab_download_template = 'Klik på \'Download\' knappen for at downloade skabelonen til din computer for redigering.';
$ab_upload = 'Overfør';
//modified string:
$ab_upload_oo_text = 'Når du er færdig med at redigere din OpenOffice.org skabelon skal du overføre den tilbage til Group-Office. Vælg en fil og klik på \'Overfør\' for at overføre din skabelon.';
$ab_file_type = 'Filtype';
$ab_import_vcf_file_ok = 'Den overførte fil er tilsyneladende i orden. Klik venligst på \'Ok\' for at tilføje den til adressebogen.';
$ab_import_failed = 'Import mislykkedes.';
//modified string:
$contacts_import_text = "Vælg en fil der skal importeres og klik på 'Ok'";

//2.06
$ab_select_mailing_groups = 'Vælg postgrupperne som kontaktpersonen tilhører';
?>
