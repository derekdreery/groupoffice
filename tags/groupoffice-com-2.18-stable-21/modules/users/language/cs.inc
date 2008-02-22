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


//2.13
$registration_mail_subject = "Vítejte v %groupoffice_title%"; //changed string
$registration_title = "Registrujte se na %groupoffice_title%"; //changed string

$lang_modules['users'] = 'Uživatelé';

//2.14
$users_enabled = 'Účet povolen';

$admin_groups_visible = 'Uživatel je viditelný pro následující skupiny';
$admin_look_feel = 'Vzhled';

//2.16
$disabled_user_fields_text = 'Vyber pole, která chcete zakázat pro editaci uživateli na jejich profilové stránce.';

$admin_disabled_user_fields = 'Zakaž uživatelská pole';
$admin_registration_unconfirmed = 'Nepotvrzený registrační e-mail';
$admin_registration_confirmation = 'Potvrzený registrační e-mail';

$admin_registration_confirmation_text = 'Tento e-mail obdrží uživatelé při aktivaci nového účtu. Pokud zanecháte prázdné, nic nebude posláno.';
$admin_registration_unconfirmed_text = 'Tento e-mail obdrží uživatelé při neaktivovaném novém účtu. Pokud zanecháte prázdné, nic nebude posláno.';

$admin_subject = 'Předmět';

//string modified:
$registration_success = "<h1>Blahopřejeme!</h1>Nový uživatel byl úspěšně vytvořen.";
$registration_success_activate = "<h1>Aktivace je vyžedována</h1>Nový uživatel byl úspěšně vytvořen, ale administrátor musí aktivovat Váš účet.";


$admin_new_user_subject = 'Nový uživatel registrován';
$admin_new_user_body = 'Nový uživatel registrován. Klikněte na odkaz níže pro otevřění účtu.';
$admin_new_user_subject_activate = 'Nový uživatel registrován. Požadována registrace';


$admin_password_changed = 'Heslo úspěšně změněno!';


$registration_self_success = 'Vítejte v %s!';
$registration_self_success_text = 'Váš účet byl úspěšně vytvořen . Nyní se můžete přihlásit s Vaším jménem a heslem.';
$us_reset='Reset';
$us_never='Nikdy';

$us_help_url='http://docs.group-office.com/index.php?folder_id=53&file_id=0';
