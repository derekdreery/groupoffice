<?php

//Polish Translation v1.0
//Author : Robert GOLIAT info@robertgoliat.com  info@it-administrator.org
//Date : January, 20 2009
//Polish Translation v1.1
//Author : Paweł Dmitruk pawel.dmitruk@gmail.com
//Date : September, 05 2010
//Polish Translation v1.2
//Author : rajmund
//Date : January, 26 2011
//
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));

$lang['users']['name'] = 'Użytkownicy';
$lang['users']['description'] = 'Moduł Administracyjny. Zarządzanie użytkownikami systemu.';

$lang['users']['deletePrimaryAdmin'] = 'Nie możesz usunąć głównego administratora';
$lang['users']['deleteYourself'] = 'Nie możesz usunąć swojego konta';

$lang['users']['error_username']='Nazwa użytkownika zawiera niedozwolone znaki';
$lang['users']['error_username_exists']='Podana nazwa użytkownika jest już używana w systemie';
$lang['users']['error_email_exists']='Podany adres e-mail jest już zarejestrowany w systemie.';
$lang['users']['error_match_pass']='Hasła nie są takie same';
$lang['users']['error_email']='Podany adres e-mail jest niewłaściwy';

$lang['users']['imported']='Zaimportowano %s użytkowników';
$lang['users']['failed']='Podczas importu wystąpił błąd';

$lang['users']['incorrectFormat']='Format pliku był niepoprawny';
$lang['link_type'][8]=$us_user = 'Użytkownik';
$lang['users']['error_user']='Użytkownik nie został utworzony';
$lang['users']['register_email_subject']='Szczegóły Twojego konta w {product_name}';
$lang['users']['register_email_body']='Utworzono dla Ciebie konto do {product_name} {url}
Informacje do zalogowania:

Użytkownik: {username}
Hasło: {password}';
$lang['users']['max_users_reached']='Maksymalna liczba użytkowników dla tego systemu została osiągnięta.';