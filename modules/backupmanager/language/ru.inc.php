<?php
/**
 * Russian translation
 * By Valery Yanchenko (utf-8 encoding)
 * vajanchenko@hotmail.com
 * 10 December 2008
*/

//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('backupmanager'));

$lang['backupmanager']['name']='Резервное копирование';
$lang['backupmanager']['description']='Настроить резервное копирование в cronjob';
$lang['backupmanager']['save_error']='Ошибка при сохрании настроек';
$lang['backupmanager']['empty_key']='Ключь пустой';
$lang['backupmanager']['connection_error']='Не возможно подключиться к серверу';
$lang['backupmanager']['no_mysql_config']='{product_name} не смог обнарудить конфигурауионный файл mysql. Этот файл нужен для создания полной резервной копии базы данных. Вы можете создать этот файл в директории /etc/groupoffice/ самостоятельно. Имя файла backupmanager.inc.php В файле должно содержаться следующее:
    <br /><br />&lt;?php<br />
    $bm_config[\'mysql_user\'] = \'\';<br />
    $bm_config[\'mysql_pass\'] = \'\';<br />
    ?><br /><br />
    Без этого файла резервные копии будут создаваться, но в них не будет содержаться данные из базы данных mysql.';
$lang['backupmanager']['target_does_not_exist']='Директория назначения не доступна или не существует!';