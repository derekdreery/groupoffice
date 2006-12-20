<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_base_language_file('account'));

//privacy
$privacy_text = "Select the users and groups that you want to be visible to.";

//security
$security_text = "Введите Ваш старый пароль, новый пароль, подтвердите. Закончите ввод кнопкой 'Записать'.";
$security_current_password = "Текущий пароль";
$security_new_password = "Новый пароль";
$security_confirm_password = "Подтверждение пароля";
$security_new_authcode = "Новый авторизационный код";
$security_confirm_authcode = "Подтверждение авторизационного кода";
$security_question = "Секретный вопрос";
$security_answer = "Ответ";
$security_wrong_password = "Неправильный пароль.";
$security_tasks = "Задача завершена";
$security_password_update = "Пароль обновлен.";
$security_authcode_update = "Авторизационные коды обновленны успешно.";
$security_question_update = "Секретные вопрос и ответ обновлены успешно.";
$security_question_fail = "You didn't fill in both the question and the answer.";

$acProfile = "Профиль";
$acSecurity = "Безопасность";
$acPrivacy = "Частное";
$acManager = "Ваша учетная запись";
$acStatistics = "Статистика";
$acProfileText = "Нажмите кнопку 'Записать' для записи Вашего профиля. Поля отмеченные * обязательны для заполнения.";
$acTitle = "Ваша учетная запись";
$ac_login_info = "Информация о входах";
$ac_registration_time = "Время регистрации";
$ac_lastlogin = "Последний вход";
$ac_logins = "Всего входов";
$ac_disk_usage = "Использование диска";
$ac_max_diskspace = "Максимально";
$ac_available_diskspace = "Доступно";
$ac_used_diskspace = "Использовано";
$ac_used = "использовано";

//translation required
$ac_add_user = 'Запрос авторизации';
$ac_auth_error = 'An error occured while authorizing. Please try again.';
$ac_auth_success = 'Your account is now visible to this user and a message has been sent to this user that you accepted his request.';
$ac_auth_decline = 'A message has been sent to this user that you have declined his request.';
$ac_auth_accept_mail_title = 'Group-Office authorization request accepted';
$ac_auth_decline_mail_title = 'Group-Office authorization request denied';

$ac_auth_accept_mail_body = 'has accepted your request to make his account visible to you.';
$ac_auth_decline_mail_body = 'has declined your request to make his account visible to you.';
$ac_request_auth = 'requests your permission to make your account visible. Do you want to make your account visible to this user?';
$ac_request_auth_mail_title ='Group-Office authorization request';
$ac_invalid_email = 'The e-mail address you entered is not registered in the system. Please try again.';
$ac_request_mail_body_prefix = 'A message was sent to ';
$ac_request_mail_body_suffix = 'requesting his permission to make his account visible to you.';
$ac_auth_text = 'Enter the e-mail address from the user who\'s account you want to make visible.';

$ac_accept = 'Разрешено';
$ac_decline = 'Decline';
$ac_salutation = 'Dear';
