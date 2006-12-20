<?php
require_once($GO_LANGUAGE->get_fallback_base_language_file('users'));

$strLogins = "Entrades";
$strRegistrationDate = "Data de registre";
$strUsers = "usuari(s)";
$user_profile = "Perfil de l'usuari";
$ac_login_info = "Informació d'entrada";
$ac_registration_time = "Hora de registre";
$ac_lastlogin = "Darrera entrada";
$ac_logins = "Entrades totals";
$strMaxOf = "registrat des del màxim de";
$strUsername = "Nom d'usuari";
$delete_fail = "Ni us podeu autosuprimir ni podeu suprimir al superusuari.";
$admin_new_user = "Nou usuari";

$admin_modules = 'Mòduls';
$admin_module_access = 'Aquest usuari té accés als següents mòduls:';

$admin_password = 'Contrassenya';
$admin_confirm_password = 'Confirmeu contrassenya';
$admin_change_password = 'Canvia contrassenya';
$admin_pass_match = 'Les contrassenyes no coincideixen. Si us plau, torneu-ho a provar.';

//registration
$registration_personal_info = "Informació personal";
$registration_account_info = "Informació del compte";
$registartion_username_text = "Introduïu un nom d'usuari i contrassenya per aquest usuari per entrar al sistema.";

$registration_question_text=<<<registration_question_text
Introduïu una pregunta que només aquest usuari sabrà respondre. Es farà aquesta pregunta en el cas que l'usuari oblidi la seva contrassenya.
Quan es respongui correctament, s'enviarà un correu electrònic a l'adreça registrada amb el nom d'usuari i la nova contrasenya per entrar.
registration_question_text;

$registration_text=<<<registration_text
Empleneu aquest formulari i feu clic a 'D'acord' per registrar-vos. Els camps marcats amb * són obligatoris.<br />
La informació que introduïu és només per fer-la servir dins de Group-Office, per tant, no serà lliurada a terceres parts.
registration_text;

$registartion_authcode_text = "Introduïu un codi d'autorització. Aquest codi serà el que els altres usuaris necessitaran per afegir aquest usuari a les seves llistes de contactes i veure els vostres detalls.";
$registartion_confirm_authcode = "Confimeu el codi d'autorització";
$registration_visibility = "Fés visible aquest usuari a tots els altres.";
$registration_success = "<h1>Felicitats!</h1>Heu estat registrats correctament i s'ha enviat un missatge de benvinguda a:";
$registration_failure = "Hi ha hagut un error mentre es registrava la vostra informació. Si us plau, torneu-ho a provar.";

$registration_folder_error = "Ha fallat la creació de la carpeta personal. Contacteu amb el vostre administrador del sistema per recuperar-la!";


$registration_email = "Crea una adreça electrònica per aquest usuari a Group-Office.";
$registration_samba = "Servidor de fitxers i impresores.";
$registration_email_error = "Hi ha hagut un error durant la creació del vostre compte de correu electrònic. Contacteu amb el vostre administrador del sistema.";
$strPasswordConfirm = "Confirmeu contrassenya";
$strPassword = "Contrassenya";

$reg_company_info = "Informació de l'empresa";

$max_user_limit = "S'ha assolit el màxim d'usuaris.";
$max_users_text = "Ho sento, no podeu registrar més usuaris. Contacteu amb el vostre administrador del sistema si voleu augmentar el número d'usuaris.";
$ml_sent_items = "Elements enviats";
$ml_spam = "Correu brossa";
$ac_samba_user = "L'usuari té acceś al servidor de fitxers i impresores.";
$ml_email_only = "Només es requereix quan no hàgiu creat una adreça de correu electrònic";
$register_new_user = 'Nou subscriptor a Group-Office';
$register_thanks = '<h2>Gràcies</h2>La vostra subscripció serà processada tant aviat com sigui possible. Rebreu un avís addicional.';
$register_accept = 'Afegeix a usuaris';
$register_addressbook = 'Afegeix a la llibreta d\'adreces';
$register_title = 'Subscriu';


//1.98
$reg_language = 'Idioma';
$reg_theme = 'Tema';

//2.05
$admin_use = 'Fés servir';
$admin_module = 'Mòdul';
$admin_manage = 'Gestiona';

$admin_groups = 'Grups d\'usuaris';
$admin_groups_user = 'L\'usuari és membre dels següents grups d\'usuaris';

//2.11

//Modified string (Added %groupoffice_title%)
$registration_mail_body=<<<registration_mail_body
Benvolgut %title% %last_name%,

Heu estat registrats a %groupoffice_title%.
Esperem que gaudiu d'aquest servei.

Podeu entrar-hi amb les següents dades:

Nom d'usuari: %username%
Contrassenya: %password%

Feu clic a l'enllaç de sota per entra-hi:

registration_mail_body;

//2.13
$registration_mail_subject = "Benvingut a %groupoffice_title%"; //changed string
$registration_title = "Registre a %groupoffice_title%"; //changed string

$lang_modules['users'] = 'Usuaris';

//2.14
$users_enabled = 'Compte activat';

$admin_groups_visible = 'L\'usuari és visible pels següents grups d\'usuaris';
$admin_look_feel = 'Aparença';
