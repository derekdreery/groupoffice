<?php

$l["backupmanager"]='Zálohování';
$l['name']='Zálohování';
$l['description']='Modul, který umožňuje nastavit zálohování pomocí služby cron';
$l['save_error']='Problém při ukládání nastavení';
$l['empty_key']='Klíč je prázdný';
$l['connection_error']='Nepodařilo se připojit k serveru';
$l['no_mysql_config']='Nepodařilo se najít konfigurační soubor pro připojení k databázím. Tento soubor je využíván pro kompletní zálohu. Vytvořte soubor s názvem backupmanager.inc.php do složky /etc/groupoffice/ a vložte do něj tyto řádky:;
    <br /><br />&lt;?php<br />;
    $bm_config[\'mysql_user\'] = \'\';<br />;
    $bm_config[\'mysql_pass\'] = \'\';<br />
    <br /><br />;
    Bez tohoto souboru se budou zálohy i nadále vytvářet, ale bez databází.';
$l['target_does_not_exist']='Cílový adresář neexistuje!';$l["backupmanager"]='Zálohování';
$l["rmachine"]='Vzdálený server';
$l["rport"]='SSH port';
$l["rtarget"]='Cílový adresář';
$l["sources"]='Zdrojové adresáře';
$l["rotations"]='Počet rotací';
$l["quiet"]='Bez upozornění';
$l["emailaddresses"]='E-mailové adresy';
$l["emailsubject"]='Předmět e-mailu';
$l["rhomedir"]='Vzdálený domovský adresář';
$l["rpassword"]='Heslo';
$l["publish"]='Poskytnout';
$l["enablebackup"]='Spustit zálohování';
$l["disablebackup"]='Zrušit zálohování';
$l["successdisabledbackup"]='Zálohování bylo úspěšně zrušeno!';
$l["publishkey"]='Veřejný klíč';
$l["publishSuccess"]='Zálohování bylo úspěšně spuštěno.';
$l["helpText"]='Tento modul umožňuje zálohu souborů a všech MySQL databází (ujistěte se, že /home/mysqlbackup je ve zdrojových adresářích) na vzdáleném serveru pomocí rsync a SSH. Při spuštění zálohování bude vzdálenému serveru poskytnut veřejný SSH klíč a zkontrolována existence cílového adresáře. Nejdříve se ujistěte, že cílový adresář existuje. Výchozí zálohování je naplánováno na půlnoc v souboru /etc/cron.d/groupoffice-backup. Naplánování můžete upravit přímo v tomto souboru nebo jej vytvořit, když neexistuje. Zálohu můžete také spustit manuálně pomocí příkazu "php /usr/share/groupoffice/modules/backupmanager/cron.php" v terminálu.';