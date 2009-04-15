<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('email'));
$lang['email']['name'] = 'Email';
$lang['email']['description'] = 'Formulario de e-mail, Webbased pequeños cliente de correo electrónico. Cada usuario podrá enviar, recibir y enviar e-mail';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = 'Usted no entrar en una persona';
$lang['email']['feedbackSMTPProblem'] = 'Este es un problema de comunicación con SMTP:';
$lang['email']['feedbackUnexpectedError'] = 'Es un error inesperado en e-mail:';
$lang['email']['feedbackCreateFolderFailed'] = 'No se puede crear la carpeta';
$lang['email']['feedbackSubscribeFolderFailed'] = 'No se pudo registrar la carpeta';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'No se pudo deregistrar la carpeta';
$lang['email']['feedbackCannotConnect'] = 'No se pudo conectar con %1$s<br/><br/> El servidor de correo devuelto: %2$s';
$lang['email']['inbox'] = 'Bandeja de entrada';

$lang['email']['spam']='Spam';
$lang['email']['trash']='Papelera';
$lang['email']['sent']='Mensajes enviados';
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
$lang['email']['feedbackDeleteFolderFailed']= 'No se ha podido eliminar la carpeta';
$lang['email']['errorGettingMessage']='No se pudo obtener mensaje de server';
$lang['email']['no_recipients_drafts']='No hay receptores';
$lang['email']['usage_limit']= '%s de %s usado';
$lang['email']['usage']= '%s usado';