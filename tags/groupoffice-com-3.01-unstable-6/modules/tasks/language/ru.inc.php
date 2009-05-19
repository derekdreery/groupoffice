<?php
/**
 * Russian translation
 * By Valery Yanchenko (utf-8 encoding)
 * vajanchenko@hotmail.com
 * 10 December 2008
*/
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Задачи';
$lang['tasks']['description']='Введите описание';

$lang['link_type'][12]=$lang['tasks']['task']='Задача';
$lang['tasks']['status']='Cтaтyc';


$lang['tasks']['scheduled_call']='Запланировать звонок в %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Необходимы действия';
$lang['tasks']['statuses']['ACCEPTED'] = 'Принято';
$lang['tasks']['statuses']['DECLINED'] = 'Отклонено';
$lang['tasks']['statuses']['TENTATIVE'] = 'Предварительно';
$lang['tasks']['statuses']['DELEGATED'] = 'Делегировано';
$lang['tasks']['statuses']['COMPLETED'] = 'Выполнено';
$lang['tasks']['statuses']['IN-PROCESS'] = 'В процессе';


$lang['tasks']['call']='Позвонить';
?>