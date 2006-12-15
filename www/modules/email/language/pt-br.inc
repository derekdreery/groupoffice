<?php
require_once($GO_LANGUAGE->get_fallback_language_file('email'));

//Email
$ml_folder_not_available = 'Você não pode criar pastas com contas do tipo POP-3 ';
$ml_from = "De";
$ml_to = " Para";
$ml_send = "Enviar";
$ml_attach = "Anexar";
$ml_attachments = "Anexos";
$ml_attachment = "Anexo";
$ml_high = "Elevada";
$ml_normal = "Normal";
$ml_low = "Baixa";
$ml_priority = "Prioridade";
$ml_subject = "Assunto";
$ml_filename = "Nome do arquivo";
$ml_total_size = "Tamanho total";
$ml_size = "Tamanho";
$ml_to_empty = "você não especificou um destinatário.";
$ml_print = "Imprimir";
$fbNoSelect = "você não selecionou uma mensagem.";

$ml_inbox = 'Caixa de Entrada';
$ml_properties = 'Propriedades';
$ml_add_account = "Adicionar uma conta";
$em_new_folder = "Nova Pasta";
$ml_your_accounts = "Suas contas de E-mail";
$ml_no_accounts = "você não tem nenhuma conta de E-mail configurada.";
$ml_original_follows = "----- Mensagem Original -----";
$ml_welcome = "Bem-vindo ao Group Office - E-mail";
$ml_text = "Clique sobre Contas para adicionar um cliente de E-mail.";
$ml_accounts = "Contas";
$ml_compose = "Escrever";
$ml_delete = "Excluir";
$ml_refresh = "Atualizar";
$ml_reply = "Responder";
$ml_forward = "Encaminhar";
$ml_reply_all = "Responder a todos";
$ml_delete_message = "Tem certeza que deseja exluir permanentemente esta mensagem?";
$ml_no_subject = "Nenhum assunto";
$ml_no_reciepent = "destinatário inválido";
$ml_online_files = "Arquivo online";
$ml_send_error = "Falha ao enviar a mensagem.";

$ml_attach_forget = "você selecionou um arquivo mas não anexou-o.";
$ml_messages = "Mensagem(ns)";
$ml_message = 'Esta mensagem';
$ml_messages2 = 'Mensagens';
$ml_new = "Nova";
$ml_delete_mailbox = "Tem certeza que deseja excluir permanentemente as mensagens selecionadas?";

$contact_profile = "Perfil do contato";
$ml_add_contact = "Adicione o remetente aos contatos";
$strHost = "Servidor";
$strDefault = "Valor comum";
$ml_edit_account = "Edite a sua conta";
$ml_new_account = "Conta nova";
$ml_no_messages = "não há nenhuma mensagem nesta pasta.";
$ml_file_too_big = "O arquivo que você tentou anexar está muito grande, o tamanho máximo para anexos está ajustado para: ";
$fbNoRightToOverwrite = "O arquivo já existe e você nâo tem permissão para sobreescrever o arquivo.";
$ml_save_attachment = "Salvar o arquivo como anexo no Group Office.";
$ml_folders = "Pastas";

$ml_select_folder = "Selecione a pasta onde o Phoebus Internet-Tools pode guardar o correio enviado ou insira um nome abaixo para criar uma pasta";
$ml_connect_failed = "A conexão com o servidor de correio falhou:";
$ml_at_port = "na porta";
$ml_solve_error = 'Clique \'Contas\' para ajustar a sua conta ou clique \'Atualizar\' para abrir uma conta comum.';
$ml_sent_items = "Itens enviados";
$ml_spam = "Spam";
$ml_use = "Uso";
$ml_synchronise = "sincronize";
$ml_move_mail = "Mudar as mensagens selecionadas para a pasta...";
$ml_filters = "Filtros";
$ml_move_to = "Mova a mensagem para a pasta:";
$ml_email_is = "Se o endereço do remetente contém:";
$ml_to_is = "Se o campo de remetentes contém:";
$ml_cc_is = "Se o campo de remetentes em cópia carbono contém:";

