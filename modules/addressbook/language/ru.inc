<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('addressbook'));

//contacts
$contacts_title = "Адресная книга";
$contacts_no_contacts = "Контакты не найдены.";
$contacts_members = "Пользователи";
$contacts_contacts = "Контакты";
$contacts_add = "Новый контакт";
$contacts_groups = "Группы";
$contacts_other = "Другие контакты";
$contacts_move_to_group = "Переместить выбранные контакты в группу...";
//$groups_no_groups = "You don't have any groups";
$groups_no_groups = "Вы не имеете ни одной группы";
$contacts_group = "Группа";
$contacts_empty_group = "Используйте выпадающее меню для перемещения выбранных контактов в другую группу.";
$contacts_this_group = 'эта группа';

$add_contacts_text = "Fill in the members E-mail adress and authorization code and click on 'Ok'";
$add_contacts_success = "Пользователь успешно добавлен";
$add_contacts_wrong = "Wrong E-mail or authorization code.";
$add_personal_text = "Нажмите 'Сохранить' для добавления контакта. Поля отмеченные * обязательны для заполнения.";
$contacts_select = "Выберете контакты для добавления и нажмите 'Ok'.";
$contacts_select_title = "Контакты";
$contact_profile = "Профиль";
$user_profile = "Профиль пользователя";
$contacts_authorise = "Авторизация";
$contacts_add_member = "Добавить этого пользователя в ваши контакты";
$strConfirmSynchronise = "This will update this contact with data the user has entered himself. Manual changes by you will be lost. Are you sure you want to continue?";
$contacts_import = "Импорт";
$contacts_export = "Экспорт";
$contacts_export_text = "Select the type of CSV you would like to create.";
$contacts_delete = "Удалить";

$contacts_import_text_group = "Select a group and the CSV file. Click on 'Ok'.";
$fbNoFile = "No file was not recieved";
$fbPleaseWait = "Пожалуйста ждите. Файл загружается...";

$strNotIncluded = "Do not import";
$contacts_import_feedback = "The following columns where found in your CSV file.<br />";
$contacts_import_feedback .= "Select the group you wish to import to or automatically create groups based on a column of the file.<br />";
$contacts_import_feedback .= " Match corrosponding Group-Office columns with the columns from the file and make sure that you don't select columns twice.";
$contacts_separator = "Values are seperated by";
$contacts_no_separator = "You didn't enter a separator character.";
$fbFilename = "Имя файла";
$contacts_import_title = "Импорт адресной книги";
$contacts_import_success = "Данные успешно импортированны";
$contacts_import_double = "Вы выбрали одну или больше колонок дваждыe. Измените это и повторите.";
//changed:
$contacts_import_noname = 'You must select the fields \'First_name\' and \'Last name\' ';
$contacts_import_incompatible = "Несовместимый формат файла.<br /><br />CSV файл содержит значения разделенные точками.";
$strMember = "Участник";
$contact_exist_warning = "<b>Замечание:</b> Этот пользователь уже находится в контактном листе.";
$contacts_add_to_group = "Добавить выбранных пользователей в группу...";
$contacts_add_to = "Добавить выбранных пользователей в контакт";
$contacts_add_user = "добавить";
$contacts_edit = "Редактировать";
$contacts_no_select = "Вы ничего не выбрали";
$contacts_this_contact = "этот контакт";

$contacts_seperator_text = 'Значения разделены символом';
$contacts_quote = 'Значения заключены в символы';
$contacts_clrf = 'Строки заканчиваются';
$contacts_auto_group = 'Automatically create groups from file';
$contacts_import_to_group = 'Импорт в группу';

$contacts_search = 'Поиск';
$cmdSearch = 'Поиск';
$contacts_search_text = "Click on a letter or enter a search phrase and click on 'search'. You can use * as a wild character.";
$contacts_results = 'результат';
$contacts_result = 'результат';


$contacts_color = 'Цвет';

$contacts_colors['black'] = 'Черный';
$contacts_colors['red'] = 'Красный';
$contacts_colors['green'] = 'Зеленый';
$contacts_colors['blue'] = 'Синий';
$contacts_colors['pink'] = 'Розовый';
$contacts_colors['orange'] = 'Оранжевый';
$contacts_colors['brown'] = 'Коричневый';

$ab_addressbooks = 'Адресная книга';
$ab_owner = 'Владелец';
$ab_subscribed = 'Используемые';
$ab_new_ab = 'Нет адресной книги';
$ab_addressbook_exists = 'Адресная книга с таким именем уже существует';
$ab_addressbook = 'Адресная книга';
$contacts_select_single = 'Кликните на контакт для выбора персоны';

$ab_group_exists = 'There already is an addressbook with this name';
$ab_group_on_file = 'Group contacts based on file';
$ab_default = 'Основная (default)';

//translation required
$ab_edit_contact = 'Нажмите на \'Ok\' для сохранения изменений. Поля обозначенные как * обязательны для заполнения';
$ab_group_on_file = 'Group contacts based on the file';
$ab_default = 'Основная';
$ab_browse = 'Просмотр';
$ab_comment = 'Комментарий';

$ab_contact = 'Контакт';
$ab_send_message = 'E-mail';
$ab_new_event = 'Событие';

