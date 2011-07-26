<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = 'E-mail';
$lang['email']['description'] = 'Módulo de e-mail; Cliente de e-mail para internet. Todo usuário pode enviar, receber e encaminhar e-mails';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = 'Você não informou um destinatário';
$lang['email']['feedbackSMTPProblem'] = 'Houve um problema de comunicação com o servidor SMTP: ';
$lang['email']['feedbackUnexpectedError'] = 'Houve um erro inesperado durante a elaboração do e-mail: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Falha ao criar a pasta';
$lang['email']['feedbackDeleteFolderFailed'] = 'Falha ao excluir a pasta';
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
$lang['email']['no_recipients']='Destinatários reservados';
$lang['email']['original_message']='--- Mensagem original ---';
$lang['email']['attachments']='Anexos';

$lang['email']['notification_subject']='Lido: %s';
$lang['email']['notification_body']='Sua mensagem com assunto "%s" foi lida em %s';

$lang['email']['errorGettingMessage']='Não pôde ler mensagem do servidor';
$lang['email']['no_recipients_drafts']='Sem destinatários';
$lang['email']['usage_limit'] = '%s de %s usado';
$lang['email']['usage'] = '%s usado';

$lang['email']['event']='Compromisso';
$lang['email']['calendar']='calendário';

$lang['email']['quotaError']="Sua caixa de e-mails está cheia. Esvazie a lixeira primeiro. Se a lixeira já está vazia e sua caixa de entrada continua cheia, você deve desabilitar a lixeira para excluir mensagens de outras pastas. Você pode desabilitar E-mail -> Administração -> Contas -> Duplo clique na conta de e-mail -> Pastas.";

$lang['email']['draftsDisabled']="A mensagem não pode ser salva porque a pasta 'Rascunhos' está desabilitada.<br /><br />Você pode configurar a pasta 'Rascunhos' em E-mail -> Administração -> Contas -> Duplo clique na conta de e-mail -> Pastas.";
$lang['email']['noSaveWithPop3']='A mensagem não pode ser salva porque uma conta POP3 não suporta isto.'; 

$lang['email']['goAlreadyStarted']='Group-Office já foi inicializado. O editor de e-mail composer está agora carregado no Group-Office. Feche esta janela e escreva sua mensagem no Group-Office.';

//At Tuesday, 07-04-2009 on 8:58 Group-Office Administrator <test@intermeshdev.nl> wrote:
$lang['email']['replyHeader']='Em %s, %s no %s %s escreveu:';
$lang['email']['alias']='Apelido';
$lang['email']['aliases']='Apelidos';

$lang['email']['noUidNext']='Seu servidor de e-mail não suporta UIDNEXT. A pasta \'Rascunhos\' foi desabilitada automaticamente.';

$lang['email']['disable_trash_folder']='Falha ao mover o e-mail para a lixeira. Isto pode ter ocorrido porque você não possui espaço em disco suficiente. Você só pode limpar as pastas desabilitando a lixeira em E-mail -> Administração -> Contas -> Duplo clique na conta de e-mail -> Pastas';

$lang['email']['error_move_folder']='Não foi possível mover a pasta';

$lang['email']['error_getaddrinfo']='O nome do servidor especificado é inválido';
$lang['email']['error_authentication']='Senha ou nome de usuário inváalido';
$lang['email']['error_connection_refused']='A conexão foi recusada. Por favor verifique o nome do servidor e o número da porta.';

