<?php
/**
 * Russian translation
 * By Valery Yanchenko (utf-8 encoding)
 * vajanchenko@hotmail.com
 * 10 December 2008
*/
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Календарь';
$lang['calendar']['description'] = 'Модуль Календарь; Каждый пользователь может добавить, редактировать или удалить события. Можно просматривать события других пользователей, и в случае необходимости можно их изменять.';

$lang['link_type'][1]='Встреча';

$lang['calendar']['groupView'] = 'Просмотр для группы';
$lang['calendar']['event']='Событие';
$lang['calendar']['startsAt']='Начинается в';
$lang['calendar']['endsAt']='Заканчивается в';

$lang['calendar']['exceptionNoCalendarID'] = 'ОШИБКА: Нет ID календаря!';
$lang['calendar']['appointment'] = 'Дело: ';
$lang['calendar']['allTogether'] = 'Все вместе';

$lang['calendar']['location']='Место';

$lang['calendar']['invited']='Вы приглашены для следующего события';
$lang['calendar']['acccept_question']='Принимаете приглашение?';

$lang['calendar']['accept']='Принять';
$lang['calendar']['decline']='Отклонить';

$lang['calendar']['bad_event']='Это событие больше не существует';

$lang['calendar']['subject']='Тема';
$lang['calendar']['status']='Cтaтyc';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Необходимо вмешательство';
$lang['calendar']['statuses']['ACCEPTED'] = 'Принято';
$lang['calendar']['statuses']['DECLINED'] = 'Отклонено';
$lang['calendar']['statuses']['TENTATIVE'] = 'Предварительно';
$lang['calendar']['statuses']['DELEGATED'] = 'Делегировано';
$lang['calendar']['statuses']['COMPLETED'] = 'Выполнено';
$lang['calendar']['statuses']['IN-PROCESS'] = 'На исполнении';


$lang['calendar']['accept_mail_subject'] = 'Приглашение для \'%s\' принято';
$lang['calendar']['accept_mail_body'] = '%s принял Ваше приглашение для:';

$lang['calendar']['decline_mail_subject'] = 'Приглашение для \'%s\' отклонено';
$lang['calendar']['decline_mail_body'] = '%s отклонил Ваше приглашение для:';

$lang['calendar']['location']='Место';
$lang['calendar']['and']='и';

$lang['calendar']['repeats'] = 'Повторять каждый %s';
$lang['calendar']['repeats_at'] = 'Повторять каждый %s в %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Повторять каждый %s %s в %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='пока'; 

$lang['calendar']['not_invited']='Вы не приглашены на это событие. Возможно Вам необходимо войти в систему под другим пользователем.';


$lang['calendar']['accept_title']='Принято';
$lang['calendar']['accept_confirm']='Владелец будет уведомлен, что Вы приняли приглашение';

$lang['calendar']['decline_title']='Отклонено';
$lang['calendar']['decline_confirm']='Владелец будет уведомлен, что Вы отклонили приглашение';

$lang['calendar']['cumulative']='Неверно задано правило повторения. Следующее событие не может начатся пока не закончится предыдущее.';

$lang['calendar']['already_accepted']='Вы уже приняли приглашение на это событие.';

$lang['calendar']['private']='Личное';

$lang['calendar']['import_success']='%s событий импортировано';

$lang['calendar']['printTimeFormat']='От %s до %s';
$lang['calendar']['printLocationFormat']=' в "%s"';
$lang['calendar']['printPage']='Стр. %s из %s';
$lang['calendar']['printList']='Список событий';

$lang['calendar']['printAllDaySingle']='Весь день';
$lang['calendar']['printAllDayMultiple']='Весь день с %s по %s';