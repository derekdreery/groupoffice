<?php
//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_language_file('addressbook'));

//contacts
$contacts_title = "Adressbok";
$contacts_no_contacts = "Inga kontakter funna.";
$contacts_members = "Medlemmar";
$contacts_contacts = "Kontakter";
$contacts_add = "Ny kontakt";
$contacts_groups = "Grupper";
$contacts_other = "Andra kontakter";
$contacts_move_to_group = "Flytta valda kontakter till grupp...";
$groups_no_groups = "Du har inga grupper";
$contacts_group = "Grupp";
$contacts_empty_group = "Använd dropdown-menyn för att flytta kontakter till denna grupp.";
$contacts_this_group = 'denna grupp';

$add_contacts_text = "Fyll i medlemmarnas e-postadresser samt authensieringskod och tryck på 'Ok'";
$add_contacts_success = "användaren inlaggd";
$add_contacts_wrong = "Felaktig e-postadress eller authensieringskod.";
$add_personal_text = "Tryck på 'Spara' för att lägga in kontakten. Fälten markerade med * måste fyllas i.";
$contacts_select = "Välj konktakter att lägga till och tryck på 'Ok'.";
$contacts_select_title = "Kontakter";
$contact_profile = "Kontaktprofil";
$user_profile = "Användarprofil";
$contacts_authorise = "Auktorisering";
$contacts_add_member = "Lägg in denna medlem till dina kontakter";
$strConfirmSynchronise = "Detta kommer att uppdatera denna kontakt med data användaren själv har anget. Manuella inställningar som du har gjort kommer att försvinna. Är du säker på att du vill fortsätta?";
$contacts_import = "Importera";
$contacts_export = "Exportera";
$contacts_export_text = "Välj den typ av CSV du vill skapa.";
$contacts_delete = "Radera";

$contacts_import_text_group = "Välj en grupp och CSV-filen. Tryck på 'Ok'.";
$fbNoFile = "Ingen fil mottogs";
$fbPleaseWait = "Var god vänta medans filen laddas upp...";

$strNotIncluded = "Importera ej";
$contacts_import_feedback = "Följande kolumner hittades i din CSV-fil.<br />";
$contacts_import_feedback .= "Välj den grupp du vill importera till eller skapa grupperna automatiskt baserat på kolumnerna i filen.<br />";
$contacts_import_feedback .= " Matcha korresponderande Group_Office kolumner med kolumner från filen och se till så du väljer kolumner flera gånger.";
$contacts_separator = "Värden separeras med";
$contacts_no_separator = "Du angav inget seperationstecken.";
$fbFilename = "Filnamn";
$contacts_import_title = "Importera Adressbok";
$contacts_import_success = "Filen importerades korrekt";
$contacts_import_double = "Du har valt en eller flera kolumner två gånger. Korrigera detta och försök igen.";
//changed:
$contacts_import_noname = 'Du måste välja namnfältet.';
$contacts_import_incompatible = "Inkompatibelt filformat.<br /><br />En CSV fil är en fil med värden separerade med (punkt)komma-tecken.";
$strMember = "Medlem";
$contact_exist_warning = "<b>Notera</b> Denna användare existerar redan i din kontaktlista.";
$contacts_add_to_group = "Lägg in valda användare till kontakter i grupp...";
$contacts_add_to = "Lägg in valda användare till kontakter";
$contacts_add_user = "Lägg till";
$contacts_edit = "Modifiera";
$contacts_no_select = "Du valde ingenting";
$contacts_this_contact = "denna kontakt";

$contacts_seperator_text = 'Värden separerade av';
$contacts_quote = 'Värden inkaplsade av';
$contacts_clrf = 'Linjer avbryts med';
$contacts_auto_group = 'Skapa grupper automatiskt från fil';
$contacts_import_to_group = 'Importera till grupp';

$contacts_search = 'Sök';
$cmdSearch = 'Sök';
$contacts_search_text = "Skriv in ett sökord och tryck på 'Sök'. Du kan använda * som matchningar.";
$contacts_results = 'resultat';
$contacts_result = 'resultat';


$contacts_color = 'Färg';

$contacts_colors['black'] = 'Svart';
$contacts_colors['red'] = 'Röd';
$contacts_colors['green'] = 'Grön';
$contacts_colors['blue'] = 'Blå';
$contacts_colors['pink'] = 'Rosa';
$contacts_colors['orange'] = 'Orange';
$contacts_colors['brown'] = 'Brun';

$ab_addressbooks = 'Adressböcker';
$ab_owner = 'Ägare';
$ab_subscribed = 'Prenumererad på';
$ab_new_ab = 'Ny adressbok';
$ab_addressbook_exists = 'En adressbok med detta namn existerar redan';
$ab_addressbook = 'Adressbok';
$contacts_select_single = 'Klicka på en kontakt för att välja denna person';

$ab_group_exists = 'Det existerar redan en adressbok med detta namn';
$ab_group_on_file = 'Gruppera kontakter baserat på en fil';
$ab_default = 'Default';

//translation required
$ab_edit_contact = 'Klicka på \'Ok\' för att spara ändringar. Fält markerade med * måste fyllas i';
$ab_group_on_file = 'Gruppera kontakter baserad på filen';
$ab_default = 'Default';
$ab_browse = 'Bläddra';
$ab_comment = 'Kommentar';

