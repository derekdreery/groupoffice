<?php
require_once($GO_LANGUAGE->get_fallback_language_file('email'));

//Email
$ml_from = "De";
$ml_to = "A";
$ml_send = "Enviar";
$ml_attach = "Anexar";
$ml_attachments = "Anexos";
$ml_attachment = "Anexo";
$ml_high = "Elevada";
$ml_normal = "Normal";
$ml_low = "Baixa";
$ml_priority = "Prioridade";
$ml_subject = "Assunto";
$ml_filename = "Nome do ficheiro";
$ml_total_size = "Tamanho total";
$ml_size = "Tamanho";
$ml_to_empty = "Voc&ecirc; n&atilde;o especificou um receptor.";
$ml_print = "C&oacute;pia";
$fbNoSelect = "Voc&ecirc; n&atilde;o selecionou uma mensagem.";

$ml_inbox = 'caixa de recep&ccedil;&atilde;o';
$ml_properties = 'propriedades';
$ml_add_account = "Addicionar uma conta";
$em_new_folder = "Novo Dossier";
$ml_your_accounts = "Suas contas de E-mail";
$ml_no_accounts = "Voc&ecirc; n&atilde;o tem nenhumas contas de E-mail configuradas.";
$ml_original_follows = "----- Mensagem Original -----";
$ml_welcome = "Bem-vindo ao Group-Office E-mail";
$ml_text = "Clique sobre contas para adicionar um cliente de E-mail.";
$ml_accounts = "contas";
$ml_compose = "Compor";
$ml_delete = "suprimir";
$ml_refresh = "refrescar";
$ml_reply = "responder";
$ml_forward = "enviar para diante";
$ml_reply_all = "responder a todos";
$ml_delete_message = "Certo que voc&ecirc; quer suprimir permanentemente esta mensagem?";
$ml_no_subject = "Nenhum assumto";
$ml_no_reciepent = "Receptores nao divulgados";
$ml_online_files = "Ficheiros online";
$ml_send_error = "Falhou ao emitir o correio.";

$ml_attach_forget = "Voc&ecirc; selecionou um ficheiro mas n&atilde;o o anexou.";
$ml_messages = "mensagem(ns)";
$ml_message = 'esta mensagem';
$ml_messages2 = 'mensagens';
$ml_new = "nova";
$ml_delete_mailbox = "Certo que voc&ecirc; quer suprimir permanentemente as mensagens selecionadas?";

$contact_profile = "Perfil do contato";
$ml_add_contact = "Adicione o remetente aos contatos";
$strHost = "Servidor";
$strDefault = "Valor comum";
$ml_edit_account = "Edite a sua conta";
$ml_new_account = "Conta nova";
$ml_no_messages = "N&atilde;o h&aacute; nenhuma mensagem neste caderno.";
$ml_file_too_big = "O ficheiro que voc&ecirc; tentou anexar &eacute; demasiado grande, o tamanho m&aacute;ximo para anexos &eacute; ajustado a: ";
$fbNoRightToOverwrite = "O ficheiro j&aacute; existe e voc&ecirc; n&atilde;o &eacute; permitido reescrever ficheiros.";
$ml_save_attachment = "Guardar ficheiro tip anexo no Group-Office.";
$ml_folders = "cadernos";

$ml_select_folder = "Selecioine o caderno onde o Group-Office pode guardar correio enviado ou encorpore um nome abaixo para criar um caderno";
$ml_connect_failed = "A liga&ccedil;&atilde;o ao servidor de correio falhou:";
$ml_at_port = "no canal";
$ml_solve_error = 'Clique \'Contas\' para ajustar a sua conta ou clique \'Refrescar\' para abrir uma conta comum.';
$ml_sent_items = "Artigos enviados";
$ml_spam = "Spam";
$ml_use = "Uso";
$ml_synchronise = "sincronize";
$ml_move_mail = "Mudar as mensagens selecionadas para o caderno...";
$ml_filters = "filtros";
$ml_move_to = "mude ent&atilde;o a mensagem para o caderno:";
$ml_email_is = "Se o endere&ccedil;o E-mail do remetente contem:";
$ml_to_is = "Se o campo de remetentes contem:";
$ml_cc_is = "Se o campo de remetentes por c&oacute;pia de carbono contem:";

$ml_subject_is = "Se o assunto contem:";
$ml_new_filter = "Filtro novo:";
$ml_field = "Campo";
$ml_contains = "Contem";
$ml_folder = "Caderno";
$ml_no_folders = "Voc&ecirc; n&atilde;o tem nenhuns cadernos e ent&atilde;o n&atilde;o pode criar filtros.";
$ml_unwanted = "Clique aqui se E-mail deste remetente n&atilde;o for desejado";
$fbFolderSize = "Tamanho do caderno";
$ml_move_up = "Aumentar a prioridade";
$ml_delete_filter = "Certo que quer suprimir este filtro?";
$ml_high_priority = "Esta mensagem tem prioridade elevada!";
$ml_low_priority = "Esta mensagem tem prioridade baixa.";
$ml_block = "obstrua o remetente";
$ml_sent_items_fail = "A mensagem foi enviada mas houve um erro ao criar esta mensagem no caderno de artigos enviados.  Por favor verifique a sua conta IMAP.";
$fbProperties = "propriedades";
$ml_notification = "Pe&ccedil;a o receptor para emitir uma notifica&ccedil;&atilde;o que a mensagem foi lida";
$ml_ask_notify = "O remetente desta mensagem pediu uma notifica&ccedil;&atilde;o que voc&ecirc; leu a mensagem. Deseja emitir um relat&oacute;rio desta mensagem como lida?";
$ml_displayed = "O seguinte E-mail foi indicado a ";
$ml_notify = "Notifica&ccedil;&atilde;o Como Lido";
$ml_delete_filter1 = "Suprimir Filtro";
$ml_html_message_attached = "Mensagens originais do tipo HTML est&atilde;o inclu&iacute;das";
$ml_add_recievers = "Adicione receptores desconhecidos ao seu Livro de Endere&ccedil;os";
$ml_no_folders = 'Voc&ecirc; n&atilde;o tem nenhuns cadernos para onde filtar';
$ml_no_filters = 'Voc&ecirc; n&atilde;o tem nenhuns filtros.';

$ml_host = 'Servidor';
$ml_port = 'Canal';
$ml_type = 'Tipo';
$ml_servertype = 'Tipo de servidor';
$ml_format = 'Formato';
$ml_loose_changes = 'As mudan&ccedil;as ser&atilde;o perdidas. Certo que voc&ecirc; quer continuar?';

$ml_markread = "marcar como lido";
$ml_markunread = "marcar como n&atilde;o lido";
$ml_root_mailbox = "a caixa postal raiz";
$ml_inside = "em";

//translation required
$ml_no_mailbox = "This is not a mailbox. This folder cannot contain messages.";
$ml_signature = 'Signature';
?>