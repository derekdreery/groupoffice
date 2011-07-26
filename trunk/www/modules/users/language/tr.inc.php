<?php
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));

$lang['users']['name'] = 'Kullanıcılar';
$lang['users']['description'] = 'Yönetici modülü. Sistem kullanıcılarının yönetimi.';

$lang['users']['deletePrimaryAdmin'] = 'Ana sistem yöneticisini silemezsiniz';
$lang['users']['deleteYourself'] = 'Kendinizi silemezssiniz';

$lang['link_type'][8]=$us_user = 'Kullanıcı';

$lang['users']['error_username']='Kullanıcı adı içersinde geçerli olmayan karaklerler var';
$lang['users']['error_username_exists']='Üzgünüm, verdiğiniz kullanıcı adı zaten mevcut';
$lang['users']['error_email_exists']='Üzgünüm, verdiğiniz E-posta adresi zaten kayıtlıdır.';
$lang['users']['error_match_pass']='Girdiğiniz parolalar uyuşmadı';
$lang['users']['error_email']='Geçersiz bir E-posta adresi girdiniz';

$lang['users']['imported']='%s kullanıcı içeri aktarıldı';
$lang['users']['failed']='Hata oluştu';

$lang['users']['incorrectFormat']='Dosya, uygun CSV formatında değildir';
?>