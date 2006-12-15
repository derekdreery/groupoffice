<?php
//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_language_file('addressbook'));
$htmldirections='rtl';
$charset='UTF-8';
//contacts
$contacts_title = "אנשי קשר";
$contacts_no_contacts = "לא נמצאו אנשי קשר";
$contacts_members = "משתמשים";
$contacts_contacts = "אנשי קשר";
$contacts_add = "איש קשר חדש";
$contacts_groups = "קבוצות";
$contacts_other = "אנשי קשר אחרים";
$contacts_move_to_group = "עבר אנשי קשר ל...";
$groups_no_groups = "אין לך שום קבוצה";
$contacts_group = "קבוצה";
$contacts_empty_group = "Use the dropdown box to move contacts to this group.";
$contacts_this_group = 'קבוצה זאות';

$add_contacts_text = "Fill in the members E-mail adress and authorization code and click on 'Ok'";
$add_contacts_success = "הוסף משתמש בהצלחה";
$add_contacts_wrong = "דואל או קוד אשור שגוי";
$add_personal_text = "לחץ על 'שמור' כדי להוסיף איש קשר. שדות מסומנות ב * חובה.";
$contacts_select = "בחר באיש קשר ולחץ על 'אשור,.";
$contacts_select_title = "אנשי קשר";
$contact_profile = "פרופיל";
$user_profile = "פרופיל המשתמש";
$contacts_authorise = "אשור";
$contacts_add_member = "הוסף משתמש זה לרשימת אנשי קשר שלך";
$strConfirmSynchronise = "פעולה זאות ההדקן האיש קשר הזה במידע שהשולך הכניס בעצמו.שינוים ידנים לא ישמרו. האם  אתה בתוח שרצונך להמשיך?";
$contacts_import = "יבאו";
$contacts_export = "יצאו";
$contacts_export_text = "Select the type of CSV you would like to create.";
$contacts_delete = "מחק";

$contacts_import_text_group = "Select a group and the CSV file. Click on 'Ok'.";
$fbNoFile = "No file was not recieved";
$fbPleaseWait = "Please wait while uploading file...";

$strNotIncluded = "Do not import";
$contacts_import_feedback = "The following columns where found in your CSV file.<br />";
$contacts_import_feedback .= "Select the group you wish to import to or automatically create groups based on a column of the file.<br />";
$contacts_import_feedback .= " Match corrosponding Group-Office columns with the columns from the file and make sure that you don't select columns twice.";
$contacts_separator = "ארכים מופרדים על ידי ";
$contacts_no_separator = "לא הכנסתה תב הפרדה";
$fbFilename = "Filename";
$contacts_import_title = "יבא רשימת אנשי קשר";
$contacts_import_success = "יבאו הקובץ בוצה בהצלחה";
$contacts_import_double = "בחרתה אחת או שתי עמודות פעמים. תקן זה ונסה שוב.";
//changed:
$contacts_import_noname = 'חיב לבחור השדות \'שם פרטי\' ב \'שם משפחה\'';
$contacts_import_incompatible = "Incompatible file format.<br /><br />A CSV file is a file with values seperated by (dot)comma's.";
$strMember = "Member";
$contact_exist_warning = "<b>הערה</b> המשתמש כבר ברשימת אנשי קשר שלך.";
$contacts_add_to_group = "הוסף המשתמשים הנבחרים לאנשי קשר בקבוצה...";
$contacts_add_to = "הוסף המשתמשים הנבחרים לאנשי קשר";
$contacts_add_user = "הוסף";
$contacts_edit = "אורך";
$contacts_no_select = "לא בחרתה כלום";
$contacts_this_contact = "איש קשר הזה";

$contacts_seperator_text = 'Values seperated by';
$contacts_quote = 'שדות מוגבלות על ידי';
$contacts_clrf = 'שורות מסתימות ב';
$contacts_auto_group = 'הצר תיקיות עבור קבוצות באופן אוטומטי';
$contacts_import_to_group = 'יבא לקבוצה';

$contacts_search = 'חפש';
$cmdSearch = 'חפש';
$contacts_search_text = "Click on a letter or enter a search phrase and click on 'search'. You can use * as a wild character.";
$contacts_results = 'תוצאות';
$contacts_result = 'תוצאה';


$contacts_color = 'צבעים';

$contacts_colors['black'] = 'שחור';
$contacts_colors['red'] = 'אדום';
$contacts_colors['green'] = 'ירוק';
$contacts_colors['blue'] = 'כחול';
$contacts_colors['pink'] = 'ורוד';
$contacts_colors['orange'] = 'כתום';
$contacts_colors['brown'] = 'חום';

$ab_addressbooks = 'רשימות אנשי קשר';
$ab_owner = 'בעל';
$ab_subscribed = 'רשום';
$ab_new_ab = 'רשימת אנשי קשר חדשה';
$ab_addressbook_exists = 'רשימת אנשי קשר באוטו שם כבר קיים';
$ab_addressbook = 'רשימה אנשי קשר';
$contacts_select_single = 'לחץ על איש קשר כדי לבחור הפרט זה';

