<?php


$l['name']='Резервное копирование';
$l['description']='Настроить резервное копирование в cronjob';
$l['save_error']='Ошибка при сохрании настроек';
$l['empty_key']='Ключь пустой';
$l['connection_error']='Не возможно подключиться к серверу';
$l['no_mysql_config']='Group-Office не смог обнарудить конфигурауионный файл mysql. Этот файл нужен для создания полной резервной копии базы данных. Вы можете создать этот файл в директории /etc/groupoffice/ самостоятельно. Имя файла backupmanager.inc.php В файле должно содержаться следующее:;
    <br /><br />&lt;?php<br />;
    $bm_config[\'mysql_user\'] = \'\';<br />;
    $bm_config[\'mysql_pass\'] = \'\';<br />
    <br /><br />;
    Без этого файла резервные копии будут создаваться, но в них не будет содержаться данные из базы данных mysql.';
$l['target_does_not_exist']='Директория назначения не доступна или не существует!';
$l["backupmanager"]='Резервное копирование';
$l["rmachine"]='Удаленный компьютер';
$l["rport"]='Порт';
$l["rtarget"]='Каталог назначения';
$l["sources"]='Исходные каталоги';
$l["rotations"]='Ротация';
$l["quiet"]='Тихий режим';
$l["emailaddresses"]='Email адреса';
$l["emailsubject"]='Email тема';
$l["rhomedir"]='Каталог на удаленном компьютере';
$l["rpassword"]='Пароль';
$l["publish"]='Publish';
$l["enablebackup"]='Начать резервное копирование';
$l["disablebackup"]='Остановить резервное копирование';
$l["successdisabledbackup"]='Резервное копирование отключено!';
$l["publishkey"]='Включить резервное копирование';
$l["publishSuccess"]='Резервное копирование включено.';
$l["helpText"]='Этот модуль создает резервную копию всех файлов и MySQL базы данных (уюедитесь что Вы добавили /home/mysqlbackup в список исходных каталогов) на удаленный серевер через rsync и SSH. Когда Вы включаете резервное копирование Ваш сервер  отправляет на сервер назначения свой публичный SSH ключь и проверяет существует ли на нем каталог назначения. Поэтому сначала убедитесь что на сервере назначения существует каталог назначения. По умолчанию резервное копирование запланированно на 00:00 в /etc/cron.d/groupoffice-backup. Вы можете изменить время запуска резервного копирования в этом файле или создать его самостоятельно если он не существует. Чтобы выполнить резервное копирование вручную запустите "php /usr/share/groupoffice/modules/backupmanager/cron.php".';