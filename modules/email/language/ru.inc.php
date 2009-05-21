<?php
/**
 * Russian translation
 * By Valery Yanchenko (utf-8 encoding)
 * vajanchenko@hotmail.com
 * 10 December 2008
*/
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('email'));
$lang['email']['name'] = 'Почта';
$lang['email']['description'] = 'Модуль Почта; Небольшой e-mail клиент. Любой пользователь может отправлять, принимать и перенаправлять почтовые сообщения';

$lang['link_type'][9]='Почта';

$lang['email']['feedbackNoReciepent'] = 'Вы не указали получателя';
$lang['email']['feedbackSMTPProblem'] = 'Невозможно связаться с SMTP сервером: ';
$lang['email']['feedbackUnexpectedError'] = 'Произошла непредвиденная ошибка при формировании почтового сообщения: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Невозможно создать папку';
$lang['email']['feedbackDeleteFolderFailed'] = 'Невозможно удалить папку';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Failed to subscribe folder';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Failed to unsubscribe folder';
$lang['email']['feedbackCannotConnect'] = 'Невозможно соедениться с %1$ по порту %3$s<br /><br />ПОчтовый сервер вернул: %2$s';
$lang['email']['inbox'] = 'Входящие';

$lang['email']['spam']='Спам';
$lang['email']['trash']='Корзина';
$lang['email']['sent']='Отправленные';
$lang['email']['drafts']='Черновики';

$lang['email']['no_subject']='Нет темы';
$lang['email']['to']='Кому';
$lang['email']['from']='От';
$lang['email']['subject']='Тема';
$lang['email']['no_recipients']='Неуказаны получатели';
$lang['email']['original_message']='--- Далее оригинал ---';
$lang['email']['attachments']='Вложения';

$lang['email']['notification_subject']='Читать: %s';
$lang['email']['notification_body']='Ваше сообщение с темой "%s" прочитано в %s';

$lang['email']['errorGettingMessage']='Невозможно получить сообщение';
$lang['email']['no_recipients_drafts']='Нет получателей';
$lang['email']['usage_limit'] = '%s из %s занято';
$lang['email']['usage'] = '%s занято';