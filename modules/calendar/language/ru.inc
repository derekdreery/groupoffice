<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('calendar'));

$sc_title = "Заголовок";
$sc_description = "Описание";
$sc_participants = "Приглашенные";

$sc_location = "Расположение";

$month_times[0] = "Первая неделя";
$month_times[1] = "Вторая неделя";
$month_times[2] = "Третья неделя";
$month_times[3] = "Четвертая неделя";

$sc_new_app = "Новое событие";
$sc_calendars = "Календари";
$sc_invited = 'You are invited to join the event described below.';

$sc_types1[0] = 'Не напоминать';
$sc_types1[1] = 'день';
$sc_types1[2] = 'неделю';
$sc_types1[3] = 'на заданную дату';
$sc_types1[4] = 'день недели';
$sc_types1[5] = 'год';


$sc_types[0] = 'Once';
$sc_types[1] = 'Неделя';
$sc_types[2] = 'Weekly';
$sc_types[3] = 'Monthly by date';
$sc_types[4] = 'Monthly by day';
$sc_types[5] = 'Года';


$sc_start_at = 'Начало в';
$sc_end_at = 'Окончание в';
$sc_start_time = 'Время начала';
$sc_end_time = 'Время конца';

$sc_at_days = 'День недели';
$sc_of_month = 'of the month.';

$sc_cycle_start = 'Cycle starts at';
$sc_cycle_end = 'Заданный день';
$sc_accept_question = 'Do you wish to accept and schedule this event?';

$sc_accept = 'accept';
$sc_decline = 'decline';

$sc_notime = 'Время не указанно';
$sc_noend = 'Нет даты окончания.';

$sc_never_happens = 'Bad input! This event never happens.';
$sc_start_later = 'Bad input!! The starting time is later then the ending time.';

$sc_add_self = 'Ручной ввод';

$sc_select_calendar = 'Select the calendar to schedule this event in';
$sc_accept_confirm = 'The event is accepted and scheduled.';
$sc_no_calendars = 'No valid calendar found to schedule this event in.';
$sc_bad_event = 'No valid event found. The event is probably deleted.';
$sc_accept_title = 'Accept Event';

$sc_decline_confirm = 'The event is declined.';
$sc_decline_title = 'Decline event';

$sc_status = 'Статус';
$sc_not_responded = 'Not responded';
$sc_accepted = 'Accepted';
$sc_declined = 'Declined';
$sc_no_participants = 'No participants';

$sc_person = 'Персона';
$sc_owner = 'Создатель';
$sc_type = 'Тип';
$sc_subscribed = 'Подписчики';

$sc_subscribe = 'Подписка';
$sc_new_calendar = 'Новый календарь';
$sc_no_calendar = 'Календарь не найден. Пожалуйста создайте хотя бы один календарь.';
$sc_day_view = 'Один&nbsp;день';
$sc_week_view = 'Одна&nbsp;неделя';
$sc_list_view = 'Последний&nbsp;просмотр';
$sc_put_in = 'Put event in';
$sc_inherit_permissions ='Inherit permissions from';
$sc_no_inherit = 'Don\'t inherit';

$sc_select_calendar_please = 'Пожалуйста выберете один календарь.';
$sc_events = 'События';

$sc_from_calendar = 'из календаря';
$sc_enitrely = 'Entirely';
$sc_delete_event = 'Удаление события';
$sc_delete_pre = 'Вы действительно ходите удалить это событие';
$sc_delete_suf = 'entirely or just from this calendar?';

$sc_refresh = 'Обновить';
$sc_reminder = 'Напомнить';
$sc_mins = 'минуты';
$sc_hour = 'час';
$sc_hours = 'часы';
$sc_day = 'день';
$sc_days = 'дни';
$sc_week = 'неделя';
$sc_weeks = 'недели';
$sc_reminder_set_to_early = "The reminder cannnot be set to appear before the previous occurence. Please shorten the reminder time or lengthen the recurring period.";
$sc_cycle_start_later = 'The cycle starting date is later or equal then the ending date.';


$cmdGenerate = 'Generate';
$sc_preferences = 'Настройки';
$sc_holidays = 'Прадники';

$strNoHolidays = 'Нет праздников в базе данных';
$strUnknown = 'Неизвестно';
$strYear = 'Год';
$strDeleteHolidaysPrefix = 'Are you sure you want to delete all holiday records of the year ';
$strDeleteHolidaysSuffix = '?';
$strReplaceHolidays = 'Are you sure you want to overwrite all existent holiday records?';
$sc_holidays_text1 = 'Please click on \''.$cmdGenerate.'\' to compute the holiday records for the selected year.';
$sc_holidays_text2 = 'Please click on \''.$cmdApply.'\' to store all computed holiday records in the database.';