$ab_group_exists = 'רשימת אנשי קשר באוטו שם כבר קיים';
$ab_group_on_file = 'קבץ אנשי קשר לפי הקובץ';
$ab_default = 'ברירת מחדל';

//translation required
$ab_edit_contact = "לחץ על 'שמור' כדי לשמור שינוים. שדות מסומנות ב * חובה";
$ab_group_on_file = 'קבץ אנשי קשר לפי הקובץ';
$ab_default = 'ברירת מחדל';
$ab_browse = 'דפדף';
$ab_comment = 'הערות';

$ab_contact = 'איש קשר';
$ab_send_message = 'דואל';
$ab_new_event = 'פעילות';

$ab_templates = 'Templates';
$ab_your_templates = 'Your';
$ab_all_templates = 'הכל';

$template_type[0] = 'דואל';
$template_type[1] = 'OpenOffice.Org';
$ab_type = 'Type';
$ab_template_exists = 'תבנית בשם הזה כבר קיים';

$sir_madam['M'] = 'אדון';
$sir_madam['F'] = 'גיברת';

$ab_upload_oo = 'upload OpenOffice.org template';
$ab_upload_nofile = 'חל שגיה בטינת המסמך';

$ab_oo_doc = 'Textdocument';
$ab_select_template = 'בחר תבנית למסמך הזה:';
$ab_add_field_data = 'אתה יכול להשתמש במסמך שלך בשדות הבאות:';
$ab_datafield = 'שדה';

$ab_no_template = 'אין תבנית';
$ab_new_note = 'רישום';

$ab_events = 'פגישות';

$ab_new_contact = 'איש קשר חדש';
$ab_new_company = 'חברה חדשה';
$ab_companies = 'חברות';
$ab_company_properties = 'פרופיל החברה';
$ab_bank_no = 'מספר בנק';
$ab_companies_count_single = 'חברה';
$ab_companies_count = 'חברות';
$ab_no_companies = 'לא נמצאו חברות';
$ab_no_company = 'אין חברה';

$ab_import_type = 'יבאו';
$ab_no_company_found = 'לא נמצא חברה';
$ab_search_company = 'חפש חברה';
$ab_edit_company = 'שנה פרופיל החברהe';
$ab_new_group = 'קבוצה חדשה';
$ab_export_type = 'יצאו';

$ab_custom_fields = 'שדות נוספות';
$ab_field_size = 'גודל';

$ab_new_field = 'שדה חדש';
$ab_invalid_field_size = 'גודל לא חוקי. בחר גודל בין 10 ל 255.';
$ab_field_exists = 'שדה עם אותו שם כבר קיים';

$ab_employees = 'עובדים';
$ab_no_employees = 'לא נמצאו עובדים';

$ab_employee_count_single = 'עובד';
$ab_employee_count_multiple = 'עובדים';
$ab_vat_no = 'מספר מע"מ';

$ab_add_new = 'הוסף חדש';
$ab_add_existing = 'הוסף קיים';

$ab_access = 'גישה';
//1.98
$ab_mailings = 'קבוצות הפצה';
$ab_mailing_groups = 'קבוצות הפצה';
$ab_no_mailing_groups = 'לא נמצאו קבוצות הפצה';
$ab_no_templates = 'לא נמצאו תבניות';
$ab_mailing_group_exists = 'קבוצה הפצה עם אותו שם כבר קיים';
$ab_contacts_count_single = 'איש קשר';
$ab_contacts_count_multiple = 'אנשי קשר';
$ab_new_mailing = 'New mailing';
$ab_all_your_addressbooks = 'כל הרשימות אנשי קשר שלך';

$ab_search_for = 'חפש ';
$ab_search_in = 'ב';
$ab_search_on = 'לפי';
$ab_search_keyword = 'משפט לחיפוש';
$ab_wildcard = '(שתמש ב \'%\' ככדומה)';

//2.03
$ab_copy_read_acl = 'התק אשורי קריאה לאנשי קשר וחברות';
$ab_copy_write_acl = 'התק אשורי כתיבה לאנשי קשר וחברות';

$ab_catagory_exists = 'קטגוריה עם אותו שם כבר קיים';
$ab_add_catagory = 'הוסף קטגוריה';
$ab_add_field = 'הוסף שדה';
$ab_catagory = 'קטגוריה';

//2.04
$ab_todos = 'תזכורות';


//2.05
$ab_download = 'הורד';
$ab_download_template = 'Click at the \'Download\' button to download the template to your PC for editing.';
$ab_upload = 'Upload';
//modified string:
$ab_upload_oo_text = 'When you finnished with editting your OpenOffice.org template you should transfer it back to Group-Office. Select the file and click at \'Upload\' to transfer your template.';
$ab_file_type = 'Filetype';
$ab_import_vcf_file_ok = "המסמך שטאנתה נראה בסדר. נא לחץ על 'אשור' ליבא אותו לרשימת אנשי קשר שלך.";
$ab_import_failed = 'יבאו נכשל';
//modified string:
$contacts_import_text = "בחר הקובץ המכיל נתונים ליבאו ולחץ של ,אשור,.";
?>
