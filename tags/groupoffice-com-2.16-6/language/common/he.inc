<?php
//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_base_language_file('common'));
$htmldirection='rtl';
$charset = 'UTF-8';
//main language menu file register login and other non logged in pages
//common
$cmdLogin = "כנס";
$cmdSave = "שמור";
$cmdBack = "חזור";
$cmdCancel = "בטל";
$cmdClose = "סגור";
$cmdOk = "אשר";
$cmdDelete = "מחק";
$cmdNext = "הבא";
$cmdPrevious = "הקודם";
$cmdContinue = "המשך";
$cmdUpdate = "עדקן";
$cmdApply = "בצע";
$cmdYes = "כן";
$cmdNo = "לא";
$cmdAdd = 'הוסף';
$cmdEdit = 'ערוך';
$cmdInvertSelection = 'הפך בחירה';

$strEdit = "ערוך";
$strThis = 'זה';
$strItems = 'פריטים';
$strDescription = 'תאור';

$strName = "שם";
$strUser = "משתמש";
$strUsername = "שם משתמש";
$strPassword = "סיסמה";
$strAuthcode = "קוד אשור";
$strCountry = "מדינה";
$strState = "מחוז";
$strCity = "עיר";
$strZip = "מיקוד";
$strAddress = "כתובת הרחוב";
$strEmail = "דואל";
$strPhone = "טלפון";
$strWorkphone = "טלפון בעבודה";
$strCellular = "נייד";
$strCompany = "חברה";
$strDepartment = "מחלקה";
$strFunction = "תפקיד";
$strQuestion = "שאלה סודית";
$strAnswer = "תשובה";
$strFax = "פקסימיליה";
$strWorkFax = "פקס בעבודה";
$strHomepage = "דף הבית";
$strWorkAddress = "כתובת בעבודה";
$strWorkZip = "מיקוד בעבודה";
$strWorkCountry = "מדינת העבודה";
$strWorkState = "מחוז העבודה";
$strWorkCity = "עיר העבודה";
$strPhoto = "תמונה";
$strComments = "הערות";
$strProperties = 'פרטים';
$strDeleteItem = "מחק";
$strDate = "תאריך";
$strSaveSuccess = "תאריך נשמר בהצלחה";
$strSaveError = "שגיאה בשמירת תאריך";
$strDataError = "שגיאה בקריאת נתונים";
$strDeleteError = "לא הצליח לשמור נתונים";
$strAclError = "שגיה בבקרת אשורים";
$strDateError = "הכנסת תאריך שגוי";
$strAccessDenied = "כניסה אסורה";
$AccessDenied_text = "אינך רשאי לצפות בחלק הזה של האתר או לבצע פעולה זאת<br>משתמש אחר הגביל אשור למידע שביקשת.";
$invalid_chars = "התווים הבאים אסורים";
$strGroups = "קבוצות";
$strOwner = "בעל";
$strDeletePrefix = "האם את/ה בטוח שברצונך למחוק?";
$strDeleteSuffix = "?";
//menu
$menu_logout = "לצאת";
$menu_applications = "תוכנות";
$menu_help = "עזרה";
$menu_about ="לגבי";
$menu_documentation = "דוקומנטציה";
$menu_configuration = "הגדרות";
$menu_groups = "נהל קבוצות משתמש";
$menu_accounts = "שנה הגדרות חשבונך";
$menu_preferences = "דייק בעדיפויות אישיות שלך";
$menu_search_discussions = "דיונים";
$menu_search_files = "קבצים בתיקיות";
$menu_search_web = "הרשת";
$menu_filetypes = "סוג קבצים";
$menu_discussions = "לוח דיונים";

$menu_users = "נהל משתמשים";
$menu_new = "חדש";
$menu_message = "הודעה";
$menu_contact = "איש קשר";
$menu_event = "פעילות";
$menu_bookmark = "סימניה";
$menu_topics = "נושאים";
$menu_support = "תמיכה";
$menu_projects = "פרויקטים";
$menu_sysinfo = "הראה מידע המערכת";
$menu_admin = 'תפריט המנהל';
$menu_modules = 'אוסף/אורד מודולים';
$menu_manual = 'הוראות שימוש';

//class validate
$error_required = "לא מילית את שדה החובה הבא.";
$error_match_auth = "קוד אישור לא תואם.";
$error_match_pass = "סיסמאות לא תואמות";
$error_min_length = "הכמות המנימלית של תווים בשדה הבא:";
$error_max_length = "הכמות המקסימלית של תווים בשדה הבא:";
$error_email = "כתובת דואל שציינת לא תקינה, נא לתקן.";
$error_email_exists = "כתובת דואל אשר בחרת כבר קיימת במערכת, נא לבחור כתובת דואל אחרת.";
$error_username_exists = "שמ משתמש כבר קיים במערכת. נא לבחור שם משתמש אחר.";
$errors_in_form = "היו טעויות בטופס, נא למלות שוב.";
$error_missing_field = "לא מילית את כל שדות החובה";

