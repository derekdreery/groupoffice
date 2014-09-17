<?php


$l['name'] = 'Календарь';
$l['description'] = 'Модуль Календарь; Каждый пользователь может добавить, редактировать или удалить события. Можно просматривать события других пользователей, и, в случае необходимости, можно их изменять.';
$l['groupView'] = 'Просмотр для группы';
$l['event']='Событие';
$l['exceptionNoCalendarID'] = 'ОШИБКА: Нет ID календаря!';
$l['allTogether'] = 'Все вместе';
$l['invited']='Вы приглашены на следующее событие';
$l['acccept_question']='Принимаете приглашение?';
$l['accept']='Принять';
$l['decline']='Отклонить';
$l['bad_event']='Это событие больше не существует';
$l['subject']='Тема';
$l['statuses']['NEEDS-ACTION'] = 'Необходимо вмешательство';
$l['statuses']['ACCEPTED'] = 'Принято';
$l['statuses']['DECLINED'] = 'Отклонено';
$l['statuses']['TENTATIVE'] = 'Предварительно';
$l['statuses']['DELEGATED'] = 'Делегировано';
$l['statuses']['COMPLETED'] = 'Выполнено';
$l['statuses']['IN-PROCESS'] = 'На исполнении';
$l['accept_mail_subject'] = 'Приглашение для \'%s\' принято';
$l['accept_mail_body'] = '%s принял Ваше приглашение для:';
$l['decline_mail_subject'] = 'Приглашение для \'%s\' отклонено';
$l['decline_mail_body'] = '%s отклонил Ваше приглашение для:';
$l['and']='и';
$l['repeats'] = 'Повторять каждый %s';
$l['repeats_at'] = 'Повторять каждый %s в %s';//eg. Repeats every month at the first monday;
$l['repeats_at_not_every'] = 'Повторять каждый %s %s в %s';//eg. Repeats every 2 weeks at monday;
$l['repeats_not_every'] = 'Повторять каждый %s %s';
$l['until']='пока'; ;
$l['not_invited']='Вы не приглашены на это событие. Возможно Вам необходимо войти в систему под другим пользователем.';
$l['accept_title']='Принято';
$l['accept_confirm']='Владелец будет уведомлен, что Вы приняли приглашение';
$l['decline_title']='Отклонено';
$l['decline_confirm']='Владелец будет уведомлен, что Вы отклонили приглашение';
$l['cumulative']='Неверно задано правило повторения. Следующее событие не может начаться, пока не закончится предыдущее.';
$l['already_accepted']='Вы уже приняли приглашение на это событие.';
$l['private']='Личное';
$l['import_success']='%s событий импортировано';
$l['printTimeFormat']='От %s до %s';
$l['printLocationFormat']=' в "%s"';
$l['printPage']='Стр. %s из %s';
$l['printList']='Список событий';
$l['printAllDaySingle']='Весь день';
$l['printAllDayMultiple']='Весь день с %s по %s';
$l['open_resource']='Свободный ресурс';
$l['resource_mail_subject']='Ресурс \'%s\' зарезервирован для \'%s\' на \'%s\'';//%s is resource name, %s is event name, %s is start date;
$l['resource_mail_body']='%s зарезервировал ресурс \'%s\'. Вы назначены отвественным за данный ресурс. Пожалуйста, примите или отклоните заявку.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['resource_modified_mail_subject']='Ресурс \'%s\', зарезервированый для \'%s\' на \'%s\', изменен';//%s is resource name, %s is event name, %s is start date;
$l['resource_modified_mail_body']='%s изменил заявку для ресурса \'%s\'. Вы назначены отвественным за данный ресурс. Пожалуйста, примите или отклоните заявку.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['your_resource_modified_mail_subject']='Ваша заявка \'%s\' на \'%s\' в состоянии \'%s\' изменена';//is resource name, %s is event name, %s is start date;
$l['your_resource_modified_mail_body']='%s изменил Вашу заявку на ресурс \'%s\'.';
$l['your_resource_accepted_mail_subject']='Ваша заявка для \'%s\' на \'%s\' принята';//%s is resource name, %s is start date;
$l['your_resource_accepted_mail_body']='%s принял Вашу заявку на ресурс \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['your_resource_declined_mail_subject']='Ваша заявка для \'%s\' на \'%s\' отклонена';//%s is resource name, %s is start date;
$l['your_resource_declined_mail_body']='%s отклонил Вашу заявку на ресурс \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name;
$l['birthday_name']='День рождения: {NAME}';
$l['birthday_desc']='{NAME} сегодня {AGE} лет';
$l['unauthorized_participants_write']='У Вас недостаточно привилегий для планирования событий следующих пользователей:<br /><br />{NAMES}<br /><br />Вы можете выслать им приглашения и они могут принять и добавить их в свой календарь.';
$l['noCalSelected'] = 'Вы не выбрали ни один календарь для просмотра. Выберите хотя бы один календарь в Настройках.';
$l['month_times'][1]='первый';
$l['month_times'][2]='второй';
$l['month_times'][3]='третий';
$l['month_times'][4]='четвертый';
$l['month_times'][5]='пятый';
$l['rightClickToCopy']='Нажмите правую кнопку мыши чтобы скопировать ссылку';
$l['invitation']='Приглашение';
$l['invitation_update']='Обновленные приглашения';
$l['cancellation']='Отказ';
$l['non_selected'] = 'в не выбранном календаре';
$l["addressbook"]='Адресная книга';
$l["appointment"]= 'Событие';
$l["appointments"]= 'События';
$l["recurrence"]= 'Повторение';
$l["options"]= 'Свойства';
$l["repeatForever"]= 'повторять всегда';
$l["repeatEvery"]= 'Повторять каждый';
$l["repeatUntil"]= 'Повторять до';
$l["busy"]= 'Отображать как занят';
$l["allDay"]= 'Без указания времени';
$l["navigation"]= 'Навигация';
$l["oneDay"]= '1 день';
$l["fiveDays"]= '5 дней';
$l["sevenDays"]= '7 дней';
$l["month"]= 'Месяц';
$l["recurringEvent"]= 'Повторяющееся событие';
$l["deleteRecurringEvent"]= 'Вы хотите удалить текушее событие или все повторяющиеся события?';
$l["singleOccurence"]= 'Текущее событие';
$l["entireSeries"]= 'Все повторы';
$l["calendar"]= 'Календарь';
$l["calendars"]= 'Календари';
$l["views"]= 'Представления';
$l["administration"]= 'Настройки';
$l["needsAction"]= 'Необходимо действие';
$l["accepted"]= 'Принято';
$l["declined"]= 'Отклонено';
$l["tentative"]= 'Предварительно';
$l["delegated"]= 'Делегировано';
$l["noRecurrence"]= 'Нет повторений';
$l["notRespondedYet"]= 'Нет ответа';
$l["days"]= 'дней';
$l["weeks"]= 'недель';
$l["monthsByDate"]= 'ежемесячно по дате';
$l["monthsByDay"]= 'ежемесячно по дню';
$l["years"]= 'лет';
$l["atDays"]= 'В дни';
$l["noReminder"]= 'Нет напоминания';
$l["reminder"]='Напоминать за';
$l["participants"]= 'Участники';
$l["checkAvailability"]= 'Проверить доступность';
$l["sendInvitation"]= 'Послать приглашение';
$l["emailSendingNotConfigured"]= 'Отправка E-mail не настроена.';
$l["privateEvent"]= 'Личное';
$l["noInformationAvailable"]= 'Нет доступной информации';
$l["noParticipantsToDisplay"]= 'Нет участников';
$l["previousDay"]= 'Предыдущий день';
$l["nextDay"]= 'Следующий день';
$l["noAppointmentsToDisplay"]= 'Нет назначенных событий';
$l["selectCalendar"]= 'Выберите календарь';
$l["selectCalendarForAppointment"]= 'Выберите календарь в котором Вы хотите разместить событие';
$l["closeWindow"]= 'Приглашение было принято и запланировано. Вы можете закрыть это окно.';
$l["list"]='Список';
$l["editRecurringEvent"]='Вы хотите отредактировать только текущее событие или все его повторения?';
$l["selectIcalendarFile"]='Выберите icalendar файл (*.ics)';
$l["location"]='Место';
$l["startsAt"]='Начинается в';
$l["endsAt"]='Заканчивается в';
$l["eventDefaults"]='Настройки по умолчанию';
$l["importToCalendar"]='Добавить событие напрямую в календари';
$l["default_calendar"]='Календарь по умолчанию';
$l["status"]='Состояние';
$l["resource_groups"]='Группы ресурсов';
$l["resource_group"]='Группа ресурсов';
$l["resources"]='Ресурсы';
$l["resource"]='Ресурс';
$l["calendar_group"]='Группа календарей';
$l["admins"]='Администраторы';
$l["no_group_selected"]='В форме содержатся ошибки. Вам необходимо выбрать группу для данного ресурса.';
$l["visibleCalendars"]='Видимые календари';
$l["visible"]='Видимый';
$l["group"]='Группа';
$l["no_status"]='Новый';
$l["no_custom_fields"]='Нет дополнительной информации.';
$l["show_bdays"]='Отображать дни рождения из адресной книги';
$l["show_tasks"]='Отображать задачи из списка задач';
$l["myCalendar"]='Мой календарь';
$l["merge"]='Объединить';
$l["ownColor"]= 'Задайте для каждого календаря свой уникальный цвет';
$l["ignoreConflictsTitle"]= 'Игнорировать конфликты?';
$l["ignoreConflictsMsg"]= 'Это событие конфликтует с другим событием в Вашем календаре. Сохранить это событие?';
$l["resourceConflictTitle"]= 'Конфликт ресурсов';
$l["resourceConflictMsg"]= 'Один или более ресурсов в этом событии в указанное время уже занят:</br>';
$l["view"]= 'Вид';
$l["calendarsPermissions"]='Права на календари';
$l["resourcesPermissions"]='Права на ресурсы';
$l["daynames"][0]='Воскресенье';
$l["daynames"][1]='Понедельник';
$l["daynames"][2]='Вторник';
$l["daynames"][3]='Среда';
$l["daynames"][4]='Четверг';
$l["daynames"][5]='Пятница';
$l["daynames"][6]='Суббота';
$l["categories"]='Категории';
$l["category"]='Категория';
$l["globalCategory"]='Общая категория';
$l["selectCategory"]='Выберите категорию';
$l["duration"]='Промежуток';
$l["move"]='Переместить';
$l["showInfo"]='Подробности';
$l["copyEvent"]='Скопировать событие';
$l["moveEvent"]='Переместить событие';
$l["eventInfo"]='Подробности';
$l["isOrganizer"]='Органайзер';
$l["sendInvitationInitial"]='Хотите разослать сообщения с приглашениями участникам?';
$l["sendInvitationUpdate"]='Хотите разослать сообщения с изменениями участникам?';
$l["sendCancellation"]='Хотите разослать всем участникам уведомление о отмене события?';
$l["forthcomingAppointments"]='Предстоящие события';
$l["quarterShort"]= 'Q';