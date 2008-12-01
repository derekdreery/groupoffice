<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('email'));
$lang['email']['name'] = 'Email';
$lang['email']['description'] = 'Formulario de e-mail, Webbased peque�os cliente de correo electr�nico. Cada usuario podr� enviar, recibir y enviar e-mail';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = 'Usted no entrar en una persona';
$lang['email']['feedbackSMTPProblem'] = 'Este es un problema de comunicaci�n con SMTP:';
$lang['email']['feedbackUnexpectedError'] = 'Es un error inesperado en e-mail:';
$lang['email']['feedbackCreateFolderFailed'] = 'No se puede crear la carpeta';
$lang['email']['feedbackSubscribeFolderFailed'] = 'No se pudo registrar la carpeta';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'No se pudo deregistrar la carpeta';
$lang['email']['feedbackCannotConnect'] = 'No se pudo conectar con %1$s<br/><br/> El servidor de correo devuelto: %2$s';
$lang['email']['inbox'] = 'Bandeja de entrada';

$lang['email']['spam']='Spam';
$lang['email']['trash']='Papelera';
$lang['email']['sent']='Mensajes enviados;
$lang['email']['drafts']='Borradores';

$lang['email']['no_subject']='No hay objeto';
$lang['email']['to']='A';
$lang['email']['from']='Da';
$lang['email']['subject']='Objeto';
$lang['email']['no_recipients']='No hay receptor';
$lang['email']['original_message']='--- Mensaje original ---';
$lang['email']['attachments']='Adjuntos';

$lang['email']['notification_subject']='Leer: %s';
$lang['email']['notification_body']='u mensaje con el asunto "%s" se muestra a %s';