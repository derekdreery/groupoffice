<?php


$l['name']='Gestor de Backups';
$l['description']='Configureu el vostre cronjob de backups';
$l['save_error']='Error desant la configuració';
$l['empty_key']='La clau està buida';
$l['connection_error']='No s\'ha pogut connectar amb el servidor';
$l['no_mysql_config']='{product_name} no ha pogut trobar el fitxer de configuració de MySQL. Aquest fitxer s\'utilitza per crear una còpia de seguretat complerta de la base de dades. Podeu crearla vosaltres mateixos afegint un fitxer backupmanager.inc.php a /etc/groupoffice/ amb el següent contingut:;
    <br /><br />&lt;?php<br />;
    $bm_config[\'mysql_user\'] = \'\';<br />;
    $bm_config[\'mysql_pass\'] = \'\';<br />
    <br /><br />;
    Sense aquest fitxer les còpies de seguretat encara es creen, però no des de la base de dades.';
$l['target_does_not_exist']='La carpeta destí no existeix!';

$l["backupmanager"]='Gestor de còpies de seguretat';
$l["rmachine"]='Ordinador remot';
$l["rport"]='Port';
$l["rtarget"]='Carpeta destí';
$l["sources"]='Carpetes orígen';
$l["rotations"]='Rotacions';
$l["quiet"]='Silenciós';
$l["emailaddresses"]='Adreces d\'e-mail';
$l["emailsubject"]='Assumpte d\'e-mail';
$l["rhomedir"]='Homedir remot';
$l["rpassword"]='Contrasenya remota';
$l["publish"]='Publicar';
$l["enablebackup"]='Iniciar còpia de seguretat';
$l["disablebackup"]='Aturar còpia de seguretat';
$l["successdisabledbackup"]='La còpia de seguretat s\'ha desactivat correctament!';
$l["publishkey"]='Activar còpia de seguretat';
$l["publishSuccess"]='La còpia de seguretat s\'ha activat correctament.';
$l["helpText"]='Aquest mòdul farà còpies de seguretat dels arxius i totes les bases de dades (assegureu-vos d\'incloure /home/mysqlbackup en les carpetes orígen) a un servidor remot mitjançant rsync i SSH. Quan activeu la còpia de seguretat es publicarà la clau pública SSH al servidor i comprovarà si la carpeta destí existeix. Llavors, primer assegureu-vos que la carpeta destí de la còpia de seguretat existeix. Per defecte, la còpia de seguretat es programa a mitjanit a /etc/cron.d/groupoffice-backup. Podeu ajustar-ne la programació en aquest arxiu o crear-la si no existeix. També podeu iniciar manualment la còpia de seguretat executant "php /usr/share/groupoffice/modules/backupmanager/cron.php" en el terminal.';