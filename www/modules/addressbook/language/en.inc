<?php
//Uncomment this line in new translations!
//require_once($GO_LANGUAGE->get_fallback_language_file('addressbook'));

//contacts
$contacts_title = "Addressbook";
$contacts_no_contacts = "No contacts found.";
$contacts_members = "Members";
$contacts_contacts = "Contacts";
$contacts_add = "New Contact";
$contacts_groups = "Groups";
$contacts_other = "Other Contacts";
$contacts_move_to_group = "Move selected contacts to group...";
$groups_no_groups = "You don't have any groups";
$contacts_group = "Group";
$contacts_empty_group = "Use the dropdown box to move contacts to this group.";
$contacts_this_group = 'this group';

$add_contacts_text = "Fill in the members E-mail adress and authorization code and click on 'Ok'";
$add_contacts_success = "User added successfully";
$add_contacts_wrong = "Wrong E-mail or authorization code.";
$add_personal_text = "Click on 'Save' to add the contact. The fields marked with a * are required.";
$contacts_select = "Select contacts to add and click on 'Ok'.";
$contacts_select_title = "Contacts";
$contact_profile = "Profile";
$user_profile = "User profile";
$contacts_authorise = "Authorization";
$contacts_add_member = "Add this member to your contacts";
$strConfirmSynchronise = "This will update this contact with data the user has entered himself. Manual changes by you will be lost. Are you sure you want to continue?";
$contacts_import = "Import";
$contacts_export = "Export";
$contacts_export_text = "Select the type of CSV you would like to create.";
$contacts_delete = "Delete";

$contacts_import_text_group = "Select a group and the CSV file. Click on 'Ok'.";
$fbNoFile = "No file was not recieved";
$fbPleaseWait = "Please wait while uploading file...";

$strNotIncluded = "Do not import";
$contacts_import_feedback = "The following columns where found in your CSV file.<br />";
$contacts_import_feedback .= "Select the group you wish to import to or automatically create groups based on a column of the file.<br />";
$contacts_import_feedback .= " Match corrosponding Group-Office columns with the columns from the file and make sure that you don't select columns twice.";
$contacts_separator = "Values are seperated by";
$contacts_no_separator = "You didn't enter a separator character.";
$fbFilename = "Filename";
$contacts_import_title = "Import Addressbook";
$contacts_import_success = "Successfully imported file";
$contacts_import_double = "You have selected one or more columns twice. Correct this and try again.";
//changed:
$contacts_import_noname = 'You must select the fields \'First_name\' and \'Last name\' ';
$contacts_import_incompatible = "Incompatible file format.<br /><br />A CSV file is a file with values seperated by (dot)comma's.";
$strMember = "Member";
$contact_exist_warning = "<b>Note:</b> This user is already in your contactlist.";
$contacts_add_to_group = "Add selected users to contacts in group...";
$contacts_add_to = "Add selected users to contacts";
$contacts_add_user = "add";
$contacts_edit = "Edit";
$contacts_no_select = "You didn't select anything";
$contacts_this_contact = "this contact";

$contacts_seperator_text = 'Values seperated by';
$contacts_quote = 'Values enclosed by';
$contacts_clrf = 'Lines terminated by';
$contacts_auto_group = 'Automatically create groups from file';
$contacts_import_to_group = 'Import to group';

$contacts_search = 'Search';
$cmdSearch = 'Search';
$contacts_search_text = "Click on a letter or enter a search phrase and click on 'search'. You can use * as a wild character.";
$contacts_results = 'results';
$contacts_result = 'result';


$contacts_color = 'Color';

$contacts_colors['black'] = 'Black';
$contacts_colors['red'] = 'Red';
$contacts_colors['green'] = 'Green';
$contacts_colors['blue'] = 'Blue';
$contacts_colors['pink'] = 'Pink';
$contacts_colors['orange'] = 'Orange';
$contacts_colors['brown'] = 'Brown';

$ab_addressbooks = 'Addressbooks';
$ab_owner = 'Owner';
$ab_subscribed = 'Subscribed';
$ab_new_ab = 'New addressbook';
$ab_addressbook_exists = 'An addressbook with this name already exists';
$ab_addressbook = 'Addressbook';
$ab_no_addressbook = 'No Addressbook';
$contacts_select_single = 'Click on a contact to select this person';

$ab_group_exists = 'There already is an addressbook with this name';
$ab_group_on_file = 'Group contacts based on file';
$ab_default = 'Default';

//translation required
$ab_edit_contact = 'Click at \'Ok\' to save changes. Fields marked with a * are required';
$ab_group_on_file = 'Group contacts based on the file';
$ab_default = 'Default';
$ab_browse = 'Browse';
$ab_comment = 'Comment';

$ab_contact = 'Contact';
$ab_send_message = 'New E-mail';
$ab_new_event = 'New event';

$ab_templates = 'Templates';
$ab_your_templates = 'Your';
$ab_all_templates = 'All';

