<?php
require_once($GO_LANGUAGE->get_fallback_base_language_file('common'));

$charset = 'UTF-8';

$lang_modules['scheduler'] = 'Календар';
$lang_modules['filesystem'] = 'Файлове';
$lang_modules['email'] = 'Поща';
$lang_modules['projects'] = 'Проекти';
$lang_modules['search'] = 'Търся';
$lang_modules['contacts'] = "Адресна книга";
$lang_modules['phone'] = "Телефонен указател";

$lang_modules['cms'] = "Сайтове";

//main language menu file register login and other non logged in pages
//common
$cmdLogin = "Влиза";
$cmdSave = "Запис";
$cmdBack = "Назад";
$cmdCancel = "Отказ";
$cmdClose = "Затваря";
$cmdOk = "ОК";
$cmdDelete = "Изтрива";
$cmdNext = "Следваш";
$cmdPrevious = "Предишен";
$cmdContinue = "Продължение";
$cmdUpdate = "Сънхронизира";
$cmdApply = "Прилага";
$cmdYes = "Да";
$cmdNo = "Не";
$cmdAdd = 'Добавя';
$cmdEdit = 'Редактиране';
$cmdInvertSelection = 'Обратна селекция';

$strEdit = "Редактиране";
$strThis = 'това';
$strItems = 'предмети';
$strDescription = 'Описание';

$strName = "Име";
$strUser = "Потребител";
$strUsername = "Потребителско име";
$strPassword = "Парола";
$strAuthcode = "Уторизиращ код";
$strCountry = "Страна";
$strState = "Положение";
$strCity = "Град";
$strZip = "ZIP/Пощенски код";
$strAddress = "Адрес";
$strEmail = "E-mail";
$strPhone = "Телефон";
$strWorkphone = "Слежебен телефон";
$strCellular = "Клетъчен телефон";
$strCompany = "Компания";
$strDepartment = "Отдел";
$strFunction = "Функция";
$strQuestion = "Секретен въпрос";
$strAnswer = "Отговор";
$strFax = "Факс";
$strWorkFax = "Факс на работа";
$strHomepage = "Домашна страница";
$strWorkAddress = "Адрес на работа";
$strWorkZip = "Zipcode на работа";
$strWorkCountry = "Страна в която работя";
$strWorkState = "Работно положение";
$strWorkCity = "Град в който работя";
$strPhoto = "Изображение";
$strComments = "Коментар";

$strProperties = 'Свойства';

$strDeleteItem = "Изтрива";

$strDate = "Дата";

$strSaveSuccess = "Данните запазени успешно";
$strSaveError = "Възникнала е грешка при запазване на данните. Моля опитайте отново.";
$strDataError = "Възникнала е грешка при четене на данните. Моля опитайте да презаредите страницата.";
$strDeleteError = "Не може да се изстрият данните.";
$strAclError = "Грешка при контрола за достъп";
$strDateError = "Въвели сте грешна дата";



$strAccessDenied = "Достъпа забранен";
$AccessDenied_text = "Не ви е позволено да видите тази част от сайта или да извършите това действие.<br />Друг потребител е ограничил достъпа до информацията която искате.";

$invalid_chars = "Следните символи не са позволени";

$strGroups = "Групи";
$strOwner = "Собственик";

$strDeletePrefix = "Сигурен ли сте че искате да изтриете ";
$strDeleteSuffix = "?";

//menu
$menu_logout = "Излизам";
$menu_applications = "Приложения";
$menu_help = "Помощ";
$menu_about = "Относно";
$menu_documentation = "Документи";
$menu_configuration = "Настройки";
$menu_groups = "Потребител&nbsp;групи";
$menu_accounts = "Вашия&nbsp;account";
$menu_preferences = "Предпочитание";
$menu_bookmarks = "Белязка";
$menu_bookmarks_manage = "Промени&nbsp;белязки";
$menu_add_bookmark = "Добави&nbsp;белязки";
$menu_add_current = "Добави в&nbsp;текущата&nbsp;страница";

$menu_invite = "Покана&nbsp;потребители";

$menu_search_discussions = "Дискусия";
$menu_search_files = "Файлове&nbsp;и&nbsp;папки";
$menu_search_web = "The&nbsp;web";
$menu_scheduler = "Каталог";
$menu_filetypes = "Типове файлове";
$menu_discussions = "Дискусионно&nbsp;табло";

$menu_users = "Потребители";
$menu_start = "Начална&nbsp;страница";
$menu_reboot = "Рестартирай&nbsp;сървъра";
$menu_halt = "Изключи&nbsp;сървера";
$menu_reboot_text = "ВНИМАНИЕ! Всички Група-Офис сервизи ще бъдат временно недостъпни. Сигурен ли сте че искате да продължите?";
$menu_halt_text = "ВНИМАНИЕ! Всички Група-Офис сервизи ще бъдат премахнати. Сигурен ли сте че искате да продължите?";
$menu_new = "Нов";
$menu_message = "Съобщения";
$menu_contact = "Контакт";
$menu_event = "Събитие";
$menu_bookmark = "Белязка";
$menu_topics = "Топици";
$menu_support = "Подръжка";
$menu_projects = "Проекти";
$menu_sysinfo = "Състемна информация";
$menu_admin = 'Администратор';
$menu_modules = 'Модули';
$menu_manual = 'Наръчник';

