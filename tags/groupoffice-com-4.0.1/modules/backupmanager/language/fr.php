<?php

$l["backupmanager"]='Gestionnaire de sauvegarde';
$l["rmachine"]='Machine distante';
$l["rport"]='Port';
$l["rtarget"]='Répertoire de destination';
$l["sources"]='Répertoires source';
$l["rotations"]='Rotations';
$l["quiet"]='Silencieux';
$l["emailaddresses"]='Adresses email';
$l["emailsubject"]='Sujet de l\'email';
$l["rhomedir"]='Remote homedir';
$l["rpassword"]='Mot de passe';
$l["publish"]='Publier';
$l["enablebackup"]='Démarrer la sauvegarde';
$l["disablebackup"]='Arrêter la sauvegarde';
$l["successdisabledbackup"]='La sauvegarde a bien été désactivée !';
$l["publishkey"]='Activer la sauvegarde';
$l["publishSuccess"]='La sauvegarde a bien été activée.';
$l["helpText"]='Ce module va sauvegarder les fichiers et toutes les bases de données MySQL (soyez sur d\'inclure /home/mysqlbackup dans les répertoires sources) sur un serveur distant en utilisant rsync et SSH. Lorsque vous activez la sauvegarde cela va envoyer la clé publique au serveur SSH et vérifier que le répertoire de destination existe. Veuillez donc vérifier que ce répertoire distant existe. Par défaut la sauvegarde est configurée pour s\'exécuter a minuit dans /etc/cron.d/groupoffice-backup. Vous pouvez modifier le planning dans ce fichier ou le créer s\'il n\'existe pas. Vous pouvez également exécuter la sauvegarde manuellement depuis le terminal avec la commande "php /usr/share/groupoffice/modules/backupmanager/cron.php".';
$l['name']='Gestionnaire de sauvegardes';
$l['description']='Configurer vos tâches de sauvegardes planifiées';
$l['save_error']='Erreur lors de la sauvegarde des paramètres';
$l['empty_key']='La clé est vide';
$l['connection_error']='Impossible de se connecter au serveur';
$l['no_mysql_config']='{product_name} n\'a pas trouvé de fichier de configuration pour MySQL. Ce fichier est nécessaire pour créer une sauvegarde de la base de données complète. Vous pouvez créer ce fichier vous même en ajoutant un fichier nommé backupmanager.inc.php dans /etc/groupoffice/ contenant les lignes suivantes :;
    <br /><br />&lt;?php<br />;
    $bm_config[\'mysql_user\'] = \'\';<br />;
    $bm_config[\'mysql_pass\'] = \'\';<br />
    <br /><br />;
    Sans ce fichier les tâches de sauvegardes peuvent toujours être crées, mais pas depuis la base de données.';
$l['target_does_not_exist']='Le répertoire de destination n\'existe pas !';
