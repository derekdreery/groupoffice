<?php
require_once($GO_LANGUAGE->get_fallback_language_file('users'));

$ac_lastlogin = "&Uacute;ltima sess&atilde;o";
$ac_logins = "Sess&otilde;es totais";
$strMaxOf = "registado por um m&aacute;ximo de";
$strUsername = "Nome de Utilizador";
$delete_fail = "Voc&ecirc; n&atilde;o pode suprimir o seu utilizador ou o super-utilizador.";
$admin_new_user = "novo utilizador";

$admin_modules = 'm&oacute;dulos';
$admin_module_access = 'Este utilizador tem acesso aos m&oacute;dulos seguintes:';

$admin_password = 'Senha';
$admin_confirm_password = 'Confirme a senha';
$admin_change_password = 'Mude a senha';
$admin_pass_match = 'As senhas n&atilde;o combinaram. Por favor tente outra vez.';

//registration
$registration_personal_info = "Informa&ccedil;&atilde;o pessoal";
$registration_account_info = "Informa&ccedil;&atilde;o sobre a sua conta";
$registartion_username_text = "Introduza o nome e senha de utilizador para acesso a este site.";

$registration_question_text=<<<registration_question_text
Incorpore uma pergunta secreta a que somente este utilizador sabe a resposta. N&oacute;s faremos esta pergunta caso que o utilizador se esquece de sua ou sua senha.
Quando a resposta correta &eacute; dada, um E-mail ser&aacute; emitido ao endere&ccedil;o registado com o nome de utilizador e uma senha nova para poder in&iacute;ciar uma sess&atilde;o.
registration_question_text;

$registration_text=<<<registration_text
Encorpore este formul&aacute;rio e clique no 'Ok' para registar. Os campos marcados com a * s&atilde;o requeridos.<br />
A informa&ccedil;&atilde;o que voc&ecirc; fornece &eacute; somente para o uso no Group-Office, e assim n&oacute;s n&atilde;o os forneceremos aos terceiros partidos.
registration_text;

$registartion_authcode_text = "Encorpore um c&oacute;digo de autoriza&ccedil;&atilde;o. O c&oacute;digo de autoriza&ccedil;&atilde;o &eacute; um c&oacute;digo que outros utilizadores necessitam para adicionar este usu&aacute;rio &agrave;s listas de contato e ver seus detalhes";
$registartion_confirm_authcode = "Confirme o c&oacute;digo de autoriza&ccedil;&atilde;o ";
$registration_visibility = "Fa&ccedil;a este utilizador vis&iacute;vel a todos os utilizadores.";
$registration_success = "<h1>Sucesso!</h1>Voc&ecirc; foi registado com sucesso e uma mensagem de bem-vinda foi emitida a:";
$registration_failure = "Um erro ocorreu ao tentar registar a sua informa&ccedil;&atilde;o. Por favor tente outra vez.";
$registration_mail_subject = "Bem-vindo ao Group-Office.";
$registration_folder_error = "Cria&ccedil;&atilde;o do caderno pessoal falhou. Contate-nos para restaurar isto!";
$registration_title = "Registe no Group-Office";
$registration_mail_body=<<<registration_mail_body
Caro senhor/senhora %name%,

Voc&ecirc; foi registado no Grupo-Escrit&oacute;rio.<br>
Esperamos que voc&ecirc; aprecie este servi&ccedil;o! 

Clique sobre a liga&ccedil;&atilde;o abaixo para in&iacute;ciar uma sess&atilde;o: 

registration_mail_body;
$registration_email = "Cr&iacute;e um endere&ccedil;o de E-mail para este utilizador no Group-Office.";
$registration_samba = "Servidor de ficheiros e imprima&ccedil;&atilde;o.";
$registration_email_error = "Um erro ocorreu ao criar a sua conta de E-mail. Contate-nos para sustenta&ccedil;&atilde;o ou suporte";
$strPasswordConfirm = "Confirme a senha";
$strPassword = "Senha";

$reg_company_info = "Informa&ccedil;&atilde;o da companhia";

$max_user_limit = "Uma quantidade m&aacute;xima de utilizadores foi alcan&ccedil;ada.";
$max_users_text = "Desculpe, voc&ecirc; n&atilde;o pode registar mais utilizadores. Contate-nos se voc&ecirc; quiser aumentar seu n&uacute;mero de utilizadores.";
$ml_sent_items = "Artigos enviados";
$ml_spam = "Spam";
$ac_samba_user = "Utilizador tem acesso ao servidor de ficheiros e imprimação.";
$ml_email_only = "Requerido somente quando voc&ecirc; n&atilde;o cr&iacute;a um endere&ccedil;o de E-mail";
?>
