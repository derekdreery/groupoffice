/**
 * Russian translation
 * By Valery Yanchenko (utf-8 encoding)
 * vajanchenko@hotmail.com
 * 10 December 2008
*/

Ext.namespace('GO.backupmanager');
GO.backupmanager.lang={};

GO.backupmanager.lang.backupmanager='Резервное копирование';
 GO.backupmanager.lang.rmachine='Удаленный компьютер';
GO.backupmanager.lang.rport='Порт';
GO.backupmanager.lang.rtarget='Каталог назначения';
GO.backupmanager.lang.sources='Исходные каталоги';
GO.backupmanager.lang.rotations='Ротация';
GO.backupmanager.lang.quiet='Тихий режим';
GO.backupmanager.lang.emailaddresses='Email адреса';
GO.backupmanager.lang.emailsubject='Email тема';
GO.backupmanager.lang.rhomedir='Каталог на удаленном компьютере';
GO.backupmanager.lang.rpassword='Пароль';
GO.backupmanager.lang.publish='Publish';
GO.backupmanager.lang.enablebackup='Начать резервное копирование';
GO.backupmanager.lang.disablebackup='Остановить резервное копирование';
GO.backupmanager.lang.successdisabledbackup='Резервное копирование отключено!';
GO.backupmanager.lang.publishkey='Включить резервное копирование';
GO.backupmanager.lang.publishSuccess='Резервное копирование включено.';
GO.backupmanager.lang.helpText='Этот модуль создает резервную копию всех файлов и MySQL базы данных (уюедитесь что Вы добавили /home/mysqlbackup в список исходных каталогов) на удаленный серевер через rsync и SSH. Когда Вы включаете резервное копирование Ваш сервер  отправляет на сервер назначения свой публичный SSH ключь и проверяет существует ли на нем каталог назначения. Поэтому сначала убедитесь что на сервере назначения существует каталог назначения. По умолчанию резервное копирование запланированно на 00:00 в /etc/cron.d/groupoffice-backup. Вы можете изменить время запуска резервного копирования в этом файле или создать его самостоятельно если он не существует. Чтобы выполнить резервное копирование вручную запустите "php /usr/share/groupoffice/modules/backupmanager/cron.php".';