<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name'] = 'Usuários';
$lang['users']['description'] = 'Módulo de administração; Administra usuários do sistema.';

$lang['users']['deletePrimaryAdmin'] = 'Você não pode apagar o usuário administrador';
$lang['users']['deleteYourself'] = 'Você não pode apagar a si próprio';

$lang['link_type'][8]=$us_user = 'Usuário';

$lang['users']['error_username']='Você colocou caracteres inválidos no nome';
$lang['users']['error_username_exists']='Desculpe, esse nome já existe';
$lang['users']['error_email_exists']='Desculpe, esse e-mail já está registrado.';
$lang['users']['error_match_pass']='Senha não informada';
$lang['users']['error_email']='Você informou um endereço de e-mail inválido';
$lang['users']['error_user']='Usuário não pode ser criado';

$lang['users']['imported']='Importados %s usuários';
$lang['users']['failed']='Falhou';

$lang['users']['incorrectFormat']='Arquivo não tem o formato CSV correto';

$lang['users']['register_email_subject']='Detalhes de sua conta no {product_name}';
$lang['users']['register_email_body']='Uma conta {product_name} foi criada para você em {url}
Os deatalhes de seu login:

Usuário: {username}
Senha: {password}';


$lang['users']['max_users_reached']='Este sistema atingiu o número máximo de usuários permitido.';

