<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('backupmanager'));

$lang['backupmanager']['name']='Gestor de Backups';
$lang['backupmanager']['description']='Configureu el vostre cronjob de backups';
$lang['backupmanager']['save_error']='Error desant la configuració';
$lang['backupmanager']['empty_key']='La clau està buida';
$lang['backupmanager']['connection_error']='No s\'ha pogut connectar amb el servidor';
$lang['backupmanager']['no_mysql_config']='{product_name} no ha pogut trobar el fitxer de configuració de MySQL. Aquest fitxer s\'utilitza per crear una còpia de seguretat complerta de la base de dades. Podeu crearla vosaltres mateixos afegint un fitxer backupmanager.inc.php a /etc/groupoffice/ amb el següent contingut:
    <br /><br />&lt;?php<br />
    $bm_config[\'mysql_user\'] = \'\';<br />
    $bm_config[\'mysql_pass\'] = \'\';<br />
    ?><br /><br />
    Sense aquest fitxer les còpies de seguretat encara es creen, però no des de la base de dades.';
$lang['backupmanager']['target_does_not_exist']='La carpeta destí no existeix!';
?>