$ab_templates = 'Шаблоны';
$ab_your_templates = 'Вы';
$ab_all_templates = 'Все';

$template_type[0] = 'E-mail';
$template_type[1] = 'OpenOffice.Org';
$ab_type = 'Тип';
$ab_template_exists = 'Шаблон с таким именем уже существует.';

$sir_madam['M'] = 'Уважаемый';
$sir_madam['F'] = 'Уважаемая';

$ab_upload_oo = 'Загрузка OpenOffice.org шаблона';
$ab_upload_nofile = 'Ошибка при отправке файла.';

$ab_oo_doc = 'Текстовый документ';
$ab_select_template = 'Select the template for this document:';
$ab_add_field_data = 'You can use the following values in your document:';
$ab_datafield = 'Datafield';

$ab_no_template = 'Нет шаблона';
$ab_new_note = 'Note';

$ab_events = 'Назначение (должность)';

$ab_new_contact = 'Новый&nbsp;контакт';
$ab_new_company = 'Новая&nbsp;организация';
$ab_companies = 'Организации';
$ab_company_properties = 'Профиль&nbsp;оргинизации';
$ab_bank_no = 'Банковский&nbsp;счет';
$ab_companies_count_single = 'организация';
$ab_companies_count = 'организации';
$ab_no_companies = 'Организации не найдены';
$ab_no_company = 'Нет организации';

$ab_import_type = 'Импорт';
$ab_no_company_found = 'Организации не найдены';
$ab_search_company = 'Просморт организаций';
$ab_edit_company = 'Изменить профиль организации';
$ab_new_group = 'Новая группа';
$ab_export_type = 'Экспорт';

$ab_custom_fields = 'Extra&nbsp;datafields';
$ab_field_size = 'Размер';

$ab_new_field = 'New datafield';
$ab_invalid_field_size = 'Неправильный размера. Выберете значение между 10 и 255.';
$ab_field_exists = 'Поле с таким именем уже существует.';

$ab_employees = 'Служащие';
$ab_no_employees = 'Служащие не найдены';

$ab_employee_count_single = 'служащий';
$ab_employee_count_multiple = 'служащие';
$ab_vat_no = 'VAT number';

$ab_add_new = 'Добавить нового';
$ab_add_existing = 'Добавть существующего';

$ab_access = 'Доступ';
//1.98
$ab_mailings = 'Отправления';
$ab_mailing_groups = 'Группы отправлений';
$ab_no_mailing_groups = 'Не найдены группы отправлений';
$ab_no_templates = 'Шаблон не найден';
$ab_mailing_group_exists = 'Группа отправлений с таким именем уже существует.';
$ab_contacts_count_single = 'контакт';
$ab_contacts_count_multiple = 'контакты';
$ab_new_mailing = 'Новое отправление';
$ab_all_your_addressbooks = 'все ваши адресные книги';

$ab_search_for = 'Поиск для';
$ab_search_in = 'в';
$ab_search_on = 'на';
$ab_search_keyword = 'Фраза для поиска';
$ab_wildcard = '(Используйте \'%\' как групповой символ)';

//2.03
$ab_copy_read_acl = 'Копировать права на ЧТЕНИЕ на контакты и организации';
$ab_copy_write_acl = 'Копировать права на ЗАПИСЬ на контакты и организации';

$ab_catagory_exists = 'Категория с таким именем уже существует.';
$ab_add_catagory = 'Добавить категорию';
$ab_add_field = 'Добавить поле';
$ab_catagory = 'Категория';

//2.04
$ab_todos = 'Задачи';


//2.05
$ab_download = 'Закачка';
$ab_download_template = 'Click at the \'Download\' button to download the template to your PC for editing.';
$ab_upload = 'Upload';
//modified string:
$ab_upload_oo_text = 'When you finnished with editting your OpenOffice.org template you should transfer it back to Group-Office. Select the file and click at \'Upload\' to transfer your template.';
$ab_file_type = 'Тип файла';
$ab_import_vcf_file_ok = 'Импорт из файла успешно завершен. Пожалуйста нажмите \'Ok\' для принятия (усыновления) адресной книги .';
$ab_import_failed = 'Импорт закончился неудачей.';
//modified string:
$contacts_import_text = "Выберете файл, содержащий данные для импорта и нажмите 'Ok'";

//2.06
$ab_select_mailing_groups = 'Select the mailing groups where this contact belongs to';

//2.11
$ab_move_to_ab = 'Перемещение выбранных кинтактов в адресную книгу...';
$ab_move_com_to_ab = 'Перемещение выбранных компаний в адресную книгу...';

//2.12
$ab_search_sender = 'Поиск e-mail';

$ab_contact_exists = 'Такой контакт уже существует. Click %s to go to the existing contact or click \'Ok\' or \'Apply\' again to save this contact anyway.';
$ab_company_exists = 'Такая компания уже существует. Click %s to go to the existing company or click \'Ok\' or \'Apply\' again to save this company anyway.';
$ab_here = 'здесь';

//2.13
$ab_search_for_keyword = 'для';

$ab_search_in = 'Посик в'; //changed
$ab_search_on_users = 'Поиск на';
