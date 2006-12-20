<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_base_language_file('common'));

#$charset = 'UTF-8';
$charset = 'koi8-r';
//$charset = 'KOI-8';
//main language menu file register login and other non logged in pages
//common
$cmdLogin = "Вход";
$cmdSave = "Записать";
$cmdBack = "Назад";
$cmdCancel = "Отмена";
$cmdClose = "Закрыть";
$cmdOk = "Ok";
$cmdDelete = "Удалить";
$cmdNext = "Далее";
$cmdPrevious = "Назад";
$cmdContinue = "Продолжить";
$cmdUpdate = "Синхронизация";
$cmdApply = "Записать";
$cmdYes = "Да";
$cmdNo = "Нет";
$cmdAdd = 'Добавить';
$cmdEdit = 'Редактировать';
$cmdInvertSelection = 'Invert selection';

$strEdit = "Редактировать";
$strThis = 'этот';
$strItems = 'заметки';
$strDescription = 'Описание';

$strName = "Имя";
$strUser = "Пользователь";
$strUsername = "Имя пользователя";
$strPassword = "Пароль";
$strAuthcode = "Автороизационный код";
$strCountry = "Страна";
$strState = "Область/Штат";
$strCity = "Город";
$strZip = "ZIP/Почтовый индекс";
$strAddress = "Почтовый адрес";
$strEmail = "E-mail";
$strPhone = "Телефон";
$strWorkphone = "Рабочий телефон";
$strCellular = "Сотовый телефон";
$strCompany = "Организация";
$strDepartment = "Отдел";
$strFunction = "Должность";
$strQuestion = "Секретный вопрос";
$strAnswer = "Ответ";
$strFax = "Факс";
$strWorkFax = "Рабочий факс";
$strHomepage = "Домашняя страница";
$strWorkAddress = "Почтовый адрес (рабочий)";
$strWorkZip = "ZIP/Почтовый индекс (рабочий)";
$strWorkCountry = "Страна (работа)";
$strWorkState = "Область/Штат (рабочий)";
$strWorkCity = "Город (работа)";
$strPhoto = "Фото";
$strComments = "Коментарий";
$strProperties = 'Настройки';
$strDeleteItem = "Удаление";
$strDate = "Дата";
$strSaveSuccess = "Данные успешно сохранены";
$strSaveError = "Произошла непредвиденная ошибка при сохранении данных.";
$strDataError = "Произошла непредвиденная ошибка при чтении данных.";
$strDeleteError = "Данные не удалены.";
$strAclError = "Ошибка доступа";
$strDateError = "Вы ввели некоректные данные";
$strAccessDenied = "Доступ запрещен";
$AccessDenied_text = "You are not allowed to view this section of the site or to perform this action.<br />Another user has restricted access to the information you requested.";
$invalid_chars = "The following characters are not allowed";
$strGroups = "Группы";
$strOwner = "Владелец";
$strDeletePrefix = "Вы действительно хотите удалить ";
$strDeleteSuffix = "?";
//menu
$menu_logout = "Выход";
$menu_applications = "Приложения";
$menu_help = "Помощь";
$menu_about = "О приложение";
$menu_documentation = "Документация";
$menu_configuration = "Конфигурация";
$menu_groups = "Управление группами";
$menu_accounts = "Изменение настроек вашей учетной записи";
$menu_preferences = "Персональные настройки";
$menu_search_discussions = "Дискуссии";
$menu_search_files = "Файлы&nbsp;и&nbsp;папки";
$menu_search_web = "WEB";
$menu_filetypes = "Файловая&nbsp;системы";
$menu_discussions = "Дискуссии";

$menu_users = "Редактирование пользователей";
$menu_new = "Новый";
$menu_message = "Сообщение";
$menu_contact = "Контакт";
$menu_event = "Событие";
$menu_bookmark = "Bookmark";
$menu_topics = "Topics";
$menu_support = "Поддержка";
$menu_projects = "Проекты";
$menu_sysinfo = "Просмотр системной информации";
$menu_admin = 'Административное меню';
$menu_modules = 'Добавить/удалить модули';
$menu_manual = 'Manual';

//class validate
$error_required = "You didn't specify the required field below.";
$error_match_auth = "The authorisation codes didn't match.";
$error_match_pass = "The passwords didn't match.";
$error_min_length = "Minimum characters of the field below:";
$error_max_length = "Maximum characters of the field below:";
$error_email = "The E-mail address you specified was invalid.";
$error_email_exists = "The E-mail address you entered already exists.";
$error_username_exists = "Sorry, the username you entered already exists. Please choose another one.";
$errors_in_form = "There were errors in the form. Correct them and try again.";
$error_missing_field = "You didn't fill in all the required fields.";

//acl control
$acl_control_auth_groups = "Авторизованные (доверенные) группы";
$acl_control_auth_users = "Авторизованные (доверенные) пользователи";
$acl_control_no_contacts = "You don't have any member contacts to select.<br /> Use the Addressbook to add members to your contacts.";

$strShowProfile = "Просмотр профиля";
$strShowGroup = "Просмотр участников группы";
$strProcessingTime = "The processing of this page took ";
$strLoggedInAs = "Вход выполнен как ";
$strAt = " at ";
$strEmailTo = "Послать E-mail сообщение: ";
$strProtectedUser = "Защищенный пользователь";

$http_not_found = "Документ не найден";
$http_not_found_text = "Запрашиваемый документ не найден. Может быть неправильно введен URL.";
$http_error = "Ошибка сервера";
$http_bad_request = "Некорректный запрос";
$strAddContact = "Добавить в контакты";