$template_type[0] = 'E-mail';
$template_type[1] = 'OpenOffice.Org';
$ab_type = 'Type';
$ab_template_exists = 'A template with this name already exists.';

$sir_madam['M'] = 'sir';
$sir_madam['F'] = 'madam';

$ab_upload_oo = 'Uploadf';
$ab_upload_nofile = 'Error while sending file.';

$ab_oo_doc = 'Textdocument';
$ab_select_template = 'Select the template for this document:';
$ab_add_field_data = 'You can use the following values in your document:';
$ab_datafield = 'Datafield';

$ab_no_template = 'No template';
$ab_new_note = 'Note';

$ab_events = 'Appointments';

$ab_new_contact = 'New&nbsp;contact';
$ab_new_company = 'New&nbsp;company';
$ab_companies = 'Companies';
$ab_company_properties = 'Company&nbsp;profile';
$ab_bank_no = 'Bank&nbsp;number';
$ab_companies_count_single = 'company';
$ab_companies_count = 'companies';
$ab_no_companies = 'No companies found';
$ab_no_company = 'No company';

$ab_import_type = 'Import';
$ab_no_company_found = 'No companies found';
$ab_search_company = 'Lookup company';
$ab_edit_company = 'Change company profile';
$ab_new_group = 'New group';
$ab_export_type = 'Export';

$ab_custom_fields = 'Extra&nbsp;datafields';
$ab_field_size = 'Size';

$ab_new_field = 'New datafield';
$ab_invalid_field_size = 'Invalid size. Choose a value between 10 and 255.';
$ab_field_exists = 'A field with this name already exists.';

$ab_employees = 'Employees';
$ab_no_employees = 'No employees found';

$ab_employee_count_single = 'employee';
$ab_employee_count_multiple = 'employees';
$ab_vat_no = 'VAT number';

$ab_add_new = 'Add new';
$ab_add_existing = 'Add existing';

$ab_access = 'Access';
//1.98
$ab_mailings = 'Mailings';
$ab_mailing_groups = 'Mailing groups';
$ab_no_mailing_groups = 'No mailing groups found';
$ab_no_templates = 'No templates found';
$ab_mailing_group_exists = 'A mailing group with this name already exists.';
$ab_contacts_count_single = 'contact';
$ab_contacts_count_multiple = 'contacts';
$ab_new_mailing = 'New mailing';
$ab_all_your_addressbooks = 'all your addressbooks';

$ab_search_for = 'Search for';
$ab_search_on = 'on';
$ab_search_keyword = 'Search phrase';
$ab_wildcard = '(Use \'%\' as wildcard)';

//2.03
$ab_copy_read_acl = 'Copy read permissions to contact and companies';
$ab_copy_write_acl = 'Copy write permissions to contact and companies';

$ab_catagory_exists = 'A category with this name already exists.';
$ab_add_catagory = 'Add category';
$ab_add_field = 'Add field';
$ab_catagory = 'Category';

//2.04
$ab_todos = 'Todo\'s';


//2.05
$ab_download = 'Download';
$ab_download_template = 'Click at the \'Download\' button to download the template to your PC for editing.';
$ab_upload = 'Upload';
//modified string:
$ab_upload_oo_text = 'When you finnished with editting your OpenOffice.org template you should transfer it back to Group-Office. Select the file and click at \'Upload\' to transfer your template.';
$ab_file_type = 'Filetype';
$ab_import_vcf_file_ok = 'The uploaded file seems to be fine. Please click on \'Ok\' to adopt it to the addressbook.';
$ab_import_failed = 'Import failed.';
//modified string:
$contacts_import_text = "Select a file containing data to be imported and click on 'Ok'";

//2.06
$ab_select_mailing_groups = 'Select the mailing groups where this contact belongs to';

//2.11
$ab_move_to_ab = 'Move selected contacts to addressbook...';
$ab_move_com_to_ab = 'Move selected companies to addressbook...';

//2.12
$ab_search_sender = 'Search e-mail';

$ab_contact_exists = 'This contact already seems to exist. Click %s to go to the existing contact or click \'Ok\' or \'Apply\' again to save this contact anyway.';
$ab_company_exists = 'This company already seems to exist. Click %s to go to the existing company or click \'Ok\' or \'Apply\' again to save this company anyway.';
$ab_here = 'here';

//2.13
$ab_search_for_keyword = 'for';

$ab_search_in = 'Search in'; //changed
$ab_search_on_users = 'Search on';

$ab_users_count_single='user';
$ab_users_count_multiple='users';
$ab_no_users='No users found';

//2.15
$ab_select_addressbook = 'Select addressbook';
$ab_move = 'Move';
$ab_file='File';

$ab_template_uploaded = 'Document is saved';
$ab_select_mailing_group = 'Select the group you want to send your mailing to';

//2.16
$ab_id = 'no.';
?>
