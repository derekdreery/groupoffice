<?php
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

//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Tâches';
$lang['tasks']['description']='Module de gestion des tâches';
$lang['link_type'][12]=$lang['tasks']['task']='Tâche';
$lang['tasks']['status']='Statut';
$lang['tasks']['scheduled_call']='Appel téléphonique programmé le  %s';
$lang['tasks']['statuses']['NEEDS-ACTION']= 'Action nécessaire';
$lang['tasks']['statuses']['ACCEPTED']= 'Accepté';
$lang['tasks']['statuses']['DECLINED']= 'Décliné';
$lang['tasks']['statuses']['TENTATIVE']= 'Tentative';
$lang['tasks']['statuses']['DELEGATED']= 'Délégué';
$lang['tasks']['statuses']['COMPLETED']= 'Terminé';
$lang['tasks']['statuses']['IN-PROCESS']= 'En cours';
$lang['tasks']['import_success']='%s tâches importées avec succès';
$lang['tasks']['call']='Appel Téléphonique';
$lang['tasks']['dueAtdate']='Terminée le %s';
$lang['tasks']['list']='Liste de tâches';
$lang['tasks']['tasklistChanged']="* Liste de tâches modifiée de '%s' à '%s'";
$lang['tasks']['statusChanged']="* Statut modifé de '%s' à '%s'";
$lang['tasks']['multipleSelected']='Plusieurs listes de tâches sélectionnées';
$lang['tasks']['incomplete_delete']='Vous n\'avez pas les droits nécessaires pour supprimer toutes les tâches sélectionnées';

