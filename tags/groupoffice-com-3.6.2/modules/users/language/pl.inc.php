<?php

//Polish Translation v1.0
//Author : Robert GOLIAT info@robertgoliat.com  info@it-administrator.org
//Date : January, 20 2009
//Polish Translation v1.1
//Author : Paweł Dmitruk pawel.dmitruk@gmail.com
//Date : September, 05 2010

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('users'));

$lang['users']['name'] = 'Użytkownicy';
$lang['users']['description'] = 'Moduł Administracyjny; Zarządzanie uzytkownikami systemu.';

$lang['users']['deletePrimaryAdmin'] = 'Nie możesz usunąć administrator';
$lang['users']['deleteYourself'] = 'Nie możesz usunąc swojego konta';

$lang['users']['error_username']='Wpisano niewłaściwe znaki w nazwę użytkownika';
$lang['users']['error_username_exists']='Wybacz, ale taki użytkownik już istnieje w systemie';
$lang['users']['error_email_exists']='Wybacz, ale podany adres e-mail jest już zarejestrowany w systemie.';
$lang['users']['error_match_pass']='Hasła nie są takie same';
$lang['users']['error_email']='Wprowadzono niewłasciwy adres e-mail';

$lang['users']['imported']='Zaimportowano %s użytkowników';
$lang['users']['failed']='Operacja zakonczona z błedem';

$lang['users']['incorrectFormat']='Plik nie posiada własciwego formatu CSV';
$lang['link_type'][8]=$us_user = 'Użytkownik';
$lang['users']['error_user']='Użytkownik nie został utworzony';
$lang['users']['register_email_subject']='Szczegóły Twojego konta w Group-Office';
$lang['users']['register_email_body']='Utworzono dla Ciebie konto do Group-Office {url}
Informacje do zalogowania:

Użytkownik: {username}
Hasło: {password}';
$lang['users']['max_users_reached']='Maksymalna liczba użytkowników dla tego systemu została osiągnięta.';