<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 *
 * French Translation
 * Author : Lionel JULLIEN
 * Date : September, 27 2010
 */

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('blacklist'));
$lang['blacklist']['name']='IP blacklist';
$lang['blacklist']['description']='Module qui bloque les adresses IP apres 5 echec d\'authentification de suite.';
$lang['blacklist']['ip']='IP';
$lang['blacklist']['ips']='Addresses IP';
$lang['blacklist']['blacklisted']='Votre adresse IP (%s) vient d\'être bloqué car vous avez effectué 5 tentatives d\'authentification infructueuses de suite. Veuillez nous contacter en fournissant votre adresse IP afin de la débloquer.';
