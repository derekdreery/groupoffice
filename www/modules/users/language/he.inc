<?php
//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_language_file('users'));
$htmldirection='rtl';
$charset='UTF-8';

$strLogins = "כניסות";
$strRegistrationDate = "תאריך הרשמה";
$strUsers = "משתמש/ים";
$user_profile = "פרופיל משתמש";
$ac_login_info = "מידע כניסה";
$ac_registration_time = "זמן הרשמה";
$ac_lastlogin = "כניסה אחרונה";
$ac_logins = "סך הכניסות";
$strMaxOf = "מתוך מקסימום של";
$strUsername = "שם משתמש";
$delete_fail = "אינך יכול למחוק את עצמך ואת משתמש העל.";
$admin_new_user = "משתמש חדש";

$admin_modules = 'מודולים';
$admin_module_access = 'למשתמש זה גישה למודולים הבאים:';

$admin_password = 'סיסמה';
$admin_confirm_password = 'אשר סיסמה';
$admin_change_password = 'שנה סיסמה';
$admin_pass_match = 'סיסמה לא תואמת, נסה שנית.';

//registration
$registration_personal_info = "מידע אישי";
$registration_account_info = "מידע חשבון";
$registartion_username_text = "הזן שם משתמש וסיסמה עבודר משתמש זה כדי להכנס לאתר.";

$registration_question_text=<<<registration_question_text
הזן שאלה סודית עבורה רק משתמש זה יודע את התשובה. השאלה תשאל במקרה שהמשתמש שכח את סיסמתו.
בהנתן התשובה הנכונה ישלח e-mail אל כתובת ה- e-mail הרשומה למשתמש עם סיסמת כניסה חדשה.
registration_question_text;

$registration_text=<<<registration_text
יש למלא טופס זה ולהקיש 'Ok' להרשמה. השדות המסומנים ב-* נחוצים.<br />
המידע ישמש רק ב- Group-Office ולא יועבר לידי צד שלישי.
registration_text;

$registartion_authcode_text = "הזן קוד אימות. קוד האימות הוא קוד שמשתמשים אחרים  יצטרכו כדי להוסיפך לרשימת אנשי הקשר שלהם ולצפות בפרטיך.";
$registartion_confirm_authcode = "אשר קוד אימות";
$registration_visibility = "הצג משתמש זה לשאר המשתמשים.";
$registration_success = "<h1>מזל טוב!</h1>נרשמת בהצלחה והודעת ברכה נשלחה ל:";
$registration_failure = "שגיאה ארעה בעת הניסיון לרשום את נתוניך. אנא נסה שנית.";
$registration_mail_subject = "ברוך הבא ל- Group-Office.";
$registration_folder_error = "כשל ביצירת התיקיה האישית. צור עימנו קשר לתיקון הבעיה.";
$registration_title = "הרשם ב- Group-Office";

$registration_email = "צור כתובת E-mail למשתמש זה ב- Group-Office.";
$registration_samba = "שרת קבצים והדפסות.";
$registration_email_error = "ארעה שגיאה בעת יצירת חשבון ה- E-mail שלך. יש ליצור עמנו קשר לקבלת תמיכה.";
$strPasswordConfirm = "אשר סיסמה";
$strPassword = "סיסמה";

$reg_company_info = "מידע על החברה";

$max_user_limit = "מגבלת מספר המשתמשים הושגה.";
$max_users_text = "אנו מצטערים אך אינך יכול לרשום משתמשים נוספים. צור עמנו קשר אם ברצונך להגדיל את מספר המשתמשים שלך.";
$ml_sent_items = "פריטים שנשלחו";
$ml_spam = "ספאם";
$ac_samba_user = "למשתמש גישה לשרת הקבצים וההדפסה.";
$ml_email_only = "נחוץ רק כאשר אינך יוצר כתובת E-mail";
$register_new_user = 'נרשם חדש ב- Group-Office';
$register_thanks = '<h2>תודה</h2>הרשמתך תעובד בהקדם האפשרי. הודעה נוספת תשלח אליך.';
$register_accept = 'הוסף למשתמשים';
$register_addressbook = 'הוסף לפנקס הכתובות';
$register_title = 'הרשם';
$registration_mail_body=<<<registration_mail_body
 %שם משפחה% %sex%,

נרשמת ב- Group-Office.
אנו מקווים כי תהנה משרותינו.

לכניסה יש להזין את המידע הבא:

שם משתמש: %username%
סיסמה: %password%

לחץ על הקישור למטה לכניסה:

registration_mail_body;

//1.98
$reg_language = 'שפה';
$reg_theme = 'ערכה';

//2.05
$admin_use = 'השתמש';
$admin_module = 'מודול';
$admin_manage = 'מודול';

$admin_groups = 'קבוצות משתמשים';
$admin_groups_user = 'המשתמש רשום בקבוצות הבאות:';
?>
