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
require($GO_LANGUAGE->get_fallback_language_file('smime'));

$lang['smime']['name']='Support de SMIME';
$lang['smime']['description']='Améliorer le module email avec le support de SMIME (signature et encryption).';
$lang['smime']['noPublicCertForEncrypt']="Impossible de crypter le message car vous ne possédez pas de certificat public pour %s. Ouvrez un message signé du destiantaire et vérifiez la signature afin d'importer la clé publique.";
$lang['smime']['noPrivateKeyForDecrypt']="Ce message est crypté et vous ne possédez pas la clé privée nécessaire au décryptage de ce message.";
$lang['smime']['badGoLogin']="Le mot de passe {product_name} est incorrecte.";
$lang['smime']['smime_pass_matches_go']="Votre mot de passe de la clé SMIME correspond bien au mot de passe {product_name}. Ceci est interdit pour des raisons des sécurité !";
$lang['smime']['smime_pass_empty']="Votre clé SMIME n'a pas de mot de passe. Ceci est interdit pour des raisons des sécurité !";
$lang['smime']['invalidCert']="Le certicat est invalide !";
$lang['smime']['validCert']="Le certificat est valide !";
$lang['smime']['certEmailMismatch']="Le certificat est valide mais l'email du certificat ne correspond pas à celui de l'expéditeur de ce message.";
$lang['smime']['decryptionFailed']='Le décryptage SMIME de ce message à echoué !';
