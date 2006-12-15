<?php
//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_language_file('users'));

$strLogins = 'Přihlášení';
$strRegistrationDate = 'Datum registrace';
$strUsers = 'uživatel(é)';
$user_profile = 'Profil uživatele';
$ac_login_info = 'Informace k přihlášení';
$ac_registration_time = 'Čas registrace';
$ac_lastlogin = 'Poslední přihlášení';
$ac_logins = 'Celkový počet přihlášení';
$strMaxOf = 'registrováno od maximálního počtu';
$strUsername = 'Uživatelské jméno';
$delete_fail = 'Administrátora a sebe smazat nemůžete.';
$admin_new_user = 'Nový uživatel';

$admin_modules = 'Moduly';
$admin_module_access = 'Tento uživatel má přístup k následujícím modulům:';

$admin_password = 'Heslo';
$admin_confirm_password = 'Potvrdit heslo';
$admin_change_password = 'Potvrdit heslo';
$admin_pass_match = 'Hesla neodpovídájí. Prosím zkuste to znovu.';

//registration
$registration_personal_info = 'Osobní informace';
$registration_account_info = 'Informace k účtu';
$registartion_username_text = 'Zadejte uživatelské jméno a heslo pro tohoto uživatele pro přihlášení na síť.';

$registration_question_text=<<<registration_question_text
Vložte bezpečnostní otázku na kterou zná odpověď jen tento uživatel. Položíme mu tuto otázku v případě, že uživatel zapomene heslo. 
Když správně odpoví bude mu zaslán e-mail s uživatelským jménem a novým heslem pro přihlášení.
registration_question_text;

$registration_text=<<<registration_text
Vyplňte tento formulář a klikněte na 'Ok' pro registraci. Pole označená * jsou nezbytná.<br />
Informace, které poskytujete jsou pouze pro potřeby Group Office a nebudou poskytnuty třetím stranám. 
registration_text;

$registartion_authcode_text = 'Zadejte přístupový kód. Přístupový kód je kód, kteří potřebují ostatní uživatelé pro přidání tohoto uživatele do svých kontaktů a prohlédnutí detailů.';
$registartion_confirm_authcode = "Potvrďte přístupový kód";
$registration_visibility = 'Zviditelnit užívatele pro všechny.';
$registration_success = '<h1>Gratulujeme!</h1>Jste úspěšně zaregistrován a uvítací zpráva byla poslána:';
$registration_failure = 'Během registrace vašich informací se stala chyba. Prosím zkuste to znovu.';
$registration_mail_subject = 'Vítejte v Group-Office.';
$registration_folder_error = 'Neúspěšné vytvoření složky. Kontaktujte nás pro znovuvytvoření!';
$registration_title = 'Zaregistrovat se na Group-Office';

$registration_email = 'Vytvořit e-mailový účet pro tohoto uživatele.';
$registration_samba = 'Souborový a tiskový server.';
$registration_email_error = 'Během vytváření vašeho e-mailového účtu se stala chyba. Kontaktuje nás.';
$strPasswordConfirm = 'Potvrdit heslo';
$strPassword = 'Heslo';

$reg_company_info = 'Informace společnosti';

$max_user_limit = 'Dosažení maximálního počtu uživatelů.';
$max_users_text = 'Promiňte nemůžeme přidat už více uživatelů. Kontaktujte nás, jestliže chcete zvýšit váš počet uživatelů.';
$ml_sent_items = 'Odeslaná pošta';
$ml_spam = 'Nevyžádané e-maily';
$ac_samba_user = 'Uživatel má přístup k souborovému a tiskovému serveru.';
$ml_email_only = 'Nezbytné, jen když nevytvoříte e-mailovou adresu.';
$register_new_user = 'Nový zápis do Group-Office';
$register_thanks = '<h2>Děkujeme</h2>Váš zápis bude zpracován jakmile to bude možné. Obdržíte další oznámení.';
$register_accept = 'Přidat k uživatelům';
$register_addressbook = 'Přidat do adresáře';
$register_title = 'Zapsat se';


//1.98
$reg_language = 'Jazyk';
$reg_theme = 'Téma';

//2.05
$admin_use = 'Použít';
$admin_module = 'Modul';
$admin_manage = 'Spravovat';

$admin_groups = 'Skupiny uživatelů';
$admin_groups_user = 'Uživatel je členem následujících skupin';

//2.11

//Modified string (Added %groupoffice_title%)
$registration_mail_body=<<<registration_mail_body
Vážená/ý,

Byl jste registrován na Group-Office.
Doufáme, že budete s našimi službami spokojen(a).

Přihlásit se můžete pomocí následujících údajů:

Uživatelské jméno: %username%

Pro přihlášení klikněte na odkaz uvedný níže:

registration_mail_body;
?>