//class validate
$error_required = "Не сте отбелязали изисканото поле отдолу.";
$error_match_auth = "Кодовете за оторизация не съвпадат.";
$error_match_pass = "Паролите не съвпадат.";
$error_min_length = "Минимум символи на полето отдолу:";
$error_max_length = "Максимум символи на полето отдолу:";
$error_email = "E-mail адресът който сте отбелязали е невалиден.";
$error_email_exists = "E-mail адресът който сте отбелязали вече съществува.";
$error_username_exists = "Съжаляваме, потребителското име което сте въвели вече съществува. Моля изберете друго.";
$errors_in_form = "Има грешки в очертанието. Поправете ги и опитайте отново.";
$error_missing_field = "Не сте попълнили всички изискани полета.";

//acl control
$acl_control_auth_groups = "Оторизирани групи";
$acl_control_auth_users = "Оторизирани потребители";
$acl_control_no_contacts = "Нямате никакви добавени контакти за да изберете.<br /> Използвайте Адресника за да добавите контакти.";

$strShowProfile = "Вижте Профил";
$strShowGroup = "Вижте членове на групата";
$strProcessingTime = "Хода на тази страница отне ";
$strLoggedInAs = "Вързан като ";
$strAt = " на ";
$strEmailTo = "Изпрати E-mail на: ";
$strProtectedUser = "Защитен потребител";

$http_not_found = "Документът не е намерен";
$http_not_found_text = "Искания документ не е намерен. Може би сте сгрешили URL.";
$http_error = "Грешка на Вутрешния Сървър";
$http_bad_request = "Лошо искане";
$strAddContact = "Добави към контакти";

$strReadRights = "Права за четене";
$strWriteRights = "Права за писане";
$strPermissions = 'права';

$editor_bold = "Получер";
$editor_italic = "Курсив";
$editor_underline = "Подчертан";
$editor_left = "Ляво";
$editor_right = "Дясно";
$editor_center = "Център";
$editor_ord_list = "Поръчан списък";
$editor_bul_list = "Отбелязан списък";
$editor_txt_color = "Цвят на текста";
$editor_back_color = "Цвят на фона";
$editor_hyperlink = "Връзки";
$editor_image = "Изображение";
$editor_rule = "Хоризонтално правило";

$editor_very_small = "Много малък";
$editor_small = "Малък";
$editor_medium = "Среден";
$editor_large = "Голям";
$editor_larger = "По-голям";
$editor_very_large = "Много голям";

$editor_strikethrough = "Просмукване през";
$editor_outdent = "Outdent";
$editor_indent = "Indent";

$editor_superscript = "Надпис";
$editor_subscript = "Подпис";
$editor_undo = "Отмяна";
$editor_redo = "Възстановяване";
$editor_copy = "Копиране";
$editor_paste = "Вмъкване";
$editor_cut = "Изрязване";

$editor_font = "Шрифт";
$editor_size = "Размер";
$editor_heading = "Заглавие";
$editor_select = "Маркирам";

$editor_mode = "Toggle editor mode";

$days[0]="Не";
$days[1]="По";
$days[2]="Вт";
$days[3]="Ср";
$days[4]="Че";
$days[5]="Пе";
$days[6]="Съ";

$months[0]="Януари";
$months[1]="Фефруари";
$months[2]="Март";
$months[3]="Април";
$months[4]="Май";
$months[5]="Юни";
$months[6]="Юли";
$months[7]="Август";
$months[8]="Септември";
$months[9]="Октомври";
$months[10]="Ноември";
$months[11]="Декември";

$full_days[0] = "Неделя";
$full_days[1] = "Понеделник";
$full_days[2] = "Вторник";
$full_days[3] = "Сряда";
$full_days[4] = "Четвъртък";
$full_days[5]= "Петък";
$full_days[6] = "Събота";
$strLoadTime = 'Генерирано в';
$strDescription = 'Описание';
$strTitle = 'Заглавие';


//translation required
$editorCmd['undo'] = "Отмяна";
$editorCmd['redo'] = "Възстановяване";
$editorCmd['bold'] = "Получер";
$editorCmd['italic'] = "Курсив";
$editorCmd['underline'] = "Подчертан";
$editorCmd['left'] = "Подравнява от ляво";
$editorCmd['right'] = "Подравнява от дясно";
$editorCmd['full'] = "Пълни";
$editorCmd['center'] = "Центрира";
$editorCmd['ord_list'] = "Номериран списък";
$editorCmd['bul_list'] = "Отбелязан списък";
$editorCmd['txt_color'] = "Цвяттекст";
$editorCmd['back_color'] = "Цвятфон";
$editorCmd['hyperlink'] = "Връзки";
$editorCmd['image'] = "Добави изображение";
$editorCmd['rule'] = "Хоризонтално правило";
$editorCmd['table'] = "Добави таблица";

$editorCmd['strikethrough'] = "Просмукване през";
$editorCmd['outdent'] = "Outdent";
$editorCmd['indent'] = "Indent";

$editorCmd['superscript'] = "Надпис";
$editorCmd['subscript'] = "Подпис";
$editorCmd['undo'] = "Отмяна";
$editorCmd['redo'] = "Възстановяване";
$editorCmd['copy'] = "Копиране";
$editorCmd['paste'] = "Вмъкване";
$editorCmd['cut'] = "Изрязване";

$editorCmd['font'] = "Фонт";
$editorCmd['size'] = "Размер";
$editorCmd['heading'] = "Заглавие";
$editorCmd['select'] = "Маркирам";
$editorCmd['mode'] = "Начин за промяна";
$editorCmd['textindicator'] = 'Текущ стил';

//translation required
$lang_modules['addressbook'] = "Адресна книга";
$cmdShowAll = 'Покажи всичко';

$cmdSearch = 'Търся';
?>
