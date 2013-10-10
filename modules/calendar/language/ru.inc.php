<?php
/**
 * Russian translation
 * By Valery Yanchenko (utf-8 encoding)
 * vajanchenko@hotmail.com
 * 10 December 2008
*/
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
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

$lang['calendar']['invited']='Вы приглашены на следующее событие';
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
$lang['calendar']['statuses']['CONFIRMED'] = 'Подтверждено';


$lang['calendar']['accept_mail_subject'] = 'Приглашение для \'%s\' принято';
$lang['calendar']['accept_mail_body'] = '%s принял Ваше приглашение для:';

$lang['calendar']['decline_mail_subject'] = 'Приглашение для \'%s\' отклонено';
$lang['calendar']['decline_mail_body'] = '%s отклонил Ваше приглашение для:';

$lang['calendar']['location']='Место';
$lang['calendar']['and']='и';

$lang['calendar']['repeats'] = 'Повторять каждый %s';
$lang['calendar']['repeats_at'] = 'Повторять каждый %s в %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Повторять каждый %s %s в %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['repeats_not_every'] = 'Повторять каждый %s %s';
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

$lang['calendar']['calendars']='Календари';

$lang['calendar']['open_resource']='Свободный ресурс';

$lang['calendar']['resource_mail_subject']='Ресурс \'%s\' зарезервирован для \'%s\' на \'%s\'';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_mail_body']='%s зарезервировал ресурс \'%s\'. Вы назначены отвественным за данный ресурс. Пожалуйста примите или отклоните заявку.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['resource_modified_mail_subject']='Ресурс \'%s\' зарезервированый для \'%s\' на \'%s\' изменен';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_modified_mail_body']='%s изменил заявку для ресурса \'%s\'. Вы назначены отвественным за данный ресурс. Пожалуйста примите или отклоните заявку.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['your_resource_modified_mail_subject']='Ваша заявка \'%s\' на \'%s\' в состоянии \'%s\' изменена';//is resource name, %s is event name, %s is start date
$lang['calendar']['your_resource_modified_mail_body']='%s изменил Вашу заявку на ресурс \'%s\'.';

$lang['calendar']['your_resource_accepted_mail_subject']='Ваша заявка для \'%s\' на \'%s\' принята';//%s is resource name, %s is start date
$lang['calendar']['your_resource_accepted_mail_body']='%s принял Вашу заявку на ресурс \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['your_resource_declined_mail_subject']='Ваша заявка для \'%s\' на \'%s\' отклонена';//%s is resource name, %s is start date
$lang['calendar']['your_resource_declined_mail_body']='%s отклонил Вашу заявку на ресурс \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['birthday_name']='День рождения: {NAME}';
$lang['calendar']['birthday_desc']='{NAME} сегодня {AGE} лет';

$lang['calendar']['unauthorized_participants_write']='У Вас недостаточно привелегий для планирования событий следующих пользователей:<br /><br />{NAMES}<br /><br />Вы можете выслать им приглашения и они могут принять и добавитьв свой календарь.';

$lang['calendar']['noCalSelected'] = 'Вы не выбрали ни один календарь для просмотра. Выберине хотя бы один календарь в Настройках.';

$lang['calendar']['month_times'][1]='первый';
$lang['calendar']['month_times'][2]='второй';
$lang['calendar']['month_times'][3]='третий';
$lang['calendar']['month_times'][4]='четвертый';
$lang['calendar']['month_times'][5]='пятый';

$lang['calendar']['rightClickToCopy']='Нажмите праву кнопку мыши чтобы скопировать ссылку';

$lang['calendar']['invitation']='Приглашение';
$lang['calendar']['invitation_update']='Обновленные приглашения';
$lang['calendar']['cancellation']='Отказ';

$lang['calendar']['non_selected'] = 'в не выбранном календаре';

$lang['calendar']['linkIfCalendarNotSupported']='Если Ваш email клиент не поддерживает функции календаря используйте ссылку:';