//acl control
$acl_control_auth_groups = "קבוצות מורשות";
$acl_control_auth_users = "משתמשים מורשים";
$acl_control_no_contacts = "אין לך שום איש קשר לסימון.<br /> השתמש באנשי קשר";

$strShowProfile = "הצג פרופיל";
$strShowGroup = "הצג שייכי הקבוצה";
$strProcessingTime = "הטיפול בדף זה לקח ";
$strLoggedInAs = "רשומ כ";
$strAt = " at ";
$strEmailTo = "שלח דאול ל: ";
$strProtectedUser = "משתמשים מוגנים";

$http_not_found = "מסמך לא נמצא";
$http_not_found_text = "מסמך לא נמצא. אולי טעית בכתובת URL";
$http_error = "שגיאה פנימית של השרת";
$http_bad_request = "בקשה שגויה";
$strAddContact = "אוסף לאנשי קשר";

$strReadRights = "הרשאות קריאה";
$strWriteRights = "הרשאות כתיבה";
$strPermissions = 'הרשאות';

$days[0]="א";
$days[1]="ב";
$days[2]="ג";
$days[3]="ד";
$days[4]="ה";
$days[5]="ו";
$days[6]="ז";

$months[0]="ינואר";
$months[1]="פברואר";
$months[2]="מרץ";
$months[3]="אפריל";
$months[4]="מאי";
$months[5]="יוני";
$months[6]="יולי";
$months[7]="אוגוסט";
$months[8]="ספטמבר";
$months[9]="אוקטובר";
$months[10]="נובמבר";
$months[11]="דצמבר";

$full_days[0] = "ראשון";
$full_days[1] = "שני";
$full_days[2] = "שלישי";
$full_days[3] = "רביעי";
$full_days[4] = "חמישי";
$full_days[5]= "שישי";
$full_days[6] = "שבת";
$strLoadTime = 'זמן טעינה';
$strDescription = 'תאור';



$lang_modules['addressbook'] = "אנשי קשר";
$cmdShowAll = 'הראה הכול';
$cmdSearch = 'חפש';

//translation required
$cmdYesToAll = 'כן לכל';
$cmdNoToAll = 'לא לכל';

$short_months[0]="ינואר";
$short_months[1]="פבר";
$short_months[2]="מרץ";
$short_months[3]="אפר";
$short_months[4]="מאי";
$short_months[5]="יוני";
$short_months[6]="יולי";
$short_months[7]="אוג";
$short_months[8]="ספט";
$short_months[9]="אוק";
$short_months[10]="נוב";
$short_months[11]="דצמ";

$strToday = 'היום';

$cmdPrint = 'הדפס';
$strFirstName = 'שם פרטי';
$strLastName = 'שם משפחה';
$strInitials = 'ראשי תיבות';
$strSex = 'מין';
$strBirthday = 'יום הולדת';
$strSexes['M'] = 'זכר';
$strSexes['F'] = 'נקבה';

$strTitle = 'תואר';
$strBeginning = 'התחלה';
$strMiddleName = 'שם נוסף';
$sir_madam['M'] = 'אדון';
$sir_madam['F'] = 'גברת';
$strModifiedAt = 'שונה ב';
$strCreatedAt = 'נוצר ב';

$htmlarea_language_name = 'he';
$strLocalization = 'לוקליזציה';


//look up the language in controls/jscalendar/lang
$jscalendar_language_name = 'calendar-en.js';


//2.01 translations

//look up language in 'classes/phpmailer/language'
$php_mailer_lang = 'he';
$strEmailFail = 'שליחת דואר נכשלה';

//for people that want to use ktmlarea
$ktml_language = 'english';

$ml_host_unreachable = ' לא ניתן להציג. בדיקה אוטומטי התבטלה.';
$strRegion = 'אזור';

//2.05
$cmdReset = 'אפס';
$strMyName = 'השם שלי';

//2.06
$strDeleteRow = 'האם אתה בטוח שברצונך למחוק את הרשומה הזאת?';

//modules
$lang_modules['scheduler'] = 'ניהול זמן';
$lang_modules['calendar'] = "לוח שנה";
$lang_modules['filesystem'] = 'קבצים';
$lang_modules['email'] = 'דואל';
$lang_modules['projects'] = 'פרויקטים';
$lang_modules['search'] = 'חיפוש';
$lang_modules['contacts'] = "אנשי קשר";
$lang_modules['cms'] = "אתרים";
$lang_modules['notes'] = "רשומות";
$lang_modules['bookmarks'] = "סימניות";
$lang_modules['summary'] = "סיכום";
$lang_modules['todos'] = "פעוליות";
$lang_modules['opentts'] = "תמיכה";
?>