$ml_subject_is = "Se o assunto contém:";
$ml_new_filter = "Filtro novo:";
$ml_field = "Campo";
$ml_contains = "contém";
$ml_folder = "Pasta";
$ml_no_folders = "você não tem nenhuma pasta então não pode criar filtros.";
$ml_unwanted = "Clique aqui se o E-mail deste remetente não for desejado";
$fbFolderSize = "Tamanho da pasta";
$ml_move_up = "Aumentar a prioridade";
$ml_delete_filter = "Tem certeza que quer excluir este filtro?";
$ml_high_priority = "Esta mensagem tem prioridade elevada!";
$ml_low_priority = "Esta mensagem tem prioridade baixa.";
$ml_block = "Bloquear o remetente";
$ml_sent_items_fail = "A mensagem foi enviada mas houve um erro ao criar esta mensagem na pasta de itens enviados.  Por favor verifique a sua conta IMAP.";
$fbProperties = "Propriedades";
$ml_notification = "Requisite confirmação de leitura";
$ml_ask_notify = "O remetente desta mensagem pediu uma notificação que você leu a mensagem. Deseja emitir um aviso de leitura?";
$ml_displayed = "O seguinte E-mail foi indicado a ";
$ml_notify = "notificação Como Lido";
$ml_delete_filter1 = "Excluir Filtro";
$ml_html_message_attached = "Mensagens originais do tipo HTML estão anexadas";
$ml_add_recievers = "Adicione destinatários desconhecidos ao seu Livro de endereços";
$ml_no_folders = 'Você não tem nenhuma pasta para onde filtar';
$ml_no_filters = 'Você não tem nenhum filtros.';

$ml_host = 'Servidor';
$ml_port = 'Porta';
$ml_type = 'Tipo';
$ml_servertype = 'Tipo de servidor';
$ml_format = 'Formato';
$ml_loose_changes = 'As mudanças serão perdidas. Tem certeza  que deseja continuar?';

$ml_markread = "marcar como lido";
$ml_markunread = "marcar como não lido";
$ml_root_mailbox = "Caixa de Entrada";
$ml_inside = "em";

$ml_no_mailbox = "Esta não está uma caixa postal. Esta pasta não contém nenhuma mensagem.";
$ml_signature = 'Assinatura';
$ml_root = 'Caixa de Entrada';
$ml_disable_accounts = 'Administração da Conta desabilitada';
$ml_trash = 'Lixo';

$ml_disable = 'Desativar';
$ml_confirm_empty_mailbox = 'você tem certeza que deseja esvaziar esta pasta (apagar tudo)?';
$ml_empty_mailbox = 'Esvaziar';
$ml_you_have = 'você tem';
$ml_new_mail_single = 'nova mensagem.';
$ml_new_mail_multiple = 'nova mensagens.';
$ml_auto_check = 'Verificar automaticamente novas mensagens para esta conta.';
//1.98
$ml_no_contacts_in_mailing_group = 'não existem contatos na lista de e-mails que você selecionou. Por favor adicione contatos ao grupo primeiro.';
$ml_attention= 'Atenção';

//2.02
$ml_autocheck_disabled = ' não pôde ser encontrado. A verificação automática de novas mensagens foi desativada para esta conta.';
$ml_mark = 'Marcar mensagens...';
$ml_flag = 'Marcado';
$ml_clearflag = 'Desmarcado';
$ml_expand_all = 'Expandir todas as pastas';
$ml_collapse_all = 'Agrupar todas as pastas';

//2.05
$ml_search = 'Busca';
$ml_answered = 'Resposta';
$ml_doesnt_matter = 'Tanto faz';
$ml_body = 'Corpo';
$ml_before = 'Mais antigo que';
$ml_since = 'Mais novo que';
$ml_select_one_criteria = 'Por favor selecione ou digite pelo menos um critestário para busca.';

