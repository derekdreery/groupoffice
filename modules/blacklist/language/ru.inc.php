<?php
/**
 * Russian translation
 * By Valery Yanchenko (utf-8 encoding)
 * vajanchenko@hotmail.com
 * 10 December 2008
*/
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('blacklist'));
$lang['blacklist']['name']='IP черный список';
$lang['blacklist']['description']='Этот модуль блокирует IP адреса после 5 неудачных входов в систему.';
$lang['blacklist']['ip']='IP';
$lang['blacklist']['ips']='IP адреса';

$lang['blacklist']['blacklisted']='Ваш IP адрес %s заблокирован по причине 3 ошибочных входов в систему с этого IP адреса. Для разблокировки свяжитесь со службой техничекской поддержки.';
$lang['blacklist']['captchaIncorrect']='Вы ввели неверный секретный код, пожалуйста попробуйте еще раз.';
$lang['blacklist']['captchaActivated']='Зарегистрировано 3 ошибки входа в систему. Для того чтобы войти в систему Вам необходимо ввести секретный код.';
