<?php
/**
 * Russian translation
 * By Valery Yanchenko (utf-8 encoding)
 * vajanchenko@hotmail.com
 * 10 December 2008
*/
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Задачи';
$lang['tasks']['description']='Введите описание';

$lang['link_type'][12]=$lang['tasks']['task']='Задача';
$lang['tasks']['status']='Статус';


$lang['tasks']['scheduled_call']='Запланировать звонок в %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Необходимы действия';
$lang['tasks']['statuses']['ACCEPTED'] = 'Принято';
$lang['tasks']['statuses']['DECLINED'] = 'Отклонено';
$lang['tasks']['statuses']['TENTATIVE'] = 'Предварительно';
$lang['tasks']['statuses']['DELEGATED'] = 'Делегировано';
$lang['tasks']['statuses']['COMPLETED'] = 'Выполнено';
$lang['tasks']['statuses']['IN-PROCESS'] = 'В процессе';

$lang['tasks']['import_success']='%s задач импортированно';

$lang['tasks']['call']='Позвонить';

$lang['tasks']['dueAtdate']='До %s';
$lang['tasks']['list']='Список задач';
$lang['tasks']['tasklistChanged']="* Список задач изменен с '%s' - '%s'";
$lang['tasks']['statusChanged']="* Статус изменен с '%s' на '%s'";
$lang['tasks']['multipleSelected']='Выбрано несколько списков задач';
$lang['tasks']['incomplete_delete']='У Вас недостаточно прав чтобы удалить все выбранные задачи';
