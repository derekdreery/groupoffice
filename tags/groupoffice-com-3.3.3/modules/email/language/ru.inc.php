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

$lang['email']['event']='Событие';
$lang['email']['calendar']='календарь';

$lang['email']['quotaError']="Ваш почтовый ящик заполнен. Для начала очистите корзину. Если она пустая и Ваш почтовый ящик все еще заполнен, отключите использование папки Корзина в:\n\nНастройки -> Учетные записи -> учетная запись -> Папки. и удалите ненужные сообщения в других папках.";

$lang['email']['draftsDisabled']="Невозможно сохранить сообщение потому что отключена папка 'Черновики' .<br /><br />Настройте ее в Настройки -> Учетные записи -> учетная запись -> Папки.";
$lang['email']['noSaveWithPop3']='Невозможно сохранить сообщение потому что POP3 учетные записи не поддерживают этого.';

$lang['email']['goAlreadyStarted']='Group-Office was al gestart. Het e-mailscherm wordt nu geladen in Group-Office. Sluit dit venster en stel uw bericht op in Group-Office.';

//At Tuesday, 07-04-2009 on 8:58 Group-Office Administrator <test@intermeshdev.nl> wrote:
$lang['email']['replyHeader']='В %s, %s на %s %s писал:';
$lang['email']['alias']='Псевдоним';
$lang['email']['aliases']='Псевдонимы';
$lang['email']['alias']='Псевдоним';
$lang['email']['aliases']='Псевдонимы';

$lang['email']['noUidNext']='Ваш почтовый сервер не поддерживает  UIDNEXT. Папка \'Черновики\' для данной учетной записи автоматически отключена.';