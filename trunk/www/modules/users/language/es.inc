<?php
require_once($GO_LANGUAGE->get_fallback_base_language_file('users'));

$strLogins = "Entradas";
$strRegistrationDate = "Fecha de registro";
$strUsers = "usuario(s)";
$user_profile = "Perfil del usuario";
$ac_login_info = "Información de entrada";
$ac_registration_time = "Hora de registro";
$ac_lastlogin = "Última entrada";
$ac_logins = "Entradas totales";
$strMaxOf = "registrado desde el máximo de";
$strUsername = "Nombre de usuario";
$delete_fail = "No puede autoeliminarse ni elimninar al superusuario.";
$admin_new_user = "Nuevo usuario";

$admin_modules = 'Módulos';
$admin_module_access = 'Este usuario tiene acceso a los siguientes módulos:';

$admin_password = 'Contraseña';
$admin_confirm_password = 'Confirmar contraseña';
$admin_change_password = 'Cambiar contraseña';
$admin_pass_match = 'Las contraseñas no coinciden. Por favor, inténtelo de nuevo.';

//registration
$registration_personal_info = "Información personal";
$registration_account_info = "Información de la cuenta";
$registartion_username_text = "Introduzca un nombre de usuario y contraseña para este usuario para entrar en el sistema.";

$registration_question_text=<<<registration_question_text
Introduzca una pregunta que sólo este usuario sabrá responder. Se hará esta pregunta en el caso que el usuario olvide su contraseña.
Cuando se responda correctamente, se enviará un correo electrónico a la dirección registrada con el nombre de usuario y la nueva contraseña para entrar.
registration_question_text;

$registration_text=<<<registration_text
Rellene este formulario y haga click en 'Aceptar" para registrarse. Los campos marcados con * son obligatorios.<br />
La información que introduzca es sólo para utilizarla dentro de Group-Office, por tanto no será entregada a terceras partes.
registration_text;

$registartion_authcode_text = "Introduzca un código de autorización. Este código será el que otros usuarios necesitarán para agregar este usuario a sus libretas de contactos y ver sus detalles.";
$registartion_confirm_authcode = "Confirmar el código de autorización";
$registration_visibility = "Hacer visible este usuario a todos los demás.";
$registration_success = "<h1>¡Felicidades!</h1>Se ha registrado correctamente y un mensaje de bienvenida ha sido enviado a:";
$registration_failure = "Ha ocurrido un error mientras se trataba de registrar su información. Por favor, inténtelo de nuevo.";

$registration_folder_error = "Ha fallado la creación de la carpeta personal. Contacte con su administrador del sistema para recuperarla!";


$registration_email = "Crear una dirección electrónica para este usuario en Group-Office.";
$registration_samba = "Servidor de ficheros e impresoras.";
$registration_email_error = "Ha ocurrido un error durante la creación de su cuenta de correo electrónico. Contacte con su administrador del sistema.";
$strPasswordConfirm = "Confirmar contraseña";
$strPassword = "Contraseña";

$reg_company_info = "Información de la empresa";

$max_user_limit = "Se ha alcanzado el máximo de usuarios.";
$max_users_text = "Lo siento, no puede registrar más usuarios. Contacte con el administrador del sistema para aumentar el número de usuarios.";
$ml_sent_items = "Elementos enviados";
$ml_spam = "Correo basura";
$ac_samba_user = "El usuario tiene acceso al servidor de ficheros e impresoras.";
$ml_email_only = "Sólo se requiere cuando no haya creado una dirección de correo electrónico";
$register_new_user = 'Nuevo subscriptor a Group-Office';
$register_thanks = '<h2>Gracias</h2>Su subscripción será procesada tan pronto como sea posible. Recibirá un aviso adicional.';
$register_accept = 'Agregar a usuarios';
$register_addressbook = 'Agregar a la libreta de direcciones';
$register_title = 'Subscribir';


//1.98
$reg_language = 'Idioma';
$reg_theme = 'Tema';

//2.05
$admin_use = 'Usar';
$admin_module = 'Módulo';
$admin_manage = 'Gestiona';

$admin_groups = 'Grupos de usuarios';
$admin_groups_user = 'El usuario es miembro de los siguientes grupos de usuarios';

//2.11

//Modified string (Added %groupoffice_title%)
$registration_mail_body=<<<registration_mail_body
Apreciado %title% %last_name%,

Ha sido registrado en %groupoffice_title%.
Esperamos que disfrute de este servicio.

Puede entrar utilizando los siguientes datos:

Nombre de usuario: %username%
Contraseña: %password%

Haga click en el enlace de abajo para entrar:

registration_mail_body;

//2.13
$registration_mail_subject = "Bienvenido a %groupoffice_title%"; //changed string
$registration_title = "Registrar en %groupoffice_title%"; //changed string

$lang_modules['users'] = 'Usuarios';

//2.14
$users_enabled = 'Cuenta activada';

$admin_groups_visible = 'El usuario es visible para los siguientes grupos de usuarios';
$admin_look_feel = 'Apariencia';