$strReadRights = "Разрешение на чтение";
$strWriteRights = "Разрешение на запись";
$strPermissions = 'разрешения';

$days[0]="Вс";
$days[1]="Пн";
$days[2]="Вт";
$days[3]="Ср";
$days[4]="Чт";
$days[5]="Пт";
$days[6]="Сб";

$months[0]="Январь";
$months[1]="Февраль";
$months[2]="Март";
$months[3]="Апрель";
$months[4]="Май";
$months[5]="Июнь";
$months[6]="Июль";
$months[7]="Август";
$months[8]="Сентябрь";
$months[9]="Октябрь";
$months[10]="Ноябрь";
$months[11]="Декабрь";

$full_days[0] = "Воскресенье";
$full_days[1] = "Понедельник";
$full_days[2] = "Вторник";
$full_days[3] = "Среда";
$full_days[4] = "Четверг";
$full_days[5]= "Пятница";
$full_days[6] = "Суббота";
$strLoadTime = 'Созданно ';
$strDescription = 'Описание';



$lang_modules['addressbook'] = "Адресная книга";
$cmdShowAll = 'Показать все';
$cmdSearch = 'Поиск';

//translation required
$cmdYesToAll = 'Да для всех';
$cmdNoToAll = 'Нет для всех';

$short_months[0]="Янв";
$short_months[1]="Фев";
$short_months[2]="Мар";
$short_months[3]="Апр";
$short_months[4]="Май";
$short_months[5]="Июл";
$short_months[6]="Июн";
$short_months[7]="Авг";
$short_months[8]="Сен";
$short_months[9]="Окт";
$short_months[10]="Ноя";
$short_months[11]="Дек";

$strToday = 'Сегодня';

$cmdPrint = 'Печать';
$strFirstName = 'Имя';
$strLastName = 'Фамилия';
$strInitials = 'Инициалы';
$strSex = 'Пол';
$strBirthday = 'День рождения';
$strSexes['M'] = 'Муж';
$strSexes['F'] = 'Жен';

$strTitle = 'Заголовок';
$strBeginning = 'Beginning';
$strMiddleName = 'Отчество';
$sir_madam['M'] = 'Уважаемый';
$sir_madam['F'] = 'Уважаемая';
$strModifiedAt = 'Изменен';
$strCreatedAt = 'Создан';

$htmlarea_language_name = 'ru';
$strLocalization = 'Localization';


//look up the language in controls/jscalendar/lang
$jscalendar_language_name = 'calendar-en.js';


//2.01 translations

//look up language in 'classes/phpmailer/language'
$php_mailer_lang = 'ru';
$strEmailFail = 'Ошибка при отправке e-mail сообщения.';

//for people that want to use ktmlarea
$ktml_language = 'russian';

$ml_host_unreachable = ' is unreachable. Automatic checking of e-mail is disabled.';
$strRegion = 'Регион';

//2.05
$cmdReset = 'Сброс';
$strMyName = 'Мое имя';

//2.06
$strDeleteRow = 'Are you sure you want to delete this row?';
$strAddressNo = 'number of house';
$strWorkAddressNo = 'Work number of house';

$strPostAddress = 'Почтовый адрес';
$strPostAddressNo = 'Post number of house';
$strPostCity = 'Город';
$strPostState = 'Область/Штат';
$strPostCountry = 'Страна';
$strPostZip = 'Почтовый индекс';
$strVisitAddress = 'Физический адрес';
$strPostAddress = 'Почтовый адрес';


//modules
$lang_modules['scheduler'] = 'Планировщик';
$lang_modules['calendar'] = "Календарь";
$lang_modules['filesystem'] = 'Файлы';
$lang_modules['email'] = 'E-mail';
$lang_modules['projects'] = 'Проекты';
$lang_modules['search'] = 'Поиск';
$lang_modules['contacts'] = "Адреса";
$lang_modules['cms'] = "Web сайты";
$lang_modules['notes'] = "Заметки";
$lang_modules['bookmarks'] = "Закладки";
$lang_modules['summary'] = "Начало";
$lang_modules['todos'] = "Задачи";
$lang_modules['opentts'] = "Помощь";

$cmdVcard = 'vCard';


//2.07
$strNotEnoughLicenses = 'You don\'t have enough licenses to use this section of'.
												' Group-Office. Contact sales@intermesh.nl if you want '.
												'to purchase additional licenses.';
												
//2.08
$alphabet = array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','К','Л','М','Н','О',
									'П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ь','Э','Ю','Я');
									
$strContact = 'Контакт';
$strRelatedTo = 'Related to';
$strProject = 'Проект';

$strNewProject = 'Новый проект';
$strNewEvent = 'Новое событие';
$strNewTodo = 'Новое ToDo (сделать)';
$strNewNote = 'Новая заметка';

$strActivities = 'Activities';
$strFile = 'Файл';

//2.10
$cmdCopy = 'Копия';
$strProjects = 'Проекты';
$strFiles = 'Файлы';
$cmdSaveNew = 'Сохранение & Новое';
$strAvailability = 'Доступное';
$strCheckAvailabilityFor = 'Проверка доступного для';
$cmdPleaseSelect = 'Пожалуйста выберете...';

//2.11
$strNewEmail = 'Новое сообщение (e-mail)';
$strConsistencyCheck = "Consistency Check";
$strSearchAll = 'Все поля';
$sync_download_clients = 'Загрулить скиентов синхронизации';

//2.12
$strNoData = 'Данные не найдены';
//2.13
$strSynchronization = 'Синхронизация';
$strSnooze = 'Snooze';
$strDismiss = 'Dismiss';
