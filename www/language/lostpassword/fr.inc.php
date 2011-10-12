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
require($GLOBALS['GO_LANGUAGE']->get_fallback_base_language_file('lostpassword'));

$lang['lostpassword']['success']='<h1>Modification du mot de passe</h1><p>Votre mot de passe a été changé avec succès. Vous pouvez maintenant aller à la page de connexion.</p>';
$lang['lostpassword']['send']='Envoyer';
$lang['lostpassword']['login']='Connexion';
$lang['lostpassword']['lost_password_subject']='Votre demande de nouveau mot de passe Group-Office';
$lang['lostpassword']['lost_password_body']='%s,

Vous avez demandé un nouveau mot de passe pour %s. Votre nom d\'utilisateur est "%s".

Cliquer sur le lien ci-dessous (ou copier le dans votre navigateur) pour changer votre mot de passe:

%s

Si vous n\'avez pas fait une demande de changement de mot de passe, suppprimer cet e-mail.';
$lang['lostpassword']['lost_password_error']='Impossible de trouver l\'adresse email demandée.';
$lang['lostpassword']['lost_password_success']='Un nouveau mot de passe vient de vous être envoyé.';
$lang['lostpassword']['enter_password']='Entrer un nouveau mot de passe';
$lang['lostpassword']['new_password']='Nouveu mot de passe';
$lang['lostpassword']['lost_password']='Mot de passe perdu';
$lang['lostpassword']['confirm_password']='Confirmer votre mot de passe';
?>