$ab_contact = 'Kontakt';
$ab_send_message = 'E-post';
$ab_new_event = 'Event';

$ab_templates = 'Mallar';
$ab_your_templates = 'Din';
$ab_all_templates = 'Alla';

$template_type[0] = 'E-post';
$template_type[1] = 'OpenOffice.Org';
$ab_type = 'Typ';
$ab_template_exists = 'En mall med detta namn existerar redan.';

$sir_madam['M'] = 'sir';
$sir_madam['F'] = 'madam';

$ab_upload_oo = 'Ladda upp OpenOffice.org mall';
$ab_upload_nofile = 'Fel vid filöverföring.';

$ab_oo_doc = 'Textdokument';
$ab_select_template = 'Välj mall för detta dokument:';
$ab_add_field_data = 'Du kan använda följande värden i ditt dokument:';
$ab_datafield = 'Datafält';

$ab_no_template = 'Ingen mall';
$ab_new_note = 'Anteckning';

$ab_events = 'Möten';

$ab_new_contact = 'Ny&nbsp;kontakt';
$ab_new_company = 'Nytt&nbsp;företag';
$ab_companies = 'Företag';
$ab_company_properties = 'Företags&nbsp;profil';
$ab_bank_no = 'Bank&nbsp;nummer';
$ab_companies_count_single = 'företag';
$ab_companies_count = 'företag';
$ab_no_companies = 'Inga företag hittade';
$ab_no_company = 'Inget företag';

$ab_import_type = 'Import';
$ab_no_company_found = 'Inga företag hittade';
$ab_search_company = 'Leta efter företag';
$ab_edit_company = 'Ändra företags profil';
$ab_new_group = 'Ny grupp';
$ab_export_type = 'Export';

$ab_custom_fields = 'Extra&nbsp;datafält';
$ab_field_size = 'Storlek';

$ab_new_field = 'Nytt datafält';
$ab_invalid_field_size = 'Felaktig storlek. Välje ett värde mellan 10 och 255.';
$ab_field_exists = 'Ett fält med detta namn existerar redan.';

$ab_employees = 'Anställda';
$ab_no_employees = 'Inga anställda hittade';

$ab_employee_count_single = 'anställd';
$ab_employee_count_multiple = 'anställda';
$ab_vat_no = 'VAT nummer';

$ab_add_new = 'Lägg till ny';
$ab_add_existing = 'Lägg till existerande';

$ab_access = 'Access';
//1.98
$ab_mailings = 'Mailings';
$ab_mailing_groups = 'Mailing listor';
$ab_no_mailing_groups = 'Inga mailing listor hittade';
$ab_no_templates = 'Inga mallar hittade';
$ab_mailing_group_exists = 'En mailing lista med detta namn existerar redan.';
$ab_contacts_count_single = 'kontakt';
$ab_contacts_count_multiple = 'kontakter';
$ab_new_mailing = 'Ny mailing';
$ab_all_your_addressbooks = 'alla dina adressböcker';

$ab_search_for = 'Sök efter';
$ab_search_in = 'i';
$ab_search_on = 'på';
$ab_search_keyword = 'Sök fras';
$ab_wildcard = '(Använd \'%\' som wildcard)';

//2.03
$ab_copy_read_acl = 'Kopiera läsrättigheter till kontakter och företag';
$ab_copy_write_acl = 'Kopiera skrivrättigheter till kontakter och företag';

$ab_catagory_exists = 'Det existerar redan en kategori med detta namn.';
$ab_add_catagory = 'Lägg till kategori';
$ab_add_field = 'Lägg till fält';
$ab_catagory = 'Kategori';

//2.04
$ab_todos = 'Uppgifter';


//2.05
$ab_download = 'Ladda ner';
$ab_download_template = 'Klicka på knappen \'Ladda ner\' för att ladda ner mallen till din PC för editering.';
$ab_upload = 'Ladda upp';
//modified string:
$ab_upload_oo_text = 'När du editerat din OpenOffice.org mall måste du ladda upp den till Group-Office. Välj filen och klicka på \'Ladda upp\' för att överföra din mall.';
$ab_file_type = 'Filtyp';
$ab_import_vcf_file_ok = 'Den uppladdade filen verkar vara okay. Klicka på \'Ok\' för att ta den i bruk i adressboken.';
$ab_import_failed = 'Importen misslyckades.';
//modified string:
$contacts_import_text = "Välj den fil som innehåller informationen du vill importera och klicka på 'Ok'";

//2.06
$ab_select_mailing_groups = 'Välj e-post lista som denna kontakt tillhör';

//2.11
$ab_move_to_ab = 'Flytta markerade kontakter till adressbok...';
$ab_move_com_to_ab = 'Flytta markerade företag till adressbok...';

//2.12
$ab_search_sender = 'Sök e-post';

$ab_contact_exists = 'Denna kontakt existerar redan. Klicka %s för att visa den existerande kontakten.';
$ab_company_exists = 'Detta företag existerar redan. Klicka %s för att visa det existerande företaget.';
$ab_here = 'här';
?>
