<?php
/////////////////////////////////////////////////////////////////////////////////
//
// Copyright Intermesh
// 
// This file is part of {product_name}. You should have received a copy of the
// {product_name} license along with {product_name}. See the file /LICENSE.TXT
// 
// If you have questions write an e-mail to info@intermesh.nl
//
// @copyright Copyright Intermesh
// @version $Id: fr.inc.php 6616 2011-01-13 09:36:17Z mschering $
// @author Merijn Schering <mschering@intermesh.nl>
//
// French Translation
// Version : 3.7.29 
// Author : Lionel JULLIEN
// Date : September, 27 2011
//
/////////////////////////////////////////////////////////////////////////////////

//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('backupmanager'));

$lang['backupmanager']['name']='Gestionnaire de sauvegardes';
$lang['backupmanager']['description']='Configurer vos tâches de sauvegardes planifiées';
$lang['backupmanager']['save_error']='Erreur lors de la sauvegarde des paramètres';
$lang['backupmanager']['empty_key']='La clé est vide';
$lang['backupmanager']['connection_error']='Impossible de se connecter au serveur';
$lang['backupmanager']['no_mysql_config']='{product_name} n\'a pas trouvé de fichier de configuration pour MySQL. Ce fichier est nécessaire pour créer une sauvegarde de la base de données complète. Vous pouvez créer ce fichier vous même en ajoutant un fichier nommé backupmanager.inc.php dans /etc/groupoffice/ contenant les lignes suivantes :
    <br /><br />&lt;?php<br />
    $bm_config[\'mysql_user\'] = \'\';<br />
    $bm_config[\'mysql_pass\'] = \'\';<br />
    ?><br /><br />
    Sans ce fichier les tâches de sauvegardes peuvent toujours être crées, mais pas depuis la base de données.';
$lang['backupmanager']['target_does_not_exist']='Le répertoire de destination n\'existe pas !';
?>
