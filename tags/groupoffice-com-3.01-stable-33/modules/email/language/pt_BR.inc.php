<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('email'));
$lang['email']['name'] = 'Email';
$lang['email']['description'] = 'Módulo de e-mail; Cliente de e-mail para internet. Todo usuário pode enviar, receber e encaminhar e-mails';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = 'Você não informou um destinatário';
$lang['email']['feedbackSMTPProblem'] = 'Houve um problema de comunicação com o servidor SMTP: ';
$lang['email']['feedbackUnexpectedError'] = 'Houve um erro inesperado durante a elaboração do e-mail: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Falha ao criar a pasta';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Falha ao inscrever a pasta';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Falha ao desinscrever a pasta';
$lang['email']['feedbackCannotConnect'] = 'Não pôde se conectar a %1$s na porta %3$s<br /><br />O servidor retornou: %2$s';
$lang['email']['inbox'] = 'Entrada';

$lang['email']['spam']='Spam';
$lang['email']['trash']='Lixo';
$lang['email']['sent']='Enviados';
$lang['email']['drafts']='Rascunhos';

$lang['email']['no_subject']='Sem assunto';
$lang['email']['to']='Para';
$lang['email']['from']='De';
$lang['email']['subject']='Assunto';
$lang['email']['no_recipients']='Undisclosed recipients';
$lang['email']['original_message']='--- Mensagem original ---';
$lang['email']['attachments']='Anexos';

$lang['email']['notification_subject']='Lido: %s';
$lang['email']['notification_body']='Sua mensagem com assunto "%s" foi lida em %s';

$lang['email']['errorGettingMessage']='Não pôde ler mensagem do servidor';
$lang['email']['no_recipients_drafts']='Sem destinatários';
$lang['email']['usage_limit'] = '%s de %s usado';
$lang['email']['usage'] = '%s usado';