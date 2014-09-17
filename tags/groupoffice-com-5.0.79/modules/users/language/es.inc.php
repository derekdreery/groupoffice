<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name'] = 'Usuarios';
$lang['users']['description'] = 'Módulo de administración; sistema de gestión de los usuarios.';

$lang['users']['deletePrimaryAdmin'] = 'Usted no puede borrar l\'Administrador Principal';
$lang['users']['deleteYourself'] = 'Usted no puede borrar su';

$lang['link_type'][8]=$us_user = 'Usuario';

$lang['users']['error_username']='Hay caracteres no válidos en el nombre de usuario';
$lang['users']['error_username_exists']='Este nombre de usuario ya existe';
$lang['users']['error_email_exists']='Esta dirección de correo electrónico ya está registrado. Puede utilizar la función de contraseña olvidada para recuperar tu contraseña.';
$lang['users']['error_match_pass']='La contraseña es incorrecta';
$lang['users']['error_email']='La dirección de correo electrónico no es válida';

$lang['users']['imported']='%s importados de usuario';
$lang['users']['failed']='Error';
$lang['users']['incorrectFormat']='Archivo no era correcta en formato CSV';
$lang['users']['register_email_subject']='Detalles de su nueva cuenta';
$lang['users']['register_email_body']='Se ha creado una cuenta {product_name} para Ud. en {url}
Sus datos de logueo son:

Usuario: {username}
Contraseña: {password}';

$lang['users']['error_user']='No se pudo crear el usuario';
$lang['users']['max_users_reached']='Se ha alcanzado el número máximo de usuarios para este sistema';