//translation required below this
$sc_client = 'Клиент';
$sc_no_contact = 'None';

$sc_background = 'Цет фона';

$sc_colors['red'] = 'Красный';
$sc_colors['green'] = 'Зеленый';
$sc_colors['blue'] = 'Синий';
$sc_colors['pink'] = 'Розовый';
$sc_colors['orange'] = 'Оранжевый';
$sc_colors['brown'] = 'Коричневый';
$sc_colors['yellow'] = 'Желтый';
$sc_colors['grey'] = 'Серый';
$sc_colors['purple'] = 'Пурпурный';
$sc_colors['white']= 'Белый';
$sc_status = 'Статус';

$sc_show_hours = 'Show hours from';
$sc_to = 'to';

$sc_today = 'Сегодня';

$sc_recur_every = 'Напоминать каждый';
$sc_months = 'месяцы';

$sc_private_event = 'Приватное событие';
$sc_calendar_exists = 'A calendar with this name already exists';
//translate
$sc_month = 'Месяц';
$sc_view = 'Просмотр';
$sc_calendar = 'Календарь';
$sc_default = 'По умолчанию';
$sc_month_view = 'Месяц';

//text changed, needs new translation
$sc_holidays_no_file_prefix = 'The holidays couldn\'t be generated, because the file containing holidays information for the ';
$sc_holidays_no_file_suffix = ' language is either empty or doesn\'t exist.';

$sc_no_events = 'События не найдены';

//2.03
$cal_export = 'Экспорт';
$cal_import = 'Импорт';
$cal_please_wait = 'Пожалуйста ждите...';
$cal_import_text = 'Select an iCal (*.ics) file and click at \'Ok\'';
$cal_filename = 'Файл';
$cal_import_success = 'Данные импортированы';

//2.04
$cal_add_todo = 'Новая задача';
$cal_todos = 'Задачи';

$cal_hide_completed = 'Скрыть завершенные';
$cal_show_all = 'Показать все задачи';
$cal_show_own = 'Только свои задачи';
$cal_show_responsible = 'Show reponsible tasks';

$cal_priority = 'Приоритет';
$cal_priority_values[0] = 'Низкий';
$cal_priority_values[1] = 'Нормальный';
$cal_priority_values[2] = 'Высокий';

$cal_status = 'Статус';
$cal_canceled = 'Отменен';
$cal_completed = 'Завершен';
$cal_percent_completed = '% завершено';

$cal_before_task_start = 'Before the task starts';
$cal_before_task_due = 'Before the task should be finnished';
$cal_responsible = 'Responsible';
$cal_due_at = 'Due at';
$cal_no_todos= 'Задачи не найдены';
$cal_resend_invitation = 'Resend invitation';

//2.05
$cal_everybody_write = 'Кто угодно может модифицировать это событие';
$cal_everybody_read = 'Только я могу модифицировать это событие';

$cal_view = 'Просмотр';
$cal_views = 'Просмотр';
$sc_view_exists = 'Просмотр с таким именем уже существует';
$cal_new_view = 'Новый просмотр';
$cal_event_calendars = 'Календарь(и)';

//2.06
$cal_view_merged = 'merged';
$cal_view_emerged = 'emerged';

//2.07
$cal_display = 'Отображение';
$sc_new_task = "Новая задача";
$sc_all_day_events = 'all day events';
$sc_all_day_event = 'all day event';
$sc_usual_events = 'события';
$sc_usual_event = 'событие';

//2.08
$sc_recur_section = 'Напоминания';
$sc_options_section = 'Опции';
$sc_close = 'Закрыть';

$cal_count_single = 'событие';
$cal_count_multiple = 'события';

$cal_until = 'until';
$cal_forever = 'всегда';

//2.09
$cal_merged_overview = 'Merged overview';
$cal_own_availability = 'Own availability';

//2.11
$cal_no_file = 'No file was recieved';
$cms_no_calendars_in_view = 'This view can\'t be displayed because there are no calendars in this view. Click at \'Views\' to manage views.';
$cal_no_reminder = 'Не напоминать';

//2.13
$cal_refresh_rate = 'Частота обновления';
$sc_seconds = 'seconds (Leave empty for no refresh)';
$cal_default_reminder = 'Напоминание по умолчанию';
?>
