<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('backupmanager'));

$lang['backupmanager']['name']='Backup Beheer';
$lang['backupmanager']['description']='Configureer uw backup cronjob';
$lang['backupmanager']['save_error']='Fout opgetreden bij het opslaan van de instellingen.';
$lang['backupmanager']['empty_key']='Sleutel is leeg';
$lang['backupmanager']['connection_error']='Kon geen verbinding maken met de server';
$lang['backupmanager']['no_mysql_config']='{product_name} heeft geen mysql config bestand kunnen vinden. Dit is nodig om een backup te kunnen maken van de volledige database. Je kunt deze zelf aanmaken door een bestand met de naam backupmanager.inc.php in /etc/groupoffice/ te plaatsen met de volgende inhoud:
    <br /><br />&lt;?php<br />
    $bm_config[\'mysql_user\'] = \'\';<br />
    $bm_config[\'mysql_pass\'] = \'\';<br />
    ?><br /><br />
    Zonder dit bestand worden de backups wel uitgevoerd, echter niet van de database.';
$lang['backupmanager']['target_does_not_exist']='De doelmap bestaat niet!';