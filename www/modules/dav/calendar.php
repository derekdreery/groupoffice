<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

// settings
require('../../Group-Office.php');

//go_debug($_POST);

require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc.php');
require_once($GO_MODULES->modules['calendar']['class_path'].'go_ical.class.inc');
require_once($GO_CONFIG->class_path.'ical2array.class.inc');


// If you want to run the SabreDAV server in a custom location (using mod_rewrite for instance)
// You can override the baseUri here.
// $baseUri = '/';

$dsn = 'mysql:dbname='.$GO_CONFIG->db_name.';host='.$GO_CONFIG->db_host;

$pdo = new PDO($dsn, $GO_CONFIG->db_user, $GO_CONFIG->db_pass);


// Files we need
require_once 'SabreDAV/lib/Sabre/autoload.php';

require('GO_CalDAV_Server.class.inc.php');
require('GO_CalDAV_Backend.class.inc.php');
require('GO_DAV_Auth_Backend.class.inc.php');

// The object tree needs in turn to be passed to the server class
$server = new GO_CalDAV_Server($pdo);


if (isset($baseUri))
    $server->setBaseUri($baseUri);

// Support for html frontend
$browser = new Sabre_DAV_Browser_Plugin(false);
$server->addPlugin($browser);

// And off we go!
$server->exec();
