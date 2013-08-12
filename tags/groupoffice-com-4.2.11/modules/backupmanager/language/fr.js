/////////////////////////////////////////////////////////////////////////////////
//
// Copyright Intermesh
// 
// This file is part of Group-Office. You should have received a copy of the
// Group-Office license along with Group-Office. See the file /LICENSE.TXT
// 
// If you have questions write an e-mail to info@intermesh.nl
//
// @copyright Copyright Intermesh
// @version $Id$
// @author Merijn Schering <mschering@intermesh.nl>
//
// French Translation
// Version : 3.7.29 
// Author : Lionel JULLIEN
// Date : September, 27 2011
//
/////////////////////////////////////////////////////////////////////////////////

GO.backupmanager.lang.backupmanager='Gestionnaire de sauvegarde';
GO.backupmanager.lang.rmachine='Machine distante';
GO.backupmanager.lang.rport='Port';
GO.backupmanager.lang.rtarget='Répertoire de destination';
GO.backupmanager.lang.sources='Répertoires source';
GO.backupmanager.lang.rotations='Rotations';
GO.backupmanager.lang.quiet='Silencieux';
GO.backupmanager.lang.emailaddresses='Adresses email';
GO.backupmanager.lang.emailsubject='Sujet de l\'email';
GO.backupmanager.lang.rhomedir='Remote homedir';
GO.backupmanager.lang.rpassword='Mot de passe';
GO.backupmanager.lang.publish='Publier';
GO.backupmanager.lang.enablebackup='Démarrer la sauvegarde';
GO.backupmanager.lang.disablebackup='Arrêter la sauvegarde';
GO.backupmanager.lang.successdisabledbackup='La sauvegarde a bien été désactivée !';
GO.backupmanager.lang.publishkey='Activer la sauvegarde';
GO.backupmanager.lang.publishSuccess='La sauvegarde a bien été activée.';
GO.backupmanager.lang.helpText='Ce module va sauvegarder les fichiers et toutes les bases de données MySQL (soyez sur d\'inclure /home/mysqlbackup dans les répertoires sources) sur un serveur distant en utilisant rsync et SSH. Lorsque vous activez la sauvegarde cela va envoyer la clé publique au serveur SSH et vérifier que le répertoire de destination existe. Veuillez donc vérifier que ce répertoire distant existe. Par défaut la sauvegarde est configurée pour s\'exécuter a minuit dans /etc/cron.d/groupoffice-backup. Vous pouvez modifier le planning dans ce fichier ou le créer s\'il n\'existe pas. Vous pouvez également exécuter la sauvegarde manuellement depuis le terminal avec la commande "php /usr/share/groupoffice/modules/backupmanager/cron.php".';
