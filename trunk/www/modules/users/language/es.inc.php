<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('users'));
$lang['users']['name'] = 'Usuarios';
$lang['users']['description'] = 'M�dulo de administraci�n; sistema de gesti�n de los usuarios.';

$lang['users']['deletePrimaryAdmin'] = 'Usted no puede borrar l\'Administrador Principal';
$lang['users']['deleteYourself'] = 'Usted no puede borrar su';

$link_type[8]=$us_user = 'Usuario';

$lang['users']['error_username']='Hay caracteres no v�lidos en el nombre de usuario';
$lang['users']['error_username_exists']='Este nombre de usuario ya existe';
$lang['users']['error_email_exists']='Esta direcci�n de correo electr�nico ya est� registrado. Puede utilizar la funci�n de contrase�a olvidada para recuperar tu contrase�a.';
$lang['users']['error_match_pass']='La contrase�a es incorrecta';
$lang['users']['error_email']='La direcci�n de correo electr�nico no es v�lida';