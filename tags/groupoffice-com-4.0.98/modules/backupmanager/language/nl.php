<?php


$l['name']='Backup Beheer';
$l['description']='Configureer uw backup cronjob';
$l['save_error']='Fout opgetreden bij het opslaan van de instellingen.';
$l['empty_key']='Sleutel is leeg';
$l['connection_error']='Kon geen verbinding maken met de server';
$l['no_mysql_config']='{product_name} heeft geen mysql config bestand kunnen vinden. Dit is nodig om een backup te kunnen maken van de volledige database. Je kunt deze zelf aanmaken door een bestand met de naam backupmanager.inc.php in /etc/groupoffice/ te plaatsen met de volgende inhoud:;
    <br /><br />&lt;?php<br />;
    $bm_config[\'mysql_user\'] = \'\';<br />;
    $bm_config[\'mysql_pass\'] = \'\';<br />
    <br /><br />;
    Zonder dit bestand worden de backups wel uitgevoerd, echter niet van de database.';
$l['target_does_not_exist']='De doelmap bestaat niet!';$l["backupmanager"]='Backup Beheer';
$l["rkey"]='Keyfile';
$l["rmachine"]='Doel host';
$l["rport"]='Poort';
$l["rtarget"]='Doelmap';
$l["sources"]='Bronmappen';
$l["rotations"]='Rotaties';
$l["quiet"]='Stil';
$l["emailaddresses"]='Email adressen';
$l["emailsubject"]='Email onderwerp';
$l["rhomedir"]='Doel homedir';
$l["rpassword"]='Wachtwoord';
$l["publish"]='Publiceer';
$l["publishkey"]='Backup inschakelen';
$l["enablebackup"]='Schakel backup in';
$l["disablebackup"]='Schakel backup uit';
$l["successdisabledbackup"]='Backup is succesvol uitgeschakeld!';
$l["publishSuccess"]='Backup is succesvol ingeschakeld.';
$l["helpText"]='Deze module kan een backup maken van bestanden en MySQL databases (U dient /home/mysqlbackup mee te nemen in de bronmappen). Het verstuurd de bestanden incrementeel via rsync icm. SSH.  Wanneer u de backup inschakeld zal een publieke SSH sleutel worden verzonden en worden gecontrolleerd of de doelmap bestaat. Standaard wordt de backup om middernacht gemaakt. U kunt het schema aanpassen of aanmaken in /etc/cron.d/groupoffice-backup. U kunt de backup ook handmatig starten door "php /usr/share/groupoffice/modules/backupmanager/cron.php" uit te voeren op de terminal.';