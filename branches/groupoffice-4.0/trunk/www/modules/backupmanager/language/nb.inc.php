<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('backupmanager'));

$lang['backupmanager']['name']='Backupmodul';
$lang['backupmanager']['description']='Sette opp din backup cronjobb';
$lang['backupmanager']['save_error']='Feil ved lagring av instillinger';
$lang['backupmanager']['empty_key']='Nøkkelen er tom';
$lang['backupmanager']['connection_error']='Kan ikke koble til serveren';
$lang['backupmanager']['no_mysql_config']='{product_name} kunne ikke finne en mysql config fil. Denne filen brukes for å lage en backup av den totale databasen. Du kan lage denne filen selv ved å opprette filen backupmanager.inc.php i /etc/groupoffice/ med følgende innhold:
    <br /><br />&lt;?php<br />
    $bm_config[\'mysql_user\'] = \'\';<br />
    $bm_config[\'mysql_pass\'] = \'\';<br />
    ?><br /><br />
    Uten denne filen blir det likevel tatt backup, men ikke av databasen.';
$lang['backupmanager']['target_does_not_exist']='Målkatalogen finnes ikke!';