//2.06
$ml_appointment = 'Esta mensagem é um compromisso. Você quer ver este compromisso no calendário?';
$ml_mails_were_sent = 'mensagens de e-mail enviadas';
$ml_novalidate_cert = 'Sem validar o certificado';
$ml_appointment_vcf = 'esta mensagem contém um endereço de contato. Você quer ver este endereço em contatos?';


//2.08 
$ml_add_senders = 'Perguntar se você quer adicionar remetentes desconhecidos a sua lista de contatos padrão';
$ml_text_format = 'Texto pleno';
$ml_html_format_is = 'HTML (Se o navegador suporta edição de HTML)';
$ml_html_format = 'HTML formatado';
$ml_always_ask_notification = 'Sempre pessa ao notificação de recebimento ao destinatário por padrão';
$ml_ask_add_sender = 'O endereço do remetente %s que pertence a %s não está em seus contatos. Gostaria de adicionar esta pessoa a lista de contatos agora?';

$ml_edit_filter = 'Editar filtro';
$ml_choose_action = 'Favor, escolha uma ação:';
$ml_search_criteria = 'Critério de busca';
$ml_sender = 'Remetente';
$ml_reciever = 'Destinatário';
$ml_cc_reciever = 'Destinatário-CC (com cópia)';

//2.10
$ml_empty_trash = 'Esvaziar&nbsp;a&nbsp;lixeira';
//2.11
$ml_drafts = 'Rascunhos';
$ml_save_draft_error = 'Falha ao salvar rascunho. Favor checar suas configurações de conta.';
$ml_search_sender = 'Buscar por outros e-mails deste remetente';

$ml_no_attachments = 'Sem arquivos anexos';
$ml_search_active = 'A função de busca está ativa. Não são todas mensagens desta pasta que estão sendo exibidas. Clique aqui para finalizar a busca';
$ml_local_files = 'Arquivos locais';
$ml_please_close_attachments = 'Favor fechar a janela de anexos primeiro e tenha certeza de que a transferência dos arquivos estão completas';

$ml_please_wait = 'Favor espere...';
$ml_max_attachment_size = 'Tamanho máximo de anexos';
$ml_select = 'Selecione o arquivo';
$ml_multiple_files = 'Arquivos multiplos';

//2.12
$ml_reenter_password = 'Por questões de segurança você deve digitar sua senha de novo para esta conta de e-mail.';

$ml_attachments_added = 'arquivos anexos';
$ml_attachment_added = 'arquivo anexo';

//2.13
$ml_download_zipped_attachments = 'Zipar todos arquivos anexos';
$ml_fullscreen = 'Ver&nbsp;tela&nbsp;cheia';
$ml_nofullscreen = 'Fechar&nbsp;tela&nbsp;cheia';
$ml_confirm_empty_trash = 'Você tem certeza que quer esvaziar a lixeira?';
$ml_seen = 'Lendo';
$ml_charset = 'Codificação de caracteres';

//2.14
$ml_no_reciever_add = "Não adicione";
$ml_add_recievers = "Adicionar destinatário desconhecido à";//changed string from 2.06

$ml_automatic_reply = 'Ativar resposta automática';

$ml_set_default = 'Definir padrão';

$ml_used_of = 'usado por';

$ml_folder_not_available = 'você não pode criar pastas extras em contas POP3';

//2.15
$ml_mark_as_read='Marcar mensagens filtradas como lidas';

$ml_get_message_error_title = 'Erro ao buscar mensagem';
$ml_get_message_error_text = 'Não pode-se pegar a mensagem do servidor. Talvez foi apagada por outro cliente. Favor clicar em \'Atualizar\'.';
$ml_spamtag = 'Spam';
$ml_confirm_no_subject = 'Você não definiu um assunto para este e-mail. Você tem certeza que quer enviá-lo mesmo assim?';
$ml_show_preview = 'Mostrar pré-vizualização quando eu selecionar a mensagem';

$ml_show_headers = 'Mostrar cabeçalhos da mensagem';
$ml_confirm_empty_mailbox = 'Você tem certeza que quer esvaziar a pasta \'%s\'?';

//2.16
$em_source = 'Fonte da mensagem';
?>


