<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('backupmanager'));

$lang['backupmanager']['name']='Zálohování';
$lang['backupmanager']['description']='Modul, který umožňuje nastavit zálohování pomocí služby cron';
$lang['backupmanager']['save_error']='Problém při ukládání nastavení';
$lang['backupmanager']['empty_key']='Klíč je prázdný';
$lang['backupmanager']['connection_error']='Nepodařilo se připojit k serveru';
$lang['backupmanager']['no_mysql_config']='Nepodařilo se najít konfigurační soubor pro připojení k databázím. Tento soubor je využíván pro kompletní zálohu. Vytvořte soubor s názvem backupmanager.inc.php do složky /etc/groupoffice/ a vložte do něj tyto řádky:
    <br /><br />&lt;?php<br />
    $bm_config[\'mysql_user\'] = \'\';<br />
    $bm_config[\'mysql_pass\'] = \'\';<br />
    ?><br /><br />
    Bez tohoto souboru se budou zálohy i nadále vytvářet, ale bez databází.';
$lang['backupmanager']['target_does_not_exist']='Cílový adresář neexistuje!';