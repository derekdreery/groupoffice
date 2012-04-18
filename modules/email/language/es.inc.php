<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = 'Email';
$lang['email']['description'] = 'Módulo de e-mail. Los usuarios podrán enviar y recibir mails';

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

$lang['email']['no_subject']='Sin asunto';
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

$lang['email']['event']='Evento';
$lang['email']['calendar']='calendario';
$lang['email']['quotaError']="Su casilla esta llena. Vacíe su papelera. Si ya se encuentra vacía y su casilla sigue llena, debe deshabilitar la papelera y luego borrar mensajes de sus otras carpetas. Puede deshabilitar la papelera en:\n\nAdministración -> Cuentas -> doble click en su cuenta -> Carpetas.";
$lang['email']['draftsDisabled']="El mensaje no pudo ser guardado porque la carpeta 'Borradores' está deshabilitada.<br /><br />Vaya a E-Mail -> Administración -> Cuentas -> doble click en su cuenta -> Carpetas para configurarla";
$lang['email']['noSaveWithPop3']='El mensaje no pudo ser guardado porque las cuentas POP3 no permiten esta opción';
$lang['email']['goAlreadyStarted']='Group-Office ya fue cargado. El compositor de e-mail esta cargado en Group-Office. Cierre esta ventana y componga su mensaje en Group-Officei.';
$lang['email']['replyHeader']='A las %s, %s en %s %s escribió:';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Aliases';
$lang['email']['noUidNext']='Su servidor de mail no soporta UIDNEXT. La carpeta \'Drafts\' será deshabilitada para esta cuenta';
$lang['email']['disable_trash_folder']='Fallo al mover el mail a la papelera. Esto podría ser ocasionado porque no tiene mas lugar. Puede liberar lugar deshabilitando la papelera en Administración -> Cuentas -> doble click en su cuenta -> Carpetas';
$lang['email']['error_move_folder']='No se puede mover la carpeta';
$lang['email']['error_getaddrinfo']='Nombre de host invalido';
$lang['email']['error_authentication']='Usuario o contraseña invalidos';
$lang['email']['error_connection_refused']='Error de conexion. Verifique el host y el puerto';
