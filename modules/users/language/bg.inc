<?php
require_once($GO_LANGUAGE->get_fallback_language_file('users'));

$strLogins = "Âëèçàíèÿ";
$strRegistrationDate = "Äàòà íà ðåãèñòðàöèÿ";
$strUsers = "ïîòðåáèòåë(è)";
$user_profile = "Ïðîôèë íà Ïîòðåáèòåëÿ";
$ac_login_info = "Èíôîðìàöèÿ çà âëèçàíåòî";
$ac_registration_time = "Âðåìå íà ðåãèñòðàöèÿ";
$ac_lastlogin = "Ïîñëåäíî âëèçàíå";
$ac_logins = "Îáùî âëèçàíèÿ";
$strMaxOf = "ðåãèñòðèðàí îò ìàêñèìóì";
$strUsername = "Ïîòðåáèòåëñêî èìå";
$delete_fail = "Íå ìîæåòå äà èçòðèåòå âàñ è ñóïåðïîòðåáèòåëÿò.";
$admin_new_user = "Íîâ ïîòðåáèòåë";

$admin_modules = 'Ìîäóëè';
$admin_module_access = 'Òîçè ïîòðåáèòåë èìà äîñòúï äî ñëåäíèòå ìîäóëè:';

$admin_password = 'Ïàðîëà';
$admin_confirm_password = 'Ïîâòîðè ïàðîëà';
$admin_change_password = 'Ïðîìåíåòå ïàðîëà';
$admin_pass_match = 'Ïàðîëàòà íå ñúâïàäà. Ìîëÿ îïèòàéòå îòíîâî.';

//registration
$registration_personal_info = "Ïåðñîíàëíà èíôîðìàöèÿ";
$registration_account_info = "Èíôîðìàöèÿ çà àêàóíòà";
$registartion_username_text = "Âúâåäåòå ïîòðåáèòåëñêî èìå è ïàðîëà çà òîçè ïîòðåáèòåë çà äà âëåçåòå â ñàéòà.";

$registration_question_text=<<<registration_question_text
Âúâåäåòå òàåí âúïðîñ íà êîéòî ñàìî òîçè ïîòðåáèòåë çíàå îòãîâîðà. Ùå çàäàäåì òîçè âúïðîñ â ñëó÷àé ÷å ïîòðåáèòåëÿ çàáðàâè íåãîâàòà/íåéíàòà ïàðîëà.
Êîãàòî å äàäåí ïðàâèëåí îòãîâîð, ùå áúäå èçïðàòåí e-mail íà ðåãèñòðèðàíèÿ e-mail àäðåñ ñ ïîòðåáèòåëñêîòî èìå è íîâà ïàðîëà çà âëèçàíå.
registration_question_text;

$registration_text=<<<registration_text
Ïîïúëíåòå òàçè ôîðìà è íàòèñíåòå 'Ok' çà äà ñå ðåãèñòðèðàòå. Ïîëåòàòà ìàðêèðàíè ñ * çà çàäúëæèòåëíè.<br />
Èíôîðìàöèÿòà êîÿòî ïðåäîñòàâÿòå å çà èçïîëçâàíå îò Group-Office òàêà ÷å íèå íÿìà äà ÿ ïðåäîñòàâÿìå íà òðåòè ëèöà.
registration_text;

$registartion_authcode_text = "Âúâåäåòå êîä çà îòîðèçàöèÿ. Êîäúò çà îòîðèçàöèÿ å êîä îò êîéòî äðóãèòå ïîòðåáèòåëè ùå èìàò íóæäà çà äà äîáàâÿò òîçè ïîòðåáèòåë êúì òåõíèÿ ñïèñúê ñ êîíòàêòè è çà äà âèäÿò âàøèòå äåòàéëè.";
$registartion_confirm_authcode = "Ïîòâúðäåòå êîä çà îòîðèçàöèÿ";
$registration_visibility = "Íàïðàâè òîçè ïîòðåáèòåë âèäèì çà âñè÷êè ïîòðåáèòåëè.";
$registration_success = "<h1>Ïîçäðàâëåíèÿ!</h1>Âèå ñòå ðåãèñòðèðàí óñïåøíî è ñúîáùåíèåòî çà äîáðå äîøúë áåøå èçïðàòåíî äî:";
$registration_failure = "Âúçíèêíàëà å ãðåøêà äîêàòî ñå ðåãèñòðèðàøå âàøàòà èíôîðìàöèÿ. Ìîëÿ îïèòàéòå îòíîâî.";
$registration_mail_subject = "Äîáðå äîøëè â Group-Office.";
$registration_folder_error = "Ñúçäàâàíåòî íà ëè÷íà ïàïêà íåäîâúðøåíî. Ñâúðæåòå ñå ñ íàñ çà äà âúçñòàíîâèòå òîâà!";
$registration_title = "Ðåãèñòðèðàéòå ñå â Group-Office";

$registration_email = "Ñúçäàéòå E-mail àäðåñ çà òîçè ïîòðåáèòåë â Group-Office.";
$registration_samba = "Ôàéë & Ïðèíò ñúðâúð.";
$registration_email_error = "Âúçíèêíàëà å ãðåøêà ïðè ñúçäàâàíåòî íà âàøèÿ E-mail àêàóíò. Ñâúðæåòå ñå ñ íàñ çà ïîâå÷å èíôîðìàöèÿ.";
$strPasswordConfirm = "Ïîâòîðè ïàðîëà";
$strPassword = "Ïàðîëà";

$reg_company_info = "Èíôîðìàöèÿ çà êîìïàíèÿòà";

$max_user_limit = "Ìàêñèìàëíèÿ áðîé ïîòðåáèòåëè å äîñòèãíàò.";
$max_users_text = "Ñúæàëÿâàìå, âèå íå ìîæåòå äà ðåãèñòðèðàòå ïîâå÷å ïîòðåáèòåëè. Ñâúðæåòå ñå ñ íàñ àêî èñêàòå äà óâåëè÷èòå âàøèÿò áðîé ïîòðåáèòåëè.";
$ml_sent_items = "Èçïðàòåíè ïðåäìåòè";
$ml_spam = "Ñïàì";
$ac_samba_user = "Ïîòðåáèòåëÿ èìà äîñòúï äî ôàéëà è ïðèíò ñúðâúðà.";
$ml_email_only = "Ñàìî ñå èçèñêâà êîãàòî íÿìàòå ñúçäàäåí E-mail àäðåñ";

//translation required
$register_new_user = 'Íîâ Group-Office ïîäïèñâà÷';
$register_thanks = '<h2>Áëàãîäàðÿ Âè</h2>. Âàøàòà ðåãèñòðàöèÿ ùå ïðîòå÷å âúçìîæíî íàé-ñêîðî. Ùå ïîëó÷èòå ïîíàòàòúøíà èíôîðìàöèÿ.';
$register_accept = 'Äîáàâè êúì ïîòðåáèòåëè';
$register_addressbook = 'Äîáàâè êúì adressbook';
$register_title = 'Ïîäïèñâàì';

//modified:
$registration_mail_body=<<<registration_mail_body
Ñêúïè ã-í/ã-æî %name%,

Âèå ñòå ðåãèñòðèðàíè â Group-Office.
Íàäÿâàìå ñå ÷å ùå îñòàíåòå äîâîëíè îò íàøèòå óñëóãè.

Âèå ìîæå äà âëåçåòå ñúñ ñëåäíèòå äàííè:

Èìå: %username%
Ïàðîëà: %password%

Íàòèñíåòå íà âðúçêàòà ïî-äîëó çà äà âëåçåòå:

registration_mail_body;
