<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('email'));
$lang['email']['name'] = 'Email';
$lang['email']['description'] = 'Módulo de e-mai. Los usuarios podran enviar y recibir mails';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = 'Debe ingresar un destinatario';
$lang['email']['feedbackSMTPProblem'] = 'Problema de conexión con el servidor SMTP:';
$lang['email']['feedbackUnexpectedError'] = 'Error inesperado al crear el email';
$lang['email']['feedbackCreateFolderFailed'] = 'No se pude crear la carpeta';
$lang['email']['feedbackSubscribeFolderFailed'] = 'No se pudo borrar la carpeta';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'No se pudo desregistrar la carpeta';
$lang['email']['feedbackCannotConnect'] = 'No se pudo conectar con %1$s puerto %3$s<br/><br/> El servidor de correos devolvió: %2$s';
$lang['email']['inbox'] = 'Bandeja de entrada';

$lang['email']['spam']='Spam';
$lang['email']['trash']='Papelera';
$lang['email']['sent']='Mensajes enviados';
$lang['email']['drafts']='Borradores';

$lang['email']['no_subject']='No hay asunto';
$lang['email']['to']='Para';
$lang['email']['from']='De';
$lang['email']['subject']='Asunto';
$lang['email']['no_recipients']='Destinatarios ocultos';
$lang['email']['original_message']='--- Mensaje original ---';
$lang['email']['attachments']='Adjuntos';

$lang['email']['notification_subject']='Leer: %s';
$lang['email']['notification_body']='Su mensaje con el asunto "%s" fue mostrado a las %s';
$lang['email']['feedbackDeleteFolderFailed']= 'No se pudo eliminar la carpeta';
$lang['email']['errorGettingMessage']='No se pudo obtener mensaje del servidor';
$lang['email']['no_recipients_drafts']='Sin destinatarios';
$lang['email']['usage_limit']= '%s de %s usado';
$lang['email']['usage']= '%s usado';
